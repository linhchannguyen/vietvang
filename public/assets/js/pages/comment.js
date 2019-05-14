$(document).ready(function(){
    $('tbody').on('click','tr td .btnToggleStatus',function(){
        var comment_id = $(this).attr('data-content');
        var base_url = $(this).attr('data-content');
        var _token = $('input[name="_token"]').val();
        var button_element = $(this);
        // console.log(comment_id);
        $.ajax({
            url:  window.location.origin +'/comments/update-status',
            type:'post',
            data:{comment_id,_token},
            success:function(data){
                console.log(button_element);
                if (data.status) {
                    // button_element.text("aaa")
                    button_element.toggleClass("btn-success",false);
                    button_element.toggleClass("btn-danger",true);
                    button_element.text("Ẩn")
                    var prev_element = button_element.prev();
                    prev_element.text("(Hiện)")
                    // console.log(prev_element);
                    
                    
                }else{
                    button_element.removeClass("btn-danger");
                    button_element.toggleClass("btn-success",true);
                    button_element.text("Hiện")
                    var prev_element = button_element.prev();
                    prev_element.text("(Ẩn)")
                }
            }
        })
    })


})