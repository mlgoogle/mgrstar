<?php

namespace Home\Controller;

use Think\Controller;

//use Think\Controller\restController;
class AdminuserController extends Controller
{

    private $user;

    public function __construct()
    {

        parent::__construct();
        $this->user = session('user');

        if (!session('user')) {
            $this ->redirect('login/login',Null,0);
        }
    }

    public function getList()
    {

        $user_info = M('admin_user');
        $pageNum = isset($_POST['pageNum']) ? $_POST['pageNum'] : 5;
        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $timeStart = date("Y-m-d H:i:s", strtotime($_POST['starTime']));
        $timeEnd = date("Y-m-d H:i:s", strtotime($_POST['endTime']));


        $map = $identity = $this->getUserIdentity(); //判断用户类型

        if (!empty($_POST['nickname'])) {
            $map['nickname'] = array('like', "%" . $_POST['nickname'] . "%");
        }
        if (!empty($_POST['cellphone'])) {
            $map['cellphone'] = array('like', "%" . $_POST['cellphone'] . "%");
        }

        if (!empty($_POST['start_time']) || !empty($_POST['end_time'])) {
            $map['registerTime'] = array(array('gt', "$timeStart"), array('lt', "$timeEnd"));
        }

        $count = $user_info->where($map)->count();// 查询满足要求的总记录数
        $list = $user_info->where($map)->page($page, $pageNum)->select();//获取分页数据

        //判断用户类型
        $identity_id = $this->user['identity_id'];


        if ($identity_id == 3) { //区域

            $whereMember['memberid'] = $this->user['memberId'];

            $whereAgent['id'] = $this->user['agentId'];

            $whereAgentSub['agentId'] = $this->user['agentId'];

            $data['agentsub_info'] = $info = M('agentsub_info')->field('id,nickname')->where($whereAgentSub)->select();

            $data['member'] = M('member_info')->field('memberid,name')->where($whereMember)->find();
            $data['agent'] = M('agent_info')->field('id,nickname')->where($whereAgent)->find();
        } else if ($identity_id == 2) { //机构
            $whereAgent['memberId'] = $this->user['memberId'];
            $data['agent_info'] = $info = M('agent_info')->field('id,nickname')->where($whereAgent)->select();

            $whereMember['memberid'] = $this->user['memberId'];
            $data['member'] = M('member_info')->field('memberid,name')->where($whereMember)->find();
            //dump(M('member_info')->_sql());
        } else { //会所
            $whereMember['uid'] = $this->user['id'];
            $data['member_info'] =  $info = M('member_info')->field('memberid,name')->where($whereMember)->select();
        }



        $Page = new \Think\Page($count, $pageNum);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $data['totalPages'] = $count;
        $data['pageNum'] = $pageNum;
        $data['page'] = $page;
        $data['totalPages'] = ceil($count / $pageNum);
        $data['list'] = $list;
//        $data['info'] = $info;
        $this->ajaxReturn($data);


    }

    public function addUser()
    {

        $adminUser = M('admin_user');
        $data['uname'] = $uname = $_POST['uname'] ? $_POST['uname'] : '';
        $pass = $_POST['password'];
        $data['pass'] = $pass ? md5($_POST['password']) : '';
        $data['nickname'] = $nickname = $_POST['nickname'];
        $data['memberId'] = $memberId = $_POST['memberId'];
        $data['agentId'] = $agentId = $_POST['agentId'];
        $data['agentSubId'] = $agentSubId = $_POST['agentSubId'];
        $data['cellphone'] = $cellphone = $_POST['cellphone'];
        $data['registerTime'] = date("Y-m-d H:i:s", time());

        if (!$memberId) {
            $return = array(
                'code' => -2,
                'message' => '请选择机构！'
            );
            $this->ajaxReturn($return);
            return false;
        }

        if (!$uname) {
            $return = array(
                'code' => -2,
                'message' => '请输入账号！'
            );
            $this->ajaxReturn($return);
            return false;
        } else {
            if (!preg_match('/^[a-z0-9]{6,10}$/', $uname)) {
                $return = array(
                    'code' => -2,
                    'message' => '帐号必须字母或数字6-10个字符！'
                );
                $this->ajaxReturn($return);
                return false;
            }
        }

        if (!$pass) {
            $return = array(
                'code' => -2,
                'message' => '请输入密码！'
            );
            $this->ajaxReturn($return);
            return false;
        }

        if (!$nickname) {
            $return = array(
                'code' => -2,
                'message' => '请填写用户名称！'
            );
            $this->ajaxReturn($return);
            return false;
        }

        if ($cellphone) {
            if (!preg_match('/^1[3-9][0-9]{9}$/', $cellphone)) {
                $return = array(
                    'code' => -2,
                    'message' => '手机号格式错误！'
                );
                $this->ajaxReturn($return);
                return false;
            }
        }

        if (M('admin_user')->where(array('uname' => $uname))->find()) {//判断用户是否存在
            $return = array(
                'code' => -2,
                'message' => '帐号已存在！'
            );
            $this->ajaxReturn($return);
            return false;
        }

        if (M('admin_user')->where(array('nickname' => $nickname))->find()) {//判断用户名称是否存在
            $return = array(
                'code' => -2,
                'message' => '帐号名称不能重复！'
            );
            $this->ajaxReturn($return);
            return false;
        }


        //判断现在登录用户的类型
        $loginIdentityId = $this->user['identity_id'];


        if($agentSubId){ //经纪人
            $data['identity_id'] = 4;
        }else if($agentId){ //经纪人
             $data['identity_id'] = 3;
        }else if($memberId){ //机构
            $data['identity_id'] = 2;
        }else{
            $data['identity_id'] = 1;
        }



        $res = $adminUser->add($data);
        //echo $adminUser->getLastSql();exit;
        if ($res) {
            $return = array(
                'code' => 0,
                'message' => 'success',

            );
        } else {
            $return = array(
                'code' => -1,
                'message' => 'fail'
            );
        }

        $this->ajaxReturn($return);
    }

