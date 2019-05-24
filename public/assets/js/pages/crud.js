$(document).ready(function() {
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    $('#checkall').change(function(){
        $('.checkItem').prop("checked", $(this).prop("checked"));
    });
    $('.deleteItem').click(function(e){
        e.preventDefault();
        var id = $(this).data("id");
        $.ajax({
            url: '/admin/delete_user/'+id,
            type: 'delete',
            data: {
                '_token': $('input[name=_token]').val(),
                'id': $('.checkItem:checked').val()
            },
            success: function (msg){
                window.setTimeout(function(){location.reload()},1500)
                swal({
                    text:"Xóa thành công.",
                    button:true,
                    icon:"success",
                    timer: 2000
                })                
            }
        });
    });
    $('#bulk_delete').click(function(e){
        e.preventDefault();
        var id = $('.checkItem:checked').map(function(){
            return $(this).val()
        }).get().join(',');
        if(!id)
        {
            swal({
                text:"Bạn chưa chọn user cần xóa.",
                button:true,
                icon:"warning",
                timer: 3000
            });
        }else {
            /**
             * Phương thức post không gửi token
             */
            $.ajax({
                method: 'post',
                url: '/admin/delete_ajax',
                data: {id: id},
                success: function(msg){
                    swal({
                        text:"Xóa thành công.",
                        button:true,
                        icon:"success",
                        timer: 2000
                    })
                    window.setTimeout(function(){location.reload()},1500)
                }
            });

            /*
            * Phương thức post gửi token
            */
            // $.ajax({
            //     method: 'post',
            //     url: '/vietvang/public/admin/delete_ajax',
            //     data: {id: id},
            //     headers: {
            //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //     },
            //     success: function(msg){
            //         swal({
            //             text:"Xóa thành công.",
            //             button:true,
            //             icon:"success",
            //             timer: 2000
            //         })
            //         window.setTimeout(function(){location.reload()},1500)
            //     }
            // });
        }
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
                $('.errorFirstname').addClass('hidden');
                $('.errorLastname').addClass('hidden');
                $('.errorBirthday').addClass('hidden');
                $('.errorPostcode').addClass('hidden');
                $('.errorPhone').addClass('hidden');
                $('.errorAddress').addClass('hidden');

                if ((data.errors)) {
                    setTimeout(function () {
                        $('#editModal').modal('show');
                        toastr.error('Validation error!', 'Error Alert', {timeOut: 5000});
                    }, 500);

                    if (data.errors.first_name) {
                        $('.errorFirstname').removeClass('hidden');
                        $('.errorFirstname').text(data.errors.first_name);
                    }
                    if (data.errors.last_name) {
                        $('.errorLastname').removeClass('hidden');
                        $('.errorLastname').text(data.errors.last_name);
                    }
                    if (data.errors.birthday) {
                        $('.errorBirthday').removeClass('hidden');
                        $('.errorBirthday').text(data.errors.birthday);
                    }
                    if (data.errors.postcode) {
                        $('.errorPostcode').removeClass('hidden');
                        $('.errorPostcode').text(data.errors.postcode);
                    }
                    if (data.errors.phone) {
                        $('.errorPhone').removeClass('hidden');
                        $('.errorPhone').text(data.errors.phone);
                    }
                    if (data.errors.address) {
                        $('.errorAddress').removeClass('hidden');
                        $('.errorAddress').text(data.errors.address);
                    }
                }else {                
                    swal({
                        text:"Cập nhật thành công.",
                        button:true,
                        icon:"success",
                        timer: 2000
                    })
                    window.setTimeout(function(){location.reload()},1500)
                }
            }
        });
    });
});