define([
    "jquery",
    "utils",
    "config",
    "dataAPI",
    "layer",
    "pagination",
    "remodal"
], function ($, utils, config, dataAPI) {

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

                var memberMark = oForm.find("input[name=memberMark]").val();
                var agentMark  = oForm.find("[name=agentMark]").val();
                var agentSubMark  = oForm.find("[name=agentSubMark]").val();
                if(memberMark){
                    oForm.find("input[name=search_id]").val(1);
                }else {
                    oForm.find("input[name=search_id]").val(0);
                }

                var data = {
                    page: 1,
                    pageNum: 10,
                    startTime: oForm.find("#dateStart").val(),
                    endTime: oForm.find("#dateEnd").val(),
                    nickname: oForm.find("[name=nickname]").val(),
                    phoneNum: oForm.find("input[name=phone]").val(),
                    memberMark: oForm.find("input[name=memberMark]").val(),
                    agentMark: oForm.find("[name=agentMark]").val(),
                    agentSubMark: oForm.find("[name=agentSubMark]").val()
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


                    // var type_member = v.member?v.member.name:'';
                    // var type_agent = v.agent?v.agent.nickname:'';
                    // var type_agent_sub = v.agent_sub?v.agent_sub.nickname:'';

                    var type_info = '<td>' +  v.type_info +'</td>';


                    var starcodeTd = '<td>' + (v.starcodename ? v.starcodename : '') + '</td>';
                    var order_numTd = '<td>' + (v.order_num ? v.order_num : 0) + '</td>';
                    var un_order_numTd = '<td>' + (v.un_order_num ? v.un_order_num : 0) + '</td>';

                    oTr +=
                        '<tr class="fadeIn animated" data-id="' + v.uid + '">'
                        + checkTd + xuTd + starcodeTd + nameTd + phoneTd + order_numTd + un_order_numTd +
                        type_info +
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
