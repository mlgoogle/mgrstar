/**
 * Created by Administrator on 2017/6/7.
 */

define(["jquery"], function ($) {
    var starAPI = {
        baseRequestUrl:"..",

        //新增
        addCarousel: function (data,cb) {
            $.post(this.baseRequestUrl+"/Star/addCarousel", data, function (result) {
                console.log(data)
                cb(result);
            })
        },

        //更新
        editCarousel: function (data,cb) {
            $.post(this.baseRequestUrl+"/Star/editCarousel", data, function (result) {
                cb(result);
            })
        },

        //软删除
        delCarousel: function (data,cb) {
            $.post(this.baseRequestUrl+"/Star/delCarousel", data, function (result) {
             cb(result);
             })
        },

        //查询
        search: function (data, cb) {
            data.pageNum = 10;
            $.post(this.baseRequestUrl + "/Star/searchCarousel", data, function (result) {
                cb(result);
            })
        },

        //输入明星名称获取明星code
        getStarInfo: function (data, cb) {
            $.post(this.baseRequestUrl + "/Star/getStarInfo", data, function (result) {
                console.log(result)
                cb(result);
            })
        },
    };
    return starAPI;
});

