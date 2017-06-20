<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <title>客户列表</title>
    <link rel="stylesheet" href="/xh/Public/template/assets/css/index.min.css">
</head>
<body>
<div class="wrap">
    <div class="header">
        <div class="clearfix layout">
            <h1><a href="/xh/index.php/Home/accountmanage/usermanage">交易管理系统</a></h1>
            <div>
                <a href="/xh/index.php/Home/accountmanage/usermanage">账户管理</a>
                <a href="/xh/index.php/Home/clientmanage/clientList" class="active">客户管理</a>
                <a href="/xh/index.php/Home/countmanage/countTable">结算管理</a>
                <a href="/xh/index.php/Home/sysmanage/pwdManage">系统管理</a>
            </div>
        </div>
    </div>
    <div class="main">
        <div class="sidebar">
            <a href="/xh/index.php/Home/clientmanage/clientList" class="active">客户列表</a>
            <a href="/xh/index.php/Home/clientmanage/chiCangSearch">持仓查询</a>
            <a href="/xh/index.php/Home/clientmanage/pingCangSearch">平仓查询</a>
            <a href="/xh/index.php/Home/clientmanage/chuJinSearch">出金查询</a>
            <a href="/xh/index.php/Home/clientmanage/ruJinSearch" >入金查询</a>
        </div>
        <div class="content">
            <!--<div class="control-bar"><a href="javascript:;" class="btn">导出EXL</a></div>-->
            <div class="search-bar">注册时间:
                <input type="text" id="dateStart" placeholder="起始时间">-<input type="text" id="dateEnd" placeholder="结束时间">
                <!--<input type="text" placeholder="关键字：机构名称">-->
                <!--<input type="text" placeholder="上级机构">-->
                <input type="text" name="nickname" placeholder="昵称">
                <input type="text" name="phone" placeholder="手机号码">
                <a href="javascript:;" class="btn J_search">查询</a>
            </div>
            <div class="data-container">
                <table>
                    <thead>
                    <tr>
                        <th></th>
                        <th>注册时间</th>
                        <th>用户昵称</th>
                        <th>手机号码</th>
                        <th>所属机构</th>
                        <th>所属经纪人</th>
                        <th>交易状态</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!--for i in list-->
                    <!--    tr-->
                    <!--        td-->
                    <!--            input(type="checkbox")-->
                    <!--        td 2016-10-12-->
                    <!--        td 瓦夏朵-->
                    <!--        td 13200989999-->
                    <!--        td 华东-->
                    <!--        td 张全蛋-->
                    <!--        td-->
                    <!--            a(href="/clientManage/clientListView/buyLog?id=123") 查看-->
                    <!--            span |-->
                    <!--            a.J_showChangeLine(href="javascript:;") 额度-->
                    <!--            span |-->
                    <!--            a.J_showStopTrade(href="javascript:;") 停止交易-->
                    </tbody>
                </table>
                <div class="pagination"></div>
            </div>
        </div>
    </div>
    <div data-remodal-id="changeLineModal" class="remodal changeLineModal">
        <div class="remodal-head">
            <div class="remodal-title">修改额度</div>
            <div data-remodal-action="cancel" class="remodal-close"></div>
        </div>
        <div class="remodal-body">
            <form class="modalForm">
                <div class="form-control">
                    <label>所属机构</label>
                    <input type="text" name="orgName" readonly>
                </div>
                <div class="form-control">
                    <label>昵称</label>
                    <input type="text" name="nickname" readonly>
                </div>
                <div class="form-control">
                    <label>手机号码</label>
                    <input type="text" name="phone" readonly>
                </div>
                <div class="form-control">
                    <label>交易额度</label>
                    <input type="text" name="line">
                </div>
            </form>
        </div>
        <div class="remodal-footer"><a href="javascript:;" data-remodal-action="confirm"
                                       class="remodal-confirm">确认</a><a href="javascript:;" data-remodal-action="cancel"
                                                                        class="remodal-cancel">取消</a></div>
    </div>
</div>
<script src="/xh/Public/template/assets/js/vendor/require.js" data-main="/xh/Public/template/assets/js/common"></script>
<script>
    require(['common'], function () {
        require(['page/clientList']);
    });
</script>
</body>
</html>