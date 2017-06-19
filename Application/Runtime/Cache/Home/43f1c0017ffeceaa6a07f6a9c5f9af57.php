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
<link rel="stylesheet" href="/htdocs/Public/template/assets/css/modals/jquery.datetimepicker.min.css">
<script src="/htdocs/Public/template/assets/js/vendor/jquery.datetimepicker.js" data-main="/htdocs/Public/template/assets/js/common"></script>

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
    .icon-star-img {
        width: 120px;
    }
    .exp {
        width: 60%;
        margin:10px 14.6%;
        line-height: 25px;
    }
    li{ list-style-type:none; }
    .close_exp {
        color: red;
        font-weight: normal;
        display: inline;
        cursor:pointer;
        font-size: 12px;
        float: right;
        width: 30px;
    }
    .lucida-div {

    }
    .lucida-div img {
        height: 240px;
        margin: 10px;
    }
</style>

<div class="control-bar">
    <h3><a href="/htdocs/index.php/Home/Lucida/listing">返回</a>   明星信息</h3>
</div>

<div class="data-container">
    <div style="margin:auto;width: 50%;padding: 25px;">
        <div class="remodal-body">
            <form class="modalForm" enctype="multipart/form-data">
                <input type="hidden" name="uid" value="<?php echo $item['uid'];?>">
                <div class="form-control">
                    <label>明星名称</label>
                    <input type="text" name="name" value="<?php echo $item['name'];?>">
                </div>
                <div class="form-control">
                    <label>明星ID</label>
                    <input type="text" name="code" value="<?php echo $item['code'];?>">
                </div>
                <div class="form-control control-label">
                    <label>国 籍</label>
                    <input type="text" name="nationality" value="<?php echo $item['nationality'];?>" />
                </div>

                <div class="form-control">
                    <label>出生年月</label>
                    <input type="text" name="birth" id="datepicker" value="<?php echo $item['birth'];?>">
                </div>
                <div class="form-control">
                    <label>职 业</label>
                    <input type="text" name="work" value="<?php echo $item['work'];?>">
                </div>

                <div class="form-control">
                    <label>毕业院校</label>
                    <input type="text" name="colleage" value="<?php echo $item['colleage'];?>">
                </div>
                <?php if (count($item) > 0) { ?>
                        <div class="form-control">
                            <label>主要经历</label>
                            <input type="text" name="exp" /> <a href="javascript:;" onclick="addExp(this, 'exp')" class="btn">添加经历</a>
                            <div class="exp addexp">
                                <?php foreach($exp as $e) { echo '<li>' . $e['star_experience'] . ' <b class="close_exp" data-id='.$e['id'].' onclick="close_exp(this)">X</b></li>'; }?>
                            </div>
                        </div>

                        <div class="form-control">
                            <label>主要成就</label>
                            <input type="text" name="ach" /> <a href="javascript:;" onclick="addExp(this, 'ach')" class="btn">添加成就</a>
                            <div class="exp addach">
                                <?php foreach($ach as $a) { echo '<li>' . $a['star_experience'] . ' <b class="close_exp" data-id='.$e['id'].' onclick="close_exp(this)">X</b></li>'; }?>
                            </div>
                        </div>
                 <?php } ?>
                <div class="form-control">
                    <label>常驻地</label>
                    <input type="text" name="resident" value="<?php echo $item['resident'];?>">
                </div>
                <div class="form-control">
                    <label>时间使用范围</label>
                    <select name="appoint_id">
                        <option value="0">-请选择-</option>
                        <?php foreach ($appoints as $val) {?>
                        <option value="<?php echo $val['mid'];?>" <?php if ($val['mid'] == $item['appoint_id']){echo "selected=selected";}?>><?php echo $val['name'];?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-control">
                    <label>身价说明</label>
                    <input type="text" name="worth" value="<?php echo $item['worth'];?>">
                </div>
                <div class="form-control">
                    <label>微博指数</label>
                    <input type="text" name="weibo" value="<?php echo $item['weibo'];?>">
                </div>
                <br>
                <div class="form-control control-label form-group">
                    <label class="control-label"></label>
                    <input name='file' type="file" multiple id="file" size="4"/>
                    <span onclick="UpladFile()" class="mybtn">上传</span>
                </div>
                <div class="lucida-div">
                    <?php foreach ($pics as $key => $pic) { echo '<span><img src="/htdocs/Public/uploads/lucida/'.$pic.'" alt=""></span>'; } ?>

                </div>
                <br>
            </form>

            <div class="form-control control-label form-group">
                <label class="control-label"></label>
                <a href="javascript:;"  class="remodal-confirm">确认</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">

    //添加经历、成就
    function addExp(obj, key) {
        var _this = $(obj);

        var id = $.trim($("input[name='code']").val());
        if (id.length == 0) return false;

        var val = $.trim($('input[name="'+key+'"]').val());
        if (val.length == 0) return false;

        $(".add"+key).append(val +"<br>");
        $('input[name="'+key+'"]').val("")

        var url = "/htdocs/index.php/Home/Lucida/addExp";
        $.ajax({
            type: "POST",
            url: url,
            dataType: "json",
            data: {key : key, val : val, uid : id},
            success: function(msg){
                console.log(msg);
            }
        });
    }

    //删除经历、成就
    function close_exp(obj) {
        var _this = $(obj);
        var id = _this.attr("data-id");

        $.ajax({
            type: "POST",
            url: "/htdocs/index.php/Home/Lucida/exp",
            dataType: "json",
            data: {id : id},
            success: function(msg){
                _this.parent().remove();
            }
        });
    }

    $(function () {
        //日历插件
        $("#datepicker").datetimepicker({ format: 'Y-m-d', timepicker: false });

        //添加、修改明星信息
        $(".remodal-confirm").click(function () {
            var id = $.trim($("input[name='uid']").val());
            var url = "/htdocs/index.php/Home/Lucida/addLucida";

            if (id.length > 0) {
                var url = "/htdocs/index.php/Home/Lucida/editLucida";
            }

            $.ajax({
                type: "POST",
                url: url,
                dataType: "json",
                data: $(".modalForm").serialize(),
                success: function(msg){
                    alert(msg.message);
                    if (msg.code != -2) {
                        window.location.href = "/htdocs/index.php/Home/Lucida/info/id/" + msg.id;
                    }
                }
            });
        });
    });

    var xhr;
    function createXMLHttpRequest() {
        if (window.ActiveXObject) {
            xhr = new ActiveXObject("Microsoft.XMLHTTP");
        } else if (window.XMLHttpRequest) {
            xhr = new XMLHttpRequest();
        }
    }

    function UpladFile() {
        var fileObj = document.getElementById("file").files;
        var FileController = '/htdocs/index.php/Home/Lucida/uploadFile';

        var len = fileObj.length;
        var form = new FormData();
        for (var i = 0; i < len; i++) {
            form.append("myfile[]", fileObj[i]);
        }

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
                var len = json.length;
                var existImgLen = parseInt($(".lucida-div img").length);

                for (var i = 0; i < len; i++) {
                    var i = parseInt(i);
                    var num = i + existImgLen + 1;
                    if (num > 5) {
                        num = (i < 1) ? 1 : i;
                    }
                    var name = "pic" + num;
                    var inp = "<input type='hidden' name='"+name+"' value='"+ json[i] +"' />";
                    $(".modalForm").append(inp);

                    var img = '<span><img src="/htdocs/Public/uploads/lucida/'+json[i]+'"></span>';
                    $(".lucida-div").append(img);
                }
            }
        }
    }
</script>


                </div>
            </div>
        </div>
    </bod>
</html>