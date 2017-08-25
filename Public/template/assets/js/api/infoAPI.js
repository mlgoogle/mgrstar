/**
 * Created by Administrator on 2017/6/7.
 */

define(["jquery"], function ($) {
    var InfoAPI = {
        baseRequestUrl:"..",

        //新增
        addInfo: function (data,cb) {
            $.post(this.baseRequestUrl+"/Info/addInfo", data, function (result) {
                console.log(data)
                cb(result);
            })
        },

        //更新
        editInfo: function (data,cb) {
            $.post(this.baseRequestUrl+"/Info/editInfo", data, function (result) {
                cb(result);
            })
        },

        //软删除
        delInfo: function (data,cb) {
            $.post(this.baseRequestUrl+"/Info/delInfo", data, function (result) {
             cb(result);
             })
        },

        //查询
        search: function (data, cb) {
            data.pageNum = 10;
            $.post(this.baseRequestUrl + "/Info/searchInfo", data, function (result) {
                cb(result);
            })
        },

        //输入明星名称获取明星code
        getInfoInfo: function (data, cb) {
            $.post(this.baseRequestUrl + "/Info/getStarInfo", data, function (result) {
                console.log(result)
                cb(result);
            })
        },
    };
    return InfoAPI;
});

