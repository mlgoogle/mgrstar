<?php
namespace Home\Controller;
use Think\Controller;
//use Think\Controller\restController;
class AccountmanageController extends Controller {

    private $user;
    private $identitys = array(1,2,3,4); // 1 交易所 2 机构 3 经纪人 4 普通客户
    public function __construct(){
      # code...
      parent::__construct();
      $this->user = $user =  session('user');
      $this->assign('user',$user);
      $this->assign('active','accountmanage');

      if(!session('user')){
        $this ->redirect('login/login',Null,0);
      }

    }

        public function orgManage(){
        $this->assign('actionUrl','orgManage');

        $this->display('accountManage/orgManage');
    }

    public function userManage(){
        $this->assign('actionUrl','userManage');
        $identityId = $this->user['identity_id'];
        $identityId = isset($identityId)?$identityId:1;
//        if($identityId==1){
//            // code;
//        }else if($identityId==2){
//           // $this->redirect('Home/accountmanage/orgManage');
//        }else if($identityId==3){
//            $this->redirect('Home/clientmanage/clientList');
//        }

      $this->display('accountManage/userManage');
    }

    public function brokerManage(){
        $this->assign('actionUrl','brokerManage');
      $this->display('accountManage/brokerManage');
    }

    public function queryPhoneNum(){
      $phoneNum = $_POST['phoneNum'];
      $map['phoneNum'] = $phoneNum;
      $user_info = M('user_info')->where($map)->find();
      $this->ajaxReturn($user_info);
    }

    public function addUser(){

      $username = $_POST['username'];
      $password = $_POST['password'];
      if(CRYPT_SHA256 == 1){
        $password = crypt(crypt($password, 't1@s#df!'),$username);
      }

      $nickname = $_POST['nickname'];
      $Model = M('user_info');
      $Model->nickname = $nickname;
      $Model->phoneNum = $username;
      $Model->passwd = $password;
      $Model->registerTime = date("Y-m-d H:i:s");

      $res = $Model->add();
      echo $res;
    }

}
