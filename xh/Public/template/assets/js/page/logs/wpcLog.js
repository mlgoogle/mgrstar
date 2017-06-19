define([
    "jquery",
    "utils",
    "config",
    "clientAPI",
    "layer",
    "pagination",
    "remodal"
], function ($, utils, config, clientAPI) {
    var page = {
        init: function () {
            this.render();
            this.bindEvents();
        },
        render: function () {
            utils.initClientInfo();
            this.fnGetList({uid: utils.getQuery('uid')}, true);
        },
        bindEvents: function () {
        },

        fnGetList: function (data, initPage) {
            var _this = this;
            var table = $(".data-container table");
            clientAPI.getWPCList(data, function (result) {
                console.log("获取平仓列表 调用成功!");
                if (result.list.length == "0") {
                    table.find("tbody").empty().html("<tr><td colspan='9'>暂无记录</td></tr>");
                    $(".pagination").hide();
                    return false;
                }
                var oTr;
                $.each(result.list, function (i, v) {
                    var timeTd = '<td>' + v.close_position_time + '</td>';
                    var idTd = '<td>' + v.tid + '</td>';
                    var goodsNameTd = '<td>' + (v.actaulInfo ? v.actaulInfo.name : "") + '</td>';
                    var goodsSizeTd = '<td>' + (v.actaulInfo ? v.actaulInfo.unit : "") + '</td>';
                    var dirTd = '<td>' + (v.buy_sell == -1 ? "卖出" : "买入") + '</td>';
                    var oprTd = '<td>' + (v.buy_sell == -1 ? "买跌" : "买涨") + '</td>';
                    var amountTd = '<td>' + v.amount + '</td>';
                    var ykTd = '<td>' + (v.result * v.gross_profit).toFixed(2) + '</td>';
                    var infoTd = '<td></td>';
                    oTr +=
                        '<tr class="fadeIn animated">'
                        + timeTd + idTd + goodsNameTd + goodsSizeTd + dirTd + amountTd + oprTd + ykTd + infoTd +
                        '</tr>';
                });
                table.find("tbody").empty().html(oTr);
            });


            //***************


            //***************
        }

    };
    page.init();

});
