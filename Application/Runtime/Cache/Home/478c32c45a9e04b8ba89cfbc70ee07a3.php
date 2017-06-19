<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <title><?php echo (!empty($title)) ? $title : '星享后台' ?></title>
    <link rel="stylesheet" href="/xh/Public/template/assets/css/index.min.css">
    <!--<link rel="stylesheet" href="/xh/Public/template/assets/css/bootstrap/css/bootstrap.css">-->

    <script src="/xh/Public/template/assets/js/vendor/jquery.min.js" data-main="/xh/Public/template/assets/js/common"></script>
</head>
<body>
<div class="wrap">
    <div class="header">
        <div class="clearfix layout">
            <h1><a href="/xh/index.php/Home/AdminBacker/index">星享管理系统</a></h1>
            <div>
                <span class="spantext" >管理员：<?php echo ($user['uname']); ?>,<a style="color:#FF0000;" href="/xh/index.php/Home/login/doLoginout" onclick="return confirm('确定退出本系统?')" >系统退出</a></span>
            </div>
        </div>
    </div>
    <div class="main">
        <style>
    .submenu{display: none;}
    .submenu li{color: #CCCCCC;padding-left: 20px;width: 70%;}
    .select{border-bottom: 2px dashed red;}

    .pli a.main {
        background-color: #55abed;
    }

    .submenu li:hover {
        color: #55abed;
    }

</style>
<div class="sidebar">
    <ul class="nav-list">
        <li class="pli">
            <a href="/xh/index.php/Home/AdminBacker/index" <?php if(CONTROLLER_NAME == 'AdminBacker'){echo 'class="active"';} ?>><i class="icon-home"></i><strong> 后台管理</strong></a>
        </li>
        <li class="pli">
            <a href="javascript:;" <?php if(CONTROLLER_NAME == 'Star'){echo 'class="active"';} ?>><i class="icon-star"></i><strong> 明星管理</strong></a>
            <ul class="submenu">
                <li><a href="/xh/index.php/Home/Star/carousel">轮播列表</a></li>
                <li><a href="#">资讯列表</a></li>
                <li><a href="#">明星列表</a></li>
                <li><a href="#">约见管理</a></li>
                <li><a href="#">约见类型管理</a></li>
                <li><a href="#">明星时间管理</a></li>
            </ul>
        </li>
        <li class="pli">
            <a href="javascript:;" <?php if(CONTROLLER_NAME == 'Customer'){echo 'class="active"';} ?>><i class="icon-tags"></i><strong> 消费者管理</strong></a>
            <ul class="submenu">
                <li><a href="#">消费者列表</a></li>
                <li><a href="#">分享统计列表</a></li>
            </ul>
        </li>
        <li class="pli">
            <a href="javascript:;"
            <?php if($action == 'dataSearch'){echo 'class="main"';} ?>
            <i class="icon-align-right"></i><strong> 数据查询</strong></a>
            <ul class="submenu">
                <li><a
                    <?php if($actionUrl == 'fundList'){echo 'class="active"';} ?>
                    href="/xh/index.php/Home/DataSearch/fundList">资金查询</a></li>
                <li><a
                    <?php if($actionUrl == 'fundList123'){echo 'class="active"';} ?>
                        href="#">持仓汇总查询</a></li>
                <li><a href="#">出入金查询</a></li>
                <li><a href="#">交易额明细查询</a></li>
                <li><a href="#">成交明细查询</a></li>
            </ul>
        </li>
        <?php if($user['identity_id']<4){ ?>
            <li class="pli">
                <a href="javascript:;"
                    <?php if($action == 'accountManage'){echo 'class="main"';} ?>  >
                    <i class="icon-user"></i>
                    <strong>
                        系统账户管理
                    </strong>
                </a>
                <ul class="submenu">

                    <li><a href="/xh/index.php/Home/accountmanage/userManage"
                        <?php if($actionUrl == 'userManage'){echo 'class="active"';} ?>
                        >账户权限</a>
                    </li>

                    <!--<li><a href="javascript:;"-->
                        <!--<?php if($actionUrl == '#'){echo 'class="active"';} ?>-->
                        <!--&gt;账户角色</a></li>-->
                    <!--
                    <li><a href="#">创建系统账户</a></li>
                    -->

                    <?php if($user['identity_id']<2){ ?>
                        <li><a href="/xh/index.php/Home/accountmanage/orgManage"
                            <?php if($actionUrl == 'orgManage'){echo 'class="active"';} ?>
                        >区域总经销列表</a>
                        </li>
                    <?php } ?>

                    <?php if($user['identity_id']<3){ ?>
                        <li><a href="/xh/index.php/Home/accountmanage/brokerManage"
                            <?php if($actionUrl == 'brokerManage'){echo 'class="active"';} ?>
                        >经销商列表</a>
                        </li>
                    <?php } ?>

                    <li><a href="/xh/index.php/Home/accountmanage/brokerSubManage"
                        <?php if($actionUrl == 'brokerSubManage'){echo 'class="active"';} ?>
                        >零售商列表</a>
                    </li>

                </ul>
            </li>
        <?php } ?>

    </ul>
</div>
<script>
    $(function () {
        $(".pli").each(function () {

            //是否已有选中的菜单
            var isActive   = $(this).children('a').hasClass('active');
            var isMain   = $(this).children('a').hasClass('main');
            var box = $(this).children("ul");

            //添加点击事件
            $(this).children('a').on("click", function () {

              //  alert(box);


                //只留一个选中样式的菜单
                $(".pli a").removeClass('active');
                $(this).children('a').addClass('active');

                //是否已是选中状态 | 取消选中
                var isOpen   = (box).hasClass('open');
                if (box != undefined && isOpen == false) {
                    box.show();
                    box.addClass("open");
                } else {
                    box.hide();
                    box.removeClass("open");
                }
            });

            //默认选中并展开子菜单
            if (isMain) {
                box.show();
                box.addClass("open");
            }

        });
    });
</script>



<!--<li><i class="fa fa-globe"></i>系统账户管理<i class="fa fa-chevron-down"></i></li>-->
<!--<ul>-->
<!--</ul>-->
        <div class="content">

<div class="control-bar"><a href="javascript:;" class="btn J_showAdd">新建</a>
    <a href="javascript:;" class="btn J_updateStatus open-i">启用</a>
    <a href="javascript:;" class="btn J_updateStatus close-i">禁用</a>
    <a href="javascript:;" class="btn J_onDel">删除</a>
</div>

<div class="data-container">
    <table>
        <thead>
        <tr>
            <th> </th>
            <th>登录账号</th>
            <th>用户昵称</th>
            <th>角色类型</th>
            <th>所属机构</th>
            <th>手机号码</th>
            <th>状态</th>
            <th>审核状态</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <div class="pagination"></div>
</div>

<div data-remodal-id="addBrokerModal" class="remodal addBrokerModal">
    <div class="remodal-head">
        <div class="remodal-title">添加经纪人</div>
        <div data-remodal-action="cancel" class="remodal-close"></div>
    </div>
    <div class="remodal-body">
        <form class="modalForm">
            <div class="form-control">
                <label>所属机构</label>
                <select name="org"></select>
            </div>
            <div class="form-control">
                <label>经纪人ID</label>
                <input type="text" name="id">
            </div>
            <div class="form-control">
                <label>经纪人名称</label>
                <input type="text" name="name">
            </div>
            <div class="form-control">
                <label>手机号码</label>
                <input type="text" name="phone">
            </div>
        </form>
    </div>
    <div class="remodal-footer">
        <a href="javascript:;"  class="remodal-confirm">确认</a>
    </div>
</div>
<div data-remodal-id="checkBrokerModal" class="remodal checkBrokerModal">
    <div class="remodal-head">
        <div class="remodal-title">审核经纪人</div>
        <div data-remodal-action="cancel" class="remodal-close"></div>
    </div>
    <div class="remodal-body">
        <form class="modalForm">
            <div class="form-control">
                <label>所属机构</label>
                <input type="text" name="orgName" readonly>
            </div>
            <div class="form-control">
                <label>经纪人ID</label>
                <input type="text" name="id" readonly>
            </div>
            <div class="form-control">
                <label>经纪人名称</label>
                <input type="text" name="name" readonly>
            </div>
            <div class="form-control">
                <label>手机号码</label>
                <input type="text" name="phone" readonly>
            </div>
        </form>
    </div>
    <div class="remodal-footer">
        <a href="javascript:;"  class="remodal-cancel J_check">拒绝</a>
        <a href="javascript:;"  class="remodal-confirm J_check">通过</a>
    </div>
</div>
<script src="/xh/Public/template/assets/js/vendor/require.js" data-main="/xh/Public/template/assets/js/common"></script>
<script>
    require(['common'], function () {
        require(['page/brokerManage']);
    });
</script>
</html>