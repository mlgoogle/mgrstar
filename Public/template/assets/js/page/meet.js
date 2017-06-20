/**
 * Created by Administrator on 2017/6/7.
 */
define([
    "jquery",
    "utils",
    "config",
    "api/meetAPI",
    "layer",
    "pagination",
    "remodal"
], function ($, utils, config, starAPI) {
    var addMeetModal = $('[data-remodal-id=addMeetModal]').remodal();

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
                addMeetModal.open();
            });
            body.on("click", ".J_showEdit", function () {
                var $this = $(this);
                var id = $this.parents('tr').attr('data-id');
                var starcode = $this.parents('tr').attr('data-code');
                var timer = $this.parents('tr').attr('data-timer');
                var oTd = $this.parents('tr').find('td');
                var addtime = oTd.eq(0).text();
                var starname = oTd.eq(1).text();
                var meettype = oTd.eq(2).text();
                var place = oTd.eq(3).text();
                var nickname = oTd.eq(4).text();
                var status = oTd.eq(5).text();
                var price = oTd.eq(6).text();

                var oForm = $(".addMeetModal .modalForm");
                oForm.find("input[name=id]").val(id);
                oForm.find("input[name=addtime]").val(addtime);
                oForm.find("input[name=starname]").val(starname);
                oForm.find("input[name=starcode]").val(starcode);
                oForm.find("input[name=timer]").val(timer);
                oForm.find("input[name=place]").val(place);
                oForm.find("input[name=meettype]").val(meettype);
                oForm.find("input[name=nickname]").val(nickname);
                oForm.find("input[name=status]").val(status);
                oForm.find("input[name=price]").val(price);
                addMeetModal.open();
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
                        starAPI.delMeet(data, function (result) {
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

            starAPI.search(data, function (result) {

                if (!result.list || result.list.length == "0") {
                    table.find("tbody").empty().html("<tr><td colspan='10'>暂无记录</td></tr>");
                    $(".pagination").hide();
                    return false;
                }
                var oTr,
                    //checkTd = '<td><input type="checkbox"></td>',
                    controlTd = "<td><a class='J_showEdit text-blue' href='javascript:;'>修改</a></td>";
                $.each(result.list, function (i, v) {
                    var add_time = '<td>' + v.order_time + '</td>';
                    var starname = '<td>' + v.starname + '</td>';
                    var active = '<td>' + v.active + '</td>';
                    var city = '<td>' + v.meet_city + '</td>';
                    var nickname = '<td>' + v.username + '</td>';
                    var micro = '<td>' + v.status + '</td>';
                    var status = '<td>' + v.price + '</td>';

                    oTr += '<tr class="fadeIn animated" data-id="' + v.mid + '" data-code="' + v.starcode + '" data-timer="'+ v.appoint_time +'">' + add_time + starname + active + city + nickname + micro + status + controlTd + '</tr>';
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
