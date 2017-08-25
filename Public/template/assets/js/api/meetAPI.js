/**
 * Created by Administrator on 2017/6/12.
 */

define(["jquery"], function ($) {
    var meetAPI = {
        baseRequestUrl:"..",

        //新增
        addMeet: function (data,cb) {
            $.post(this.baseRequestUrl+"/Meet/addMeet", data, function (result) {
                console.log(data)
                cb(result);
            })
        },

        //更新
        editMeet: function (data,cb) {
            $.post(this.baseRequestUrl+"/Meet/editMeet", data, function (result) {
                cb(result);
            })
        },

        //软删除
        delMeet: function (data,cb) {
            $.post(this.baseRequestUrl+"/Meet/delMeet", data, function (result) {
             cb(result);
             })
        },

        //查询
        search: function (data, cb) {
            data.pageNum = 10;
            $.post(this.baseRequestUrl + "/Meet/searchMeet", data, function (result) {
                cb(result);
            })
        }
    };
    return meetAPI;
});

