define([
    "jquery",
    "utils",
    "config",
    "profitAPI",
    "layer",
    "pagination",
    "remodal"
], function ($, utils, config, profitAPI) {
    var addBrokerModal = $('[data-remodal-id=addBrokerModal]').remodal();
    var editBrokerModal = $('[data-remodal-id=editBrokerModal]').remodal();
    var withdrawalsModel = $('[data-remodal-id=withdrawalsModel]').remodal();
    var body = $("body");
    var brokerId;


    var page = {
        init: function () {
            this.render();
            this.bindEvents();
        },
        render: function () {
            this.initModal();
           // this.initOrgList();
           // this.initTopOrgList();

            this.fnGetList({}, true);
        },
        bindEvents: function () {
            //this.onSearch();
            //this.onAdd();
            // this.onUpdateUserStatus();
            //this.onCheck();
        },

        initTopOrgList: function () {
            var oSelectSearch = $("select[name=level]");
            var oSelect = $("select[name=orgTopLevel]");
            var optionSearchStr = '<option value="">上级机构</option>';
            var optionStr = '';
            var data = {
                pageNum: '',
                page: ''
            };
            accountAPI.getTopOrgList(data, function (result) {
                console.log('一级机构列表-调用成功');
                if(result) {
                    $.each(result, function (i, v) {
                        optionStr += '<option value="' + v.memberid + '">' + v.name + '</option>';
                        optionSearchStr += '<option value="' + v.memberid + '">' + v.name + '</option>';
                    });
                }
                oSelectSearch.html(optionSearchStr);
                oSelect.html(optionStr);
            });
        },
        initOrgList: function () {
            var oSelect = $("select[name=org]");
            var optionStr = "<option value='0'>请选择</option>";
            var data = {
                pageNum: '',
                page: ''
            };
            accountAPI.getOrgList(data, function (result) {
                console.log('机构列表-调用成功');
                $.each(result.list, function (i, v) {
                    optionStr += '<option value="' + v.memberid + '">' + v.name + '</option>'
                });
                oSelect.html(optionStr);
            });

        },

        initModal: function () {
            $(".J_showAdd").on("click", function () {

                addBrokerModal.open();
            });

            $(".J_showEdit").on("click", function () {

                editBrokerModal.open();
            });

            $(".put_withdrawals").on('click',function () {
                withdrawalsModel.open();
            });
            //添加 绑定银行卡
            $(".add-bank").on("click", function () {
                var oForm = $(".addBrokerModal");

                var bankPersonName = oForm.find('input[name=bankPersonName]').val();
                var bankAccount = oForm.find('input[name=bankAccount]').val();
                data = {
                    bankPersonName:bankPersonName,
                    bankAccount:bankAccount,
                };
                profitAPI.addBank(data,function (result){

                    if(result.code == -2){
                        layer.msg(result.message);
                        return false;
                    }else{
                        layer.msg(result.message);

                        addBrokerModal.close();
                        window.setTimeout(
                            'window.location.reload()',
                            2000
                        );
                    }


                });

            });

            //提现
            $(".withdrawals-bank").on("click", function () {
                var oForm = $(".withdrawalsModel");

               // var bankPersonName = oForm.find('input[name=bankPersonName]').val();
                var bankAccount    = oForm.find('input[name=bankAccount]').val();
                var bankSum        = oForm.find('input[name=bankSum]').val();
                var bankName       = oForm.find('input[name=bankName]').val();
                data = {
                    bankSum:bankSum,
                //    bankPersonName:bankPersonName,
                    bankAccount:bankAccount,
                    bankName : bankName
                };
                profitAPI.withdrawals(data,function (result){

                    if(result.code == -2){
                        layer.msg(result.message);
                        return false;
                    }else{
                        layer.msg(result.message);

                        editBrokerModal.close();

                        window.setTimeout(
                            'window.location.reload()',
                            2000
                        );
                    }


                });

                // addBrokerModal.close();
            });


            //修改 更换银行卡
            $(".edit-bank").on("click", function () {
                var oForm = $(".editBrokerModal");

                var bankPersonName = oForm.find('input[name=bankPersonName]').val();
                var bankAccount    = oForm.find('input[name=bankAccount]').val();
                var id = oForm.find('input[name=id]').val();
                data = {
                    id:id,
                    bankPersonName:bankPersonName,
                    bankAccount:bankAccount
                };
                profitAPI.editBank(data,function (result){

                    if(result.code == -2){
                        layer.msg(result.message);
                        return false;
                    }else{
                        layer.msg(result.message);

                        editBrokerModal.close();

                        window.setTimeout(
                            'window.location.reload()',
                            2000
                        );
                    }



                });

                // addBrokerModal.close();
            });


            $(document).on('closed', '.remodal', function (e) {
                $(this).find(".modalForm")[0].reset();
            });

        },

        onSearch: function () {
            var _this = this;
            var oForm = $(".search-bar");
            $(".J_search").on("click", function () {
                var data = {
                    page: 1,
                    memberid: oForm.find("select[name=level]").val(),
                    // agentId:  oForm.find("select[name=agent]").val(),
                    nickname: oForm.find("input[name=nickname]").val(),
                    phone: oForm.find("input[name=phone]").val()
                };
                _this.fnGetList(data, true);
            });
        },


        fnGetList: function (data, initPage) {
            var _this = this;
            var table = $(".data-container table");

            // showLoading(".J_consumeTable");
            profitAPI.agentProfit(data, function (result) {
                console.log("获取经纪人列表 调用成功!");
                if (!result.list) {
                    table.find("tbody").empty().html("<tr><td colspan='9'>暂无记录</td></tr>");
                    $(".pagination").hide();
                    return false;
                }
                var oTr = '',
                    checkTd = '<td><input type="checkbox"></td>',
                    controlTd = "<td>" +
                        "<a class='J_showCheckBroker text-blue' href='javascript:;'> 修改 </a>" +
                        "</td>";
                $.each(result.list, function (i, v) {
                    var xuTd = '<td>' + v.id + '</td>';
                    var markTd = '<td>' + v.mark + '</td>';
                    var nicknameTd = '<td>' + (v.nickname?v.nickname:'') + '</td>';
                    var orderNumTd  =  '<td>' + (v.order_num?v.order_num:0) + '</td>';
                    var orderSumPriceTd  =  '<td>' + (v.order_sum_price?v.order_sum_price:0) + '</td>';
                    var profit_price = v.profit_price?v.profit_price:0;
                    var profitPriceTd  =  '<td>' + profit_price + '</td>';

                    var profit_sum_price = v.profit_sum_price;


                    oTr += '<tr class="fadeIn animated" data-id="' + v.id + '">' + checkTd + xuTd + nicknameTd + markTd + orderNumTd + orderSumPriceTd
                         + profitPriceTd + '</tr>';

                    var oForm = $(".withdrawalsModel");
                    var bankSum = profit_sum_price*100;
                    oForm.find('input[name=bankSum]').val(bankSum);
                    $('#profit_priceTd_text').text(profit_sum_price);
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
