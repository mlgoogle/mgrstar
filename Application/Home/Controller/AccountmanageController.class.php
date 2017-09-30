<?php
namespace Home\Controller;
use Think\Controller;
//use Think\Controller\restController;
class AccountmanageController extends CTController {

   // private $user;
    private $identitys = array(1,2,3,4); // 1 交易所 2 机构 3 经纪人 4 普通客户

    public function __construct(){
        # code...T
        parent::__construct();

       // $this->menu = new \Home\Model\menuModel();
       // $this->assign('menu',$this->menu->menuRow());

        $sessionName = C('user');

        $this->user = $user =  session($sessionName);
        $this->assign('user',$user);
        $this->assign('active','accountmanage');

        if(!session($sessionName)){
            $this ->redirect('login/login',Null,0);
        }

        $identity_id = $user['identity_id'];

        if($identity_id<2){
            $this->assign('identity_status', 1);
        }

        $this->assign('action','accountManage');
    }

    public function orgManage(){

        $this->errorAddress();//权限

        $identityId = $this->user['identity_id'];

        if($identityId<0) return false;

        if($identityId==1){
            // code;
        }else if($identityId==2){
            $this->redirect('Home/accountmanage/brokerManage');
        }else if($identityId==3){
            $this->redirect('Home/accountmanage/brokerSubManage');
        }else {
            $this->redirect('Home/accountmanage/brokerSubManage');
        }

        $this->assign('actionUrl','orgManage');

        $this->display('accountManage/orgManage');
    }

    public function userManage(){
        $this->errorAddress();//权限

        $this->assign('actionUrl','userManage');

        $this->display('accountManage/userManage');
    }

    public function brokerManage(){
        $this->errorAddress();//权限

        $identityId = $this->user['identity_id'];

        if($identityId==4){
            $this->redirect('Home/accountmanage/brokerSubManage');
        }

        $this->assign('actionUrl','brokerManage');
        $this->display('accountManage/brokerManage');
    }

    public function brokerSubManage(){
        $this->errorAddress();//权限

        $this->assign('actionUrl','brokerSubManage');
        $this->display('accountManage/brokerSubManage');
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
