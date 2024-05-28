/* View Data */
$(document).ready(function () {

    $('.view_data').click(function (e) {
        e.preventDefault();

        var user_id = $(this).closest('tr').find('.user_id').text();

        $.ajax({
            type: "POST",
            url: "insert.php",
            data: {
                'click_view_btn': true,
                'user_id': user_id,
            },
            success: function (response) {
                $('.view_user_data').html(response);
                $('#viewusermodal').modal('show');
            }
        })
    });
});

/* edit data */
$(document).ready(function () {
$('.edit_data').click(function (e) {
    e.preventDefault();

    var user_id = $(this).closest('tr').find('.user_id').text();

    $.ajax({
        type: "POST",
        url: "insert.php",
        data: {
            'click_edit_btn': true,
            'user_id': user_id,
        },
        success: function (response) {
            // Set the hidden input field with user_id
            $('#user_id').val(user_id);

            $.each(response, function (Key, value) {
                $('#name').val(value['name']);
                $('#username').val(value['username']);
                $('#password').val(value['password']);
            });

            $('#editdata').modal('show');
        }
    });
});
});


/* Confirm Delete */
$('.confirm_delete_btn').click(function (e) {
    e.preventDefault();

    var user_id = $(this).closest('tr').find('.user_id').text();
    $('#confirm_delete_id').val(user_id);
    $('#deleteusermodal').modal('show');

    $.ajax({
        method: "POST",
        url: "insert.php",
        data: {
            'confirm_delete_btn': true,
            'user_id': user_id,
        },
        success: function (response) {
            console.log(response);
        }
    });
});


