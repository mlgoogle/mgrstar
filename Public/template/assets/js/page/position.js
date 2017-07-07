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
            dataAPI.getUserOrderinfo(data, function (result) {
                console.log("获取客户管理列表 调用成功!");

                if (!result.list || result.list.length == "0") {
                    table.find("tbody").empty().html("<tr><td colspan='8'>暂无记录</td></tr>");
                    $(".pagination").hide();
                    return false;
                }
                var oTr,
                    checkTd = '<td><input type="checkbox"></td>';

                $.each(result.list, function (i, v) {

                    var xuTd   = '<td>' + v.uid + '</td>';

                   // var starcodeTd = '<td>' + (v.starcode?v.starcode:0) + '</td>';

                    var nameTd = '<td>' + v.nickname + '</td>';
                    var phoneTd = '<td>' + v.phoneNum + '</td>';

                    console.log(v);

                    var type_member = v.member?v.member.name:'';
                    var type_agent = v.agent?v.agent.nickname:'';
                    var type_agent_sub = v.agent_sub?v.agent_sub.nickname:'';

                    var type_info = '<td>' +  type_member + ',' + type_agent + ',' + type_agent_sub +'</td>';


                    //console.log(v.finished_buy_price.nums);


                    if(v.finished_buy_price) {

                        $.each(v.finished_buy_price, function (i, t) {

                            var starcodeTd = '<td>' + (t.starcode ? t.starcode : '') + '</td>';
                            var finished_buy_price = '<td>' + (t.order_num ? t.order_num : 0) + '</td>';
                            var unfinished_buy_price = '<td>' + (t.un_order_num ? t.un_order_num : 0) + '</td>';
                           // alert(starcodeTd);
                            oTr +=
                                '<tr class="fadeIn animated" data-id="' + v.uid + '">'
                                + checkTd + xuTd + starcodeTd + nameTd + phoneTd + finished_buy_price + unfinished_buy_price +
                                type_info +
                                '</tr>';
                        });
                    }else {

                        var starcodeTd = '<td>' + '' + '</td>';
                        var finished_buy_price = '<td>' + 0 + '</td>';
                        var unfinished_buy_price = '<td>' +  0  + '</td>';

                        oTr +=
                            '<tr class="fadeIn animated" data-id="' + v.uid + '">'
                            + checkTd + xuTd + starcodeTd + nameTd + phoneTd + finished_buy_price + unfinished_buy_price + type_info +
                            '</tr>';
                    }
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
