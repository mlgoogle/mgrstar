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
                if(result) {
                    $.each(result, function (i, v) {
                        optionStr += '<option value="' + v.memberid + '">' + v.name + '</option>';
                        optionSearchStr += '<option value="' + v.memberid + '">' + v.name + '</option>';
                    });
                }
                oSelectSearch.html(optionSearchStr);
                oSelect.html(optionStr);
            });
        },
        initOrgList: function () {
            var oSelect = $("select[name=org]");
            var optionStr = "<option value='0'>请选择</option>";
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


                var oForm = $(".addBrokerModal .modalForm");
                var provinceSelect = oForm.find("select[name=province]");
                var provinceOptionStr = '<option value="0">选择省份</option>';
                //省市
                data = {};
                accountAPI.getProvince(data, function (result) {

                    if (!result.list) {
                        provinceSelect.html(provinceOptionStr);
                        // return false;
                    }else {
                        $.each(result.list, function (i, v) {
                            provinceOptionStr += '<option value="' + v.id + '">'+v.province+'</option>';
                        });
                        provinceSelect.html(provinceOptionStr);
                    }
                });

                oForm.find("select[name=city]").css('display','none');

                addBrokerModal.open();
            });


            //点击机构选择框，获取城市
            body.on("change","select[name=province]",function() {

                var oForm = $(".addBrokerModal .modalForm");
                var citySelect = oForm.find("select[name=city]");
                var cityOptionStr = '';

                var data = {
                    pid: $(this).val(),
                    page: ''
                };
                accountAPI.getCity(data, function (result) {
                    console.log('经纪人列表-调用成功');
                    if (!result.list) {
                        citySelect.html(cityOptionStr);
                        return false;
                    }

                    $.each(result.list, function (i, t) {

                        cityOptionStr += '<option value="' + t.code  + '">' + t.city  + '</option>';
                    });
                    citySelect.html(cityOptionStr);

                    citySelect.css('display','inline');
                });
            });

            body.on("click", ".J_showCheckBroker", function () {
                var $this = $(this);
                brokerId = $this.parents('tr').attr('data-id');
                var oTd = $this.parents('tr').find('td');
                var orgName = oTd.eq(3).text();
                var orgSubName = oTd.eq(4).text();

                var mark = oTd.eq(2).text();
                var brokerName = oTd.eq(5).text();
                var phone = oTd.eq(6).text();
                var oForm = $(".checkBrokerModal .modalForm");

                var memberId = oTd.eq(9).text();
                var agentId  = oTd.eq(10).text();
                oForm.find("input[name=org]").val(memberId);
                oForm.find("input[name=agent]").val(agentId);

                oForm.find("input[name=id]").val(mark);
                oForm.find("input[name=name]").val(brokerName);
                oForm.find("input[name=phone]").val(phone);
                oForm.find("input[name=brokerId]").val(brokerId);

                checkBrokerModal.open();

                //
                memberObj = oForm.find("select[name=org] option[value='" + memberId + "']");

                //  oForm.find("select[name=org] option:selected").attr("selected", false);
                oForm.find("select[name=org] option").each(function (a,v) {
                    $(this).attr("selected", false);
                });

                // memberObj.attr("selected", true);
                memberObj.remove();

                oForm.find("select[name=org]").append("<option value='" + memberId +  "' selected >" + orgName + "</option>");  //为Select追加一个Option(下拉项)



                var data= {memberid:memberId};


                var agentSelect = oForm.find("select[name=agent]");
                var agentOptionStr = '<option value="0">选择区域经纪人</option>';

                accountAPI.getAgentList(data,function (result){


                    if (!result.list) {
                        agentSelect.html(agentOptionStr);
                        return false;
                    }

                    $.each(result.list, function (i, t) {

                        var selected = '';
                        if(t.id==agentId){
                            selected = 'selected';
                        }

                        agentOptionStr += '<option value="' + t.id + '" ' + selected + '>' + t.nickname + '</option>';
                    });
                    agentSelect.html(agentOptionStr);

                });

            });

            $(document).on('closed', '.remodal', function (e) {
                $(this).find(".modalForm")[0].reset();
            });


            //点击机构选择框，获取下级区域经纪人
            body.on("change",".org_select",function() {

                var agentSelect = $("select[name=agent]");
                var agentOptionStr = '<option value="0">选择区域经纪人</option>';

                var data = {
                    memberid: $(this).val(),
                    page: ''
                };
                accountAPI.getAgentList(data, function (result) {
                    console.log('经纪人列表-调用成功');
                    if (!result.list) {
                        agentSelect.html(agentOptionStr);
                        return false;
                    }

                    $.each(result.list, function (i, t) {

                        agentOptionStr += '<option value="' + t.id + '">' + t.nickname + '</option>';
                    });
                    agentSelect.html(agentOptionStr);

                });
            });
        },

        onSearch: function () {
            var _this = this;
            var oForm = $(".search-bar");
            $(".J_search").on("click", function () {
                var data = {
                    page: 1,
                    memberid: oForm.find("select[name=level]").val(),
                    // agentId:  oForm.find("select[name=agent]").val(),
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
                //if ($this.hasClass("disabled")) return;
                //$this.addClass("disabled");
                var provinceId = oForm.find('select[name=province]').val();
                var code = oForm.find('select[name=city]').val();
                var data = {
                    memberid: oForm.find('select').val(),
                    agentId:  oForm.find("select[name=agent]").val(),
                    mark: oForm.find('[name=id]').val(),
                    nickname: oForm.find('[name=name]').val(),
                    phone: oForm.find('[name=phone]').val(),
                    provinceId: provinceId,
                    province: oForm.find('select[name=province] option[value='+ provinceId +']').text(),
                    code: code,
                    city: oForm.find('select[name=city] option[value='+ code +']').text(),
                    company: oForm.find('input[name=company]').val(),
                };

                accountAPI.addAgentSub(data, function (result) {

                    if (result.code == 0) {
                        addBrokerModal.close();
                        layer.msg("新建成功");
                        _this.fnGetList({}, true);
                    }else if(result.code == -2){
                        layer.msg(result.message);
                    }  else {
                        layer.msg("新建失败");
                    }
                    // $this.removeClass("disabled");
                });
            })
        },
        /**
         * 修改经纪人
         */
        onCheck: function () {
            var _this = this;
            var btn = $(".checkBrokerModal .J_check");
            var oForm = $(".checkBrokerModal form");
            //var oTd = $this.parents('tr').find('td');
            btn.on("click", function () {
                var $this = $(this);

                var data = {
                    id:  oForm.find('input[name=brokerId]').val(),
                    memberId: oForm.find('select[name=org]').val(),
                    agentId: oForm.find('select[name=agent]').val(),
                    mark: oForm.find('input[name=id]').val(),
                    nickname: oForm.find('input[name=name]').val(),
                    phone: oForm.find('input[name=phone]').val(),
                    // verify: verify
                };



                accountAPI.updateAgentSub(data, function (result) {
                    if (result.code == 0) {
                        checkBrokerModal.close();
                        _this.fnGetList({}, true);
                    } else if(result.code == -2){
                        layer.msg(result.message);
                    }else {
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

                accountAPI.updateBrokerSubStatus(data, function (result) {
                    var text = data.status === 1 ? '启用成功' : '禁用成功';

                    if (result.code == 0) {
                        layer.msg(text);
                        _this.fnGetList({}, true);
                    }else {
                        layer.msg("操作失败");
                    }
                })
            })

        },
        fnGetList: function (data, initPage) {
            var _this = this;
            var table = $(".data-container table");
            // showLoading(".J_consumeTable");
            accountAPI.searchAgentSub(data, function (result) {
                console.log("获取经纪人列表 调用成功!");
                if (!result.list) {
                    table.find("tbody").empty().html("<tr><td colspan='9'>暂无记录</td></tr>");
                    $(".pagination").hide();
                    return false;
                }
                var oTr = '',
                    checkTd = '<td><input type="checkbox"></td>',
                    controlTd = "<td>" +
                        "<a class='J_showCheckBroker text-blue' href='javascript:;'> 修改 </a>" +
                        "</td>";
                $.each(result.list, function (i, v) {
                    var codeTd = '<td>' + v.id + '</td>';
                    var markTd = '<td>' + v.mark + '</td>';
                    var memberNameTd = '<td>' + (v.memberInfo?v.memberInfo.name:'') + '</td>';
                    var agentNameTd  = '<td>' + v.agentInfo.nickname + '</td>';
                    var nameTd = '<td>' + v.nickname + '</td>';
                    var typeTd = '<td>' + config.roleType[v.type] + '</td>'; // 角色类型
                    var orgTd = '<td>' + (v.memberInfo ? v.memberInfo.name : "" ) + '</td>';
                    var phoneTd = '<td>' + v.phone + '</td>';
                    var statusTd = '<td>' + config.brokerStatus[v.status] + '</td>';
                    var checkStatusTd = '<td>' + config.brokerCheckStatus[v.verify] + '</td>';

                    var memberId = '<td style="display: none">'+ v.memberId+'</td>';
                    var agentId = '<td style="display: none">'+ v.agentId+'</td>';

                    oTr += '<tr class="fadeIn animated" data-id="' + v.id + '">' + checkTd + codeTd + markTd + memberNameTd
                        + agentNameTd + nameTd + phoneTd + statusTd  + controlTd + memberId + agentId + '</tr>';
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
