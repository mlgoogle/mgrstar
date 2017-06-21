<?php
return array(
	//'配置项'=>'配置值'
  //数据库配置信息
    'DB_TYPE'   => 'mysqli', // 数据库类型
    'DB_HOST'   => '139.224.34.22', // 服务器地址
    'DB_NAME'   => 'star', // 数据库名
    'DB_USER'   => 'star', // 用户名
    'DB_PWD'    => 'pEQrhRrG', // 密码
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
    //  '__DOMAIN__'=>"http://localhost/tp-hangkong-jiandan/index.php/Home/"
    ),
    'TMPL_CACHE_ON' => false,//禁止模板编译缓存
    'HTML_CACHE_ON' => false,//禁止静态缓存

);
