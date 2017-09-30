<?php
return array(
	//'配置项'=>'配置值'
    //数据库配置信息


    'DB_TYPE'   => 'mysqli', // 数据库类型
    'DB_HOST'   => 'localhost', // 服务器地址
    'DB_NAME'   => 'star', // 数据库名
    'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => 'root', // 密码


    'DB_PORT'   => 3306, // 端口
    'DB_PARAMS' =>  array(), // 数据库连接参数
    'LOG_RECORD' => true, // 开启日志记录
    'DB_CHARSET'=> 'utf8', // 字符集
    'DB_DEBUG'  =>  TRUE, // 数据库调试模式 开启后可以记录SQL日志
    'DEFAULT_CONTROLLER' => 'AdminBacker', // 默认控制器名称
    'DEFAULT_ACTION' => 'index', // 默认操作名称

    'TMPL_PARSE_STRING'=>array(
      '__CSS__' => __ROOT__.'/Public/template/assets/css',
      '__JS__' => __ROOT__.'/Public/template/assets/js',
      '__IMG__' => __ROOT__.'/Public/template/assets/img',
      '__FRONTS__' => __ROOT__.'/Public/template/assets/front',
      '__ROOTHOME__'=>__ROOT__.'/index.php/Home',
      '__PUSHSDk__'=>__ROOT__.'/PushSdk',
    //  '__DOMAIN__'=>"http://localhost/tp-hangkong-jiandan/index.php/Home/"
    ),
    'TMPL_CACHE_ON' => false,//禁止模板编译缓存
    'HTML_CACHE_ON' => false,//禁止静态缓存


    'CGI_STAR_URl' => 'http://122.144.169.214/cgi-bin/star/quotations/v1/refreshstar.fcgi',

    //七牛云
    'qn_domain' => 'http://ouim6qew1.bkt.clouddn.com/',
    'qn_ak' => '4jvwuLa_Xcux7WQ40KMO89DfinEuI3zXizMpwnc7',
    'qn_sk' => '8tSk8O9VS0vl9zh8jUV1mkR1GijH2KyXMLbVel_T',

    //提现配置
    'post_url'      => 'https://back.byepay.cn:30006/payment/gateway/100305100100/',  //请求地址
    'key'           => '8470D3D03BA7A19B9E0FD63EC96591E3',
    'withdrawals_data' => array(
        'subMerNo'      => '10000740', // 商户编号
        'acctNo'        =>  '698548828', // 银行卡号
        'notifyUrl'     => 'http://'.$_SERVER['HTTP_HOST'].'/index.php/Home/Profit/notifyUrl', // 异步请求地址
        'isCompay'      => '1', // 对公对私标识0为对私，1为对公
        'customerName'  => '民生银行羊城支行', //代付账户名称
    ),
    // user session name save
    'user'      => 'user',
    
);
