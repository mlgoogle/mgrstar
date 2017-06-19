/**
 * Created by DAY on 2017/4/15.
 */

define(["jquery"], function ($) {
    var sysAPI = {
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
        }

    };
    return sysAPI;
});
