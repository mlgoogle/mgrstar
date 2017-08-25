/**
 * Created by Administrator on 2017/6/7.
 */

define(["jquery"], function ($) {
    var lucidaAPI = {
        baseRequestUrl:"..",

        //新增
        addLucida: function (data,cb) {
            $.post(this.baseRequestUrl+"/Lucida/addLucida", data, function (result) {
                console.log(data)
                cb(result);
            })
        },

        //更新
        editLucida: function (data,cb) {
            $.post(this.baseRequestUrl+"/Lucida/editLucida", data, function (result) {
                cb(result);
            })
        },

        //软删除
        delLucida: function (data,cb) {
            $.post(this.baseRequestUrl+"/Lucida/delLucida", data, function (result) {
             cb(result);
             })
        },

        //查询
        search: function (data, cb) {
            data.pageNum = 10;
            $.post(this.baseRequestUrl + "/Lucida/searchLucida", data, function (result) {
                cb(result);
            })
        },

        //输入明星名称获取明星code
        getLucidaInfo: function (data, cb) {
            $.post(this.baseRequestUrl + "/Lucida/getLucidaInfo", data, function (result) {
                console.log(result)
                cb(result);
            })
        },
    };
    return lucidaAPI;
});

