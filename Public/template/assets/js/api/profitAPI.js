/**
 * Created by Administrator on 2017/6/12.
 */

define(["jquery"], function ($) {
    var profitAPI = {
        baseRequestUrl:"..",

        //经纪人佣金
        subAgentProfit: function (data,cb) {
            $.post(this.baseRequestUrl+"/Profit/subAgentProfit", data, function (result) {
                cb(result);
            })
        },

        //经销商(区域经纪人)佣金
        agentProfit: function (data,cb) {
            $.post(this.baseRequestUrl+"/Profit/agentProfit", data, function (result) {
                cb(result);
            })
        },

        //机构佣金
        memberProfit: function (data,cb) {
            $.post(this.baseRequestUrl+"/Profit/memberProfit", data, function (result) {
                cb(result);
            })
        },

        //新增银行卡
        addBank: function (data,cb) {
            $.post(this.baseRequestUrl+"/Profit/addBank", data, function (result) {
                cb(result);
            })
        },

        //修改银行卡
        editBank: function (data,cb) {
            $.post(this.baseRequestUrl+"/Profit/editBank", data, function (result) {
                cb(result);
            })
        },


        //提现
        withdrawals: function (data,cb) {
            $.post(this.baseRequestUrl+"/Profit/withdrawals", data, function (result) {
                cb(result);
            })
        },

        //提现记录列表
        profitLogList: function (data,cb) {
            $.post(this.baseRequestUrl+"/Profit/profitLogList", data, function (result) {
                cb(result);
            })
        },


    };
    return profitAPI;
});

