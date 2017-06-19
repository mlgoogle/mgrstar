/**
 * Created by DAY on 2017/4/15.
 */

define(["jquery"], function ($) {
    var clientAPI = {

        baseRequestUrl: "..",
        /**
         * 客户列表-修改额度
         */
        changeLine: function (data, cb) {
            $.post(this.baseRequestUrl + "/addOrg.php", data, function (result) {
                cb(result);
            })
        },
        /**
         * 客户列表-停止交易
         */
        stopTrade: function (data, cb) {
            $.post(this.baseRequestUrl + "/addOrg.php", data, function (result) {
                cb(result);
            })
        },

        /**
         * 客户列表-列表
         */
        getClientList: function (data, cb) {
            data.pageNum=10;
            $.post(this.baseRequestUrl + "/user/getlist", data, function (result) {
                cb(result);
            })
        },
        /**
         * 客户列表-未平仓列表
         */
        getWPCList: function (data, cb) {
            data.pageNum=10;
            $.post(this.baseRequestUrl + "/trade/openPosition", data, function (result) {
                cb(result);
            })
        },
        /**
         * 客户列表-已平仓列表
         */
        getYPCList: function (data, cb) {
            data.pageNum=10;
            $.post(this.baseRequestUrl + "/trade/closePosition", data, function (result) {
                cb(result);
            })
        },
        /**
         * 客户列表-出金列表
         */
        getOutMoneyList: function (data, cb) {
            data.pageNum=10;
            $.post(this.baseRequestUrl + "/trade/out", data, function (result) {
                cb(result);
            })
        },
        /**
         * 客户列表-入金列表
         */
        getInMoneyList: function (data, cb) {
            data.pageNum=10;
            $.post(this.baseRequestUrl + "/trade/keep", data, function (result) {
                cb(result);
            })
        },


        /**
         * 获取商品列表（商品下拉列表）
         */
        getGoodsList: function (data, cb) {
            $.post(this.baseRequestUrl + "/trade/getgoodlist", data, function (result) {
                cb(result);
            })
        },

        /**
         * 持仓查询-列表
         */
        getCCList: function (data, cb) {
            data.pageNum = 10;
            $.post(this.baseRequestUrl + "/trade/getopentradelist", data, function (result) {
                cb(result);
            })
        },


        /**
         * 平仓查询-列表
         */
        getPCList: function (data, cb) {
            data.pageNum = 10;
            $.post(this.baseRequestUrl + "/trade/getclosetradelist", data, function (result) {
                cb(result);
            })
        },


        /**
         * 出金查询-列表
         */
        getCJList: function (data, cb) {
            data.pageNum = 10;
            $.post(this.baseRequestUrl + "/trade/getouts", data, function (result) {
                cb(result);
            })
        },
        /**
         * 入金查询-列表
         */
        getRJList: function (data, cb) {
            data.pageNum = 10;
            $.post(this.baseRequestUrl + "/trade/getkeeps", data, function (result) {
                cb(result);
            })
        }

    };
    return clientAPI;
});
