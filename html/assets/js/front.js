$(function(){
    $('a[href^=#s-]').click(function(){
        var speed = 500;
        var href= $(this).attr("href");
        var target = $(href == "#" || href == "" ? 'html' : href);
        var position = target.offset().top;
        $("html, body").animate({scrollTop:position}, speed, "swing");
        return false;
    });

    $('#inquiry_validate').on('click', function() {
        var errFlg = false;
        var mail = $('#inquiry_mail').val();
        if (!$('#inquiry_name').val()) {
            errFlg = dispErrMsg('#err_name', 'お名前を入力してください');
        }
        if (!mail) {
            errFlg = dispErrMsg('#err_mail', 'メールアドレスを入力してください');
        } else if (!mail.match(/^([a-z0-9_]|\-|\.|\+)+@(([a-z0-9_]|\-)+\.)+[a-z]{2,6}$/i)) {
            errFlg = dispErrMsg('#err_mail', 'メールアドレスを正しくを入力してください');
        }
        if (!$('#inquiry_subject').val()) {
            errFlg = dispErrMsg('#err_subject', '件名入力してください');
        }
        if (!$('#inquiry_body').val()) {
            errFlg = dispErrMsg('#err_body', 'お問い合わせ内容を入力してください');
        }
        if (!errFlg) {
            // エラーがない場合
            $('[id^=err_]').html('');
            $('#confirm_name').html($('#inquiry_name').val());
            $('#confirm_mail').html($('#inquiry_mail').val());
            $('#confirm_subject').html($('#inquiry_subject').val());
            $('#confirm_body').html($('#inquiry_body').val().replace(/\n/g, "<br>"));
            $('#confirm_modal').modal('show');
        }
    });
    $('#inquiry_submit').on('click', function() {
        var data = $('#inquiry_form').serialize();
        $.ajax({
            type: 'POST',
            url: '/',
            cache: false,
            data: data,
            success: function(json) {
                eval("var data =" + json);
                if (data['code'] == 'error') {
                    $('#message').html(data['message']).removeClass('alert-info').addClass('alert-danger').show();
                } else if (data['code'] == 'success') {
                    $('#message').html(data['message']).removeClass('alert-danger').addClass('alert-info').show();
                }
                $('#confirm_modal').modal("hide");
            },
            error: function() {
                $('#message').html('通信中にエラーが発生いたしました。再度お問い合わせください。').removeClass('alert-info').addClass('alert-danger');
            }
        });
    });
    function dispErrMsg(id, msg) {
        $(id).html('<span id="err_name" class="text-danger">' + msg + '</span>');
        return true;
    }
});
