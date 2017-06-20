
define([
    "jquery",
    "utils",
    "config",
    "clientAPI",
    "layer",
    "pagination",
    "remodal"
], function ($, utils, config, clientAPI) {
    var body = $("body");

    var page = {
        init: function () {
            this.render();
            this.bindEvents();
        },
        render: function () {
            utils.initDatePicker();
            this.fnGetList({},true);
        },
        bindEvents: function () {
            this.onSearch();
        },

        onSearch: function () {
            var _this = this;
            $(".J_search").on("click", function () {
                var oForm = $(".search-bar");
                var data = {
                    page: 1,
                    startTime: oForm.find("#dateStart").val(),
                    endTime: oForm.find("#dateEnd").val()
                };
                _this.fnGetList(data, true);
            });

        },

        fnGetList: function (data, initPage) {
            var _this = this;
            var table = $(".data-container table");
            clientAPI.getCJList(data, function (result) {
                console.log("获取出金列表 调用成功!");
                if (!result.list || result.list.length == "0") {
                    table.find("tbody").empty().html("<tr><td colspan='5'>暂无记录</td></tr>");
                    $(".pagination").hide();
                    return false;
                }
                var oTr;
                $.each(result.list, function (i, v) {

                    if(v.status==1){
                        var statusMeg = '处理中';
                    }else if(v.status==2){
                        var statusMeg = '成功';
                    }else if(v.status==3){
                        var statusMeg = '失败';
                    }else if(v.status==4){
                        var statusMeg = '退款';
                    }else{
                        var statusMeg = '未知';
                    }

                    var timeTd = '<td>' + v.handleTime + '</td>';
                    var codeTd = '<td>' + v.wid + '</td>';
                    var tradeTypeTd = '<td>' + statusMeg + '</td>';
                    var amountTd = '<td>' + v.money + '</td>';
                    var clientNameTd = '<td>' + (v.userInfo ? v.userInfo.phoneNum : "") + '</td>';
                    oTr += '<tr class="fadeIn animated">' + timeTd + codeTd + tradeTypeTd + amountTd + clientNameTd + '</tr>';
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