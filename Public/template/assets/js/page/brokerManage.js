define([
    "jquery",
    "utils",
    "config",
    "accountAPI",
    "layer",
    "pagination",
    "remodal"
], function ($, utils, config, accountAPI) {
    var addBrokerModal = $('[data-remodal-id=addBrokerModal]').remodal();
    var checkBrokerModal = $('[data-remodal-id=checkBrokerModal]').remodal();
    var body = $("body");
    var brokerId;


    var page = {
        init: function () {
            this.render();
            this.bindEvents();
        },
        render: function () {
            this.initModal();
            this.initOrgList();
            this.initTopOrgList();

            this.fnGetList({}, true);
        },
        bindEvents: function () {
            this.onSearch();
            this.onAdd();
            this.onDel();
            this.onUpdateUserStatus();
            this.onCheck();
        },

        initTopOrgList: function () {
            var oSelectSearch = $("select[name=level]");
            var oSelect = $("select[name=orgTopLevel]");
            var optionSearchStr = '<option value="">上级机构</option>';
            var optionStr = '';
            var data = {
                pageNum: '',
                page: ''
            };
            accountAPI.getTopOrgList(data, function (result) {
                console.log('一级机构列表-调用成功');
                $.each(result, function (i, v) {
                    optionStr += '<option value="' + v.memberid + '">' + v.name + '</option>';
                    optionSearchStr += '<option value="' + v.memberid + '">' + v.name + '</option>';
                });
                oSelectSearch.html(optionSearchStr);
                oSelect.html(optionStr);
            });
        },
        initOrgList: function () {
            var oSelect = $("select[name=org]");
            var optionStr = "";
            var data = {
                pageNum: '',
                page: ''
            };
            accountAPI.getOrgList(data, function (result) {
                console.log('机构列表-调用成功');
                $.each(result.list, function (i, v) {
                    optionStr += '<option value="' + v.memberid + '">' + v.name + '</option>'
                });
                oSelect.html(optionStr);
            });

        },

        initModal: function () {
            $(".J_showAdd").on("click", function () {
                addBrokerModal.open();
            });
            body.on("click", ".J_showCheckBroker", function () {
                var $this = $(this);
                brokerId = $this.parents('tr').attr('data-id');
                var oTd = $this.parents('tr').find('td');
                var orgName = oTd.eq(4).text();
                var brokerName = oTd.eq(2).text();
                var phone = oTd.eq(5).text();
                var oForm = $(".checkBrokerModal .modalForm");
                oForm.find("input[name=orgName]").val(orgName);
                oForm.find("input[name=id]").val(brokerId);
                oForm.find("input[name=name]").val(brokerName);
                oForm.find("input[name=phone]").val(phone);
                checkBrokerModal.open();
            });
            $(document).on('closed', '.remodal', function (e) {
                $(this).find(".modalForm")[0].reset();
            });
        },

        onSearch: function () {
            var _this = this;
            var oForm = $(".search-bar");
            $(".J_search").on("click", function () {
                var data = {
                    page: 1,
                    memberid: oForm.find("select[name=level]").val(),
                    nickname: oForm.find("input[name=nickname]").val(),
                    phone: oForm.find("input[name=phone]").val()
                };
                _this.fnGetList(data, true);
            });
        },

        onAdd: function () {
            var _this = this;
            var btn = $(".addBrokerModal .remodal-confirm");
            var oForm = $(".addBrokerModal form");
            btn.on("click", function () {
                var $this = $(this);
                if ($this.hasClass("disabled")) return;
                $this.addClass("disabled");
                var data = {
                    memberid: oForm.find('select').val(),
                    uid: oForm.find('[name=id]').val(),
                    nickname: oForm.find('[name=name]').val(),
                    phone: oForm.find('[name=phone]').val()
                };
                accountAPI.addBroker(data, function (result) {
                    if (result.code == 0) {
                        addBrokerModal.close();
                        layer.msg("新建成功");
                        _this.fnGetList({}, true);
                    } else if(result.code == -2){
                        layer.msg(result.message);
                    }else {
                        layer.msg("新建失败");
                    }
                    $this.removeClass("disabled");
                });
            })
        },
        /**
         * 审核经纪人
         */
        onCheck: function () {
            var _this = this;
            var btn = $(".checkBrokerModal .J_check");
            // var oForm = $(".checkBrokerModal form");
            btn.on("click", function () {
                var $this = $(this);
                if ($this.hasClass("disabled")) return;
                $this.addClass("disabled");
                var verify;
                if ($this.hasClass('remodal-confirm')) {
                    verify = 1;
                } else {
                    verify = 2;
                }
                var data = {
                    code: brokerId,
                    verify: verify
                };
                accountAPI.checkBroker(data, function (result) {
                    if (result.code == 0) {
                        checkBrokerModal.close();
                        _this.fnGetList({}, true);
                    } else {
                        layer.msg("操作失败");
                    }
                    $this.removeClass("disabled");
                });
            })
        },

        /**
         * 删除
         */
        onDel: function () {
            var _this = this;
            $(".J_onDel").on("click", function () {
                var selectArr = utils.getCheckedArr();
                if (!selectArr.length) {
                    layer.msg("请选择要操作的数据");
                    return;
                }
                var data = {
                    id: selectArr
                };
                accountAPI.delBroker(data, function (result) {
                    if (result.code == 0) {
                        layer.msg("删除成功");
                        _this.fnGetList({}, true);
                    } else {
                        layer.msg("删除失败");
                    }

                })
            })
        },

        /**
         * 启用/禁用
         */
        onUpdateUserStatus: function () {
            var _this = this;
            $(".J_updateStatus").on("click", function () {
                var idArr = utils.getCheckedArr();
                if (!idArr.length) {
                    layer.msg("请选择要操作的数据");
                    return;
                }
                var data = {
                    id: idArr,
                    status: $(this).hasClass('open-i') ? 1 : 0
                };

                accountAPI.updateBrokerStatus(data, function (result) {
                    var text = data.status === 1 ? '启用成功' : '禁用成功';
                    if (result.code == 0) {
                        layer.msg(text);
                        _this.fnGetList({}, true);
                    } else {
                        layer.msg("操作失败");
                    }
                })
            })

        },
        fnGetList: function (data, initPage) {
            var _this = this;
            var table = $(".data-container table");
            // showLoading(".J_consumeTable");
            accountAPI.searchBroker(data, function (result) {
                console.log("获取经纪人列表 调用成功!");
                if (!result.list || result.list.length == "0") {
                    table.find("tbody").empty().html("<tr><td colspan='9'>暂无记录</td></tr>");
                    $(".pagination").hide();
                    return false;
                }
                var oTr,
                    checkTd = '<td><input type="checkbox"></td>',
                    controlTd = "<td>" +
                        "<a class='J_showCheckBroker text-blue' href='javascript:;'> 审核 </a>" +
                        "</td>";
                console.log(controlTd);
                $.each(result.list, function (i, v) {
                    var codeTd = '<td>' + v.code + '</td>';
                    var nameTd = '<td>' + v.nickname + '</td>';
                    var typeTd = '<td>' + config.roleType[v.type] + '</td>'; // 角色类型
                    var orgTd = '<td>' + (v.memberInfo ? v.memberInfo.name : "" ) + '</td>';
                    var phoneTd = '<td>' + v.phone + '</td>';
                    var statusTd = '<td>' + config.brokerStatus[v.status] + '</td>';
                   // var percentFee = '<td>' + v.percentFee + '</td>';
                    var checkStatusTd = '<td>' + config.brokerCheckStatus[v.verify] + '</td>';
                    oTr += '<tr class="fadeIn animated" data-id="' + v.code + '">' + checkTd + codeTd + nameTd + typeTd
                        + orgTd + phoneTd + statusTd + checkStatusTd + controlTd + '</tr>';
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
