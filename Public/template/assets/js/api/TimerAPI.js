/**
 * Created by Administrator on 2017/6/12.
 */

define(["jquery"], function ($) {
    var timerAPI = {
        baseRequestUrl:"..",

        //新增
        addTimer: function (data,cb) {
            $.post(this.baseRequestUrl+"/Timer/addTimer", data, function (result) {
                console.log(data)
                cb(result);
            })
        },

        //更新
        editTimer: function (data,cb) {
            $.post(this.baseRequestUrl+"/Timer/editTimer", data, function (result) {
                cb(result);
            })
        },

        //软删除
        delTimer: function (data,cb) {
            $.post(this.baseRequestUrl+"/Timer/delTimer", data, function (result) {
             cb(result);
             })
        },

        //查询
        search: function (data, cb) {
            data.pageNum = 10;
            $.post(this.baseRequestUrl + "/Timer/searchTimer", data, function (result) {
                cb(result);
            })
        }
    };
    return timerAPI;
});

