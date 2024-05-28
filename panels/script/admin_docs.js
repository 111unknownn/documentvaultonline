"text/javascript">


// Sort by
$('#sortByDocuments').on('change', function() {
$('#sortByDocumentsButton')[0].click();
});


// Filter By Change Event
$('#filterByDocuments').off('change').on('change', function () {
// Get the search value
var searchValue = $('[name="q"]').val();

// Update the hidden input with the search value
$('[name="q"]').val(searchValue);

// Trigger form submission
$('#filterByDocumentsButton')[0].click();
});

// Key Document Click Event
$(".key-document-cta").off('click').on('click', function (e) {
e.preventDefault();

var documentId = $(this).attr('data-id');
var fileName = $(this).attr('data-name');
$('#keyDocumentModalLabel').text(fileName);

$.get("main.php", { keyDocumentId: documentId }, function (response, status) {
if (status) {
    $('#getDocumentKey').val(response);
}
});
});


// download document
$('.download-document-cta').off('click').on('click', function(){
    var documentId = $(this).attr('data-id')
    var fileName = $(this).attr('data-name')
    $('#downloadDocumentModalLabel').text(fileName)

    // download now
    $('#downloadDocumentForm').off('submit').on('submit', function(e){
        e.preventDefault()
        $('#downloadDocumentDownloadNow').prop("disabled",true)
        $('#downloadDocumentLabel').removeClass('d-none')

        var formData = new FormData(this)
        formData.append("downloadDocumentId",documentId)
        $.ajax({
            url: "main.php",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(response){
                if($.trim(response) === "incorrect"){
                    alert('Incorrect KEY!')
                    $('#downloadDocumentDownloadNow').prop("disabled",false)
                    $('#downloadDocumentLabel').addClass('d-none')
                }
                else{
                    var getResponse = response.substring(0, response.indexOf(","))
                    var filePath = response.substring(response.indexOf(",")+1)

                    if(getResponse === "empty"){
                        $('#downloadDocumentDownloadNow').prop("disabled",false)
                        $('#downloadDocumentLabel').addClass('d-none')

                        // create temporary element
                        var body = document.body
                        var createElement = document.createElement("a")
                        createElement.setAttribute("href", 'encryptedFiles/'+filePath)
                        createElement.setAttribute("target", '_blank')
                        createElement.setAttribute("download", fileName)
                        body.appendChild(createElement)
                        createElement.click()

                    }
                    else if(getResponse === "correct"){
                        $('#downloadDocumentDownloadNow').prop("disabled",false)
                        $('#downloadDocumentLabel').addClass('d-none')

                        // create temporary element
                        var body = document.body
                        var createElement = document.createElement("a")
                        createElement.setAttribute("href", 'tmpFiles/'+filePath)
                        createElement.setAttribute("target", '_blank')
                        createElement.setAttribute("download", fileName)
                        body.appendChild(createElement)
                        createElement.click()
                    }
                    else{
                        alert(response)
                        $('#downloadDocumentDownloadNow').prop("disabled",false)
                        $('#downloadDocumentLabel').addClass('d-none')
                    }
                }
            }
        })
    })
})

// edit document
$('.edit-document-cta').off('click').on('click', function(){
    var documentId = $(this).attr('data-id')
    var fileName = $(this).attr('data-name')

    $.get("main.php", {editDocumentId : documentId}, function(response, status){
        if(status){
            $("#updateFileForm").html(response)

            // update file
            $('#updateFileForm').off('submit').on('submit', function(e){
                e.preventDefault()

                var formData = new FormData(this)
                $.ajax({
                    url: "main.php",
                    type: "POST",
                    data: formData, 
                    contentType: false,
                    processData: false,
                    success: function(response){
                        if($.trim(response) === ""){
                            alert("Updated succesfully")
                        }
                    }
                })
            })

            //change file
            $('#changeFileForm').off('submit').on('submit', function(e){
                e.preventDefault()
                $('#changeFileLabel').removeClass('d-none')
                $('#changeFileButton').prop('disabled', true)

                var formData = new FormData(this)
                formData.append("changeDocumentId", documentId)

                $.ajax({
                    url: "main.php",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response){
                        if($.trim(response) === ""){
                            alert("Upload file completed");
                            $('#changeFileLabel').addClass('d-none')
                            $('#changeFileButton').prop('disabled', false)
                            $('#changeFileForm input').val('')
                        }
                        else{
                            alert(response)
                            $('#changeFileLabel').addClass('d-none')
                            $('#changeFileButton').prop('disabled', false)
                        }
                    }
                })
            })
        }
    })
})

