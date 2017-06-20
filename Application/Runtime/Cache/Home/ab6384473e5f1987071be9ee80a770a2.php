<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <title>持仓查询</title>
    <link rel="stylesheet" href="/xh/Public/template/assets/css/index.min.css">
</head>
<body>
<div class="wrap">
    <div class="header">
        <div class="clearfix layout">
            <h1><a href="/xh/index.php/Home/accountmanage/userManage">交易管理系统</a></h1>
            <div>
                <a href="/xh/index.php/Home/accountmanage/userManage">账户管理</a>
                <a href="/xh/index.php/Home/clientmanage/clientList" class="active">客户管理</a>
                <a href="/xh/index.php/Home/countmanage/countTable">结算管理</a>
                <a href="/xh/index.php/Home/sysmanage/pwdManage">系统管理</a>
            </div>
        </div>
    </div>
    <div class="main">
        <div class="sidebar">
            <a href="/xh/index.php/Home/clientmanage/clientList">客户列表</a>
            <a href="/xh/index.php/Home/clientmanage/chiCangSearch" class="active">持仓查询</a>
            <a href="/xh/index.php/Home/clientmanage/pingCangSearch">平仓查询</a>
            <a href="/xh/index.php/Home/clientmanage/chuJinSearch">出金查询</a>
            <a href="/xh/index.php/Home/clientmanage/ruJinSearch" >入金查询</a>
        </div>
        <div class="content">
            <div class="search-bar">范围时间:
                <input type="text" id="dateStart" placeholder="起始时间">-
                <input type="text" id="dateEnd" placeholder="结束时间">
                <select name="goodsName" id=""></select>
                <a href="javascript:;" class="btn J_search">查询</a>
            </div>
            <div class="data-container tab-container">
                <table>
                    <thead>
                        <tr>
                            <th>日期</th>
                            <th>交易订单号</th>
                            <th>商品名称</th>
                            <th>交易类型</th>
                            <th>金额</th>
                            <th>客户名称</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <div class="pagination"></div>
            </div>
        </div>
    </div>
</div>
<script src="/xh/Public/template/assets/js/vendor/require.js" data-main="/xh/Public/template/assets/js/common"></script>
<script>
    require(['common'], function () {
        require(['page/CCSearch']);
    });
</script>
</body>
</html>