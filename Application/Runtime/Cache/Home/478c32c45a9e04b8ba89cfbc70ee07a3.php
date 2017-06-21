<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <title><?php echo (!empty($title)) ? $title : '星享后台' ?></title>
    <link rel="stylesheet" href="/work/star/mgrstar/Public/template/assets/css/index.min.css">
    <!--<link rel="stylesheet" href="/work/star/mgrstar/Public/template/assets/css/bootstrap/css/bootstrap.css">-->

    <script src="/work/star/mgrstar/Public/template/assets/js/vendor/jquery.min.js" data-main="/work/star/mgrstar/Public/template/assets/js/common"></script>
    <script>
        var publicUrl = "/work/star/mgrstar/Public";
        var rootUrl = "/work/star/mgrstar/index.php/Home";
    </script>
</head>
<body>
<div class="wrap">
    <div class="header">
        <div class="clearfix layout">
            <h1><a href="/work/star/mgrstar/index.php/Home/AdminBacker/index">星享管理系统</a></h1>
            <div>
            <span class="spantext" >管理员：<?php echo session('user')['uname'];?>,<a style="color:#FF0000;" href="/work/star/mgrstar/index.php/Home/login/doLoginout" onclick="return confirm('确定退出本系统?')" >系统退出</a></span>

            </div>
        </div>
    </div>
    <div class="main">
        <style>
    .submenu{display: none;}
    .submenu li{color: #CCCCCC;}
    .act{background-color: #FFB6C1}
    .select{border-bottom: 2px dashed red;}
    .icon-star-img {
        width: 120px;
    }
 .pli a.main {
        background-color: #55abed;
    }

    .submenu li:hover {
        color: #55abed;
    }
*{font-size: 14px;}
</style>
<?php
 $starArr = array('Star', 'Info', 'Lucida', 'Meet', 'Appoint', 'Timer'); $cusArr = array( 'Customer'); $starCtr = array('carousel', 'listing', 'meet', 'appoint'); ?>
<div class="sidebar">
    <ul class="nav-list">
        <li class="pli">
            <a href="/work/star/mgrstar/index.php/Home/AdminBacker/index" <?php if(CONTROLLER_NAME == 'AdminBacker'){echo 'class="active"';} ?>><i class="icon-home"></i><strong> 后台管理</strong></a>
        </li>
        <li class="pli">
            <a href="javascript:;" <?php if(in_array(CONTROLLER_NAME, $starArr)){echo 'class="active"';} ?>><i class="icon-star"></i><strong> 明星管理</strong></a>
            <ul class="submenu">
                <li><a href="/work/star/mgrstar/index.php/Home/Star/carousel" <?php if(CONTROLLER_NAME == 'Star' && ACTION_NAME =='carousel'){echo 'class="act"';} ?>>轮播列表</a></li>
                <li><a href="/work/star/mgrstar/index.php/Home/Info/listing" <?php if(CONTROLLER_NAME == 'Info' && ACTION_NAME =='listing'){echo 'class="act"';} ?>>资讯列表</a></li>
                <li><a href="/work/star/mgrstar/index.php/Home/Lucida/listing" <?php if(CONTROLLER_NAME == 'Lucida' && ACTION_NAME =='listing'){echo 'class="act"';} ?>>明星列表</a></li>
                <li><a href="/work/star/mgrstar/index.php/Home/Meet/meet" <?php if(CONTROLLER_NAME == 'Meet' && ACTION_NAME =='meet'){echo 'class="act"';} ?>>约见管理</a></li>
                <li><a href="/work/star/mgrstar/index.php/Home/Appoint/appoint" <?php if(CONTROLLER_NAME == 'Appoint' && ACTION_NAME =='appoint'){echo 'class="act"';} ?>>约见类型管理</a></li>
                <li><a href="/work/star/mgrstar/index.php/Home/Timer/listing" <?php if(CONTROLLER_NAME == 'Timer' && ACTION_NAME =='listing'){echo 'class="act"';} ?>>明星时间管理</a></li>
            </ul>
        </li>
        <li class="pli">
            <a href="javascript:;" <?php if(in_array(CONTROLLER_NAME, $cusArr)){echo 'class="active"';} ?>><i class="icon-tags"></i><strong> 消费者管理</strong></a>
            <ul class="submenu">
                <li><a href="/work/star/mgrstar/index.php/Home/Customer/customer" <?php if(CONTROLLER_NAME == 'Customer' && ACTION_NAME =='customer'){echo 'class="act"';} ?>>消费者列表</a></li>
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
                    href="/work/star/mgrstar/index.php/Home/DataSearch/fundList">资金查询</a></li>
                <li><a
                    <?php if($actionUrl == 'position'){echo 'class="active"';} ?>
                        href="/work/star/mgrstar/index.php/Home/DataSearch/position">持仓汇总查询</a></li>
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

                    <li><a href="/work/star/mgrstar/index.php/Home/accountmanage/userManage"
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
                        <li><a href="/work/star/mgrstar/index.php/Home/accountmanage/orgManage"
                            <?php if($actionUrl == 'orgManage'){echo 'class="active"';} ?>
                        >区域总经销列表</a>
                        </li>
                    <?php } ?>

                    <?php if($user['identity_id']<3){ ?>
                        <li><a href="/work/star/mgrstar/index.php/Home/accountmanage/brokerManage"
                            <?php if($actionUrl == 'brokerManage'){echo 'class="active"';} ?>
                        >经销商列表</a>
                        </li>
                    <?php } ?>

                    <li><a href="/work/star/mgrstar/index.php/Home/accountmanage/brokerSubManage"
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
            $(this).children("a").bind("click", function () {

              //  alert(box);


                //只留一个选中样式的菜单
                $(".pli a").removeClass('active');
                $(".pli ul").each(function () {
                    $(this).hide();
                    $(this).removeClass("open");
                })
                $(this).addClass('active');

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
            if (isActive || isMain) {
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

<div class="control-bar">
    <?php if ($count < 5) { ?>
    <a href="javascript:;" class="btn J_showAdd">新建</a>
    <?php } ?>
    <a href="javascript:;" class="btn J_onDel">删除</a>
</div>

<div class="data-container">
    <table>
        <thead>
        <tr>
            <th>#</th>
            <th>明星ID</th>
            <th>明星名称</th>
            <th>轮播顺序</th>
            <th>轮播图</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <div class="pagination"></div>
</div>

<div data-remodal-id="addCarouselModal" class="remodal addCarouselModal">
    <div class="remodal-head">
        <div class="remodal-title">明星轮播图</div>
        <div data-remodal-action="cancel" class="remodal-close"></div>
    </div>
    <div class="remodal-body">
        <form class="modalForm" enctype="multipart/form-data">
            <input type="text" name="id" style="display: none">
            <div class="form-control">
                <label>明星名称</label>
                <input type="text" name="starname">
            </div>
            <div class="form-control control-label">
                <label>轮播图片</label>
                <input type="text" name="pic_url" id="pic_url" class="txt" readonly />
            </div>
                <style>
                    .mybtn{
                        display: inline-block;
                        width: 40px;
                        border:1px solid #CCCCCC;
                        border-radius: 4px;
                        height: 30px;
                        line-height: 30px;
                        cursor: pointer;
                    }
                    .icon-star-img {
                        width: 120px;
                    }
                </style>
            <div class="form-control control-label form-group">
                <label class="control-label"></label>
                <input type="file" name="file" class="file" id="file"/>
                <span onclick="UpladFile()" class="mybtn">上传</span>
            </div>

            <div class="form-control">
                <label>明星ID</label>
                <input type="text" name="starcode" value="" readonly>
            </div>
            <div class="form-control">
                <label>轮播顺序</label>
                <input type="text" name="sort">
            </div>
        </form>
    </div>
    <div class="remodal-footer">
        <a href="javascript:;"  class="remodal-confirm">确认</a>
    </div>
</div>

<div id="browse" class="browse">
</div>

<script src="/work/star/mgrstar/Public/template/assets/js/vendor/require.js" data-main="/work/star/mgrstar/Public/template/assets/js/common"></script>

<script type="text/javascript">
    var xhr;
    function createXMLHttpRequest() {
        if (window.ActiveXObject) {
            xhr = new ActiveXObject("Microsoft.XMLHTTP");
        } else if (window.XMLHttpRequest) {
            xhr = new XMLHttpRequest();
        }
    }

    function UpladFile() {
        var fileObj = document.getElementById("file").files[0];
        var FileController = '/work/star/mgrstar/index.php/Home/Star/uploadFile';
        var form = new FormData();
        form.append("myfile", fileObj);
        createXMLHttpRequest();
        xhr.onreadystatechange = handleStateChange;
        xhr.open("post", FileController, true);
        xhr.send(form);
    }

    function handleStateChange() {
        if (xhr.readyState == 4) {
            if (xhr.status == 200 || xhr.status == 0) {
                var result = xhr.responseText;
                var json = eval("(" + result + ")");
                console.log(json.file);

                $("#pic_url").val(json.file);
            }
        }
    }
</script>

<script>
    require(['common'], function () {
        require(['page/star']);
    });
</script>



                </div>
            </div>
        </div>
    </bod>
</html>