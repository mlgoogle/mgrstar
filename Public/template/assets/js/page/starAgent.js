/**
 * Created by Administrator on 2017/6/7.
 */
define([
    "jquery",
    "utils",
    "config",
    "api/starAPI",
    "layer",
    "pagination",
    "remodal"
], function ($, utils, config, starAPI) {
    var addUserModal = $('[data-remodal-id=addUserModal]').remodal();

    var body = $("body");
    var page = {
        init: function () {
            this.render();
            this.bindEvents();
        },

        render: function () {
            this.initModal();
            this.fnGetList({}, true);
        },

        bindEvents: function () {
            this.onAdd();
            this.onDel();
            this.onUpdateUserStatus();
            this.onSearch();
            this.onStarInfo();
        },

        initModal: function () {
            $(".J_showAdd").on("click", function () {
                var oForm = $(".addUserModal .modalForm");
               // $(".pic1_div").html('');
                oForm.find("input[name=uname]").val('');
                oForm.find("input[name=nickname]").val('');
                oForm.find("input[name=phone]").val('');
               // oForm.find("input[name=starcode]").parent().css('display','block');
               // oForm.find("input[name=password]").parent().css('display','none');

              //  oForm.find("input[name=starname]").removeAttr("readonly");
                addUserModal.open();
            });
            body.on("click", ".J_showEdit", function () {
                var $this = $(this);
                var id = $this.parents('tr').attr('data-id');
                var oTd = $this.parents('tr').find('td');
                var starcode= $this.parents('tr').attr('data-code');
                var uname = oTd.eq(1).text();
                var nickname = oTd.eq(2).text();
                var phone = oTd.eq(5).text();



                var oForm = $(".addUserModal .modalForm");
                oForm.find("input[name=id]").val(id);
                oForm.find("input[name=uname]").val(uname);
                oForm.find("input[name=uname]").css("border","none");//"border:none"
                oForm.find("input[name=uname]").attr("readonly", "readonly");
                oForm.find("input[name=nickname]").val(nickname);
                oForm.find("input[name=nickname]").css("border","none");
                oForm.find("input[name=nickname]").attr("readonly", "readonly");
                oForm.find("input[name=starcode]").parent().css('display','none');
                oForm.find("input[name=password]").parent().css('display','block');
                oForm.find("input[name=phone]").val(phone);
               // oForm.find("input[name=phone]").attr("readonly", "readonly");

                //oForm.find("input[name=sort]").attr("readonly", "readonly");
                addUserModal.open();
            });

            $(document).on('closed', '.remodal', function (e) {
                $(this).find(".modalForm")[0].reset();
            });
        },
        onStarInfo: function () {
            var _this = $(".addUserModal input[name='starname']");
            _this.on("blur", function () {
                starAPI.getStarUserInfo({starname: _this.val()}, function (result) {
                    if(result.code==-2){
                        layer.msg(result.message);
                        $(".addUserModal input[name=starcode]").val('');
                    }else {
                        $(".addUserModal input[name=starname]").attr("value", result.star_name);
                        $(".addUserModal input[name=starcode]").val(result.star_code);
                    }
                })
            })
        },
        onAdd: function () {
            var _this = this;
            var confirmBtn = $(".addUserModal .remodal-confirm");
            var oForm = $(".addUserModal form");
            confirmBtn.on("click", function (e) {
                e.preventDefault();
                var $this = $(this);
                var id = oForm.find('[name=id]').val();

                var data = {
                    id : id,
                    phone: oForm.find('[name=phone]').val(),
                    uname: oForm.find('[name=uname]').val(),
                    nickname: oForm.find('[name=nickname]').val(),
                    password: oForm.find('[name=password]').val()
                };

                if (id > 0) {
                    starAPI.editStarAgent(data, function (result) {
                        if (result.code == 0) {
                            layer.msg('修改成功');
                            addUserModal.close();
                            $this.removeClass("disabled");
                            _this.fnGetList({}, true);
                        } else if(result.code == -2){
                            layer.msg(result.message);
                        }else {
                            layer.msg("操作失败");
                        }
                    })
                    return false;
                }

                starAPI.addStarAgent(data, function (result) {
                            if (result.code == 0) {
                                layer.msg('添加成功');
                                addUserModal.close();
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

        onDel: function () {
            var _this = this;
            $(".J_onDel").on("click", function () {
                var idArr = utils.getCheckedArr();


                if (idArr.length > 0) {
                    layer.confirm('确定删除选中的列表项吗？', {icon: 3}, function (index) {
                        var data = {ids : idArr};
                        starAPI.DelStarAgent(data, function (result) {
                            _this.fnGetList({}, true);
                        });
                        layer.close(index)
                    });
                } else {
                    layer.alert("请先选择要删除的列表项", {icon: 0});
                }
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

                starAPI.updateUserStatus(data,function (result) {
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

        //明星账号列表

        fnGetList: function (data, initPage) {
            var _this = this;
            var table = $(".data-container table");

            starAPI.starAgentList(data, function (result) {

                if (!result.list || result.list.length == "0") {
                    table.find("tbody").empty().html("<tr><td colspan='10'>暂无记录</td></tr>");
                    $(".pagination").hide();
                    return false;
                }
                var oTr,
                    checkTd = '<td><input type="checkbox"></td>',
                    controlTd = "<td><a class='J_showEdit text-blue' href='javascript:;'>修改</a></td>";
                $.each(result.list, function (i, v) {
                    var unameTd = '<td>' + v.uname + '</td>';
                    var nicknameTd = '<td>' + v.nickname + '</td>';
                    var markTd = '<td>' + v.mark + '</td>';

                    var numTd = '<td>' + (v.number?v.number:0) + '</td>';
                    var phoneTd = '<td>' + v.phone + '</td>';
                    var statusTd = '<td>' + (v.status==0?'启动':'禁用') + '</td>';



                    oTr += '<tr class="fadeIn animated" data-id="' + v.id + '" data-code= "' + '' + '"  >' + checkTd +  unameTd
                        + nicknameTd + markTd  + numTd + phoneTd + statusTd + controlTd + '</tr>';
                });
                table.find("tbody").empty().html(oTr);
                if (initPage) {
                    var pageCount = result.totalPages;
                    if (pageCount > 0) {
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
