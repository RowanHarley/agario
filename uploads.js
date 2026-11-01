(function () {
    "use strict";

    var MAX_UPLOAD_BYTES = 2 * 1024 * 1024;
    var MAX_SKIN_NAME_LENGTH = 13;
    var MAX_IMAGE_DIMENSION = 4096;
    var MAX_NICKNAME_LENGTH = 15;
    var form = document.getElementById("uploadForm");
    var input = document.getElementById("fileToUpload");
    var button = document.getElementById("uploadButton");
    var status = document.getElementById("uploadStatus");
    var preview = document.getElementById("skinPreview");
    var placeholder = document.getElementById("previewPlaceholder");
    var example = document.getElementById("nicknameExample");
    var previewUrl = null;

    function showStatus(message, success) {
        status.textContent = message;
        status.className = "status " + (success ? "status-success" : "status-error");
        status.hidden = false;
    }

    function validateFile(file) {
        if (!file) return "Please choose a PNG image.";
        if (!/^[a-z0-9-]+\.png$/i.test(file.name)) {
            return "Use a PNG filename containing only letters, numbers or hyphens, e.g. earth.png.";
        }
        if (file.name.length - 4 > MAX_SKIN_NAME_LENGTH) {
            return "Use a skin name of 13 characters or fewer so its tag fits in your nickname.";
        }
        if (file.size === 0) return "This file is empty. Please choose a PNG image.";
        if (file.size > MAX_UPLOAD_BYTES) return "Your skin must be 2 MiB or smaller.";
        return "";
    }

    function showFileError(message) {
        input.setCustomValidity(message);
        showStatus(message, false);
    }

    preview.onload = function () {
        if (preview.naturalWidth > MAX_IMAGE_DIMENSION || preview.naturalHeight > MAX_IMAGE_DIMENSION) {
            showFileError("Your skin must be no larger than 4096 × 4096 pixels.");
            return;
        }
        preview.hidden = false;
        placeholder.hidden = true;
    };

    preview.onerror = function () {
        showFileError("This image could not be previewed. Please choose a valid PNG image.");
    };

    input.addEventListener("change", function () {
        input.setCustomValidity("");
        status.hidden = true;
        preview.hidden = true;
        placeholder.hidden = false;
        example.textContent = "{earth}Player";
        if (previewUrl) {
            URL.revokeObjectURL(previewUrl);
            previewUrl = null;
        }

        var file = input.files[0];
        if (!file) return;
        var error = validateFile(file);
        if (error) {
            showFileError(error);
            return;
        }

        var skinTag = "{" + file.name.slice(0, -4).toLowerCase() + "}";
        var nickname = "Player".slice(0, MAX_NICKNAME_LENGTH - skinTag.length);
        example.textContent = skinTag + nickname;
        previewUrl = URL.createObjectURL(file);
        preview.src = previewUrl;
    });

    function setUploading(uploading) {
        button.disabled = uploading;
        input.disabled = uploading;
        button.textContent = uploading ? "Uploading…" : "Upload skin";
        form.setAttribute("aria-busy", String(uploading));
    }

    // Older browsers can still submit the form normally.
    if (!window.fetch || !window.FormData) return;

    form.addEventListener("submit", function (event) {
        event.preventDefault();
        if (button.disabled) return;
        var error = validateFile(input.files[0]) || input.validationMessage;
        if (error) {
            showStatus(error, false);
            return;
        }

        var data = new FormData(form);
        setUploading(true);
        status.hidden = true;

        fetch(form.action, {
            method: "POST",
            headers: {Accept: "application/json"},
            body: data
        }).then(function (response) {
            return response.json();
        }).then(function (result) {
            showStatus(result.message, result.success === true);
        }).catch(function () {
            showStatus("Could not confirm the upload. Check your connection and the gallery before trying again.", false);
        }).then(function () {
            setUploading(false);
        });
    });
}());
