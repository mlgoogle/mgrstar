/**
 * Created by Administrator on 2017/6/7.
 */
define([
    "jquery",
    "utils",
    "config",
    "api/customerAPI",
    "layer",
    "pagination",
    "remodal"
], function ($, utils, config, customerAPI) {
    var addCustomerModal = $('[data-remodal-id=addCustomerModal]').remodal();

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
                addCustomerModal.open();
            });
            body.on("click", ".J_showEdit", function () {
                var $this = $(this);
                var id = $this.parents('tr').attr('data-id');
                var idcard = $this.parents('tr').attr('data-idcard');
                var oTd = $this.parents('tr').find('td');
                var realname = oTd.eq(3).text();
                var phoneNum = oTd.eq(2).text();
                var nickname = oTd.eq(4).text();
                var recommend = oTd.eq(5).text();

                // id + registerTime + phoneNum + realname + nickname + recommend + registerStatus + agentId + controlTd
                var oForm = $(".addCustomerModal .modalForm");
                oForm.find("#realname").text(realname);
                oForm.find("#phoneNum").text(phoneNum);
                oForm.find("#nickname").text(nickname);
                oForm.find("#real").text(idcard);
                oForm.find("#recommend").text(recommend);
                addCustomerModal.open();
            });

            $(document).on('closed', '.remodal', function (e) {
                $(this).find(".modalForm")[0].reset();
            });
        },
        onAdd: function () {
            var _this = this;
            var confirmBtn = $(".addCustomerModal .remodal-confirm");
            var oForm = $(".addCustomerModal form");
            confirmBtn.on("click", function (e) {
                e.preventDefault();
                var $this = $(this);
                var id = oForm.find('[name=id]').val();

                var data = {
                    id : id,
                    customername: oForm.find('[name=customername]').val(),
                    oForm: oForm.find("input[name=status]").val(),
                    micro: oForm.find('[name=micro]').val()
                };

                if (id > 0) {
                    customerAPI.editCustomer(data, function (result) {
                        if (result.code == 0) {
                            layer.msg('修改成功');
                            addCustomerModal    .close();
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

                customerAPI.addCustomer(data, function (result) {
                    if (result.code == 0) {
                        layer.msg('添加成功');
                        addCustomerModal    .close();
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
                        customerAPI.delCustomer(data, function (result) {
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

            customerAPI.search(data, function (result) {

                if (!result.list || result.list.length == "0") {
                    table.find("tbody").empty().html("<tr><td colspan='10'>暂无记录</td></tr>");
                    $(".pagination").hide();
                    return false;
                }
                var oTr,
                    checkTd = '<td><input type="checkbox"></td>',
                    controlTd = "<td><a class='J_showEdit text-blue' href='javascript:;'>查看</a></td>";
                $.each(result.list, function (i, v) {
                    var id = '<td>' + v.uid + '</td>';
                    var phoneNum = '<td>' + v.phoneNum + '</td>';
                    var registerTime = '<td>' + v.registerTime + '</td>';
                    var realname = '<td>' + v.realname + '</td>';
                    var nickname = '<td>' + v.nickname + '</td>';
                    var agentId = '<td>' + v.agentId + '</td>';
                    var registerStatus = '<td>' + v.isreal + '</td>';
                    var recommend = '<td>' + v.recommend + '</td>';

                    oTr += '<tr class="fadeIn animated" data-id="' + v.id + '" data-idcard="' +v.idcards+ '">' + id + registerTime + phoneNum + realname + nickname + recommend + registerStatus + agentId + controlTd + '</tr>';
                });

                oTr += '<tr><td colspan="8">当前消费者总数为：'+ result.total +'</td></tr>';
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
