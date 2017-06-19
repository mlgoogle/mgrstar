define(["jquery","sysAPI","layer"], function ($,sysAPI) {
    var page = {
        init: function () {
            this.render();
            this.bindEvents();
        },
        render: function () {

        },
        bindEvents: function () {
            this.onSubmit();
        },

        onSubmit: function () {
            var error = $(".error-tips");
            $(".submit").on("click",function(){
                var data = {
                    oldpassword: $("[name=oldPassword]").val(),
                    newpassword: $("[name=newPassword]").val()
                };
                var confirmPwd = $("[name=confirmPassword]").val();
                if(data.oldpassword === ""){
                    error.text("请填写旧密码");
                    return;
                }else if(data.newpassword === ""){
                    error.text("请填写新密码");
                    return;
                }else if(data.newpassword.length < 6 || data.newpassword.length >15 || !data.newpassword.match(/^[A-Za-z0-9]+$/)){
                    error.text("新密码为长度6-15位数字或字母");
                    return;
                }else if(data.newpassword !== confirmPwd){
                    error.text("新密码与确认密码不一致");
                    return;
                }else{
                    error.text("");
                }

                sysAPI.resetPwd(data,function (result) {
                    if(result.code === 0){
                        layer.alert("修改成功");
                        window.location.reload();
                    }else{
                        layer.alert(result.message);
                    }
                })

            });
        }

    };
    page.init();

});
