/**
 * Created by Administrator on 2017/6/7.
 */
define([
    "jquery",
    "utils",
    "config",
    "api/lucidaAPI",
    "layer",
    "pagination",
    "remodal"
], function ($, utils, config, lucidaAPI) {
    var addLucidaModal = $('[data-remodal-id=addLucidaModal]').remodal();

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
            this.onLucidaInfo();
        },

        initModal: function () {
            $(".J_showAdd").on("click", function () {
                addLucidaModal.open();
            });
            body.on("click", ".J_showEdit", function () {
                var $this = $(this);
                var id = $this.parents('tr').attr('data-id');
                var oTd = $this.parents('tr').find('td');
                var lucidacode= oTd.eq(1).text();
                var lucidaname = oTd.eq(2).text();
                var sort = oTd.eq(3).text();
                var pic_url = $this.parents("tr").attr("data-src");

                var oForm = $(".addLucidaModal .modalForm");
                oForm.find("input[name=id]").val(id);
                oForm.find("input[name=lucidaname]").val(lucidaname);
                oForm.find("input[name=pic_url]").val(pic_url);
                oForm.find("input[name=lucidacode]").val(lucidacode);
                oForm.find("input[name=sort]").val(sort);
                addLucidaModal.open();
            });

            $(document).on('closed', '.remodal', function (e) {
                $(this).find(".modalForm")[0].reset();
            });
        },
        onLucidaInfo: function () {
            var _this = $(".addLucidaModal input[name='lucidaname']");
            _this.on("blur", function () {
                lucidaAPI.getLucidaInfo({lucidaname: _this.val()}, function (result) {
                    $(".addLucidaModal input[name='lucidaname']").attr("value", result.lucida_name);
                    $(".addLucidaModal input[name='lucidacode']").attr("value", result.lucida_code);
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
                        lucidaAPI.delLucida(data, function (result) {
                            _this.fnGetList({}, true);
                            ajaxUrl();
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

            lucidaAPI.search(data, function (result) {

                if (!result.list || result.list.length == "0") {
                    table.find("tbody").empty().html("<tr><td colspan='10'>暂无记录</td></tr>");
                    $(".pagination").hide();
                    return false;
                }
                var oTr,
                    checkTd = '<td><input type="checkbox"></td>';
                $.each(result.list, function (i, v) {
                    var href = rootUrl+"/Lucida/info/id/"+v.uid;
                    var controlTd = "<td><a class='J_showEdit1 text-blue' href='"+href+"' >修改</a></td>";
                    var add_time = '<td>' + v.add_time + '</td>';
                    var code = '<td>' + v.code + '</td>';
                    var name = '<td>' + v.name + '</td>';
                    var src =  v.pic_src;
                    //var src = v.pic1;
                    var pic1 = v.pic1;
                    var pic_url = '<td><img src="'+ qn_domain + pic1 +'" class="icon-star-img"></td>';

                    if(v.status_type == 0){
                        var status_style = 'class="btn btn-up-status"';

                        var edit_status = '下架';
                    }else {
                        var status_style = 'class="btn btn-status"';
                        var edit_status = '上架';
                    }

                    var status = '<td>' + v.status + '</td>';

                    var edit_status_name = '<td><a href="javascript:;" ' + status_style + ' onclick="status(this)" data-id="'+ v.uid +'">' + edit_status + '</a></td>';

                    oTr += '<tr class="fadeIn animated" data-id="' + v.uid + '"  data-src="'+ src +'">' + checkTd + add_time + name + code + pic_url + status +
                        edit_status_name + controlTd + '</tr>';
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
