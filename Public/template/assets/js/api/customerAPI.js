/**
 * Created by Administrator on 2017/6/12.
 */

define(["jquery"], function ($) {
    var customerAPI = {
        baseRequestUrl:"..",

        //新增
        addCustomer: function (data,cb) {
            $.post(this.baseRequestUrl+"/customer/addCustomer", data, function (result) {
                console.log(data)
                cb(result);
            })
        },

        //更新
        editCustomer: function (data,cb) {
            $.post(this.baseRequestUrl+"/customer/editCustomer", data, function (result) {
                cb(result);
            })
        },

        //软删除
        delCustomer: function (data,cb) {
            $.post(this.baseRequestUrl+"/customer/delCustomer", data, function (result) {
             cb(result);
             })
        },

        //查询
        search: function (data, cb) {
            data.pageNum = 10;
            $.post(this.baseRequestUrl + "/customer/searchCustomer", data, function (result) {
                cb(result);
            })
        }
    };
    return customerAPI;
});

