/**
 * Created by Administrator on 2017/6/7.
 */
define([
    "jquery",
    "utils",
    "config",
    "api/timerAPI",
    "layer",
    "pagination",
    "remodal"
], function ($, utils, config, timerAPI) {
    var addTimerModal = $('[data-remodal-id=addTimerModal]').remodal();

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
            this.onDel();
            this.onSearch();
        },
        initModal: function () {
            $(".J_showAdd").on("click", function () {
                addTimerModal.open();
            });
            body.on("click", ".J_showEdit", function () {
                var $this = $(this);
                var id = $this.parents('tr').attr('data-id');
                //checkTd + id + add_time + starname + ownseconds + status + controlTd
                var oTd = $this.parents('tr').find('td');
                var starname = oTd.eq(3).text();
                var ownseconds = oTd.eq(4).text();
                var status = oTd.eq(5).text();

                var oForm = $(".addTimerModal .modalForm");
                oForm.find("input[name=id]").val(id);
                oForm.find("input[name=starname]").val(starname);
                oForm.find("input[name=ownseconds]").val(ownseconds);
                oForm.find("input[name=status]").val(status);
                addTimerModal.open();
            });

            $(document).on('closed', '.remodal', function (e) {
                $(this).find(".modalForm")[0].reset();
            });
        },
        onDel: function () {
            var _this = this;
            $(".J_onDel").on("click", function () {
                var idArr = utils.getCheckedArr();

                if (idArr.length > 0) {
                    layer.confirm('确定删除选中的列表项吗？', {icon: 3}, function (index) {
                        var data = {ids : idArr};
                        timerAPI.delTimer(data, function (result) {
                            _this.fnGetList({}, true);
                        });
                        layer.close(index)
                    });
                } else {
                    layer.alert("请先选择要删除的列表项", {icon: 0});
                }
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

            timerAPI.search(data, function (result) {

                if (!result.list || result.list.length == "0") {
                    table.find("tbody").empty().html("<tr><td colspan='10'>暂无记录</td></tr>");
                    $(".pagination").hide();
                    return false;
                }

                var nameVal = ['第一期','第二期','第三期','第四期','第五期'];

                var oTr,
                    checkTd = '<td><input type="checkbox"></td>';
                $.each(result.list, function (i, v) {
                    var href = rootUrl+"/Timer/info/id/"+v.id;
                    var controlTd = "<td><a class='J_showEdit1 text-blue' href='"+href+"' >修改/查看</a></td>";

                    var id = '<td>' + v.id + '</td>';
                    var total_micro = '<td>' + (v.total_micro?v.total_micro:0) + '</td>';
                    var starnamecode = '<td>' + v.starname + '/' + v.starcode + '</td>';

                    //var add_time = '<td>' + v.add_time + '</td>';
                   // var ownseconds = '<td>' + v.micro + '</td>';

                    var sortName = '<td>' + (nameVal[v.sort-1]?nameVal[v.sort-1]:'未知') + '</td>';

                    var micro   = '<td>' + v.micro + '</td>';

                    var publish_time   = '<td>' + (v.publish_begin_time?v.publish_begin_time:0)  + '--' + (v.publish_end_time?v.publish_end_time:0) + '</td>';

                    var publish_last_time  = '<td>' + v.publish_last_time + '</td>';

                    if(v.status_type==0) { //上线
                        var status_style = 'class="btn btn-up-status"';
                        var edit_status = '下线';
                    }else {
                        var status_style = 'class="btn btn-status"';
                        var edit_status = '上线';
                    }


                    var status = '<td>' + v.status + '</td>';
                    var edit_status_name = '<td><a href="javascript:;" ' + status_style + 'onclick="status(this)" data-id="'+ v.id +'">' + edit_status + '</a></td>';

                    oTr += '<tr class="fadeIn animated" data-id="' + v.id + '">' + checkTd + id + starnamecode  + total_micro + sortName +
                        micro + publish_time + publish_last_time + status + edit_status_name + controlTd + '</tr>';
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
