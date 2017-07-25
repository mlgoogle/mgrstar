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
    var addCarouselModal = $('[data-remodal-id=addCarouselModal]').remodal();

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
            this.onStarInfo();
        },

        initModal: function () {
            $(".J_showAdd").on("click", function () {
                var oForm = $(".addCarouselModal .modalForm");
                $(".pic1_div").html('');
                oForm.find("input[name=starname]").removeAttr("readonly");
                addCarouselModal.open();
            });
            body.on("click", ".J_showEdit", function () {
                var $this = $(this);
                var id = $this.parents('tr').attr('data-id');
                var oTd = $this.parents('tr').find('td');
                var starcode= $this.parents('tr').attr('data-code');
                var starname = oTd.eq(1).text();
                var sort = oTd.eq(2).text();
                var pic_url = $this.parents("tr").attr("data-src");
                var local_pic = $this.parents("tr").attr("data-local");

                var oForm = $(".addCarouselModal .modalForm");
                oForm.find("input[name=id]").val(id);
                oForm.find("input[name=starname]").val(starname);
                oForm.find("input[name=starname]").attr("readonly", "readonly");
                oForm.find("input[name=pic_url]").val(pic_url);
                oForm.find("input[name=local_pic]").val(local_pic);
                oForm.find("input[name=starcode]").val(starcode);
                oForm.find("input[name=starcode]").attr("readonly", "readonly");
                oForm.find("input[name=sort]").val(sort);

                var img = '<div><img src="'+ pic_url +'"></div>';
                $(".pic1_div").html(img);

                //oForm.find("input[name=sort]").attr("readonly", "readonly");
                addCarouselModal.open();
            });

            $(document).on('closed', '.remodal', function (e) {
                $(this).find(".modalForm")[0].reset();
            });
        },
        onStarInfo: function () {
            var _this = $(".addCarouselModal input[name='starname']");
            _this.on("blur", function () {
                starAPI.getStarInfo({starname: _this.val()}, function (result) {
                    if(result.code==-2){
                        layer.msg(result.message);
                        $(".addCarouselModal input[name='starcode']").attr("value", '');
                    }else {
                        $(".addCarouselModal input[name='starname']").attr("value", result.star_name);
                        $(".addCarouselModal input[name='starcode']").attr("value", result.star_code);
                    }
                })
            })
        },
        onAdd: function () {
            var _this = this;
            var confirmBtn = $(".addCarouselModal .remodal-confirm");
            var oForm = $(".addCarouselModal form");
            confirmBtn.on("click", function (e) {
                e.preventDefault();
                var $this = $(this);
                var id = oForm.find('[name=id]').val();

                var data = {
                    id : id,
                    starname: oForm.find('[name=starname]').val(),
                    starcode: oForm.find('[name=starcode]').val(),
                    pic_url: oForm.find('[name=pic_url]').val(),
                    local_pic: oForm.find('[name=local_pic]').val(),
                    sort: oForm.find('[name=sort]').val()
                };

                if (id > 0) {
                    starAPI.editCarousel(data, function (result) {
                        if (result.code == 0) {
                            layer.msg('修改成功');
                            addCarouselModal    .close();
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

                starAPI.addCarousel(data, function (result) {
                    if (result.code == 0) {
                        layer.msg('添加成功');
                        addCarouselModal    .close();
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
                        starAPI.delCarousel(data, function (result) {
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
                    //var code = '<td>' + v.code + '</td>';
                    var starname = '<td>' + v.name + '</td>';
                    var sort = '<td>' + v.sort + '</td>';
                    var src = v.pic1;//publicUrl + '/uploads/carousel/'+ v.local_pic;
                    //var url = v.pic_url;
                    var pic_url = '<td><img src="'+src +'" class="icon-star-img"></td>';
                    var display_on_home = v.display_on_home?v.display_on_home:0;

                    if(display_on_home == 0){
                        var status_style = 'class="btn btn-up-status"';
                        var status_name  = '下线';
                    }else {
                        var status_style = 'class="btn btn-status"';
                        var status_name  = '上线';
                    }

                    var status = '<td><a href="javascript:;" ' + status_style + ' onclick="status(this)" data-code="'+ v.code +'">' + status_name + '</a></td>';


                    oTr += '<tr class="fadeIn animated" data-id="' + v.uid + '" data-code= "' + v.code + '" data-src="'+ src +'" data-local="'+ v.local_pic +'">' + checkTd +  starname
                        + sort + pic_url + status + controlTd + '</tr>';
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