    public function delUser()
    {
        $adminUser = M('admin_user');
        $id = $_POST['id'];
        foreach ($id as $key => $value) {
            $map['id'] = $value;
            $res = $adminUser->where("id= $value")->delete();

        }
        if ($res) {
            $return = array(
                'code' => 0,
                'message' => 'success',

            );
        } else {
            $return = array(
                'code' => -1,
                'message' => 'fail',

            );
        }
        $this->ajaxReturn($return);
    }

    public function updateUser()
    {
        $adminUser = M('admin_user');
        $id = $_POST['id'];
        //$data['uname'] = $_POST['uname'];
        $pass = $_POST['password'];
        $data['pass'] = md5($_POST['password']);
        $data['nickname'] = $nickname = $_POST['nickname'];
        $data['memberId'] = $memberId =  $_POST['memberId'];
        $data['agentId'] = $agentId = $_POST['agentId'];
        $data['agentSubId'] = $agentSubtId = $_POST['agentSubtId'];
        $data['cellphone'] = $cellphone = $_POST['cellphone'];


        if(!$memberId) {
            $return = array(
                'code' => -2,
                'message' => '请选择机构！'
            );
            $this->ajaxReturn($return);
            return false;
        }


        if(!$pass) {
            $return = array(
                'code' => -2,
                'message' => '请填写密码！'
            );
            $this->ajaxReturn($return);
            return false;
        }

        if(empty($nickname)){
            $return = array(
                'code' => -2,
                'message' => '请填写用户名称！'
            );
            $this->ajaxReturn($return);
            return false;
        }

        if(empty($cellphone)){
            $return = array(
                'code' => -2,
                'message' => '请填写手机号！'
            );
            $this->ajaxReturn($return);
            return false;
        }

        if ($cellphone) {
            if (!preg_match('/^1[3-9][0-9]{9}$/', $cellphone)) {
                $return = array(
                    'code' => -2,
                    'message' => '手机号格式错误！'
                );
                $this->ajaxReturn($return);
                return false;
            }
        }

        $res = $adminUser->where("id = $id")->save($data);


        if ($res) {
            $return = array(
                'code' => 0,
                'message' => 'success',

            );
        } else {
            $return = array(
                'code' => -1,
                'message' => 'fail'
            );
        }
        $this->ajaxReturn($return);
    }

    public function updateStatus()
    {
        $adminUser = M('admin_user');

        $map = $_POST['id'];
        $data['status'] = $_POST['status'];
        foreach ($map as $key => $value) {
            $adminUser->where("id= $value")->save($data);

        }
        $return = array(
            'code' => 0,
            'message' => 'success',

        );
        $this->ajaxReturn($return);
    }

    public function resetPassword()
    {
        $adminUser = M('admin_user');

        $id = $_POST['id'];
        $data['pass'] = md5($_POST['password']);
        $res = $adminUser->where("id= $id")->save($data);
        if ($res) {
            $return = array(
                'code' => 0,
                'message' => 'success',

            );
        } else {
            $return = array(
                'code' => -1,
                'message' => 'fail',

            );
        }
        $this->ajaxReturn($return);
    }

    protected function getUserIdentity()
    {
        //判断用户类型
        $identity_id = $this->user['identity_id'];
        $map = array();

        if ($identity_id == 4) { //经纪人用户
            //$identity_id = 3;
            return false;
            $map['memberId'] = $this->user['memberId'];
            $map['agentId'] = $this->user['agentId'];
            $map['agentSubId'] = $this->user['agentSubId'];
        }
        if ($identity_id == 3) { //区域经纪人
            //$identity_id = 3;
            $map['memberId'] = $this->user['memberId'];
            $map['agentId'] = $this->user['agentId'];
            $map['agentSubId'] = array('gt', 0);
        } else if ($identity_id == 2) { //机构用户
            // $identity_id = 2;
            $map['memberId'] = $this->user['memberId'];
            $map['agentId'] = array('gt', 0);
        } else {  // 交易所用户
            //$identity_id = 1;

            $map['memberId'] = array('gt', 0);

        }
        return $map;

    }
}