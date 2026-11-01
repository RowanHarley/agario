<?php
const MAX_SKIN_BYTES = 2 * 1024 * 1024;
const MAX_SKIN_NAME_LENGTH = 13;
const MAX_SKIN_DIMENSION = 4096;

function upload_response($success, $message, $statusCode, $skinName = '') {
    http_response_code($statusCode);
    header('Cache-Control: no-store');
    header('X-Content-Type-Options: nosniff');

    $accept = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : '';
    if (strpos($accept, 'application/json') !== false) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array('success' => $success, 'message' => $message, 'skinName' => $skinName));
        exit;
    }

    header('Content-Type: text/html; charset=utf-8');
    $title = $success ? 'Skin uploaded!' : 'Upload unsuccessful';
    $safeMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    $statusClass = $success ? 'status-success' : 'status-error';
    $actionUrl = $success ? 'index.html' : 'uploads.html';
    $actionLabel = $success ? 'Play now' : 'Try again';
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= $title ?> · Agar Clone</title>
        <link rel="icon" type="image/png" href="split.png">
        <link rel="stylesheet" href="uploads.css">
    </head>
    <body>
    <main class="upload-card">
        <a class="back-link" href="index.html">&larr; Back to game</a>
        <h1><?= $title ?></h1>
        <p class="status <?= $statusClass ?>" role="status"><?= $safeMessage ?></p>
        <a class="btn btn-primary" href="<?= $actionUrl ?>"><?= $actionLabel ?></a>
        <footer>
            <a href="uploads.html">Upload another skin</a> &middot;
            <a href="gallery/">Skins gallery</a>
        </footer>
    </main>
    </body>
    </html>
    <?php
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    upload_response(false, 'Please choose an image on the upload page first.', 405);
}

$upload = isset($_FILES['fileToUpload']) ? $_FILES['fileToUpload'] : null;
if (!is_array($upload) || !isset($upload['error']) || !is_int($upload['error'])) {
    upload_response(false, 'No image was received. Choose a PNG up to 2 MiB. The server may also limit upload sizes.', 400);
}

switch ($upload['error']) {
    case UPLOAD_ERR_OK:
        break;
    case UPLOAD_ERR_INI_SIZE:
    case UPLOAD_ERR_FORM_SIZE:
        upload_response(false, 'The image exceeds the upload limit. Choose a PNG up to 2 MiB; your server may have a lower limit.', 413);
        break;
    case UPLOAD_ERR_NO_FILE:
        upload_response(false, 'Please choose a PNG image.', 400);
        break;
    case UPLOAD_ERR_PARTIAL:
        upload_response(false, 'The upload was interrupted. Please try again.', 400);
        break;
    default:
        upload_response(false, 'The server could not receive your image. Please try again later.', 500);
}

if (!isset($upload['name'], $upload['tmp_name']) ||
    !is_string($upload['name']) || !is_string($upload['tmp_name']) ||
    !is_uploaded_file($upload['tmp_name'])) {
    upload_response(false, 'Invalid upload. Please choose a PNG image.', 400);
}

// A strict filename prevents paths, executable extensions and unsafe gallery names.
if (!preg_match('/\A([a-z0-9-]+)\.png\z/i', $upload['name'], $matches)) {
    upload_response(false, 'Use a PNG filename containing only letters, numbers or hyphens, e.g. earth.png.', 400);
}
$skinName = strtolower($matches[1]);
if (strlen($skinName) > MAX_SKIN_NAME_LENGTH) {
    upload_response(false, 'Use a skin name of 13 characters or fewer so its tag fits in your nickname.', 400);
}

$temporaryPath = $upload['tmp_name'];
$size = filesize($temporaryPath);
if ($size === false || $size === 0 || $size > MAX_SKIN_BYTES) {
    upload_response(false, 'Choose a non-empty PNG image no larger than 2 MiB.', 413);
}

$image = @getimagesize($temporaryPath);
if ($image === false || $image[2] !== IMAGETYPE_PNG) {
    upload_response(false, 'This file is not a valid PNG image.', 400);
}
if ($image[0] < 1 || $image[1] < 1 ||
    $image[0] > MAX_SKIN_DIMENSION || $image[1] > MAX_SKIN_DIMENSION) {
    upload_response(false, 'Your skin must be no larger than 4096 × 4096 pixels.', 400);
}

$directory = __DIR__ . '/skins/';
if (!is_dir($directory) || !is_writable($directory)) {
    upload_response(false, 'Uploads are unavailable. The server administrator must make the skins folder writable.', 503);
}
$target = $directory . $skinName . '.png';

// Exclusive creation also prevents simultaneous uploads from overwriting a skin.
$destination = @fopen($target, 'xb');
if ($destination === false) {
    if (file_exists($target)) {
        upload_response(false, 'That skin name is already taken. Rename your file and try again.', 409);
    }
    upload_response(false, 'The server could not save your skin. Please try again later.', 500);
}
$source = @fopen($temporaryPath, 'rb');
if ($source === false) {
    fclose($destination);
    unlink($target);
    upload_response(false, 'The server could not read your upload. Please try again later.', 500);
}
$copied = @stream_copy_to_stream($source, $destination);
fclose($source);
$closed = fclose($destination);
if ($copied !== $size || !$closed) {
    unlink($target);
    upload_response(false, 'The server could not finish saving your skin. Please try again later.', 500);
}

upload_response(true, 'Skin uploaded! Use {' . $skinName . '} in your nickname. It may take up to 30 seconds to appear in an open game.', 201, $skinName);
