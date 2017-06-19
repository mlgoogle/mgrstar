define([
    "jquery",
    "utils",
    "config",
    "clientAPI",
    "layer",
    "pagination",
    "remodal"
], function ($, utils, config, clientAPI) {
    var changeLineModal = $('[data-remodal-id=changeLineModal]').remodal();
    var body = $("body");

    var page = {
        init: function () {
            this.render();
            this.bindEvents();
        },
        render: function () {
            this.initModal();
            this.initEventBind();
            // this.fnGetList({},true);
        },
        bindEvents: function () {
            this.onSearch();
            this.onStopTrade();
        },
        initEventBind: function () {
            utils.initDatePicker();
        },
        initModal: function () {
            body.on("click", ".J_showChangeLine", function () {
                var $this = $(this);
                var oTd = $this.parents('tr').find('td');
                var orgName = oTd.eq(4).text();
                var nickname = oTd.eq(2).text();
                var phone = oTd.eq(3).text();
                var oForm = $(".changeLineModal .modalForm");
                oForm.find("input[name=orgName]").val(orgName);
                oForm.find("input[name=nickname]").val(nickname);
                oForm.find("input[name=phone]").val(phone);
                changeLineModal.open();
            });

            $(document).on('closed', '.remodal', function (e) {
                $(this).find(".modalForm")[0].reset();
            });
        },
        onSearch: function () {


        },
        onStopTrade: function () {
            body.on("click", ".J_showStopTrade", function () {
                var $this = $(this);
                var id = $this.parents("tr").attr("data-id");
                var name = $this.parents("tr").find("td").eq(2).text();
                layer.confirm(
                    "警告！停止交易后用户能正常登录，但是无法进行交易",
                    {},
                    function () {
                        clientAPI.stopTrade({id: id}, function (result) {
                            layer.msg("操作成功");
                        });
                        layer.msg("操作成功");
                    })
            });
        },
        fnGetList: function (data, initPage) {
            var _this = this;
            var table = $(".data-container table");
            // showLoading(".J_consumeTable");
            // var data = {};
            // clientAPI.search(data, function (result) {
            //
            // });
            var result = {
                "totalPages": 64,
                "pageNum": 5,
                "page": 1,
                "list": [
                    {
                        "tid": "141474220113067346",
                        "uid": "5",
                        "code_id": "12",
                        "buy_sell": "-1",
                        "code": null,
                        "symbol": "fx_sjpycnh",
                        "name": "上海-东京1分钟",
                        "close_type": "6",
                        "amount": "3",
                        "open_position_time": "1490003017",
                        "close_position_time": "1490003077",
                        "gross_profit": "33.4023",
                        "open_price": "0.06111",
                        "open_cost": "33.4023",
                        "open_charge": "0.15",
                        "close_price": "0.06111",
                        "pos_limit": "0",
                        "stop": "0",
                        "deferred": "0",
                        "is_deferred": null,
                        "result": "-1",
                        "handle": "0",
                        "phoneNum": "18668169052",
                        "nickname": "",
                        "type": "0",
                        "headUrl": "",
                        "passwd": "4bcf73028a526f5ae6899759ab332c3d3b173855bef3b22b19224cd5233d39c0",
                        "cashLv": "0",
                        "registerTime": "2017-03-20 12:59:02",
                        "registerStatus": "0",
                        "gender": "0",
                        "lastLoginTime": "2017-04-13 09:56:35",
                        "lastLoginIp": "60.186.229.22",
                        "memberId": null,
                        "agentId": null,
                        "recommend": null
                    },
                    {
                        "tid": "2049001728881135512",
                        "uid": "5",
                        "code_id": "12",
                        "buy_sell": "1",
                        "code": null,
                        "symbol": "fx_sjpycnh",
                        "name": "上海-东京1分钟",
                        "close_type": "6",
                        "amount": "3",
                        "open_position_time": "1490003023",
                        "close_position_time": "1490003083",
                        "gross_profit": "33.4023",
                        "open_price": "0.06111",
                        "open_cost": "33.4023",
                        "open_charge": "0.15",
                        "close_price": "0.061104",
                        "pos_limit": "0",
                        "stop": "0",
                        "deferred": "0",
                        "is_deferred": null,
                        "result": "-1",
                        "handle": "2",
                        "phoneNum": "18668169052",
                        "nickname": "",
                        "type": "0",
                        "headUrl": "",
                        "passwd": "4bcf73028a526f5ae6899759ab332c3d3b173855bef3b22b19224cd5233d39c0",
                        "cashLv": "0",
                        "registerTime": "2017-03-20 12:59:02",
                        "registerStatus": "0",
                        "gender": "0",
                        "lastLoginTime": "2017-04-13 09:56:35",
                        "lastLoginIp": "60.186.229.22",
                        "memberId": null,
                        "agentId": null,
                        "recommend": null
                    },
                    {
                        "tid": "7418135986911958157",
                        "uid": "5",
                        "code_id": "12",
                        "buy_sell": "-1",
                        "code": null,
                        "symbol": "fx_sjpycnh",
                        "name": "上海-东京1分钟",
                        "close_type": "6",
                        "amount": "3",
                        "open_position_time": "1490003025",
                        "close_position_time": "1490003085",
                        "gross_profit": "33.4023",
                        "open_price": "0.06111",
                        "open_cost": "33.4023",
                        "open_charge": "0.15",
                        "close_price": "0.061104",
                        "pos_limit": "0",
                        "stop": "0",
                        "deferred": "0",
                        "is_deferred": null,
                        "result": "1",
                        "handle": "0",
                        "phoneNum": "18668169052",
                        "nickname": "",
                        "type": "0",
                        "headUrl": "",
                        "passwd": "4bcf73028a526f5ae6899759ab332c3d3b173855bef3b22b19224cd5233d39c0",
                        "cashLv": "0",
                        "registerTime": "2017-03-20 12:59:02",
                        "registerStatus": "0",
                        "gender": "0",
                        "lastLoginTime": "2017-04-13 09:56:35",
                        "lastLoginIp": "60.186.229.22",
                        "memberId": null,
                        "agentId": null,
                        "recommend": null
                    },
                    {
                        "tid": "2702018922270788022",
                        "uid": "5",
                        "code_id": "12",
                        "buy_sell": "1",
                        "code": null,
                        "symbol": "fx_sjpycnh",
                        "name": "上海-东京1分钟",
                        "close_type": "6",
                        "amount": "3",
                        "open_position_time": "1490003027",
                        "close_position_time": "1490003087",
                        "gross_profit": "33.4023",
                        "open_price": "0.06111",
                        "open_cost": "33.4023",
                        "open_charge": "0.15",
                        "close_price": "0.061104",
                        "pos_limit": "0",
                        "stop": "0",
                        "deferred": "0",
                        "is_deferred": null,
                        "result": "-1",
                        "handle": "2",
                        "phoneNum": "18668169052",
                        "nickname": "",
                        "type": "0",
                        "headUrl": "",
                        "passwd": "4bcf73028a526f5ae6899759ab332c3d3b173855bef3b22b19224cd5233d39c0",
                        "cashLv": "0",
                        "registerTime": "2017-03-20 12:59:02",
                        "registerStatus": "0",
                        "gender": "0",
                        "lastLoginTime": "2017-04-13 09:56:35",
                        "lastLoginIp": "60.186.229.22",
                        "memberId": null,
                        "agentId": null,
                        "recommend": null
                    },
                    {
                        "tid": "7395381939219531417",
                        "uid": "5",
                        "code_id": "12",
                        "buy_sell": "-1",
                        "code": null,
                        "symbol": "fx_sjpycnh",
                        "name": "上海-东京1分钟",
                        "close_type": "6",
                        "amount": "3",
                        "open_position_time": "1490003029",
                        "close_position_time": "1490003089",
                        "gross_profit": "33.4023",
                        "open_price": "0.06111",
                        "open_cost": "33.4023",
                        "open_charge": "0.15",
                        "close_price": "0.061104",
                        "pos_limit": "0",
                        "stop": "0",
                        "deferred": "0",
                        "is_deferred": null,
                        "result": "1",
                        "handle": "1",
                        "phoneNum": "18668169052",
                        "nickname": "",
                        "type": "0",
                        "headUrl": "",
                        "passwd": "4bcf73028a526f5ae6899759ab332c3d3b173855bef3b22b19224cd5233d39c0",
                        "cashLv": "0",
                        "registerTime": "2017-03-20 12:59:02",
                        "registerStatus": "0",
                        "gender": "0",
                        "lastLoginTime": "2017-04-13 09:56:35",
                        "lastLoginIp": "60.186.229.22",
                        "memberId": null,
                        "agentId": null,
                        "recommend": null
                    }
                ]
            };
            console.log("获取客户管理列表 调用成功!");
            if (result.list.length == "0") {
                table.find("tbody").empty().html("<tr><td colspan='7'>暂无记录</td></tr>");
                $(".pagination").hide();
                return false;
            }
            var oTr,
                checkTd = '<td><input type="checkbox"></td>',
                controlTd =
                    "<td>" +
                    "<a class='text-blue' href='__ROOTHOME__/clientmanage/clientListView/buyLog?id=123'> 查看 </a> | " +
                    "<a class='J_showChangeLine text-blue' href='javascript:;'> 额度 </a> | " +
                    "<a class='J_showStopTrade text-blue' href='javascript:;'> 停止交易 </a>" +
                    "</td>";
            $.each(result.list, function (i, value) {
                var timeTd = '<td>' + value.code_id + '</td>';
                var nameTd = '<td>' + value + '</td>';
                var phoneTd = '<td>' + value.phone + '</td>';
                var orgTd = '<td>' + config.orgType[value.orgType] + '</td>';
                var brokerTd = '<td>' + config.upLevel[value.upLevel] + '</td>';
                oTr += '<tr class="fadeIn animated">' + checkTd + timeTd + nameTd + phoneTd + orgTd + brokerTd + controlTd + '</tr>';
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
        }

    };
    page.init();

});
