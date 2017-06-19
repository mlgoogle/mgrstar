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