// view document
$('.view-document-cta').off('click').on('click', function(){
var documentId = $(this).attr('data-id');
var fileName = $(this).attr('data-name');

// Set the modal title
$('#viewDocumentModalLabel').text(fileName);

// Request the content from the server
$.get("main.php", {viewDocumentId: documentId}, function(response, status){
if(status === 'success'){
    // Set the content in the modal body
    $('#fileContent').html(response);
    
    // Show the modal
    $('#viewDocumentModal').modal('show');
} else {
    // Handle error
    console.error('Failed to fetch document content.');
}
});
});



// about document
$('.about-document-cta').off('click').on('click', function(){
    var documentId = $(this).attr('data-id')
    var fileName = $(this).attr('data-name')
    $('#aboutDocumentModalLabel').text(fileName)

    $.get("main.php", {aboutDocumentId : documentId}, function(response, status){
        if(status){
            $('#aboutModalBody').html(response)
        }
    })
})


  //CHANGE PASSWORD
$(document).ready(function () {
$('#changePasswordButton').on('click', function () {
console.log("Button clicked");

var newPassword = $('#newPassword').val();
var confirmPassword = $('#confirmPassword').val();

// Perform validation
if (newPassword !== confirmPassword) {
    alert("New password and confirm password do not match");
    return;
}

// Additional validation: Check for at least one uppercase letter and one special character
var uppercaseRegex = /[A-Z]/;
var specialCharacterRegex = /[!@#$%^&*()_+{}\[\]:;<>,.?~\\/-]/;

if (!uppercaseRegex.test(newPassword) || !specialCharacterRegex.test(newPassword)) {
    alert("Password must contain at least one uppercase letter and one special character");
    return;
}

// AJAX request to update password
$.ajax({
    url: "update_password.php", // Replace with your actual backend endpoint
    type: "POST",
    data: {
        newPassword: newPassword
    },
    dataType: "json", // Expect JSON response from the server
    success: function (response) {
        // Handle the response from the server
        if (response.status === "success") {
            alert("Password changed successfully");
            $('#settingsModal').modal('hide'); // Close the modal on success
        } else {
            alert("Error changing password: " + response.message);
        }
    },
    error: function (error) {
        alert("An error occurred: " + error.responseText);
    }
});
});
});

//UPDATE PROFILE
$(document).ready(function () {
// Fetch and display the user's current profile information
fetchCurrentProfile();

$('#updateProfileButton').on('click', function () {
    console.log("Update Profile Button clicked");

    var fullName = $('#updateFullName').val();
    var email = $('#updateEmail').val();

    // Perform validation (you can add more validation as needed)
    if (!fullName || !email) {
        alert("Please enter both full name and email");
        return;
    }

    // AJAX request to update profile
    $.ajax({
        url: "update_profile.php", // Replace with your actual backend endpoint
        type: "POST",
        data: {
            fullName: fullName,
            email: email
        },
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                alert("Profile updated successfully");
                // Fetch and display the updated profile information
                fetchCurrentProfile();
                $('#profileModal').modal('hide'); // Close the modal on success
            } else {
                alert("Error updating profile: " + response.message);
            }
        },
        error: function (error) {
            alert("An error occurred: " + error.responseText);
        }
    });
});

// Function to fetch and display the user's current profile information
function fetchCurrentProfile() {
    // AJAX request to fetch profile information
    $.ajax({
        url: "fetch_profile.php", // Replace with your actual backend endpoint
        type: "GET",
        dataType: "json",
        success: function (profile) {
            // Display current profile information
            $('#currentFullName').text(profile.fullName);
            $('#currentEmail').text(profile.email);
        },
        error: function (error) {
            console.log("Error fetching profile: " + error.responseText);
        }
    });
}
});


