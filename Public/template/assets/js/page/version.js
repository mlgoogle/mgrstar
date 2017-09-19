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
            this.fnGetVersionLogList({},true);
        },
        bindEvents: function () {
            this.onChange();
           // this.onUpdateUserStatus();
        },

        initModal: function () {

            body.on("click", ".J_showChangeUser", function () {
                var  ttypeNameArr = ['IOS用户端','安卓用户端','IOS明星端','安卓明星端'];

                var oForm = $(".changeUserModal .modalForm");
                var $this = $(this);
                var id = $this.parents('tr').attr('data-id');

                var oTd = $this.parents('tr').find('td');
                var appName = oTd.eq(2).text();
                var VersionName = oTd.eq(3).text();
                var Size = oTd.eq(4).text();
                var UpdateDesc = oTd.eq(5).text();
                var Url = oTd.eq(6).text();


                $('.remodal-title').text(ttypeNameArr[id]);

                oForm.find('[name=appName]').val(appName);
                oForm.find('[name=VersionName]').val(VersionName);
                oForm.find('[name=Size]').val(Size);
                oForm.find("textarea[name=UpdateDesc]").text(UpdateDesc);
                oForm.find('[name=Url]').val(Url);
                oForm.find('[name=ttype]').val(id);



                // id: userId,
                //     memberId: oForm.find('[name=org]').val(),
                //     agentId: oForm.find('[name=agent]').val(),
                //     agentSubtId: oForm.find('[name=agentSub]').val(),
                //     //uid: oForm.find('[name=username]').val(),
                //     password: oForm.find('[name=password]').val(),
                //     nickname: oForm.find('[name=nickname]').val(),
                //     cellphone:oForm.find('[name=cellphone]').val(),


                changeUserModal.open();
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
                        layer.msg("新建失败");
                    }
                    // $this.removeClass("disabled");
                });
            })
        },


        //更新记录列表
        fnGetVersionLogList: function (data, initPage) {
            var _this = this;
            var table = $(".data-container.version-log-list table");
            setAPI.versionLogList(data,function (result) {

                if (!result.list || result.list.length == "0") {
                    table.find("tbody").empty().html("<tr><td colspan='10'>暂无记录</td></tr>");
                    $(".pagination.log-list").hide();
                    return false;
                }

                var oTr = '';
                checkTd = '<td><input type="checkbox"></td>';
                controlTd = "<td>  </td>";
                $.each(result.list, function (i, v) {
                    var adminNameTd = '<td>' + v.adminName + '</td>';
                    var versionNameTd = '<td>' + v.VersionName + '</td>';
                    var urlTd = '<td>' + (v.Url?v.Url:'') + '</td>';

                    var ttypeNameTd = '<td>' + v.ttypeName + '</td>';

                    var createTimeTd = '<td>' + v.create_time + '</td>';



                    oTr += '<tr class="fadeIn animated" data-id="' + v.id + '" data-code= "' + v.starcode + '"  >' + checkTd
                        + adminNameTd  + versionNameTd + urlTd + ttypeNameTd + createTimeTd + '</tr>';
                });


                table.find("tbody").empty().html(oTr);
                if (initPage) {
                    var pageCount = result.totalPages;
                    if (pageCount > 0) {
                        $(".pagination.log-list").show().html("").createPage({
                            pageCount: pageCount,
                            current: 1,
                            backFn: function (p) {
                                var newData = data;
                                newData.page = p;
                                _this.fnGetProfitLogList(data)
                            }
                        })
                    }
                }

            });
        },




        fnGetList: function (data, initPage) {
            var _this = this;
            var table = $(".data-container table").eq(0);

            setAPI.versionList(data, function (result) {
                console.log("获取用户管理列表 调用成功!");

                if (!result.list || result.list.length == "0") {
                    table.find("tbody").empty().html("<tr><td colspan='9'>暂无记录</td></tr>");
                    $(".pagination.list").hide();
                    return false;
                }

                var oTr,
                    checkTd = '<td><input type="checkbox"></td>',
                    controlTd = "<td>" +
                        "<a class='J_showChangeUser text-blue' href='javascript:;'> 修改 </a>  " +
                        "</td>";

                $.each(result.list, function (i, v) {


                    var appNameTd = '<td>' + v.appName + '</td>'
                    var VersionNameTd = '<td>' + v.VersionName + '</td>';
                    var ttypeTd = '<td>' + v.ttype + '</td>';
                    var typeNameTd = '<td>' + v.typeName + '</td>';
                    var SizeTd = '<td>' + v.Size + '</td>';
                    var UpdateDescTd = '<td>' + v.UpdateDesc + '</td>';

                    var UrlTd = '<td>' + v.Url + '</td>';

                    var ReleaseTimeTd = '<td>' + v.ReleaseTime + '</td>';

                    oTr += '<tr class="fadeIn animated" data-id="'+v.ttype+'">' +  checkTd + typeNameTd + appNameTd + VersionNameTd  +
                        SizeTd  + UpdateDescTd + UrlTd + ReleaseTimeTd  + controlTd +  '</tr>';
                });
                table.find("tbody").empty().html(oTr);

                if (initPage) {
                    var pageCount = result.totalPages;
                    if (pageCount > 0) {
                        console.log("页数：" + pageCount);
                        $(".pagination.list").show().html("").createPage({
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
