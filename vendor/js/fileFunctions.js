function openEditModal(filename) {
    // Remove the 'readonly' attribute to make the textarea editable.
    $('#editedFileContent').prop('readonly', false);

    // You can update the Save Changes button behavior here.
    console.log('Opening edit modal for filename:', filename);
    $('#editModal').modal('show');
}

function openViewModal(filename) {
    console.log('Opening view modal for filename:', filename);
    $.ajax({
        url: 'view_file.php',
        type: 'GET',
        data: { filename: filename },
        success: function (response) {
            console.log('Received response:', response);
            $('#fileContent').val(response);
            $('#editFileButton').attr('onclick', 'openEditModal("' + filename + '")');
            $('#viewModal').modal('show');

            $('#saveEditButton').off('click').on('click', function () {
                var editedContent = $('#editedFileContent').val();
                console.log('Saving edited content:', editedContent);
                $.ajax({
                    url: 'save_file.php',
                    type: 'POST',
                    data: { filename: filename, content: editedContent },
                    success: function (response) {
                        console.log('Save response:', response);
                        if (response === 'success') {
                            alert('Changes saved successfully!');
                        } else {
                            alert('Failed to save changes.');
                        }
                    }
                });
            });
        }
    });
}

function getFilenameFromEditModal() {
    // You need to implement this function to get the filename from the Edit Modal.
    // You can add an attribute to the Edit Modal to store the filename and retrieve it here.
}
