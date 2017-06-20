define(["jquery"], function ($) {
    var dataAPI = {
        baseRequestUrl:"..",
        login: function (data,cb) {
            $.post(this.baseRequestUrl+"/login/dologin",data,function (result) {
                cb(result);
            })
        },
        resetPwd: function (data,cb) {
            $.post(this.baseRequestUrl+"/login/doRestPassword",data,function (result) {
                cb(result);
            })
        },

        getUserinfo:function (data,cb) {
            $.post(this.baseRequestUrl+"/dataSearch/getUserInfo",data,function (result) {
                cb(result);
            })
        },

        //持仓

        getUserOrderinfo:function (data,cb) {

            $.post(this.baseRequestUrl + "/dataSearch/getUserOrderinfo", data, function (result) {
                cb(result);
            })

        }

    };
    return dataAPI;
});