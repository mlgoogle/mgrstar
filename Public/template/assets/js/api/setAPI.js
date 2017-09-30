/**
 * Created by ctdone on 2017/9/4.
 */

define(["jquery"], function ($) {
    var setAPI = {
        baseRequestUrl:"..",

        versionList: function (data,cb) {
            $.post(this.baseRequestUrl+"/set/versionList",data,function (result) {
                cb(result);
            })
        },

        versionLogList: function (data,cb) {
            $.post(this.baseRequestUrl+"/set/versionLogList",data,function (result) {
                cb(result);
            })
        },


        changeVersion: function (data,cb) {
            $.post(this.baseRequestUrl+"/set/changeVersion",data,function (result) {
                cb(result);
            })
        },

        menuList: function (data,cb) {
            $.post(this.baseRequestUrl+"/set/menuList",data,function (result) {
                cb(result);
            })
        },

        groupList: function (data,cb) {
            $.post(this.baseRequestUrl+"/set/groupList",data,function (result) {
                cb(result);
            })
        },

        addGroup: function (data,cb) {
            $.post(this.baseRequestUrl+"/set/addGroup",data,function (result) {
                cb(result);
            })
        },

        editGroup: function (data,cb) {
            $.post(this.baseRequestUrl+"/set/editGroup",data,function (result) {
                cb(result);
            })
        },

        editAdmin: function (data,cb) {
            $.post(this.baseRequestUrl+"/set/editAdmin",data,function (result) {
                cb(result);
            })
        },

        adminList: function (data,cb) {
            $.post(this.baseRequestUrl+"/set/adminList",data,function (result) {
                cb(result);
            })
        },

    };
    return setAPI;
});
