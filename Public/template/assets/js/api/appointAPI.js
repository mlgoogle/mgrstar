/**
 * Created by Administrator on 2017/6/12.
 */

define(["jquery"], function ($) {
    var appointAPI = {
        baseRequestUrl:"..",

        //新增
        addAppoint: function (data,cb) {
            $.post(this.baseRequestUrl+"/Appoint/addAppoint", data, function (result) {
                console.log(data)
                cb(result);
            })
        },

        //更新
        editAppoint: function (data,cb) {
            $.post(this.baseRequestUrl+"/Appoint/editAppoint", data, function (result) {
                cb(result);
            })
        },

        //软删除
        delAppoint: function (data,cb) {
            $.post(this.baseRequestUrl+"/Appoint/delAppoint", data, function (result) {
             cb(result);
             })
        },

        //查询
        search: function (data, cb) {
            data.pageNum = 10;
            $.post(this.baseRequestUrl + "/Appoint/searchAppoint", data, function (result) {
                cb(result);
            })
        }
    };
    return appointAPI;
});

