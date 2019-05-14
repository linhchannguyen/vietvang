$(document).ready(function () {
    $('.contact-btn').on('click', function (e) {
        e.preventDefault();
        $('#contact-modal').modal('show')
        $('.nav-mobile').css({
            "display":"none"
        })
    })

    $('#btnComment').on('click', function (e) {
        e.preventDefault();
        var _token = $('input[name="_token"]').val();
        var message = $('#message').val();
        var name = $('#name').val();
        var post_id = $('#post_id').val();
        var type = 1;
        var asset = $('#asset').val();
        console.log(message + ":::" + name + ':::' + _token);
        $.ajax({
            url: "/post-detail/comment-ajax",
            type: 'post',
            data: {
                message,
                name,
                _token,
                type,
                post_id
            },
            success: function (data) {
                swal({
                    
                    text:"Đã ghi nhận đóng góp ý kiến!",
                    button:true,
                    icon:"success"
                })
            //     var comment_element = `<li class="media">
            //     <div class="media-left">
            //     <a href="#"><img  class="img-responsive" alt="" src="${asset}uploads/static/avatar-icon.png"> </a>
            //     </div>
            //     <div class="media-body">
            //         <h5 class="comment-author"><a href="#">${data.name}</a></h5>
            //         <p class="comment">${data.message}</p>
            //         <a href="#" class="comment-date">${data.created_at}</a>
                    
            //     </div>
            // </li>`;
            //     $('.media-list').append(comment_element);
            //     $('#message').attr("value", "");
            //     $('#name').text("value", "");
            //     var count = parseInt($('#count-comment').text());
            //     count += 1;
            //     $('#count-comment').text(count)
            //     if (count % 5 == 1) {
            //         location.reload();
            //     }
            }
        })
    })




})