//open and close password eyes
document.addEventListener('DOMContentLoaded', function () {
// Function to toggle password visibility
function togglePasswordVisibility(inputId, openEyeId, closeEyeId) {
const passwordInput = document.getElementById(inputId);
const openEye = document.getElementById(openEyeId);
const closeEye = document.getElementById(closeEyeId);

openEye.addEventListener('click', function () {
    passwordInput.type = 'text';
    openEye.style.display = 'none';
    closeEye.style.display = 'block';
});

closeEye.addEventListener('click', function () {
    passwordInput.type = 'password';
    openEye.style.display = 'block';
    closeEye.style.display = 'none';
});

// Set the initial state to show closed eyes
passwordInput.type = 'password';
openEye.style.display = 'block';
closeEye.style.display = 'none';
}

// Toggle password visibility for new password
togglePasswordVisibility('newPassword', 'closeEyeNewPassword', 'openEyeNewPassword');

// Toggle password visibility for confirm password
togglePasswordVisibility('confirmPassword', 'closeEyeConfirmPassword', 'openEyeConfirmPassword');
});

//Prevent CLicking Outside The Notification Modal
$(document).ready(function() {
console.log("After modal initialization");$(document).ready(function() {
// Initialize the profile modal without showing it immediately
$('#profileModal').modal({
backdrop: 'static',  // Disable clicking outside the modal
keyboard: false  // Disable closing the modal with the keyboard
});

// Show the profile modal after a short delay (e.g., 500 milliseconds)
setTimeout(function() {
$('#profileModal').modal('hide');
}, 500);
});

// Initialize the modal without showing it immediately
$('#notificationModal').modal({
backdrop: 'static',  // Disable clicking outside the modal
keyboard: false  // Disable closing the modal with the keyboard
});

// Show the modal after a short delay (e.g., 500 milliseconds)
setTimeout(function() {
$('#notificationModal').modal('hide');
console.log("Modal shown");
}, 500);
});

//Prevent CLicking Outside The Settings Modal
$(document).ready(function() {
console.log("After modal initialization");$(document).ready(function() {
// Initialize the profile modal without showing it immediately
$('#profileModal').modal({
backdrop: 'static',  // Disable clicking outside the modal
keyboard: false  // Disable closing the modal with the keyboard
});

// Show the profile modal after a short delay (e.g., 500 milliseconds)
setTimeout(function() {
$('#settingsModal').modal('hide');
}, 500);
});

// Initialize the modal without showing it immediately
$('#settingsModal').modal({
backdrop: 'static',  // Disable clicking outside the modal
keyboard: false  // Disable closing the modal with the keyboard
});

// Show the modal after a short delay (e.g., 500 milliseconds)
setTimeout(function() {
$('#settingsModal').modal('hide');
console.log("Modal shown");
}, 500);
});

//Prevent CLicking Outside The Profile Modal
$(document).ready(function() {
console.log("After modal initialization");$(document).ready(function() {
// Initialize the profile modal without showing it immediately
$('#profileModal').modal({
backdrop: 'static',  // Disable clicking outside the modal
keyboard: false  // Disable closing the modal with the keyboard
});

// Show the profile modal after a short delay (e.g., 500 milliseconds)
setTimeout(function() {
$('#ProfileModal').modal('hide');
}, 500);
});

// Initialize the modal without showing it immediately
$('#ProfileModal').modal({
backdrop: 'static',  // Disable clicking outside the modal
keyboard: false  // Disable closing the modal with the keyboard
});

// Show the modal after a short delay (e.g., 500 milliseconds)
setTimeout(function() {
$('#ProfileModal').modal('hide');
console.log("Modal shown");
}, 500);
});

// Function to handle sending a message
function sendMessage() {
// Close the modal after 2 seconds and reload messages
setTimeout(function () {
    $('#sendMessageModal').modal('hide');
    loadMessages(1); // Assuming you have a loadMessages function, replace it with the correct function if needed
}, 2000);
}

