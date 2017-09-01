<?php
namespace Home\Controller;
use Think\Controller;
class LoginController extends Controller {

    private $sessionName ;

    public function __construct(){
        parent::__construct();
        $this->sessionName = C('user');
    }

    public function login(){
      $sessionName = $this->sessionName ;

      $user = session($sessionName);

      if($user){
          $this ->redirect('adminBacker/index',Null,0);
      }else {
          $this->display('login/login');
      }
    }
  public function dologin(){

      $where = array(
          'uname' => I('post.uname'),
          'pass'=> $p = md5(I('post.pass')),
          'status' => 0
      );


      $res = M('admin_user')->where($where)->find();

      if($res){
          session($this->sessionName,$res);//store seession
          $this->ajaxReturn(array(
              'code'=>0,
              'message'=>'success',
              'data'=>'登陆成功',

          ));
      }else{
          $this->ajaxReturn(array(
              'code'=>-1,
              'message'=>'fail',
              'data'=>'密码错误',

          ));
      }

    }

    public function doRestPassword(){

        $user = session($this->sessionName);
        if($user){
             $oldPassword = $_POST['oldpassword'];
             $newPassword = $_POST['newpassword'];
             $map['pass'] = md5($oldPassword);
             $map['id'] = $user['id'];
             $data['pass'] = md5($newPassword);
             $res = M('admin_user')->where($map)->find();
             if($res){
                 $a = M('admin_user')->where($map)->save($data);
                 if($a){
                   session($this->sessionName,null);
                     $return =array(
                         'code'=>0,
                         'message'=>'修改成功',
                     );
                 }else{
                     $return =array(
                         'code'=>-1,
                         'message'=>'失败',
                     );
                 }
             }else{
                 $return =array(
                     'code'=>-1,
                     'message'=>'原密码错误',
                 );
             }
        }else{
            $return  =array(
                'code'=>-2,
                'message'=>'还没有登陆'
            );
        }
        $this->ajaxReturn($return);
    }

    public function doLoginout(){
        session($this->sessionName,0);
        $this->display('login/login');
    }


}
