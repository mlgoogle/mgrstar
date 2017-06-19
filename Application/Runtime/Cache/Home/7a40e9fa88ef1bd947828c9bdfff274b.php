<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <title><?php echo (!empty($title)) ? $title : '星享后台' ?></title>
    <link rel="stylesheet" href="/htdocs/Public/template/assets/css/index.min.css">
    <!--<link rel="stylesheet" href="/htdocs/Public/template/assets/css/bootstrap/css/bootstrap.css">-->

    <script src="/htdocs/Public/template/assets/js/vendor/jquery.min.js" data-main="/htdocs/Public/template/assets/js/common"></script>
    <script>
        var publicUrl = "/htdocs/Public";
        var rootUrl = "/htdocs/index.php/Home";
    </script>
</head>
<body>
<div class="wrap">
    <div class="header">
        <div class="clearfix layout">
            <h1><a href="/htdocs/index.php/Home/AdminBacker/index">星享管理系统</a></h1>
            <div>
                <span class="spantext" >管理员：<?php echo ($user['uid']); ?>,<a style="color:#FF0000;" href="/htdocs/index.php/Home/login/doLoginout" onclick="return confirm('确定退出本系统?')" >系统退出</a></span>
            </div>
        </div>
    </div>
    <div class="main">
        <style>
    .submenu{display: none;}
    .submenu li{color: #CCCCCC;padding-left: 20px;width: 65%;}
    .select{border-bottom: 2px dashed red;}
    .icon-star-img {
        width: 120px;
    }
</style>
<?php
 $starArr = array('Star', 'Info', 'Lucida', 'Appoint', 'Timer', 'Meet'); $cusArr = array( 'Customer'); ?>
<div class="sidebar">
    <ul class="nav-list">
        <li class="pli">
            <a href="/htdocs/index.php/Home/AdminBacker/index" <?php if(CONTROLLER_NAME == 'AdminBacker'){echo 'class="active"';} ?>><i class="icon-home"></i><strong> 后台管理</strong></a>
        </li>
        <li class="pli">
            <a href="javascript:;" <?php if(in_array(CONTROLLER_NAME, $starArr)){echo 'class="active"';} ?>><i class="icon-star"></i><strong> 明星管理</strong></a>
            <ul class="submenu">
                <li><a href="/htdocs/index.php/Home/Star/carousel">轮播列表</a></li>
                <li><a href="/htdocs/index.php/Home/Info/listing">资讯列表</a></li>
                <li><a href="/htdocs/index.php/Home/Lucida/listing">明星列表</a></li>
                <li><a href="/htdocs/index.php/Home/Meet/meet">约见管理</a></li>
                <li><a href="/htdocs/index.php/Home/Appoint/appoint">约见类型管理</a></li>
                <li><a href="/htdocs/index.php/Home/Timer/listing">明星时间管理</a></li>
            </ul>
        </li>
        <li class="pli">
            <a href="javascript:;" <?php if(in_array(CONTROLLER_NAME, $cusArr)){echo 'class="active"';} ?>><i class="icon-tags"></i><strong> 消费者管理</strong></a>
            <ul class="submenu">
                <li><a href="/htdocs/index.php/Home/Customer/listing">消费者列表</a></li>
                <li><a href="#">分享统计列表</a></li>
            </ul>
        </li>
        <li class="pli">
            <a href="javascript:;"><i class="icon-align-right"></i><strong> 数据查询</strong></a>
            <ul class="submenu">
                <li><a href="#">资金查询</a></li>
                <li><a href="#">持仓汇总查询</a></li>
                <li><a href="#">出入金查询</a></li>
                <li><a href="#">交易额明细查询</a></li>
                <li><a href="#">成交明细查询</a></li>
            </ul>
        </li>
        <li class="pli">
            <a href="javascript:;"><i class="icon-user"></i><strong> 系统账户管理</strong></a>
            <ul class="submenu">
                <li><a href="#">账户权限</a></li>
                <li><a href="#">账户角色</a></li>
                <li><a href="#">创建系统账户</a></li>
                <li><i class="fa fa-globe"></i>系统账户管理<i class="fa fa-chevron-down"></i></li>
                <ul>
                    <li><a href="#">区域总经销列表</a></li>
                    <li><a href="#">经销商列表</a></li>
                    <li><a href="#">零售商列表</a></li>
                </ul>
            </ul>
        </li>
    </ul>
</div>
<script>
    $(function () {
        $(".pli").each(function () {

            //是否已有选中的菜单
            var isActive   = $(this).children('a').hasClass('active');
            var box = $(this).children("ul");

            //添加点击事件
            $(this).children("a").bind("click", function () {

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
            if (isActive) {
                box.show();
                box.addClass("open");
            }

        });
    });
</script>
        <div class="content">

<style>
    .form-control label {
        width: 110px;
        display: inline-block;
        text-align: right;
        padding: 10px 10px;
    }
    .mybtn{
        display: inline-block;
        width: 40px;
        border:1px solid #CCCCCC;
        border-radius: 4px;
        height: 30px;
        line-height: 30px;
        cursor: pointer;
    }
    .exp {
        margin:15px 0px;
        line-height: 25px;
    }
    .timer-list {
        width:60%;
        margin-top: 25px;
        border-top: 1px solid #ccc;
        padding: 10px;
    }
   .exp li{
        list-style-type:none;
        border-bottom: 1px solid #ccc;
        margin: 10px 0px 15px 0px;
    }
     .close_exp {
        color: red;
        font-weight: normal;
        display: inline;
        cursor:pointer;
        font-size: 12px;
        float: right;
        width: 30px;
    }
    .add-free{
        border:1px solid #ccc;
        margin-bottom: 5px;
        font-weight: normal;
        display: inline-block;
        cursor:pointer;
        font-size: 12px;
        float: right;
        border-radius: 2px;
        width: 60px;
        text-align: center;
    }
    .info-secs {
        display: inline-block;
        float: right;
        margin-right: 50%;
    }
    .info-notice {display: inline-block; color: red;}
</style>

<div class="control-bar">
    <h3><a href="/htdocs/index.php/Home/Timer/listing">返回</a>   明星时间管理</h3>
</div>

<div class="data-container">
    <div style="margin:auto;width: 50%;padding: 25px;">
        <div class="remodal-body">
            <form class="modalForm" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $item['id'];?>">
                <div class="form-control">
                    <label>明星名称</label>
                    <?php echo $item['starname'];?>
                </div>
                <div class="form-control control-label">
                    <label>明星状态</label>
                    <?php echo $item['status'];?>
                </div>
                <div class="form-control">
                    <label>总发行时间</label>
                    <input type="text" name="micro" value="<?php echo $item['micro'];?>">
                    <span class="info-notice"> <?php echo ($item['micro'] < 1) ? '!!!填写发行时间即可分配消费者持有' : ''; ?></span>
                    <a href="javascript:;" class="remodal-confirm btn">确认</a>
                </div>

                <?php if (count($item) > 0 && $item['micro'] > 0) { ?>

                    <div class="timer-list">
                        <h3 style="text-align: center">消费者列表</h3><br>
                        <div class="form-control">
                            <input type="text" name="fans" placeholder="昵称/UID" />
                            <input type="text" name="secs" placeholder="秒数" />
                            <a href="javascript:;" onclick="addExp(this)" class="btn add-fans">添加</a>
                            <span class="info-notice fans-notice"></span>

                            <div class="exp addexp">
                                <li><span class="secs-total">消费总数 : <?php echo count($timer); ?></span>
                                    <b href="javascript:;" class="add-free">平均分配</b>
                                    <span class="info-secs secs-free" style="margin-right: 34%;">可用值 : <?php echo $free;?></span>
                                </li>
                                <?php if ($free > 0) { ?>
                                    <textarea name="free-timer" style="width: 450px;height: 100px;" placeholder="请用英文,逗号分隔"/></textarea>
                                <?php } ?>
                                <br>
                                <?php foreach($timer as $t) { $userMark = (!empty($t['nickname'])) ? $t['nickname'] : $t['fansid']; echo '<li> ' . $userMark . '<b class="close_exp" data-id='.$t['id'].' onclick="close_exp(this)">X</b><span class="info-secs">' .$t['secs'] .'</span> </li>'; }?>
                            </div>
                        </div>
                    </div>
                 <?php } ?>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">

    //添加经历、成就
    function addExp(obj) {
        var _this = $(obj);
        var id = parseInt($("input[name='id']").val());
        if (id == 0) return false;

        var fans = $.trim($("input[name='fans']").val());
        var secs = $.trim($("input[name='secs']").val());
        secs = parseInt(secs);

        if (fans.length < 1 || secs < 1) {
            $(".fans-notice").text("请输入正确的值")
            return false;
        } else {
            $(".fans-notice").text("")
        }

        $.ajax({
            type: "POST",
            url: "/htdocs/index.php/Home/Timer/add",
            dataType: "json",
            data: {fans : fans, secs : secs, id : id},
            success: function(msg){
                if (msg.code == 1) {
                    $(".secs-total").text("消费总数 : " + msg.total);
                    $(".secs-free").text("可用值 : " + msg.free);
                    $(".addexp").append("<li> " + msg.fans + "<b class='close_exp'>&nbsp;</b><span class='info-secs'>" + msg.secs + "</span></li>");
                } else if (msg.code == -2) {
                    $(".fans-notice").text(msg.message);
                } else {
                    $(".fans-notice").text("");
                }
            }
        });
    }

    //删除经历、成就
    function close_exp(obj) {
        var _this = $(obj);
        var id = parseInt(_this.attr("data-id"));
        if (id < 1) {return false;}

        $.ajax({
            type: "POST",
            url: "/htdocs/index.php/Home/Timer/fstatus",
            dataType: "json",
            data: {id : id, free : "<?php echo $free;?>", total : "<?php echo count($timer); ?>"},
            success: function(msg){
                $(".secs-total").text("消费总数 : " + msg.total);
                $(".secs-free").text("可用值 : " + msg.free);
                _this.parent().remove();
            }
        });
    }

    $(function ()
    {
        $(".add-free").on("click", function () {
            var id = parseInt($("input[name='id']").val());
            if (id == 0) return false;

            var uids = $.trim($("textarea[name='free-timer']").val());
            if (uids.length == 0) return false;

            $.ajax({
                type: "POST",
                url: "/htdocs/index.php/Home/Timer/avg",
                dataType: "json",
                data: {id : id, uids : uids},
                success: function(msg){
                    if (msg.code == -2) {
                        $(".fans-notice").text(msg.message)
                    } else if (msg.code == 1) {
                        window.location.reload(true);
                    } else {
                        $(".fans-notice").text("")
                    }
                }
            });
        });

        $("input[name='fans']").on("blur", function () {
            var fans = $.trim($(this).val());
            if (fans.length < 1) return false;

            $.ajax({
                type: "POST",
                url: "/htdocs/index.php/Home/Timer/getFans",
                dataType: "json",
                data: {fans : fans},
                success: function(msg){
                    if (msg.code == -2) {
                        $(".fans-notice").text(msg.message)
                    } else if (msg.code == 0) {
                        $(".fans-notice").text("")
                    }
                }
            });
        })

        //添加、修改明星信息
        $(".remodal-confirm").click(function () {
            var id = $.trim($("input[name='id']").val());
            var micro = $.trim($("input[name='micro']").val());
            micro = parseInt(micro);

            if (parseInt(micro) < 1) {
                return false;
            }
            var url = "/htdocs/index.php/Home/Timer/addTimer";

            if (id.length > 0) {
                var url = "/htdocs/index.php/Home/Timer/editTimer";
            }

            $.ajax({
                type: "POST",
                url: url,
                dataType: "json",
                data: {id : id, micro : micro},
                success: function(msg){
                    alert(msg.message);
                    if (msg.code != -2) {
                        window.location.href = "/htdocs/index.php/Home/Timer/info/id/" + msg.id;
                    }
                }
            });
        });
    });
</script>


                </div>
            </div>
        </div>
    </bod>
</html>