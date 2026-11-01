<?php
header('Cache-Control: no-store');

$skinPaths = glob(__DIR__ . '/../skins/*.png');
$skinPaths = $skinPaths === false ? array() : array_filter($skinPaths, 'is_file');
usort($skinPaths, function ($left, $right) {
    $newestFirst = filemtime($right) - filemtime($left);
    return $newestFirst !== 0 ? $newestFirst : strcasecmp(basename($left), basename($right));
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Skins Gallery · Agar Clone</title>
    <link rel="icon" type="image/png" href="../split.png">
    <link rel="stylesheet" href="../uploads.css">
    <link rel="stylesheet" href="gallery.css">
</head>
<body>
<main class="upload-card gallery-card">
    <a class="back-link" href="../index.html">&larr; Back to game</a>
    <header class="gallery-header">
        <div>
            <h1>Skins gallery</h1>
            <p class="text-muted">Find a new look for your cell.</p>
        </div>
        <a class="btn btn-primary" href="../uploads.html">Upload a skin</a>
    </header>

    <div class="skin-tip">
        Add a skin tag to your nickname, e.g. <code>{earth}Player</code>.
        Only your player name appears on your cell and the leaderboard.
    </div>

    <p class="gallery-summary text-muted">
        <?= count($skinPaths) ?> <?= count($skinPaths) === 1 ? 'skin' : 'skins' ?>
        <span class="sort-label">Newest first</span>
    </p>
    <?php if (empty($skinPaths)): ?>
        <p class="gallery-empty text-muted">No skins yet. <a href="../uploads.html">Upload the first one!</a></p>
    <?php else: ?>
        <ul class="skin-list">
            <?php foreach ($skinPaths as $skinPath):
                $skinName = htmlspecialchars(pathinfo($skinPath, PATHINFO_FILENAME), ENT_QUOTES, 'UTF-8');
                $imageUrl = '../skins/' . rawurlencode(basename($skinPath));
            ?>
                <li>
                    <figure class="skin-card">
                        <img class="skin-image" src="<?= $imageUrl ?>" alt="<?= $skinName ?> skin"
                             width="112" height="112" loading="lazy">
                        <figcaption class="skin-name"><code>{<?= $skinName ?>}</code></figcaption>
                    </figure>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <footer>Your own design? <a href="../uploads.html">Upload a custom skin</a></footer>
</main>
</body>
</html>
