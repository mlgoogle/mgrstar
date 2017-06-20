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
            dataAPI.getUserinfo(data, function (result) {
                console.log("获取客户管理列表 调用成功!");
                if (result.list.length == "0") {
                    table.find("tbody").empty().html("<tr><td colspan='7'>暂无记录</td></tr>");
                    $(".pagination").hide();
                    return false;
                }
                var oTr,
                    checkTd = '<td><input type="checkbox"></td>';

                $.each(result.list, function (i, v) {
                    var timeTd = '<td>' + v.registerTime + '</td>';
                    var xuTd   = '<td>' + v.uid + '</td>';
                    var endTimeTd   = '<td>'+''+ '</td>';
                    var nameTd = '<td>' + v.nickname + '</td>';
                    var phoneTd = '<td>' + v.phoneNum + '</td>';
                    var orgTd = '<td>' + (v.memberInfo ? v.memberInfo.name : "") + '</td>';
                    var brokerTd = '<td>' + (v.ageInfo ? v.ageInfo.nickname : "") + '</td>';

                    var dayTimeTd =  '<td>'+''+ '</td>';//结算日
                    var numberTd =  '<td>'+''+ '</td>';//消费者编号
                    var sellPriceTd =  '<td>' +   (v.sell_info ?v.sell_info.order_sum_price:0 )+ '</td>'; // 收入
                    var buyPriceTd =  '<td>' +  (v.buy_info ?v.buy_info.order_sum_price:0 ) + '</td>'; // 支出
                    var startCapitalTd = '<td>'+''+ '</td>';//起始资金
                    var endCapitalTd = '<td>'+''+ '</td>';//期末资金
                    var balanceTd =  '<td>' +  (v.balance_info?v.balance_info.balance:0) + '</td>'; //余额 可用资金
                    var freezeTd =  '<td>' +  (v.freeze_info?v.freeze_info.order_sum_price:0) + '</td>'; //冻结资金


                   var  sell_price = (v.sell_info ?v.sell_info.order_sum_price:0 );
                   var  buy_price = v.buy_info ?v.buy_info.order_sum_price:0 ;
                   var  sell_total_price =  v.total_info ?v.total_info.order_sum_price:0 ;// 卖家时,没成功的为资产

                    statusTd = sell_price -  buy_price < 0?'亏':'赢';
                    var  total_order_sum_price = sell_total_price + buy_price;
                    var statusTd = '<td>' + statusTd + '</td>';
                    var totalTd  = '<td>' + total_order_sum_price  + '</td>'; //总资产;


                    var controlTd =
                        "<td>" +
                        "<a class='text-blue' href='../clientmanage/wpclog?uid=" + v.uid + "'> 查看 </a> " +
                        // "<a class='J_showChangeLine text-blue' href='javascript:;'> 额度 </a> | " +
                        // "<a class='J_showStopTrade text-blue' href='javascript:;'> 停止交易 </a>" +
                        "</td>";
                    oTr +=
                        '<tr class="fadeIn animated" data-id="' + v.uid + '">'
                        + checkTd + xuTd + dayTimeTd + numberTd + nameTd + sellPriceTd + buyPriceTd + startCapitalTd +
                        endCapitalTd + freezeTd + balanceTd + statusTd + totalTd +
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
