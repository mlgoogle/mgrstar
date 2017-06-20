<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <title></title>
    <link rel="stylesheet" href="/Public/template/assets/css/index.min.css">
  </head>
  <body>
    <div class="wrap">
      <div class="header">
        <div class="clearfix layout">
          <h1><a href="/index.php/Home/accountmanage/userManage">交易管理系统</a></h1>
          <div><a href="/index.php/Home/accountmanage/userManage" >账户管理</a><a href="/index.php/Home/clientmanage/clientList"class="active">客户管理</a><a href="/index.php/Home/countmanage/countTable">结算管理</a><a href="/index.php/Home/sysmanage/pwdManage">系统管理</a></div>
        </div>
      </div>
      <div class="main">
        <div class="sidebar"><a href="/clientManage/clientList" class="active">客户列表</a><!--<a href="/clientManage/chiCangSearch">持仓查询</a><a href="/clientManage/pingCangSearch">平仓查询</a><a href="/clientManage/chuRuJinSearch">出入金查询</a>--></div>
        <div class="content">
          <div class="control-bar">
            客户信息 &nbsp;&nbsp;
            昵称：<em><?php echo ($userInfo["nickname"]); ?></em>&nbsp;&nbsp; 手机号码：<em><?php echo ($userInfo["phoneNum"]); ?></em>&nbsp;&nbsp;<!--<a href="javascript:;" class="btn">重置密码</a>-->
          </div>
          <!--<div class="search-bar">
            <label>类型：</label>
            <select name="type">
              <option value="0">全部</option>
              <option value="1">入金</option>
              <option value="2">出斤</option>
            </select>
            <input id="dateStart" type="text" placeholder="开始时间" class="picker"><span>--</span>
            <input id="dateEnd" type="text" placeholder="结束时间" class="picker"><a href="javascript:;" class="btn">查询</a>
          </div>-->
          <div class="data-container">
            <div class="tab-btns"><a href="/index.php/Home/clientmanage/wpcLog">未平仓</a><a href="/index.php/Home/clientmanage/ypcLog">已平仓</a><a href="/index.php/Home/clientmanage/outLog" class="active">出金记录</a><a href="/index.php/Home/clientmanage/inLog">入金记录</a></div>
            <table>
              <thead>
                <tr>
                  <th>日期</th>
                  <th>交易订单号</th>
                  <th>金额(元)</th>
                  <th>手续费(元)</th>
                  <th>状态</th>
                  <th>备注</th>
                </tr>
              </thead>
              <tbody>
                <!--for i in list-->
                <!--    tr-->
                <!--        td 2016-10-12-->
                <!--        td AS1223901-->
                <!--        td 入金-->
                <!--        td 1000-->
                <!--        td 平台处理中-->
                <!--        td 未审核/已审核-->
                <!--        td 买涨-->
              </tbody>
            </table>
            <div class="pagination"></div>
          </div>
        </div>
      </div>
    </div>
    <script src="/Public/template/assets/js/vendor/require.js" data-main="/Public/template/assets/js/common"></script>
    <script>
      require(['common'], function () {
          require(['page/logs/outLog']);
      });
    </script>
  </body>
</html>