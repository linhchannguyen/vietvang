$(document).ready(function() {
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    $('#checkall').change(function(){
        $('.checkItem').prop("checked", $(this).prop("checked"));
    });
    $('.deleteItem').click(function(e){
        e.preventDefault();
        var id = $(this).data("id");
        $.ajax({
            url: '/vietvang/public/admin/delete_user/'+id,
            type: 'delete',
            data: {
                '_token': $('input[name=_token]').val(),
                'id': $('.checkItem:checked').val()
            },
            success: function (msg){
                location.reload();
            }
        });
    });
    $('#bulk_delete').click(function(e){
        e.preventDefault();
        var id = $('.checkItem:checked').map(function(){
            return $(this).val()
        }).get().join(',');
        $.ajax({
            method: 'post',
            url: '/vietvang/public/admin/delete_ajax',
            data: {id: id},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(msg){
                location.reload();
            }
        });
    });
    $(document).on('click', '.edit-modal', function() {
        $('.modal-title').text('Edit');
        $('#id_edit').val($(this).data('id'));
        $('#edit_first_name').val($(this).data('firstname'));
        $('#edit_last_name').val($(this).data('lastname'));
        $('#edit_birthday').val($(this).data('birthday'));
        $('#edit_post_code').val($(this).data('postcode'));
        $('#edit_phone').val($(this).data('phone'));
        $('#edit_address').val($(this).data('address'));
        id = $('#id_edit').val();
        $('#editModal').modal('show');
    });
    $('.modal-footer').on('click', '.edit', function() {
        e = document.getElementById("edit_gender");
        gender_id = e.options[e.selectedIndex].value;
        alert(gender_id);
        $.ajax({
            type: 'PUT',
            url: 'update_user/' + id,
            data: {
                '_token': $('input[name=_token]').val(),
                'id': $("#id_edit").val(),
                'first_name': $('#edit_first_name').val(),
                'last_name': $('#edit_last_name').val(),
                'gender_id': gender_id,
                'birthday': $('#edit_birthday').val(),
                'postcode': $('#edit_post_code').val(),
                'phone': $('#edit_phone').val(),
                'address': $('#edit_address').val(),
            },
            success: function(data) {
                $('.errorTitle').addClass('hidden');
                $('.errorContent').addClass('hidden');

                if ((data.errors)) {
                    setTimeout(function () {
                        $('#editModal').modal('show');
                        toastr.error('Validation error!', 'Error Alert', {timeOut: 5000});
                    }, 500);

                    if (data.errors.title) {
                        $('.errorTitle').removeClass('hidden');
                        $('.errorTitle').text(data.errors.title);
                    }
                    if (data.errors.content) {
                        $('.errorContent').removeClass('hidden');
                        $('.errorContent').text(data.errors.content);
                    }
                }
                location.reload();
            }
        });
    });
});