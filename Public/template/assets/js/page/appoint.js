/**
 * Created by Administrator on 2017/6/7.
 */
define([
    "jquery",
    "utils",
    "config",
    "api/appointAPI",
    "layer",
    "pagination",
    "remodal"
], function ($, utils, config, starAPI) {
    var addAppointModal = $('[data-remodal-id=addAppointModal]').remodal();

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
            this.onSearch();
        },

        initModal: function () {

            $(".J_showAdd").on("click", function () {
                $(".star_url_div").html('');
                $(".star_url2_div").html('');
                addAppointModal.open();
            });
            body.on("click", ".J_showEdit", function () {
                var oForm = $(".addAppointModal .modalForm");

                var $this = $(this);
                var id = $this.parents('tr').attr('data-id');
                var oTd = $this.parents('tr').find('td');
                var appointname = oTd.eq(3).text();
                var micro = oTd.eq(4).text();
                var url1   = $this.parents('tr').attr('data-url1');
                var url2   = $this.parents('tr').attr('data-url2');

                oForm.find("input[name=id]").val(id);
                oForm.find("input[name=appointname]").val(appointname);
                oForm.find("input[name=micro]").val(micro);

                oForm.find("input[name=showpic_url]").val(url1);
                oForm.find("input[name=pic_url2]").val(url2);


                var img = '<img src="' + qn_domain + url1 +'">'; //
                $(".star_url_div").html(img); //选中
                var img2 = '<img src="' + qn_domain + url2 +'">'; //+ qn_domain
                $(".star_url2_div").html(img2); //未选中
                addAppointModal.open();
            });

            $(document).on('closed', '.remodal', function (e) {
                $(this).find(".modalForm")[0].reset();
            });
        },
        onAdd: function () {
            var _this = this;
            var confirmBtn = $(".addAppointModal .remodal-confirm");
            var oForm = $(".addAppointModal form");
            confirmBtn.on("click", function (e) {
                e.preventDefault();
                var $this = $(this);
                var id = oForm.find('[name=id]').val();

                var data = {
                    id : id,
                    appointname: oForm.find('[name=appointname]').val(),
                    oForm: oForm.find("input[name=status]").val(),
                    micro: oForm.find('[name=micro]').val(),
                    showpic_url: oForm.find('[name=showpic_url]').val(),
                    local_pic: oForm.find('[name=local_pic]').val(),
                    pic_url2: oForm.find('[name=pic_url2]').val()
                };

                if (id > 0) {
                    starAPI.editAppoint(data, function (result) {
                        if (result.code == 0) {
                            layer.msg('修改成功');
                            addAppointModal    .close();
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

                starAPI.addAppoint(data, function (result) {
                    if (result.code == 0) {
                        layer.msg('添加成功');
                        addAppointModal    .close();
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
                        starAPI.delAppoint(data, function (result) {
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
                    checkTd = '<td><input type="checkbox"></td>',
                    controlTd = "<td><a class='J_showEdit text-blue' href='javascript:;'>修改</a></td>";
                $.each(result.list, function (i, v) {
                    var id = '<td>' + v.mid + '</td>';
                    var appointname = '<td>' + v.name + '</td>';
                    var add_time = '<td>' + v.add_time + '</td>';
                    var micro = '<td>' + v.price + '</td>';

                    if(v.status_type == 0){
                        var status_style = 'class="btn btn-up-status"';
                        var edit_status = '下线';
                    }else{
                        var status_style = 'class="btn btn-status"';
                        var edit_status = '上线';
                    }

                    var status = '<td>' + v.status + '</td>';

                    var edit_status_name = '<td><a href="javascript:;" ' + status_style + ' onclick="status(this)" data-id="'+ v.mid +'">' + edit_status + '</a></td>';

                    oTr += '<tr class="fadeIn animated" data-id="' + v.mid +
                        '" data-url1="'  + v.data_url1 + '" data-url2="'  +  v.data_url2 + '" >' +
                        checkTd + id + add_time + appointname + micro + status + edit_status_name + controlTd + '</tr>';
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
