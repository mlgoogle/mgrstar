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
            utils.initDatePicker();
            utils.initClientInfo();
            this.fnGetList({uid: utils.getQuery('uid')}, true);
        },
        bindEvents: function () {
            this.onSearch();
        },

        onSearch: function () {

        },
        fnGetList: function (data, initPage) {
            var _this = this;
            var table = $(".data-container table");
            clientAPI.getOutMoneyList(data, function (result) {
                console.log("获取出金记录 调用成功!");
                if (result.list.length == "0") {
                    table.find("tbody").empty().html("<tr><td colspan='6'>暂无记录</td></tr>");
                    $(".pagination").hide();
                    return false;
                }
                var oTr;
                $.each(result.list, function (i, v) {
                    var timeTd = '<td>' + v.withdrawTime + '</td>';
                    var idTd = '<td>' + v.wid + '</td>';
                    var moneyTd = '<td>' + v.money + '</td>';
                    var chargeTd = '<td>' + v.charge + '</td>';
                    var statusTd = '<td>' + config.outStatus[v.status] + '</td>';
                    var infoTd = '<td></td>';
                    oTr +=
                        '<tr class="fadeIn animated">'
                        + timeTd + idTd + chargeTd + moneyTd + statusTd + infoTd +
                        '</tr>';
                });
                table.find("tbody").empty().html(oTr);
                if (initPage) {
                    var pageCount = result.totalPages;
                    if (pageCount > 0) {
                        console.log("页数：" + pageCount);
                        $(".pagination").show().html("").createPage({
                            pageCount: pageCount,
                            current: 1,
                            backFn: function (p) {
                                var newData = data;
                                newData.page = p;
                                _this.fnGetList(data)
                            }
                        })
                    }
                }
            });


        }

    };
    page.init();

});
