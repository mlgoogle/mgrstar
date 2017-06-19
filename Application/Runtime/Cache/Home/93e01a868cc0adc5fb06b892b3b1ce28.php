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
          <div><a href="/index.php/Home/accountmanage/userManage" >账户管理</a><a href="/index.php/Home/clientmanage/clientList"class="active">客户管理</a><a href="/index.php/Home/countManage/countTable">结算管理</a><a href="/index.php/Home/sysmanage/pwdManage">系统管理</a></div>
        </div>
      </div>
      <div class="main">
        <div class="sidebar"><a href="/index.php/Home/clientmanage/clientList" class="active">客户列表</a><!--<a href="/clientManage/chiCangSearch">持仓查询</a><a href="/clientManage/pingCangSearch">平仓查询</a><a href="/clientManage/chuRuJinSearch">出入金查询</a>--></div>
        <div class="content">
          <div class="control-bar">
            客户信息 &nbsp;&nbsp;
            昵称：<em><?php echo ($userInfo["nickname"]); ?></em>&nbsp;&nbsp; 手机号码：<em><?php echo ($userInfo["phoneNum"]); ?></em>&nbsp;&nbsp;<!--<a href="javascript:;" class="btn">重置密码</a>-->
          </div>
          <!--<div class="search-bar">
            <label>类型：</label>
            <select name="caopanType">
              <option value="0">买涨</option>
              <option value="1">买跌</option>
            </select>
            <input id="dateStart" type="text" placeholder="开始时间" class="picker"><span>--</span>
            <input id="dateEnd" type="text" placeholder="结束时间" class="picker">
            <input type="text" placeholder="商品名称"><a href="javascript:;" class="btn">查询</a>
          </div>-->
          <div class="data-container">
            <div class="tab-btns"><a href="/clientmanage/wpcLog" class="active">未平仓</a><a href="/index.php/Home/clientmanage/ypcLog">已平仓</a><a href="/index.php/Home/clientmanage/outLog">出金记录</a><a href="/index.php/Home/clientmanage/inLog">入金记录</a></div>
            <table>
              <thead>
                <tr>
                  <th>日期</th>
                  <th>订单号</th>
                  <th>商品名称</th>
                  <th>商品规格</th>
                  <th>建仓放向</th>
                  <th>手数</th>
                  <th>操盘类型</th>
                  <th>盈亏</th>
                  <th>备注</th>
                </tr>
              </thead>
              <tbody>
                <!--for i in list-->
                <!--    tr-->
                <!--        td 2016-10-12-->
                <!--        td AS1223901-->
                <!--        td 奥斯卡-->
                <!--        td 1分时-->
                <!--        td-->
                <!--        td 6-->
                <!--        td 买涨-->
                <!--        td -100-->
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
          require(['page/logs/wpcLog']);
      });
    </script>
  </body>
</html>