<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <title><?php echo (!empty($title)) ? $title : '星享后台' ?></title>
    <link rel="stylesheet" href="/Public/template/assets/css/index.min.css">
    <!--<link rel="stylesheet" href="/Public/template/assets/css/bootstrap/css/bootstrap.css">-->

    <script src="/Public/template/assets/js/vendor/jquery.min.js" data-main="/Public/template/assets/js/common"></script>
    <script>
        var publicUrl = "/Public";
        var rootUrl = "/index.php/Home";
    </script>
</head>
<body>
<div class="wrap">
    <div class="header">
        <div class="clearfix layout">
            <h1><a href="/index.php/Home/AdminBacker/index">星享管理系统</a></h1>
            <div>
            <span class="spantext" >管理员：<?php echo ($user['uname']); ?>,<a style="color:#FF0000;" href="/index.php/Home/login/doLoginout" onclick="return confirm('确定退出本系统?')" >系统退出</a></span>

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
            <a href="/index.php/Home/AdminBacker/index" <?php if(CONTROLLER_NAME == 'AdminBacker'){echo 'class="active"';} ?>><i class="icon-home"></i><strong> 后台管理</strong></a>
        </li>
        <li class="pli">
            <a href="javascript:;" <?php if(in_array(CONTROLLER_NAME, $starArr)){echo 'class="active"';} ?>><i class="icon-star"></i><strong> 明星管理</strong></a>
            <ul class="submenu">
                <li><a href="/index.php/Home/Star/carousel" <?php if(CONTROLLER_NAME == 'Star' && ACTION_NAME =='carousel'){echo 'class="act"';} ?>>轮播列表</a></li>
                <li><a href="/index.php/Home/Info/listing" <?php if(CONTROLLER_NAME == 'Info' && ACTION_NAME =='listing'){echo 'class="act"';} ?>>资讯列表</a></li>
                <li><a href="/index.php/Home/Lucida/listing" <?php if(CONTROLLER_NAME == 'Lucida' && ACTION_NAME =='listing'){echo 'class="act"';} ?>>明星列表</a></li>
                <li><a href="/index.php/Home/Meet/meet" <?php if(CONTROLLER_NAME == 'Meet' && ACTION_NAME =='meet'){echo 'class="act"';} ?>>约见管理</a></li>
                <li><a href="/index.php/Home/Appoint/appoint" <?php if(CONTROLLER_NAME == 'Appoint' && ACTION_NAME =='appoint'){echo 'class="act"';} ?>>约见类型管理</a></li>
                <li><a href="/index.php/Home/Timer/listing" <?php if(CONTROLLER_NAME == 'Timer' && ACTION_NAME =='listing'){echo 'class="act"';} ?>>明星时间管理</a></li>
            </ul>
        </li>
        <li class="pli">
            <a href="javascript:;" <?php if(in_array(CONTROLLER_NAME, $cusArr)){echo 'class="active"';} ?>><i class="icon-tags"></i><strong> 消费者管理</strong></a>
            <ul class="submenu">
                <li><a href="/index.php/Home/Customer/customer" <?php if(CONTROLLER_NAME == 'Customer' && ACTION_NAME =='customer'){echo 'class="act"';} ?>>消费者列表</a></li>
                <!--<li><a href="#">分享统计列表</a></li>-->
            </ul>
        </li>
        <li class="pli">
            <a href="javascript:;"
            <?php if($action == 'dataSearch'){echo 'class="main"';} ?>
            <i class="icon-align-right"></i><strong> 数据查询</strong></a>
            <ul class="submenu">
                <li><a
                    <?php if($actionUrl == 'fundList'){echo 'class="active"';} ?>
                    href="/index.php/Home/DataSearch/fundList">资金查询</a></li>
                <li><a
                    <?php if($actionUrl == 'position'){echo 'class="active"';} ?>
                        href="/index.php/Home/DataSearch/position">持仓汇总查询</a></li>
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

                    <li><a href="/index.php/Home/accountmanage/userManage"
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
                        <li><a href="/index.php/Home/accountmanage/orgManage"
                            <?php if($actionUrl == 'orgManage'){echo 'class="active"';} ?>
                        >区域总经销列表</a>
                        </li>
                    <?php } ?>

                    <?php if($user['identity_id']<3){ ?>
                        <li><a href="/index.php/Home/accountmanage/brokerManage"
                            <?php if($actionUrl == 'brokerManage'){echo 'class="active"';} ?>
                        >经销商列表</a>
                        </li>
                    <?php } ?>

                    <li><a href="/index.php/Home/accountmanage/brokerSubManage"
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
                        <th>序号</th>
                        <th>结算日</th>
                        <th>消费者编码</th>
                        <th>消费者名称</th>
                        <th>收入</th>
                        <th>支出</th>

                        <th>期初资金</th>
                        <th>期末资金</th>
                        <th>冻结资金</th>
                        <th>可用资金</th>
                        <th>浮动盈亏</th>
                        <th>资产总值</th>

                    </tr>
                    </thead>
                    <tbody>

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
<script src="/Public/template/assets/js/vendor/require.js" data-main="/Public/template/assets/js/common"></script>
<script>
    require(['common'], function () {
        require(['page/fundList']);
    });
</script>
</body>



                </div>
            </div>
        </div>
    </bod>
</html>