<?php

namespace Home\Controller;
use Think\Controller;

/**
 * CT - CloudTop类
 * 继承自 TP 的 Controller 用于类扩展 eg:登录验证
 * @date finished at 2017-6-20
 *
 * Class CTController
 * @package Home\Controller
 */
class CTController extends Controller
{
    protected $user;
    protected   $hostUrl;

    public function _initialize(){

        $this->hostUrl = C('qn_domain'); //图片域名

        $sessionName = C('user');

        $user = $this->user = session($sessionName);


        if(!$this->user){
            $this ->redirect('login/login',Null,0);
        }


        $identity_id = $user['identity_id'];

        if($identity_id<2){
            $this->assign('identity_status', 1);
        }
    }
}