define([], function () {
    var config = {
        serviceUrl          : "", //开发环境


        orgStatus           : ["启用","禁用"],
        tradeStatus         : ["可以交易","禁止交易"],
        userStatus          : ["启用","禁用"],
        brokerStatus        : ["禁用","启用"],
        brokerCheckStatus   : ["审核中","通过","未通过"],
        inStatus            : ["失败","处理中","成功","失败","失败","失败"],
        outStatus           : ["处理中","处理中","成功","失败","退款"],
        CRJCheckStatus      : ["","已通过","未审核"],

        orgType             : ["航空","陆运","海运","其他"],
        upLevel             : ["","华东大区","陆运","海运","其他"],
        roleType            : ["","结算专员","客服"],
        CPType              : ["", "买涨", "买跌"],
        tradeType           : ["", "入金", "出金"],
        LSType              : ["", "开户", "银联转入", "微信转入", "买入清算", "买出清算", "银证转出"],
        moneyLogType        : ["", "收入", "支出"],

        REG: {
            password: "",
            smsCode: "",
            phone: /^0?1[3|4|5|7|8][0-9]\d{8}$/,
            cellphone: "",
            money: ""

        }
    };

    return config;
});