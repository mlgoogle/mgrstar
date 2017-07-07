define([
    "jquery",
    "utils",
    "config",
    "accountAPI",
    "layer",
    "pagination",
    "remodal"
], function ($, utils, config, accountAPI) {
    var addOrgModal = $('[data-remodal-id=addOrgModal]').remodal();
    var changeOrgModal = $('[data-remodal-id=changeOrgModal]').remodal();
    var oInput = $(".data-container table tbody input[type=checkbox]:checked");
    var memberid;


    var body = $("body");
    var page = {
        init: function () {
            this.render();
            this.bindEvents();
        },

        render: function () {
            this.initModal();
            this.fnGetList({}, true);
            this.initTopOrgList();
        },

        bindEvents: function () {
            this.onAdd();
            // this.onDel();
            this.onChange();
            this.onSearch();
            this.onUpdateOrgStatus();
        },

        initModal: function () {
            $(".J_showAdd").on("click", function () {
                addOrgModal.open();
            });
            body.on("click", ".J_showChangeOrg", function () {
                var $this = $(this);
                memberid = $this.parents('tr').attr('data-id');
                var oTd = $this.parents('tr').find('td');
                var id = oTd.eq(1).text();
                var code = oTd.eq(2).text();
                var name = oTd.eq(3).text();
                //var type = oTd.eq(3).text();
              //  var upLevel = oTd.eq(4).text();
                var phone = oTd.eq(4).text();
                var cellphone = oTd.eq(6).text();
                var oForm = $(".changeOrgModal .modalForm");
                oForm.find("input[name=orgCode]").val(code);
                oForm.find("input[name=orgName]").val(name);
              //  oForm.find("input[name=orgLevel]").val(name);
             //   oForm.find("input[name=orgType]").val(name);
                oForm.find("input[name=phone]").val(phone);
            //    oForm.find("input[name=cellphone]").val(cellphone);
                changeOrgModal.open();
            });

            $(document).on('closed', '.remodal', function (e) {
                $(this).find(".modalForm")[0].reset();
                $('.J_topOrg').hide();
            });
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

        onAdd: function () {
            var _this = this;
            var confirmBtn = $(".addOrgModal .remodal-confirm");
            var oForm = $(".addOrgModal form");
            var orgLevelSelect = oForm.find('[name=orgLevel]');
            var orgTop = oForm.find('.J_topOrg');
            orgLevelSelect.on('change', function () {
                var $this = $(this);
                if ($this.val() == 0) {
                    orgTop.hide();
                } else {
                    orgTop.show();
                }
            });

            confirmBtn.on("click", function (e) {
                e.preventDefault();
                var $this = $(this);
                //if ($this.hasClass("disabled")) return;
                //$this.addClass("disabled");
                var data = {
                    name: oForm.find('[name=orgName]').val(),
                    mark: oForm.find('[name=orgCode]').val(),
                  //  superMemberid: orgLevelSelect.val() == 0 ? 0 : oForm.find('[name=orgTopLevel]').val(),
                  //  type: oForm.find('[name=orgType]').val(),
                    tel: oForm.find('[name=phone]').val(),
                    phone: oForm.find('[name=cellphone]').val()
                };

                // todo Validate
                accountAPI.addOrg(data, function (result) {
                    if (result.code == 0) {
                        layer.msg('添加成功');
                        addOrgModal.close();
                        $this.removeClass("disabled");
                        _this.fnGetList({}, true);
                    } else if(result.code == -2){
                        layer.msg(result.message);
                    }else {
                        layer.msg("操作失败");
                    }

                })
            })
        },

        /**
         * 启用/禁用
         */
        onUpdateOrgStatus: function () {
            var _this = this;
            $(".J_updateStatus").on("click", function () {
                var idArr = utils.getCheckedArr();
                if (!idArr.length) {
                    layer.msg("请选择要操作的数据");
                    return;
                }
                var data = {
                    id: idArr,
                    status: $(this).hasClass('open-i') ? 0 : 1
                };
                accountAPI.updateOrgStatus(data, function (result) {
                    var text = data.status === 0 ? '启用成功' : '禁用成功';
                    if (result.code == 0) {
                        layer.msg(text);
                        _this.fnGetList({}, true);
                        // oInput.each(function () {
                        //     $(this).parents("tr").find("td").eq(7).text(config.orgStatus[data.status])
                        // })
                    }else {
                        layer.msg("操作失败");
                    }
                })
            })

        },

        onDel: function () {
            $(".J_showDel").on("click", function () {
                var idArr = utils.getCheckedArr();
                if (idArr.length > 0) {
                    layer.confirm('确定删除选中的列表项吗？', {icon: 3}, function (index) {
                        accountAPI.delOrg(idArr, function (result) {
                            oInput.each(function () {
                                $(this).parents("tr").remove();
                            })
                        });
                        layer.close(index)
                    });
                } else {
                    layer.alert("请先选择要删除的列表项", {icon: 0});
                }
            })
        },

        onChange: function () {
            var _this = this;
            var confirmBtn = $(".changeOrgModal .remodal-confirm");
            var oForm = $(".changeOrgModal form");
            var orgLevelSelect = oForm.find('[name=orgLevel]');
            var orgTop = oForm.find('.J_topOrg');
            orgLevelSelect.on('change', function () {
                var $this = $(this);
                if ($this.val() == 0) {
                    orgTop.hide();
                } else {
                    orgTop.show();
                }
            });

            confirmBtn.on("click", function () {
                var $this = $(this);
               // if ($this.hasClass("disabled")) return;
               // $this.addClass("disabled");
                var data = {
                    memberid: memberid,
                    name: oForm.find('[name=orgName]').val(),
                    mark: oForm.find('[name=orgCode]').val(),
                    superMemberid: orgLevelSelect.val() == 0 ? 0 : oForm.find('[name=orgTopLevel]').val(),
                    type: oForm.find('[name=orgType]').val(),
                    tel: oForm.find('[name=phone]').val(),
                    phone: oForm.find('[name=cellphone]').val()
                };
                accountAPI.changeOrg(data, function (result) {
                    if (result.code == 0) {
                        layer.msg("修改成功");
                        changeOrgModal.close();
                        $this.removeClass("disabled");
                        _this.fnGetList({}, true);
                    } else if(result.code == -2){
                        layer.msg(result.message);
                    }else {
                        layer.msg("操作失败");
                    }
                })
            })
        },

        onSearch: function () {
            var _this = this;
            $(".J_search").on("click", function () {
                var oForm = $(".search-bar");
                var data = {
                    page: 1,
                    superMemberid: oForm.find("select[name=level]").val(),
                    name: oForm.find("input[name=orgName]").val() || ""
                };
                _this.fnGetList(data, true);
            });
        },

        fnGetList: function (data, initPage) {
            var _this = this;
            var table = $(".data-container table");
            // showLoading(".J_consumeTable");
            accountAPI.searchOrg(data, function (result) {
                console.log("获取机构管理列表 调用成功!");
                if (!result.list || result.list.length == "0") {
                    table.find("tbody").empty().html("<tr><td colspan='10'>暂无记录</td></tr>");
                    $(".pagination").hide();
                    return false;
                }
                var oTr,
                    checkTd = '<td><input type="checkbox"></td>',
                    controlTd = "<td><a class='J_showChangeOrg text-blue' href='javascript:;'>修改</a></td>";
                $.each(result.list, function (i, v) {
                    var iTd = '<td>' + i + '</td>';
                    var xhTd = '<td>' + v.memberid + '</td>';

                    var codeTd = '<td>' + v.mark + '</td>';
                    var orgNameTd = '<td>' + v.name + '</td>';
                    var orgTypeTd = '<td>' + config.orgType[v.type] + '</td>';
                    var upLevelTd = '<td>' + (v.superMemberInfo ? v.superMemberInfo.name : "") + '</td>';
                    var phoneTd = '<td>' + v.tel + '</td>';
                    var cellphoneTd = '<td>' + v.phone + '</td>';
                    var statusTd = '<td>' + config.orgStatus[v.status] + '</td>';
                    oTr += '<tr class="fadeIn animated" data-id="' + v.memberid + '">' + checkTd + xhTd + codeTd
                        + orgNameTd + phoneTd  + statusTd + controlTd + '</tr>';
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