$(document).ready(function () {
// Show/hide message input based on message type
$('#messageType').change(function () {
if ($(this).val() === 'file') {
    $('#fileAttachment').show();
    $('#messageInput').hide();
} else if ($(this).val() === 'message') {
    $('#fileAttachment').hide();
    $('#messageInput').show();
} else {
    $('#fileAttachment').hide();
    $('#messageInput').hide();
}
});

// On form submission
$('form').submit(function (e) {
e.preventDefault();

// AJAX request to handle form submission
$.ajax({
    type: 'POST',
    url: $(this).attr('action'),
    data: new FormData(this),
    processData: false,
    contentType: false,
    success: function (response) {
        // Display the success message
        $('#confirmationMessage').html('<div class="alert alert-success" role="alert">' + response + '</div>').show();
        $('#errorMessage').hide();
        // Call the sendMessage function to close and refresh the modal after 2 seconds
        sendMessage();
        updateNotification(); // Call the function to update notification number
    },
    error: function (error) {
        // Display the error message
        $('#errorMessage').html('<div class="alert alert-danger" role="alert">Error sending message/file.</div>').show();
        $('#confirmationMessage').hide();
    }
});
});
});


// Function to update notification number
function updateNotification() {
setInterval(function(){
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("noti_number").innerHTML = this.responseText;
        }
    };
    xhttp.open("GET", "data.php", true);
    xhttp.send();
}, 1000);
}

function goBackToUserPage() {
window.location.href = '../panels/admin_page.php';
}

function goBackToDashboard() {
window.location.href = '../panels/admin_page.php';
}

//Upload Function and sends ti main.php
$(document).ready(function() {
// Show the modal when the page loads
$('#uploadModal').modal({
backdrop: 'static',  // Disable clicking outside the modal
keyboard: false      // Disable closing the modal with the keyboard
});

// Handle upload button click
$('#uploadFileButton').click(function() {
// Submit the form
$('#uploadForm').submit();
});

// Handle form submission
$('#uploadForm').on('submit', function(e) {
e.preventDefault();  // Prevent the default form submission

// Show loading spinner and progress bar
$('#loadingSpinner').removeClass('d-none');
$('#uploadLabel').removeClass('d-none');
$('.progress').removeClass('d-none');
$('#uploadFileButton').prop('disabled', true);

var formData = new FormData(this);
$.ajax({
    url: "main.php",
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
            // Reset form and hide elements
            $('#uploadForm')[0].reset();
            $('#loadingSpinner').addClass('d-none');
            $('#uploadLabel').addClass('d-none');
            $('.progress').addClass('d-none');
            $('#uploadFileButton').prop('disabled', false);
            // Hide modal and redirect to admin page
            $('#uploadModal').modal('hide');
            window.location.href = '../panels/admin_page.php';
        } else {
            alert(response);
            $('#loadingSpinner').addClass('d-none');
            $('#uploadLabel').addClass('d-none');
            $('.progress').addClass('d-none');
            $('#uploadFileButton').prop('disabled', false);
        }
    },
    error: function (xhr, status, error) {
        alert("An error occurred: " + error);
    }
});
});
});

// when i click close button it will back in admin_dicuments.php
function goBack() {
// Check if the upload modal was opened from the admin_documents page
if (window.location.pathname.includes('admin_documents')) {
window.location.href = 'admin_documents.php'; // Navigate back to admin_documents.php
} else {
// Default behavior: navigate back one step in history
window.history.back();
}
}


//disable upload button unless they all completed put in iput fields
document.addEventListener('DOMContentLoaded', function () {
// Function to check if all required fields are filled and enable/disable the upload button accordingly
function checkFields() {
var fileInput = document.getElementById('uploadFile');
var titleInput = document.getElementById('uploadTitle');
var authorInput = document.getElementById('uploadAuthor');
var keywordsInput = document.getElementById('uploadKeywords');
var versionInput = document.getElementById('uploadVersion');

var uploadButton = document.getElementById('uploadFileButton');

if (fileInput.value && titleInput.value && authorInput.value && keywordsInput.value) {
    uploadButton.disabled = false;
} else {
    uploadButton.disabled = true;
}
}

// Event listeners to call checkFields() when input values change
var fileInput = document.getElementById('uploadFile');
var titleInput = document.getElementById('uploadTitle');
var authorInput = document.getElementById('uploadAuthor');
var keywordsInput = document.getElementById('uploadKeywords');
var versionInput = document.getElementById('uploadVersion');

if (fileInput && titleInput && authorInput && keywordsInput && versionInput) {
fileInput.addEventListener('change', checkFields);
titleInput.addEventListener('input', checkFields);
authorInput.addEventListener('input', checkFields);
keywordsInput.addEventListener('input', checkFields);
versionInput.addEventListener('input', checkFields);
}
});
