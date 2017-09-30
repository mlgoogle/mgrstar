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
            this.onChange();
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

                addUserModal.open();
            });

            $(".remodal-close").on("click", function () {
                $('input:checkbox').removeAttr('checked');
            });


            body.on('click','.J_showEdit',function () {

                var $this = $(this);
                var id = $this.parents('tr').attr('data-id');
                var memuIds = $this.parents('tr').attr('data-menuids');

                var memuIdArr = new Array(); //定义一数组

                memuIdArr = memuIds.split(","); //字符分割


                $(".modalForm div span em").each(function () {
                    var $this_true = $(this);
                    memuId = $this_true.attr('data-id');

                    for (i=0;i<memuIdArr.length ;i++ ) {

                        if(memuId == memuIdArr[i]){
                            $this_true.find("input[type=checkbox]").prop('checked',true);
                        }
                    }

                });

                var oTd = $this.parents('tr').find('td');

                var group_name = oTd.eq(1).text();
                var summary = oTd.eq(3).text();
                oForm.find("input[name=id]").val(id);
                oForm.find("input[name=group_name]").val(group_name);
                oForm.find("input[name=summary]").val(summary);
                addUserModal.open();
            });

            body.on('click','.menu-right',function () {
                var __this = $(this);

                __this.attr('class','menu-down');
                __this.find('a').css('color', '#080808');
                var pid = __this.prev().val();

                data = {
                    pid:pid
                };

            });

            body.on("click",".menu-down",function () {
                var __this = $(this);

                __this.attr('class','menu-right');
                __this.find('a').css('color', '#800061');
                var pid = __this.parents('tr').attr('data-id');

                $('.class_' + pid ).remove();

            });

            body.on("click",".remodal-confirm",function () {
                var __this = $(this);
                var id = oForm.find("input[name=id]").val();


                var group_name = oForm.find("input[name=group_name]").val();
                var summary = oForm.find("input[name=summary]").val();
                var menuIds = [],menuNames = [];

                $(".modalForm div span em").each(function () {
                    var $this = $(this);
                    
                    if($this.find("input[type=checkbox]").prop("checked")){
                        menuIds.push($this.attr('data-id'));
                        menuNames.push($this.attr('data-name'));
                    }
                });

                var data = {
                    id        :id,
                    group_name: group_name,
                    summary   : summary,
                    menuIds   : menuIds,
                    menuNames : menuNames
                };

                if(id){
                    setAPI.editGroup(data,function (result) {
                        if(result.code==0){
                            addUserModal.close();
                            layer.msg(result.message);
                            $('input:checkbox').removeAttr('checked'); //清空选中
                            _this.fnGetList({},true);
                        }else{
                            layer.msg(result.message);
                        }
                    });
                }else {
                    setAPI.addGroup(data, function (result) {
                        if (result.code == 0) {
                            addUserModal.close();
                            layer.msg(result.message);
                            $('input:checkbox').removeAttr('checked'); //清空选中
                            _this.fnGetList({}, true);
                        } else {
                            layer.msg(result.message);
                        }
                    });
                }

            });

        },

        /**
         * 修改用户
         */
        onChange: function () {
            var _this = this;
            var btn = $(".changeUserModal .remodal-confirm");
            var oForm = $(".changeUserModal form");
            btn.on("click", function () {
                var data = {
                    ttype: oForm.find('[name=ttype]').val(),
                    VersionName: oForm.find('[name=VersionName]').val(),
                    Size: oForm.find('[name=Size]').val(),
                    Url: oForm.find('[name=Url]').val(),
                    UpdateDesc: oForm.find('textarea[name=UpdateDesc]').val(),
                };


                setAPI.changeVersion(data,function (result) {

                    if(result.code==0){
                        changeUserModal.close();
                        layer.msg("修改成功");
                        _this.fnGetList({},true);
                    }else if(result.code == -2){
                        layer.msg(result.message);
                    }else{
                        layer.msg("修改失败");
                    }
                });
            })
        },




        fnGetList: function (data, initPage) {
            var _this = this;
            var table = $(".data-container table");

            setAPI.groupList(data, function (result) {
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


                    var groupNameTd = '<td> ' + v.group_name + '</td>'
                    var menuNamesTd = '<td>' + v.menu_names + '</td>';
                    var summaryTd = '<td>' + v.summary + '</td>';
                    var createTimeTd = '<td>' + v.create_time + '</td>';

                    oTr += ' <tr id="tr_' + id + '" class="fadeIn animated" data-id="'+v.id+'" data-menuIds ="' + v.menu_ids + '" >' +  checkTd + groupNameTd + menuNamesTd + summaryTd  +
                        createTimeTd  + controlTd +  '</tr> ' ;
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
