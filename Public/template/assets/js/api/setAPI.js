/**
 * Created by ctdone on 2017/9/4.
 */

define(["jquery"], function ($) {
    var setAPI = {
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
    return setAPI;
});
