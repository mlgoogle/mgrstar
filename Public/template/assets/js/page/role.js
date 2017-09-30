define([
    "jquery",
    "utils",
    "config",
    "setAPI",
    "layer",
    "pagination",
    "remodal"
], function ($, utils, config, setAPI) {
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
            this.fnGetList({}, true);
        },
        bindEvents: function () {
            //this.onChange();
           // this.onUpdateUserStatus();
        },

        initModal: function () {
            var oForm = $(".addUserModal .modalForm");
            var _this = this;

            $(".J_showAdd").on("click", function () {
                oForm.find("input[name=group_name]").val('');
                oForm.find("input[name=summary]").val('');
                oForm.find("input[name=phone]").val('');
                oForm.find("input[type=checkbox]").attr('checked',false);
                oForm.find("select[name=admin_id]").val(0);

                $('#admin_name').attr('type','hidden');
                oForm.find("select[name=admin_id]").css('display','inline-block');

                addUserModal.open();
            });

            $(".remodal-close").on("click", function () {
                $('input:radio').removeAttr('checked');
            });


            body.on('click','.J_showEdit',function () {

                var $this = $(this);
                var id = $this.parents('tr').attr('data-id');
                var groupId = $this.parents('tr').attr('data-group-id');

                oForm.find("input[type=radio][value='" + groupId + "']").prop("checked",true);

                var oTd = $this.parents('tr').find('td');

                var admin = oTd.eq(1).text();

                $('#admin_name').val(admin);
                $('#admin_name').attr('type','text');
                oForm.find("select[name=admin_id]").css('display','none');
                oForm.find("select[name=admin_id]").val(id);
                addUserModal.open();
            });


            body.on("click",".remodal-confirm",function () {
                var __this = $(this);
                var id = oForm.find("input[name=id]").val();


                var admin_id = oForm.find("select[name=admin_id]").val();
               // var group_id = oForm.find("input[name=group_id]").val();
                var menuIds = [],menuNames = [];

                var group_id = 0;
                $(".modalForm div span em").each(function () {
                    var $this_true = $(this);

                    if($this_true.find("input[type=radio]").prop("checked")){
                        group_id = $this_true.attr('data-id');
                    }

                });

                var data = {
                    id        :admin_id,
                    group_id  : group_id,
                };

                setAPI.editAdmin(data,function (result) {
                    if(result.code==0){
                        addUserModal.close();
                        layer.msg(result.message);
                        $('input:radio').removeAttr('checked'); //清空选中
                        _this.fnGetList({},true);
                    }else{
                        layer.msg(result.message);
                    }
                });


            });

        },




        fnGetList: function (data, initPage) {
            var _this = this;
            var table = $(".data-container table");

            setAPI.adminList(data, function (result) {
                console.log("获取用户管理列表 调用成功!");

                if (!result.list || result.list.length == "0") {
                    table.find("tbody").empty().html("<tr><td colspan='9'>暂无记录</td></tr>");
                    $(".pagination").hide();
                    return false;
                }



                var oTr = '',
                    checkTd = '<td><input type="checkbox"></td>';

                $.each(result.list, function (i, v) {

                    var id = (v.id?v.id:0);

                    controlTd = "<td>" +
                        "<a class='J_showEdit text-blue' href='javascript:;'> 修改 </a>  " +
                        "</td>";


                    var unameTd = '<td> ' + v.uname + '</td>'
                    var groupNameTd = '<td>' + v.group_name + '</td>';
                    var summaryTd = '<td>' + v.summary + '</td>';
                    var menuNameTd = '<td>' + v.menu_name + '</td>';

                    oTr += ' <tr id="tr_' + id + '" class="fadeIn animated" data-id="'+v.id+'" data-group-id="' + v.group_id + '" >' +  checkTd + unameTd + groupNameTd + summaryTd  +
                        menuNameTd  + controlTd +  '</tr> ' ;
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
