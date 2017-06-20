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
            this.getAgentSelect({},false); // 获取 区域 和 经纪人
            this.getAgentSubSelect({},false); // 获取经纪人
        },
        bindEvents: function () {
            this.onSearch();
            this.onAdd();
            this.onChange();
            this.onDel();
            this.onUpdateUserStatus();
            this.onRestPwd();
        },


        getAgentSubSelect:function (data,initMember) {
            if(initMember) {
                // 机构下的经纪人 start
                var agentSubSelect = $("select[name=agentSub]");
                var agentSubOptionStr = "";

                accountAPI.getAgentSubOne(data, function (result) {
                    console.log('经纪人列表-调用成功');
                    if(!result.list || result.list==''){
                        agentSubSelect.html(agentSubOptionStr);
                        return false;
                    }else {
                        agentSubOptionStr += '<option value="'+result.list.id+'" >'+result.list.nickname+'</option>';
                    }
                    agentSubSelect.html(agentSubOptionStr);

                });
            }
        },

        getAgentSelect: function (data,initMember) {
            if(initMember) {
                // 机构下的经纪人 start
                var agentSelect= $("select[name=agent]");
                var agentOptionStr = "";

                accountAPI.getAgentOne(data, function (result) {
                    console.log('经纪人列表-调用成功');

                    if(!result.list || result.list==''){
                        agentSelect.html(agentOptionStr);
                        return false;
                    }else {
                        agentOptionStr += '<option value="'+result.list.id+'" >'+result.list.nickname+'</option>';
                    }
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
            body.on("click", ".J_showChangeUser", function () {
                var $this = $(this);

                userId = $this.parents('tr').attr('data-id');
                var oTd = $this.parents('tr').find('td');
                var orgName = oTd.eq(4).text();
                var username = oTd.eq(1).text();
                var nickname = oTd.eq(2).text();
                var roleType = oTd.eq(3).text();
                var cellphone = oTd.eq(3).text();


                var memberId = oTd.eq(7).text();
                var agentId  = oTd.eq(8).text();
                var agentSubId  = oTd.eq(9).text();


                var oForm = $(".changeUserModal .modalForm");
                oForm.find("input[name=orgName]").val(orgName);
                oForm.find("input[name=username]").val(username);
                oForm.find("input[name=nickname]").val(nickname);
                oForm.find("input[name=roleType]").val(roleType);
                oForm.find("input[name=cellphone]").val(cellphone);

                memberObj = oForm.find("select[name=org] option[value='" + memberId + "']");
                console.log(memberObj);

                oForm.find("select[name=org] option:selected").attr("selected", false);
                memberObj.attr("selected", true);

                var data = {
                    agentId: agentId,
                    page: ''
                };

                var dataSub = {
                    agentSubId: agentSubId,
                    page: ''
                };



                _this.getAgentSelect(data, true);// 调用 机构->区域经纪人列表
                //alert(agentId);
                _this.getAgentSubSelect(dataSub, true); //  机构->经纪人列表

                // var data = {
                //     pageNum: '',
                //     page: ''
                // };
                // accountAPI.getOrgList(data, function (result) {
                //     console.log(data);
                // });

                var b = oForm.find('select[name=org]').html();

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

            $(document).on('closed', '.remodal', function (e) {
                $(this).find(".modalForm")[0].reset();
            });

            //点击机构选择框，获取下级区域经纪人
            body.on("change",".org_select",function(){
               //  alert(4534543);

                var agentSelect= $("select[name=agent]");
                var agentOptionStr = '<option value="0">选择区域经纪人</option>';

                var data = {
                    memberid: $(this).val(),
                    page: ''
                };

                //清空 经纪人

                var agentSubSelect= $("select[name=agentSub]");
                var agentSubOptionStr = '<option value="0">选择经纪人</option>';

                agentSubSelect.html(agentSubOptionStr);
                 //

                accountAPI.getAgentList(data, function (result) {
                    console.log('经纪人列表-调用成功');
                    if(!result.list){
                        agentSelect.html(agentOptionStr);
                        return false;
                    }

                    $.each(result.list, function (i, t) {

                        agentOptionStr += '<option value="'+t.id+'">'+t.nickname+'</option>';
                    });
                    agentSelect.html(agentOptionStr);

                });

            });

            //点击区域经纪人选择框，获取下级经纪人
            body.on("change",".agent_select",function(){
                //  alert(4534543);

                var agentSubSelect= $("select[name=agentSub]");
                var agentSubOptionStr = '<option value="0">选择经纪人</option>';

                var data = {
                    agentId: $(this).val(),
                    page: ''
                };
                accountAPI.getAgentSubList(data, function (result) {

                    if(!result.list){
                        agentSubSelect.html(agentSubOptionStr);
                        return false;
                    }

                    console.log(result.list);

                    $.each(result.list, function (i, t) {

                        agentSubOptionStr += '<option value="'+t.id+'">'+t.nickname+'</option>';
                    });
                    agentSubSelect.html(agentSubOptionStr);

                });

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
            /*
            accountAPI.getOrgList(data, function (result) {
                console.log('机构列表-调用成功');
                $.each(result.list, function (i, v) {
                    optionStr += '<option value="'+v.memberid+'">'+v.name+'</option>'
                });
                oSelect.html(optionStr);
            });
            */
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
                //if ($this.hasClass("disabled")) return;
                //$this.addClass("disabled");
                var data = {
                    memberId: oForm.find('[name=org]').val(),
                    agentId: oForm.find('[name=agent]').val(),
                    agentSubId: oForm.find('[name=agentSub]').val(),
                    uname: oForm.find('[name=username]').val(),
                    password: oForm.find('[name=password]').val(),
                    nickname: oForm.find('[name=nickname]').val(),
                    cellphone: oForm.find('[name=cellphone]').val(),
                    // role: oForm.find('[name=roleType]').val()
                };


                accountAPI.addUser(data,function (result) {
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
               // if ($this.hasClass("disabled")) return;
                // $this.addClass("disabled");
                var data = {
                    id: userId,
                    memberId: oForm.find('[name=org]').val(),
                    agentId: oForm.find('[name=agent]').val(),
                    agentSubtId: oForm.find('[name=agentSub]').val(),
                    //uid: oForm.find('[name=username]').val(),
                    password: oForm.find('[name=password]').val(),
                    nickname: oForm.find('[name=nickname]').val(),
                    cellphone:oForm.find('[name=cellphone]').val(),
                    // role: oForm.find('[name=roleType]').val()
                };

                //alert(data.agentSubtId);
                accountAPI.changeUser(data,function (result) {

                    if(result.code==0){
                        changeUserModal.close();
                        layer.msg("修改成功");
                        _this.fnGetList({},true);
                    }else if(result.code == -2){
                        layer.msg(result.message);
                    }else{
                        layer.msg("新建失败");
                    }
                   // $this.removeClass("disabled");
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
         * 重置密码
         */
        onRestPwd: function () {
            var btn = $(".resetPwdModal .remodal-confirm");
            var oForm = $(".resetPwdModal form");
            btn.on("click", function () {
                var $this = $(this);
                if ($this.hasClass("disabled")) return;
                $this.addClass("disabled");
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

            accountAPI.getUserList(data, function (result) {
                console.log("获取用户管理列表 调用成功!");

                //

                if(result.info = result.member_info){
                    var oSelect = $("select[name=org]");
                    var optionStr = '<option value="0" >请选择机构</option>';
                    $.each(result.info, function (i, v) {
                        console.log('添加用户列表-调用成功');
                        optionStr += '<option value="' + v.memberid + '">' + v.name + '</option>'
                    });
                }else if(result.info = result.agent_info){
                   // $("select[name=org]").parent().remove();

                    $("select[name=org]").html('<option value="' + result.member.memberid + '"  >' + result.member.name + '</option>');
                    $("select[name=org]").attr('disabled',true);

                    var oSelect = $("select[name=agent]");
                    var optionStr = '<option value="0" >请选择区域经纪人</option>';
                    $.each(result.info, function (i, v) {
                        console.log('添加用户列表-调用成功');
                        optionStr += '<option value="' + v.id + '">' + v.nickname + '</option>'
                    });
                }else if(result.info = result.agentsub_info){
                    //$("select[name=org]").parent().remove();
                    //$("select[name=agent]").parent().remove();


                    $("select[name=org]").html('<option value="' + result.member.memberid + '"  >' + result.member.name + '</option>');
                    $("select[name=org]").attr('disabled',true);


                    $("select[name=agent]").html('<option value="' + result.agent.id + '"  >' + result.agent.nickname + '</option>');
                    $("select[name=agent]").attr('disabled',true);

                    var oSelect = $("select[name=agentSub]");
                    var optionStr = '<option value="0" >请选择经纪人</option>';
                    $.each(result.info, function (i, v) {
                        console.log('添加用户列表-调用成功');
                        optionStr += '<option value="' + v.id + '">' + v.nickname + '</option>'
                    });
                }else{
                    // code
                }

                oSelect.html(optionStr);
                //

                if (!result.list || result.list.length == "0") {
                    table.find("tbody").empty().html("<tr><td colspan='9'>暂无记录</td></tr>");
                    $(".pagination").hide();
                    return false;
                }


                var oTr,
                    checkTd = '<td><input type="checkbox"></td>',
                    controlTd = "<td>" +
                        "<a class='J_showChangeUser text-blue' href='javascript:;'> 修改 </a>  " +
                        // "<a class='J_showResetPwd text-blue' href='javascript:;'> 重置密码 </a>" +
                        "</td>";



                $.each(result.list, function (i, v) {

                    if(v.memberInfo){
                      var name =v.memberInfo.name;
                    }else{
                      var name ="";
                    }

                    var unameTd = '<td>' + v.uname + '</td>'
                    var usernameTd = '<td>' + v.nickname + '</td>';
                    var roleTypeTd = '<td>' + config.roleType[v.roleType || 0] + '</td>';
                    //var orgTd = '<td>' + (v.memberInfo ? v.memberInfo.name : "") + '</td>';
                    var phoneTd = '<td>' + (v.cellphone?v.cellphone:"") + '</td>';
                    var timeTd = '<td>' + v.registerTime + '</td>';
                    var statusTd = '<td>' + config.userStatus[v.status] + '</td>';

                    var viewTd = '<td>' + '查看权限' + '</td>';

                    var memberTd = '<td style="display: none">' + v.memberId + '</td>';
                    var agentTd = '<td style="display: none">' + v.agentId + '</td>';
                    var agentSubTd = '<td style="display: none">' + v.agentSubId + '</td>';

                    oTr += '<tr class="fadeIn animated" data-id="'+v.id+'">' +  checkTd + unameTd + usernameTd  +
                        phoneTd  + statusTd + controlTd + viewTd + memberTd + agentTd + agentSubTd + '</tr>';
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
