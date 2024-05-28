
    // download document
    $('.download-document-cta').off('click').on('click', function () {
    var documentId = $(this).attr('data-id')
    var fileName = $(this).attr('data-name')
    $('#downloadDocumentModalLabel').text(fileName)

    // download now
    $('#downloadDocumentForm').off('submit').on('submit', function (e) {
        e.preventDefault()
        $('#downloadDocumentDownloadNow').prop("disabled", true)
    $('#downloadDocumentLabel').removeClass('d-none')

    var formData = new FormData(this)
    formData.append("downloadDocumentId", documentId)
    $.ajax({
        url: "main",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (response) {
                if ($.trim(response) === "incorrect") {
        alert('Incorrect KEY!')
                    $('#downloadDocumentDownloadNow').prop("disabled", false)
    $('#downloadDocumentLabel').addClass('d-none')
                }
    else {
                    var getResponse = response.substring(0, response.indexOf(","))
    var filePath = response.substring(response.indexOf(",") + 1)

    if (getResponse === "empty") {
        $('#downloadDocumentDownloadNow').prop("disabled", false)
                        $('#downloadDocumentLabel').addClass('d-none')

    // create temporary element
    var body = document.body
    var createElement = document.createElement("a")
    createElement.setAttribute("href", 'encryptedFiles/' + filePath)
    createElement.setAttribute("target", '_blank')
    createElement.setAttribute("download", fileName)
    body.appendChild(createElement)
    createElement.click()

                    }
    else if (getResponse === "correct") {
        $('#downloadDocumentDownloadNow').prop("disabled", false)
                        $('#downloadDocumentLabel').addClass('d-none')

    // create temporary element
    var body = document.body
    var createElement = document.createElement("a")
    createElement.setAttribute("href", 'tmpFiles/' + filePath)
    createElement.setAttribute("target", '_blank')
    createElement.setAttribute("download", fileName)
    body.appendChild(createElement)
    createElement.click()
                    }
    else {
        alert(response)
                        $('#downloadDocumentDownloadNow').prop("disabled", false)
    $('#downloadDocumentLabel').addClass('d-none')
                    }
                }
            }
        })
    })
})

    // edit document
    $('.edit-document-cta').off('click').on('click', function () {
    var documentId = $(this).attr('data-id')
    var fileName = $(this).attr('data-name')

    $.get("main", {editDocumentId: documentId }, function (response, status) {
        if (status) {
        $("#updateFileForm").html(response)

            // update file
            $('#updateFileForm').off('submit').on('submit', function (e) {
        e.preventDefault()

                var formData = new FormData(this)
    $.ajax({
        url: "main",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (response) {
                        if ($.trim(response) === "") {
        alert("Updated succesfully")
    }
                    }
                })
            })

    //change file
    $('#changeFileForm').off('submit').on('submit', function (e) {
        e.preventDefault()
                $('#changeFileLabel').removeClass('d-none')
    $('#changeFileButton').prop('disabled', true)

    var formData = new FormData(this)
    formData.append("changeDocumentId", documentId)

    $.ajax({
        url: "main",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (response) {
                        if ($.trim(response) === "") {
        alert("Upload file completed");
    $('#changeFileLabel').addClass('d-none')
    $('#changeFileButton').prop('disabled', false)
    $('#changeFileForm input').val('')
                        }
    else {
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
    $('.view-document-cta').off('click').on('click', function () {
    var documentId = $(this).attr('data-id');
    var fileName = $(this).attr('data-name');

    // Set the modal title
    $('#viewDocumentModalLabel').text(fileName);

    // Request the content from the server
    $.get("main", {viewDocumentId: documentId }, function (response, status) {
        if (status === 'success') {
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
    $('.about-document-cta').off('click').on('click', function () {
    var documentId = $(this).attr('data-id')
    var fileName = $(this).attr('data-name')
    $('#aboutDocumentModalLabel').text(fileName)

    $.get("main", {aboutDocumentId: documentId }, function (response, status) {
        if (status) {
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
    
        //open and close eye for password 
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
    
    function openCategoryModal(category) {
            // AJAX request to fetch documents in the specified category
            $.ajax({
                url: "fetch_documents.php", // Replace with your actual backend endpoint
                type: "GET",
                data: {
                    category: category
                },
                dataType: "json",
                success: function (documents) {
                    // Populate modal body with document information
                    var modalBody = document.getElementById('categoryDocuments');
                    modalBody.innerHTML = '';
    
                    if (documents.length > 0) {
                        documents.forEach(function (document) {
                            modalBody.innerHTML += '<p>' + document.title + '</p>';
                            // Add more details as needed
                        });
                    } else {
                        modalBody.innerHTML = '<p>No documents found in this category.</p>';
                    }
    
                    // Open the modal
                    $('#categoryModal').modal('show');
                },
                error: function (error) {
                    console.log("An error occurred: " + error.responseText);
                }
            });
        }
    

    

    
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
    
    
     // Function to load messages
    function loadMessages(pageNumber) {
        // Your code to load messages goes here
    }
    
    
    function goBackToAdmin() {
        window.location.href = 'admin_page.php';
    }
        
        function goBack(page) {
            if (page === 'user_page') {
                window.location.href = 'user_page.php'; // Navigate back to user_page.php
            } else if (page === 'admin_page') {
                window.location.href = 'admin_page.php'; // Navigate back to admin_page.php
            }
        }
    
    
    $('#changePasswordButton').on('click', function () {
    var currentPassword = $('#currentPassword').val();
    var newPassword = $('#newPassword').val();
    var confirmPassword = $('#confirmPassword').val();
    
    // Perform validation
    if (newPassword !== confirmPassword) {
        alert("New password and confirm password do not match");
        return;
    }
    
    // AJAX request to update password
    $.ajax({
        url: "main.php",  // Replace with your actual backend endpoint
        type: "POST",
        data: {
            currentPassword: currentPassword,
            newPassword: newPassword
        },
        success: function (response) {
            // Handle the response from the server
            if (response === "success") {
                alert("Password changed successfully");
                $('#settingsModal').modal('hide');  // Close the modal on success
            } else {
                alert("Error changing password: " + response);
            }
        },
        error: function (error) {
            alert("An error occurred: " + error.responseText);
        }
    
        
    });
    });
    
      //Upload Function and sends to main.php
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
             
            },
            error: function (error) {
                // Display the error message
                $('#errorMessage').html('<div class="alert alert-danger" role="alert">Error sending message/file.</div>').show();
                $('#confirmationMessage').hide();
            }
        });
    });
    });
    
    




    
    function goBackToUserPage() {
        window.location.href = '../panels/user_page.php';
    }
    
    function goBackToDashboard() {
        window.location.href = '../panels/user_page.php';
    }
    
    
    function goBack(page) {
        if (page === 'user_page') {
            window.location.href = 'user_page.php'; // Navigate back to user_page.php
        } else if (page === 'admin_page') {
            window.location.href = 'admin_page.php'; // Navigate back to admin_page.php
        } else {
            // Default behavior: navigate back one step in history
            window.history.back();
        }
    }
    function fetchDocumentsByType(category, fileType) {
        $.ajax({
            url: 'fetch_documents.php',
            type: 'GET',
            data: { category: category },
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    console.error('Error fetching documents:', response.error);
                    return;
                }
                $('#categoryDocuments').empty();
                var row;
                $.each(response, function(index, doc) {
                    if (doc.category === fileType) {
                        if (index % 5 === 0) {
                            row = $('<div class="row"></div>');
                            $('#categoryDocuments').append(row);
                        }
                        var documentItem = $('<div class="col-md-2 document-item"></div>');
                        var documentLink = $('<a href="' + doc.file_path + '" class="document-link">' + doc.title + '</a>');
                        documentItem.append(documentLink);
                        row.append(documentItem);
                    }
                });
            },
            error: function(response) {
                if (response.error) {
                    console.error('Error fetching documents:', response.error);
                    return;
                }
            }
        });
    }
    
    function openCategoryModal(category) {
        fetchDocumentsByType(category, ".docx");
        $('#categoryModal').modal('show');
    }
    
    function openPDFCategoryModal(category) {
        fetchDocumentsByType(category, ".pdf");
        $('#pdfModal').modal('show');
    }
    
    function openPPTXCategoryModal(category) {
        fetchDocumentsByType(category, ".pptx");
        $('#pptxModal').modal('show');
    }
    
    function openZIPCategoryModal(category) {
        fetchDocumentsByType(category, ".zip");
        $('#zipModal').modal('show');
    }
    
    function openEXCELCategoryModal(category) {
        fetchDocumentsByType(category, ".xlsx");
        $('#excelModal').modal('show');
    }
    
    function openTXTCategoryModal(category) {
        fetchDocumentsByType(category, ".txt");
        $('#txtModal').modal('show');
    }
    
    



//getting the folders in database
$(document).ready(function() {
    $('#uploadModal').on('shown.bs.modal', function () {
        // Fetch folders via AJAX when modal is opened
        $.ajax({
            url: 'fetch_folders.php', // Update with your PHP script
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                var folderDropdown = $('#folderId');
                folderDropdown.empty(); // Clear previous options
                if(response.success) {
                    // Populate dropdown with fetched folders
                    $.each(response.folders, function(index, folder) {
                        folderDropdown.append($('<option>', {
                            value: folder.folder_id,
                            text: folder.folder_name
                        }));
                    });
                } else {
                    folderDropdown.append($('<option>', {
                        value: '',
                        text: 'No folders found'
                    }));
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching folders:', error);
            }
        });
    });
});

// Assuming you are using jQuery for AJAX

// Function to fetch total users and update the card content
function updateTotalUsers() {
    $.ajax({
        url: 'fetch_total_users.php', // Path to your PHP script that fetches total users
        type: 'GET',
        success: function(response) {
            $('#totalUsersCardBody').text(response + ' Users');
        },
        error: function() {
            $('#totalUsersCardBody').text('Error fetching total users');
        }
    });
}

// Assuming your form has an ID of 'user_form'
$('#user_form').submit(function(event) {
    event.preventDefault(); // Prevent form submission

    // Your form submission code here
    
    // After form submission, update the total users
    updateTotalUsers();
});


$(document).ready(function() {
    // Function to fetch total users and update the card content
    function updateTotalUsers() {
        $.ajax({
            url: 'fetch_total_users.php', // Path to your PHP script that fetches total users
            type: 'GET',
            success: function(response) {
                $('#totalUsersCardBody').text(response + ' Users');
            },
            error: function() {
                $('#totalUsersCardBody').text('Error fetching total users');
            }
        });
    }

    // Submit form via AJAX
    $('#user_form').submit(function(event) {
        event.preventDefault(); // Prevent form submission

        // Your form submission code here

        // After form submission, update the total users
        updateTotalUsers();
    });

    // Update total users on page load
    updateTotalUsers();
});


//fetching overall files in the database
"https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"
$(document).ready(function() {
    // Function to fetch total files from the database
    function fetchTotalFiles() {
        $.ajax({
            url: 'fetch_total_files.php', // Path to your PHP script that fetches total files
            type: 'GET',
            success: function(response) {
                $('#totalFilesCardBody').text(response + ' Files'); // Display only the number without "Files"
            },
            error: function() {
                $('#totalFilesCardBody').text('Error fetching total files');
            }
        });
    }

    // Call the function to fetch total files when the page loads
    fetchTotalFiles();
});


//fetching owned files 
$(document).ready(function() {
    function fetchYourTotalFiles() {
        $.ajax({
            url: 'owned_files.php',
            type: 'GET',
            success: function(response) {
                $('#yourTotalFiles').text(response + ' Files');
            },
            error: function() {
                $('#yourTotalFiles').text('Error fetching your total files');
            }
        });
    }

    fetchYourTotalFiles();
});




// to the smooth transition of sidebar
$(document).ready(function() {
    $('#recipient').on('input', function() {
        var input = $(this).val();
        if (input.length >= 2) { // You can adjust the minimum length for search
            $.ajax({
                url: 'search_recipient.php', // Path to your PHP script to handle search
                method: 'POST',
                data: { query: input },
                success: function(response) {
                    $('#recipientSuggestions').html(response);
                }
            });
        } else {
            $('#recipientSuggestions').empty();
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');
    const dropdowns = sidebar.querySelectorAll('.sidebar-dropdown');

    dropdowns.forEach(dropdown => {
        const parentItem = dropdown.closest('.sidebar-item');
        parentItem.addEventListener('mouseenter', () => {
            dropdown.style.maxHeight = dropdown.scrollHeight + 'px'; // Set max-height dynamically
            dropdown.style.opacity = '1';
        });
        parentItem.addEventListener('mouseleave', () => {
            dropdown.style.maxHeight = '0';
            dropdown.style.opacity = '0';
        });
    });

    const toggleLinks = sidebar.querySelectorAll('.sidebar-link[data-bs-toggle="collapse"]');
    toggleLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (sidebar.classList.contains('expand')) {
                sidebar.classList.remove('expand');
            } else {
                sidebar.classList.add('expand');
            }
        });
    });
});


function openMessage(messageContainer, senderName, messageType, messageContent, filePath) {
    $('#messageModal').modal('show');
    document.getElementById('modalSender').innerText = 'From: ' + senderName;
    document.getElementById('modalType').innerText = 'Type: ' + messageType;
    document.getElementById('modalContent').innerText = 'Content: ' + messageContent;

    if (messageType === 'file') {
        document.getElementById('modalFileLink').innerHTML = '<a href="' + filePath + '" target="_blank">Download File</a>';
    } else {
        document.getElementById('modalFileLink').innerHTML = '';
    }

    // Display the sent time in the modal
    var modalTimeValue = document.getElementById('modalTimeValue');
    modalTimeValue.innerText = messageContainer.getAttribute('data-time');

    // Append the reply button to the modal footer
    var modalFooter = document.querySelector('#messageModal .modal-footer');
    modalFooter.innerHTML = ''; // Clear existing content
    var replyButton = document.createElement('button');
    replyButton.className = 'btn btn-primary';
    replyButton.innerText = 'Reply';
    replyButton.onclick = function () {
        openReplyModal(senderName);
    };
    modalFooter.appendChild(replyButton);
}


//function for opening modal in reply
function openReplyModal(sender) {
    // Ensure the recipient input exists before setting its value
    var recipientInput = document.getElementById('recipientInput');
    if (recipientInput) {
        recipientInput.value = sender;
        // Show the reply modal
        $('#replyModal').modal('show');
    } else {
        console.error('Recipient input not found');
    }
}

function addClickHandlers() {
    var messageContainers = document.getElementsByClassName('message-container');
    for (var i = 0; i < messageContainers.length; i++) {
        messageContainers[i].setAttribute('onclick', 'openMessage(this,' +
            JSON.stringify(messageContainers[i].getAttribute('data-sender')) + ',' +
            JSON.stringify(messageContainers[i].getAttribute('data-type')) + ',' +
            JSON.stringify(messageContainers[i].getAttribute('data-content')) + ',' +
            JSON.stringify(messageContainers[i].getAttribute('data-file')) +
            ')');
    }
}




$(document).ready(function() {
    // Function to fetch initial counts and display notifications and recent activities
    function fetchInitialCountsAndData() {
        // Fetch initial counts from session variables
        $.ajax({
            url: 'get_counts.php',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                $('#notificationBadge').text(response.notification_count);
                $('#activityBadge').text(response.activity_count);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching counts:', error);
            }
        });

        // Fetch and display notifications
        fetchNotifications();

        // Fetch and display recent activities
        fetchRecentActivities();
    }

    // Attach click event listener to the activity dropdown
    $('#activityDropdown').click(function() {
        // Reset activity count to zero in the UI
        $('#activityBadge').text('0');

        // Fetch recent activities when the dropdown is clicked
        fetchRecentActivities();

        // Optionally, you can mark activities as "read" in the database here (AJAX request)
        // This part depends on your application logic
        // Mark notifications as "read" in the database (AJAX request)
        $.ajax({
            url: 'mark_activities_as_read.php',
            method: 'POST',
            success: function(response) {
                console.log('Activities marked as read');
            },
            error: function(xhr, status, error) {
                console.error('Error marking activities as read:', error);
                // Handle error (e.g., display an error message)
            }
        });
    });

    // Attach click event listener to the notification bell icon
    $('#notificationDropdown').click(function() {
        // Reset notification count to zero in the UI
        $('#notificationBadge').text('0');

        // Fetch and display notifications when the bell icon is clicked
        fetchNotifications();

        // Mark notifications as "read" in the database (AJAX request)
        $.ajax({
            url: 'mark_notifications_as_read.php',
            method: 'POST',
            success: function(response) {
                console.log('Notifications marked as read');
            },
            error: function(xhr, status, error) {
                console.error('Error marking notifications as read:', error);
                // Handle error (e.g., display an error message)
            }
        });
    });

    // Fetch initial counts and data when the document is ready
    fetchInitialCountsAndData();
});

function fetchNotifications() {
    $.ajax({
        url: 'fetch_data.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            var notifications = data.notifications;
            var unreadCount = notifications.filter(notification => notification.status === 'unread').length;
            $('#notificationBadge').text(unreadCount);

            $('#notificationMenu').empty();

            notifications.forEach(function(notification) {
                var timestamp = new Date(notification.created_at);
                var formattedTimestamp = timestamp.toLocaleString();

                var notificationLink = $('<a>')
                    .addClass('dropdown-item')
                    .attr('href', '')
                    .text(notification.description);

                var timestampSpan = $('<span>')
                    .addClass('notification-timestamp')
                    .text(formattedTimestamp);

                notificationLink.append(timestampSpan);

                var listItem = $('<li>').append(notificationLink);

                if (notification.status === 'unread') {
                    listItem.addClass('unread-notification');
                }

                $('#notificationMenu').append(listItem);
            });
        },
        error: function(xhr, status, error) {
            console.error('Error fetching notifications:', error);
        }
    });
}

function fetchRecentActivities() {
    $.ajax({
        url: 'fetch_data.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            var activities = data.recentActivities;

            // Update the activity badge count
            $('#activityBadge').text(activities.length);

            $('#activityMenu').empty();

            activities.forEach(function(activity) {
                var timestamp = new Date(activity.timestamp);
                var formattedTimestamp = timestamp.toLocaleString();
            
                var activityLink = $('<a>')
                    .addClass('dropdown-item activity-item')
                    .attr('href', '#')
                    .text(activity.description);
            
                var timestampSpan = $('<span>')
                    .addClass('activity-timestamp')
                    .text(formattedTimestamp);
            
                activityLink.append(timestampSpan);
            
                var listItem = $('<li>').append(activityLink);
            
                if (activity.status === 'unread') {
                    listItem.addClass('unread-activity');
                }
            
                $('#activityMenu').append(listItem);
            });
        },
        error: function(xhr, status, error) {
            console.error('Error fetching recent activities:', error);
        }
    });
}


