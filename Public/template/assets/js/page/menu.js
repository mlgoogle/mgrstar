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

            body.on("click",".menu-right",function () {
               var __this = $(this);
                __this.attr('class','menu-down');
                var pid = __this.parents('tr').attr('data-id');

                data = {
                    pid:pid
                }

                var oTr = '', classId = 'class_' + pid,
                    checkTd = '<td><input type="checkbox"></td>';
                setAPI.menuList(data,function (result) {

                    if (!result.list || result.list.length == "0") {
                       // table.find("tbody").empty().html("<tr><td colspan='9'>暂无记录</td></tr>");
                       // $(".pagination").hide();
                        return false;
                    }

                    $.each(result.list, function (i, v) {
                        var href = rootHome + "/Set/menu_info/id/" + v.id;
                        controlTd = "<td>" +
                            "<a class='J_showChangeUser text-blue' href='" + href + "'> 修改 </a>  " +
                            "</td>";

                        var menuNameTd = '<td> <a class="menu-left" id="menu_id_' + v.id + '"  href="javascript:;"></a> ' + v.menu_name + '</td>'
                        var menuFileTd = '<td>' + v.menu_file + '</td>';
                        var menuSummaryTd = '<td>' + v.menu_summary + '</td>';
                        var createTimeTd = '<td>' + v.create_time + '</td>';

                        oTr += '<tr class="fadeIn animated '+ classId +'" data-id="' + v.id + '">' + checkTd + menuNameTd + menuFileTd + menuSummaryTd +
                            createTimeTd + controlTd + '</tr>';
                    });


                    $('#tr_' + pid ).after(oTr);
                    //__this.parent().parent().after(oTr);


                });

            });

            body.on("click",".menu-down",function () {
                var __this = $(this);

                __this.attr('class','menu-right');
                var pid = __this.parents('tr').attr('data-id');

                $('.class_' + pid ).remove();

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
                        layer.msg("新建失败");
                    }
                    // $this.removeClass("disabled");
                });
            })
        },




        fnGetList: function (data, initPage) {
            var _this = this;
            var table = $(".data-container table");

            setAPI.menuList(data, function (result) {
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

                    var href= rootHome + "/Set/menu_info/id/"+v.id;
                    controlTd = "<td>" +
                        "<a class='J_showChangeUser text-blue' href='" + href + "'> 修改 </a>  " +
                        "</td>";


                    var menuNameTd = '<td> ' + v.menu_name + ' <a id="menu_id_' + v.id + '" class="menu-right" href="javascript:;"></a></td>'
                    var menuFileTd = '<td>' + v.menu_file + '</td>';
                    var menuSummaryTd = '<td>' + v.menu_summary + '</td>';
                    var createTimeTd = '<td>' + v.create_time + '</td>';

                  //  var divTd = '<div id="div_' + v.id +'"></div>';

                    oTr += ' <tr id="tr_' + id + '" class="fadeIn animated" data-id="'+v.id+'">' +  checkTd + menuNameTd + menuFileTd + menuSummaryTd  +
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
