define([
    "jquery",
    "utils",
    "config",
    "dataAPI",
    "layer",
    "pagination",
    "remodal"
], function ($, utils, config, dataAPI) {
    console.log(dataAPI);

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
            this.fnGetList({pageNum: 10}, true);
        },
        bindEvents: function () {
            this.onSearch();
            // this.onStopTrade();
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
            var _this = this;
            $(".J_search").on("click", function () {
                var oForm = $(".search-bar");
                var data = {
                    page: 1,
                    startTime: oForm.find("#dateStart").val(),
                    endTime: oForm.find("#dateEnd").val(),
                    nickname: oForm.find("[name=nickname]").val(),
                    phoneNum: oForm.find("input[name=phone]").val()
                };
                _this.fnGetList(data, true);
            });
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
            dataAPI.getSuccessInfo(data, function (result) {
                console.log("获取客户管理列表 调用成功!");
                if (result.list.length == "0") {
                    table.find("tbody").empty().html("<tr><td colspan='7'>暂无记录</td></tr>");
                    $(".pagination").hide();
                    return false;
                }
                var oTr,
                    checkTd = '<td><input type="checkbox"></td>';

                $.each(result.list, function (i, v) {

                    var xuTd   = '<td>' + v.id + '</td>';

                    var close_timeTd = '<td>' + v.close_time + '</td>';//close_time
                    var order_idTd   =  '<td>' + v.order_id + '</td>';

                    var buyNameTd = '<td>' + v.buy_name + '</td>';
                    var buyPhoneTd = '<td>' + v.buy_phone + '</td>';
                    var buyStatus  = '<td>' + '买入' + '</td>';

                    var sellNameTd = '<td>' + v.sell_name + '</td>';
                    var sellPhoneTd = '<td>' + v.sell_phone + '</td>';
                    var sellStatus  = '<td>' + '买出' + '</td>';

                    var type_member = v.member?v.member.name:'';
                    var type_agent_sub = v.agent_sub?v.agent_sub.nickname:'';

                    var type_info = '<td>' +  type_member + ',' + type_agent_sub +'</td>';

                    var starcodeTd = '<td>' + (v.starcode?v.starcode:0) + '</td>';

                    var type_status = '<td>' + '' + '</td>';

                    var order_numTd = '<td>' + v.order_num + '</td>';
                    var order_priceTd = '<td>' + v.order_price+ '</td>';

                    var order_total = '<td>' + '' + '</td>';


                    oTr +=
                        '<tr class="fadeIn animated" data-id="' + v.uid + '">'
                        + checkTd + xuTd + close_timeTd + order_idTd + sellPhoneTd + sellNameTd + buyStatus +
                        buyPhoneTd + buyNameTd + sellStatus +  starcodeTd + type_status + order_numTd
                        + order_priceTd + order_total +
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
