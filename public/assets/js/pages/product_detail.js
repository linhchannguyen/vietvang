$(document).ready(function(){
    $('.contact-btn').on('click',function(e){
        e.preventDefault();
        $('#contact-modal').modal('show')
        $('.nav-mobile').css({
            "display":"none"
        })
    })

    $('#btnSubmit_Product').on('click',function(e){
        e.preventDefault();
        var _token = $('input[name="_token"]').val();
        var message = $('#message').val();
        var name = $('#name').val();
        var product_id = $('#product_id').val();
        var type = $('#type').val();
        var asset = $('#asset').val();
        console.log(message + ":::" + name + ':::' + _token);
        if(!message)
        {
            swal({
                text: "Vui lòng để lại nội dung trước khi gửi!",
                button: true,
                icon: "error"
            }) 
        }else if (!name){
            swal({
                text: "Vui lòng để lại tên trước khi gửi!",
                button: true,
                icon: "error"
            }) 
        }
        $.ajax({
            url:"/product-detail/comment-ajax",
            type:'post',
            data:{message,name,_token,type,product_id},
            success:function (data){
                swal({
                    text: "Cảm ơn đóng góp của bạn!",
                    button: true,
                    icon: "success"
                }) 
                $('#message').val("");
                $('#name').val("");
            }
            // success:function(data){
            //     var comment_element = 
            //     `<li class="media">
            //         <div class="media-left">
            //             <img alt="" src="${asset}assets/img/user.png">
            //         </div>
            //         <div class="media-body">
            //             <h5 class="comment-author"><span>${data.name}</span></h5>
            //             <p class="comment">${data.message}</p>
            //             <span class="comment-date">${data.date}</span>
            //         </div>
            //     </li>`;
            // $('.media-list').append(comment_element);
            // $('#message').attr("value","");
            // $('#name').text("value","");
            // var count = parseInt($('#count-comment').text());
            // count +=1;
            // $('#count-comment').text(count);
            // if (count%5==1) {
            //     alert("Đã ghi nhận phản hồi của bạn. Xin cảm ơn!");
            //     location.reload();
            // }
            // }
        })
    })
})