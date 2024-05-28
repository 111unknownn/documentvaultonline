<?php
    session_start(); // Start the session
    include '../config/config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload file</title>

    <script src="https://kit.fontawesome.com/fe90e88d78.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/admin_pannel.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="../images/favicons.png">
</head>
<style>
     #uploadVersionDiv {
        display: none;
    }
</style>
<body class="d-flex align-items-center justify-content-center vh-100">


    <!-- Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
          <button type="button" class="btn-close" aria-label="Close" onclick="goBack()"></button>
          </div>
          <div class="modal-body">
            <!-- Correct placement of the target attribute -->
            <form id="uploadForm" class="shadow rounded p-3" target="_self">
                <h2 class="text-center">Upload File</h2>
                <div class="mb-3">
                    <label for="uploadFile" class="form-label">File (Max: 25mb)</label>
                    <input required id="uploadFile" name="uploadFile" type="file" accept=".xlsx,.docx, .doc, .ppt, .pptx, .txt, .zip, .pdf" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="uploadTitle" class="form-label">Title</label>
                    <input required id="uploadTitle" name="uploadTitle" type="text" class="form-control" placeholder="Enter title...">
                </div>
                <div class="mb-3">
                    <label for="uploadAuthor" class="form-label">Author</label>
                    <input required id="uploadAuthor" name="uploadAuthor" type="text" class="form-control" placeholder="Enter author...">
                </div>
                <div class="mb-3">
                    <label for="uploadKeywords" class="form-label">Keywords (Searchable)</label>
                    <input required id="uploadKeywords" name="uploadKeywords" type="text" class="form-control" placeholder="ex: (documents, .ppt)">
                </div>
                <div id="uploadVersionDiv" class="mb-3">
                <label for="uploadVersion" class="form-label">Version</label>
                <input  id="uploadVersion" name="uploadVersion" type="text" class="form-control" placeholder="ex: 1.0.0">
            </div>
             <footer class="d-flex align-items-center justify-content-between gap-2">
    <div class="text-start">
        <button type="submit" class="btn btn-primary" id="uploadButton">Upload</button>
    </div>
    <p class="mb-0 d-none" id="uploadLabel">Uploading & Encrypting, please wait...</p>
    <div class="progress d-none" style="width: 100%;">
        <div class="progress-bar" role="progressbar" style="width: 0%;" id="progressBar">
            <span id="progressText">0%</span>
        </div>
    </div>
    <!-- Customized spinner element -->
    <div class="spinner-border text-success d-none" role="status" id="loadingSpinner">
        <span class="visually-hidden">Loading...</span>
    </div>
</footer>



            </form>
          </div>
          <div class="modal-footer">
            
        </div>
      </div>
    </div>
 <!-- External JavaScript -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" defer></script>

<script type="text/javascript">
$(document).ready(function() {
    console.log("Before modal initialization");
    // Initialize the modal without showing it immediately
    $('#uploadModal').modal({
        backdrop: 'static',  // Disable clicking outside the modal
        keyboard: false  // Disable closing the modal with the keyboard
    });

    // Show the modal after a short delay (e.g., 500 milliseconds)
    setTimeout(function() {
        $('#uploadModal').modal('show');
        console.log("Modal shown");
    }, 500);

    // Upload file
    $('#uploadForm').on('submit', function (e) {
        e.preventDefault();  // Prevent the default form submission behavior

        // Show customized loading spinner
        $('#loadingSpinner').removeClass('d-none');
        // Show uploading label
        $('#uploadLabel').removeClass('d-none');
        // Disable upload button
        $('#uploadButton').prop('disabled', true);
        // Show progress bar
        $('.progress').removeClass('d-none');

        var formData = new FormData(this);
        $.ajax({
            url: "main",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            xhr: function () {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function (evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = (evt.loaded / evt.total) * 100;
                        $('#progressBar').width(percentComplete + '%');
                        $('#progressText').text(percentComplete.toFixed(2) + '%');
                    }
                }, false);
                return xhr;
            },
            success: function (response) {
                if ($.trim(response) === "") {
                    alert("Upload file completed");
                    // Hide customized loading spinner on success
                    $('#loadingSpinner').addClass('d-none');
                    // Hide uploading label
                    $('#uploadLabel').addClass('d-none');
                    // Enable upload button
                    $('#uploadButton').prop('disabled', false);
                    // Clear form fields
                    $('#uploadForm')[0].reset();
                    // Hide progress bar
                    $('.progress').addClass('d-none');
                    // Close the modal
                    $('#uploadModal').modal('hide');
                    // Reload the page
                    window.location.reload();
                } else {
                    alert(response);
                    // Hide customized loading spinner on error
                    $('#loadingSpinner').addClass('d-none');
                    // Hide uploading label
                    $('#uploadLabel').addClass('d-none');
                    // Enable upload button
                    $('#uploadButton').prop('disabled', false);
                    // Hide progress bar
                    $('.progress').addClass('d-none');
                }
            },
            uploadProgress: function (event, position, total, percentComplete) {
                $('#progressBar').width(percentComplete + '%');
                $('#progressText').text(percentComplete.toFixed(2) + '%');
            },
            error: function (xhr, status, error) {
                alert("An error occurred: " + error);
            }
        });
    });
});



</script>



</body>
</html>
