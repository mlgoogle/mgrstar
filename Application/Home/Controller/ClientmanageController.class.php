<?php
namespace Home\Controller;
use Think\Controller;
//use Think\Controller\restController;
class ClientmanageController extends Controller {
    /*
    *
    *
    */
    public function __construct(){
      # code...
      parent::__construct();
      $sessionName = C('user');

      if(!session($sessionName)){

        $this ->redirect('login/login',Null,0);
      }

    }

    public function clientList(){
        $this->display('clientManage/clientList');
    }

    public function chiCangSearch(){
        $this->display('clientManage/chiCangSearch');
    }
    public function pingCangSearch(){
        $this->display('clientManage/pingCangSearch');
    }
    public function chuJinSearch(){
        $this->display('clientManage/chuJinSearch');
    }
    public function ruJinSearch(){
        $this->display('clientManage/ruJinSearch');
    }
    
    public function buyLog(){
        $uid= $_GET['uid'];
        $user_info = M('user_info');
        $map['uid'] = $uid;
        $userInfo = $user_info->where($map)->find();
        $this->assign('userInfo',$userInfo);
        $this->display('clientManage/clientListView/buyLog');
    }
    public function inlog(){
        $uid= $_GET['uid'];
        $user_info = M('user_info');
        $map['uid'] = $uid;
        $userInfo = $user_info->where($map)->find();
        $this->assign('userInfo',$userInfo);
        $this->display('clientManage/clientListView/inLog');
    }
    public function outLog(){
        $uid= $_GET['uid'];
        $user_info = M('user_info');
        $map['uid'] = $uid;
        $userInfo = $user_info->where($map)->find();
        $this->assign('userInfo',$userInfo);
        $this->display('clientManage/clientListView/outLog');
    }

    public function wpcLog(){
        $uid= $_GET['uid'];
        $user_info = M('user_info');
        $map['uid'] = $uid;
        $userInfo = $user_info->where($map)->find();
        $this->assign('userInfo',$userInfo);
        $this->display('clientManage/clientListView/wpcLog');
    }
    public function ypcLog(){
        $uid= $_GET['uid'];
        $user_info = M('user_info');
        $map['uid'] = $uid;
        $userInfo = $user_info->where($map)->find();
        $this->assign('userInfo',$userInfo);
        $this->display('clientManage/clientListView/ypcLog');
    }
}
