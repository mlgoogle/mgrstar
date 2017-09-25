<?php
namespace Home\Controller;

use Think\Controller;

class OperateController extends CTController{

    public function __construct(){

        parent::__construct();

    }

    public function push(){
        $this->errorAddress();//权限

        $userInfo = $this->userinfoList();

        $this->assign('userInfo', $userInfo);
        $this->assign('title', '消息推送');
        $this->display('operate/push');
    }

    public function statistics(){

        $this->errorAddress();//权限

        $this->assign('title', '友盟统计');
        $this->display('operate/statistics');

    }

    public function userinfoList(){
        $starUserInfoModel = M('star_userinfo');

        $map['starcode'] = array('exp',' is not null');
        $data = $starUserInfoModel->where($map)->select();


        return $data;
    }




}


