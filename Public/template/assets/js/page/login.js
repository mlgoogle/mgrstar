define(["jquery","sysAPI","layer"], function ($,sysAPI) {
    var page = {
        init: function () {
            this.render();
            this.bindEvents();
        },
        render: function () {

        },
        bindEvents: function () {
            this.onLogin();
        },

        onLogin: function () {
            var error = $(".error-tips");
            $(".submit").on("click",function(){
                var data = {
                    uname: $("#username").val().trim(),
                    pass: $("#password").val()
                };
                if(data.account === ""){
                    error.text("用户名/手机号不能为空");
                    return;
                }else if(data.pass === ""){
                    error.text("密码不能为空");
                    return;
                }else if(data.uname.length < 3 || data.uname.length >15 || !data.uname.match(/^[A-Za-z0-9]+$/)){
                    error.text("用户名/手机号为长度3-15位数字或字母");
                    return;
                }else if(data.pass.length < 6 || data.pass.length >15 || !data.pass.match(/^[A-Za-z0-9]+$/)){
                    error.text("密码为长度6-15位数字或字母");
                    return;
                }else{
                    error.text("");
                }

                // layer.msg("登录成功");
                // setTimeout(function () {
                //     window.location.href="../accountmanage/useranage";
                // },2000);
                sysAPI.login(data,function (result) {

                    if(result.code === 0){
                        layer.alert("登录成功");
                        setTimeout(function () {
                            window.location.href="../adminBacker/index";
                        },2000);
                    }else{

                        layer.alert(result.data);
                    }
                })

            });
        }

    };
    page.init();

});