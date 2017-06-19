define([
    "jquery",
    "utils",
    "config",
    "accountAPI",
    "layer",
    "pagination",
    "remodal"
], function ($, utils, config, accountAPI) {
    var addUserModal = $('[data-remodal-id=addUserModal]').remodal();
    var changeUserModal = $('[data-remodal-id=changeUserModal]').remodal();
    var resetPwdModal = $('[data-remodal-id=resetPwdModal]').remodal();
    var editFeeModal = $('[data-remodal-id=editFeeModal]').remodal();
    var body = $("body");
    var userId;

    var page = {
        init: function () {
            this.render();
            this.bindEvents();
        },
        render: function () {
            this.initModal();
            this.initOrgList();
            this.fnGetList({}, true);
            this.getAgentRow({},false); // 获取 经纪人
        },
        bindEvents: function () {
            this.onSearch();
            this.onAdd();
            this.onChange();
            this.onDel();
            this.onUpdateUserStatus();
            this.onRestPwd();
            this.onEditFee();//手续费百分比
           // this.getAgentRow(); // 获取 经纪人
        },


        getAgentRow: function (data,initMember) {
            if(initMember) {
                // 机构下的经纪人 start
                var agentSelect= $("select[name=agent]");
                var agentOptionStr = "";

                // var data = {
                //     memberid: memberId,
                //     page: ''
                // };
                accountAPI.getAgentList(data, function (result) {
                    console.log('经纪人列表-调用成功');
                    if(!result.list){
                        agentSelect.html(agentOptionStr);
                        return false;
                    }

                    $.each(result.list, function (i, t) {
                        var selected = '';

                        if(data.agentId==t.code){
                             selected = 'selected';
                        }

                        agentOptionStr += '<option value="'+t.code+'" ' + selected + '>'+t.nickname+'</option>';
                    });
                    agentSelect.html(agentOptionStr);

                });


                // end

            }
        },

        initModal: function () {
            var _this = this;
            $(".J_showAdd").on("click", function () {
                addUserModal.open();
            });

            //打开 修改用户
            body.on("click", ".J_showChangeUser", function () {
                var $this = $(this);

                userId = $this.parents('tr').attr('data-id');
                var oTd = $this.parents('tr').find('td');
                var orgName = oTd.eq(4).text();
                var username = oTd.eq(1).text();
                var nickname = oTd.eq(2).text();
                var roleType = oTd.eq(3).text();
                var memberId = oTd.eq(10).text();
                var agentId  = oTd.eq(11).text();

                var oForm = $(".changeUserModal .modalForm");

                oForm.find("input[name=orgName]").val(orgName);
                oForm.find("input[name=username]").val(username);
                oForm.find("input[name=nickname]").val(nickname);
                oForm.find("input[name=roleType]").val(roleType);
                //选择 机构
                //orgArr = oForm.find("select[name=org]");
                var b = null;
                if(memberId==0){
                    var memberdefaultId = oForm.find("select[name=org] option:selected ").val();

                    var data = {
                        memberid: memberdefaultId,
                        agentId: 0,
                        page: ''
                    };

                    _this.getAgentRow(data, true);// 调用 机构->经纪人列表
                   // var b = oForm.find('select[name=org]').html();

                }else{
                    memberObj = oForm.find("select[name=org] option[value='" + memberId + "']");
                    console.log(memberObj);

                    oForm.find("select[name=org] option:selected").attr("selected", false);
                    memberObj.attr("selected", true);

                    var data = {
                        memberid: memberId,
                        agentId: agentId,
                        page: ''
                    };

                    _this.getAgentRow(data, true);// 调用 机构->经纪人列表

                    var b = oForm.find('select[name=org]').html();
                }

                changeUserModal.open();

                if(b) {
                    oForm.find('select[name=org]').html(b);
                }
            });



            body.on("click", ".J_showResetPwd", function () {
                var $this = $(this);
                userId = $this.parents('tr').attr('data-id');
                var oTd = $this.parents('tr').find('td');
                var username = oTd.eq(1).text();
                var nickname = oTd.eq(2).text();
                var oForm = $(".resetPwdModal .modalForm");
                oForm.find("input[name=username]").val(username);
                oForm.find("input[name=nickname]").val(nickname);
                changeUserModal.open();
                resetPwdModal.open();
            });

            /*
              手续费弹出框
             */

            body.on("click",".J_editPercentFee",function(){
                var $this = $(this);
                userId = $this.parents('tr').attr('data-id');
                var oTd = $this.parents('tr').find('td');
                percentFee = oTd.eq(8).text();
                percentFee = percentFee.replace(/(\d+)%/g,'$1');
                percentFee = Number(percentFee);

                var oForm = $(".editFeeModal .modalForm");
                oForm.find("input[name=percentFee]").val(percentFee);

                editFeeModal.open();
            });


            //点击机构选择框，获取下级经纪人
            body.on("change",".org_select",function(){
                //    alert(4534543);

                var agentSelect= $("select[name=agent]");
                var agentOptionStr = "";

                var data = {
                    memberid: $(this).val(),
                    page: ''
                };
                accountAPI.getAgentList(data, function (result) {
                    console.log('经纪人列表-调用成功');
                    if(!result.list){
                        agentSelect.html(agentOptionStr);
                        return false;
                    }

                    $.each(result.list, function (i, t) {

                        agentOptionStr += '<option value="'+t.code+'">'+t.nickname+'</option>';
                    });
                    agentSelect.html(agentOptionStr);

                });

            });


            $(document).on('closed', '.remodal', function (e) {
                $(this).find(".modalForm")[0].reset();
            });
        },




        /**
         * 初始化 所属机构下拉列表
         */
        initOrgList: function () {
            var oSelect = $("select[name=org]");
            var optionStr = "";
            var data = {
                pageNum: '',
                page: ''
            };

            var agentSelect= $("select[name=agent]");

            accountAPI.getOrgList(data, function (result) {
                console.log('机构列表-调用成功');
                var agentOptionStr = '';
                if(result.code_identity){
                    var agentOptionStr = "<option value='0'>不选经纪人</option>";
                }

                if(!result.list) {
                    //console.log(oSelect.parent().text());
                    oSelect.parent().html('<span style="color: #ef5350">请先创建机构再来添加用户</span>');
                    return false;
                }

                $.each(result.list, function (i, v) {
                    if(v.agent_info){
                        $.each(v.agent_info, function (j, t) {
                            agentOptionStr += '<option value="'+t.code+'">'+t.nickname+'</option>';
                        });
                        agentSelect.html(agentOptionStr);
                    }
                   // console.log(v);
                    optionStr += '<option value="'+v.memberid+'">'+v.name+'</option>';
                });
                oSelect.html(optionStr);
            });
        },

        onSearch: function () {
            var _this = this;
            $(".J_search").on("click",function () {
                var data={
                    // role: $("[name=roleType]").val(),
                    cellphone: $("[name=phone-s]").val(),
                    // orgName: $("[name=orgName]").val(),
                    nickname: $("[name=nickname-s]").val()
                };
                _this.fnGetList(data, true);
            })
        },

        /**
         * 添加用户
         */
        onAdd: function () {
            var _this = this;
            var btn = $(".addUserModal .remodal-confirm");
            var oForm = $(".addUserModal form");
            btn.on("click", function () {
                var $this = $(this);
                // if ($this.hasClass("disabled")) return;
                // $this.addClass("disabled");


                username = oForm.find('[name=username]').val();
                password = oForm.find('[name=password]').val();

                if(!username) {
                    layer.msg("帐号不能为空！");
                    return false;
                }

                if(!password) {
                    layer.msg("密码不能为空！");
                    return false;
                }

               var memberId = oForm.find('[name=org]').val();
               var agentId=oForm.find('[name=agent]').val();

                if(!memberId) {
                    layer.msg("请选择机构！");
                    return false;
                }
                //layer.msg(memberId);
                var data = {
                    memberId: memberId,//oForm.find('select').val(),
                    agentId: agentId?agentId:0,
                   // org_id:org_id?org_id:0,
                   // agent_id:agent_id?agent_id:0,
                    uid: oForm.find('[name=username]').val(),
                    password: oForm.find('[name=password]').val(),
                    nickname: oForm.find('[name=nickname]').val(),
                    // role: oForm.find('[name=roleType]').val()
                };
               // alert(org_id);


                accountAPI.addUser(data,function (result) {
                    console.log(result.code);
                    if(result.code==0){
                        addUserModal.close();
                        layer.msg("新建成功");
                        _this.fnGetList({},true);

                    }else if(result.code==-2){
                        layer.msg(result.message);
                    }else{
                        layer.msg("新建失败");
                    }
                    $this.removeClass("disabled");
                });
            })
        },

        /**
         * 修改用户
         */
        onChange: function () {
            var _this = this;
            var btn = $(".changeUserModal .remodal-confirm");
            var oForm = $(".changeUserModal form");
            btn.on("click", function () {
                var $this = $(this);
                if ($this.hasClass("disabled")) return;
                $this.addClass("disabled");

                var memberId = oForm.find('[name=org]').val();
                var agentId=oForm.find('[name=agent]').val();

                var data = {
                    id: userId,
                    memberId: memberId,//oForm.find('select').val(),
                    agentId: agentId,//oForm.find('select').val(),
                    uid: oForm.find('[name=username]').val(),
                    password: oForm.find('[name=password]').val(),
                    nickname: oForm.find('[name=nickname]').val(),
                    // role: oForm.find('[name=roleType]').val()
                };
                accountAPI.changeUser(data,function (result) {
                    if(result.code==0){
                        changeUserModal.close();
                        layer.msg("修改成功");
                        _this.fnGetList({},true);
                    }else if(result.code== -2){
                        layer.msg(result.message);
                    }else{
                        layer.msg("新建失败");
                    }
                    $this.removeClass("disabled");
                });
            })
        },

        /**
         * 删除用户
         */
        onDel: function () {
            var _this = this;
            $(".J_onDel").on("click", function () {
                var selectArr = utils.getCheckedArr();
                if(!selectArr.length){
                    layer.msg("请选择要操作的数据");
                    return;
                }
                var data={
                    id: selectArr
                };
                accountAPI.delUser(data,function (result) {
                    if(result.code == 0){
                        layer.msg("删除成功");
                        _this.fnGetList({}, true);
                    } else{
                        layer.msg("删除失败");
                    }

                })
            })
        },

        /**
         * 启用/禁用用户
         */
        onUpdateUserStatus: function () {
            var _this = this;
            $(".J_updateStatus").on("click", function () {
                var idArr = utils.getCheckedArr();
                if(!idArr.length){
                    layer.msg("请选择要操作的数据");
                    return;
                }
                var data={
                    id: idArr,
                    status: $(this).hasClass('open-i') ? 0 : 1
                };

                accountAPI.updateUserStatus(data,function (result) {
                    var text = data.status === 0 ? '启用成功' : '禁用成功';
                    if(result.code == 0){
                        layer.msg(text);
                        _this.fnGetList({}, true);
                    } else{
                        layer.msg("操作失败");
                    }
                })
            })

        },


        /**
         * 修改会员手续费
         */
        onEditFee: function () {
            var btn = $(".editFeeModal .remodal-confirm");
            var oForm = $(".editFeeModal form");
            var _this = this;
            btn.on("click", function () {
                var $this = $(this);
                // if ($this.hasClass("disabled")) return;
                // $this.addClass("disabled");
                percentFee = oForm.find('[name=percentFee]').val();
                percentFee = percentFee.replace(/(\d+)%/g,'$1');

                if(!percentFee) {
                    layer.msg("请填写百分比！");
                    return false;
                }

                if(percentFee > 100 || percentFee < 0 ) {
                    layer.msg("百分比在0-100之间！");
                    return false;
                }


                //percentFee = percentFee * 100;


                var data = {
                    id: userId,
                    percentFee: percentFee
                };

                accountAPI.editFee(data,function (result) {
                    if(result.code == 0){
                        editFeeModal.close();
                        $this.removeClass("disabled");
                        layer.msg("修改百分比成功");
                        _this.fnGetList({}, true);
                    } else if(result.code == -2){
                        layer.msg(result.message);
                        return false;
                    }else{
                        layer.msg("操作失败");
                    }

                });
            });
        },

        /**
         * 重置密码
         */
        onRestPwd: function () {
            var btn = $(".resetPwdModal .remodal-confirm");
            var oForm = $(".resetPwdModal form");

            btn.on("click", function () {
                var $this = $(this);
                // if ($this.hasClass("disabled")) return false;
                // $this.addClass("disabled");

                password = oForm.find('[name=password]').val();
                if(password==0) {
                    layer.msg("密码不能为空！");
                    return false;
                }

                var data = {
                    id: userId,
                    password: oForm.find('[name=password]').val()
                };


                accountAPI.resetPwd(data,function (result) {

                    if(result.code == 0){
                        resetPwdModal.close();
                        $this.removeClass("disabled");
                        layer.msg("重置密码成功");
                    } else{
                        layer.msg("操作失败");
                    }

                });
            });
        },

        fnGetList: function (data, initPage) {
            var _this = this;
            var table = $(".data-container table");

            data.page = $(".current").text();

            accountAPI.getUserList(data, function (result) {
                console.log("获取用户管理列表 调用成功!");
                if (!result.list ) { //|| result.list.length == "0"
                    table.find("tbody").empty().html("<tr><td colspan='9'>暂无记录</td></tr>");
                    $(".pagination").hide();
                    return false;
                }
                var oTr,
                    checkTd = '<td><input type="checkbox"></td>',
                    controlTd = "<td>" +
                        "<a class='J_showChangeUser text-blue' href='javascript:;'> 修改 </a> | " +
                        "<a class='J_showResetPwd text-blue' href='javascript:;'> 重置密码 </a>" +
                        "</td>";
                $.each(result.list, function (i, v) {
                    if(v.memberInfo){
                      var name =v.memberInfo.name;
                    }else{
                      var name ="";
                    }
                    //percentFee

                    var usernameTd = '<td>' + v.uid + '</td>';
                    var nicknameTd = '<td>' + v.nickname + '</td>';
                    var roleTypeTd = '<td>' + config.orgType[v.orgType || 0] + '</td>';
                    var orgTd = '<td>' + (v.memberInfo ? v.memberInfo.name : "") + '</td>';
                    var phoneTd = '<td>' + (v.cellphone?v.cellphone:"") + '</td>';
                    var timeTd = '<td>' + v.registerTime + '</td>';
                    var percentFee = '<td>' + "<a class='J_editPercentFee text-blue' href='javascript:;'>" + v.percentFee + "% </a>"+'</td>';
                    var statusTd = '<td>' + config.userStatus[v.status] + '</td>';

                    var memberTd = '<td style="display: none">' + v.memberId + '</td>';
                    var agentTd = '<td style="display: none">' + v.agentId + '</td>';

                    oTr += '<tr class="fadeIn animated" data-id="'+v.id+'">' + checkTd + usernameTd + nicknameTd + roleTypeTd + orgTd
                        + phoneTd + timeTd + statusTd + percentFee + controlTd + memberTd + agentTd + '</tr>';

                });
                table.find("tbody").empty().html(oTr);

                if (initPage) {
                    var pageCount = result.totalPages;

                    pageCurrent = $(".current").text();
                    pageCurrent = pageCurrent?pageCurrent:1;

                    if (pageCount > 0) {
                        console.log("页数：" + pageCount);
                        $(".pagination").show().html("").createPage({
                            pageCount: pageCount,
                            current: pageCurrent,
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
