<?php

namespace Home\Controller;

use Think\Controller;

//use Think\Controller\restController;
class MemberController extends Controller
{

    private $user;

    public function __construct()
    {

        $this->user = session('user');
        if (!session('user')) {
            //$this->ajaxReturn(array('code'=>-1,'message'=>'fail','data'=>'not login'));

        }
    }

    public function getList()
    {
        $member_info = M('member_info');
        $pageNum = isset($_POST['pageNum']) ? $_POST['pageNum'] : 5;
        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        if (!empty($_POST['superMemberid'])) {
            $map['superMemberid'] = $_POST['superMemberid'];
        }
        if (!empty($_POST['name'])) {
            $map['name'] = array('like', "%" . $_POST['name'] . "%");
        }


        $count = $member_info->where($map)->count();// 查询满足要求的总记录数
        $list = $member_info->where($map)->page($page, $pageNum)->select();//获取分页数据
        foreach ($list as $key => $value) {
            $list[$key]['superMemberInfo'] = "";
            if (!empty($value['superMemberid'])) {
                $mmap['memberid'] = $value['superMemberid'];

                $superMemberInfo = $member_info->where($mmap)->find();

                $list[$key]['superMemberInfo'] = $superMemberInfo;
            }
        }
        $Page = new \Think\Page($count, $pageNum);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $data['totalPages'] = $count;
        $data['pageNum'] = $pageNum;
        $data['page'] = $page;
        $data['totalPages'] = ceil($count / $pageNum);
        $data['list'] = $list;
        $this->ajaxReturn($data);
    }

    public function getlistall()
    {
        $member_info = M('member_info');
        $list = $member_info->where($map)->select();//获取分页数据
        foreach ($list as $key => $value) {
            # code...
            $list[$key]['superMemberInfo'] = "";
            if (!empty($value['superMemberid'])) {
                $map['memberid'] = $value['superMemberid'];
                $superMemberInfo = $member_info->where($map)->find();
                $list[$key]['superMemberInfo'] = $superMemberInfo;
            }
        }
        $this->ajaxReturn($list);
    }

    public function add()
    {

        $member_info = M('member_info');

        $name = $_POST['name'];//机构名称
        $mark = $_POST['mark'];
        $tel = $_POST['tel'];
        $phone = $_POST['phone'];

        if (!$name) {
            $return = array(
                'code' => -2,
                'message' => '请填写机构名称！'
            );
            $this->ajaxReturn($return);

            return false;
        }


        if(empty($mark)){
            $return = array(
                'code' => -2,
                'message' => '请填写机构编码！'
            );
            $this->ajaxReturn($return);

            return false;
        }

        if (!$tel) {
            $return = array(
                'code' => -2,
                'message' => '请填写手机号！'
            );
            $this->ajaxReturn($return);

            return false;
        }

        if($member_info->where(array('mark'=>$mark ))->find()){
            $return = array(
                'code' => -2,
                'message' => '编码不能重名！'
            );
            $this->ajaxReturn($return);

            return false;
        }

        $status = 0;//机构名称
        $data['name'] = $name;
        $data['mark'] = $mark;
        $data['tel'] = $tel;
        $data['phone'] = $phone;
        $data['status'] = $status;
        $data['uid'] = $this->user['id'];


        $res = $member_info->add($data);

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

    //查询所有根机构
    public function getRootList()
    {
        $member_info = M('member_info');
        $data = $member_info->where("superMemberid = 0 ")->select();//获取分页数据
        $this->ajaxReturn($data);
    }

    public function updateMember()
    {
        $member_info = M('member_info');

        $data['name'] = $name = $_POST['name']; //机构名称
        $data['mark'] = $mark = $_POST['mark'];
        $data['superMemberid'] = $_POST['superMemberid'];
        $data['type'] = $_POST['type'];
        $data['tel'] = $tel = $_POST['tel'];
        $data['phone'] = $_POST['phone'];

        $map['memberid'] = $id = $_POST['memberid'];

        if(empty($name)){
            $return = array(
                'code' => -2,
                'message' => '请填写机构名称！'
            );
            $this->ajaxReturn($return);

            return false;
        }


        if(empty($mark)){
            $return = array(
                'code' => -2,
                'message' => '请填写机构编码！'
            );
            $this->ajaxReturn($return);

            return false;
        }

        if (!$tel) {
            $return = array(
                'code' => -2,
                'message' => '请填写手机号！'
            );
            $this->ajaxReturn($return);

            return false;
        }


        $where['mark'] = $mark;
        $where['memberid'] = array('neq',$id);

        if($member_info->where($where)->find()){
            $return = array(
                'code' => -2,
                'message' => '编码不能重名！'
            );
            $this->ajaxReturn($return);

            return false;
        }


        $res = $member_info->where($map)->save($data);

        if ($res) {
            $return = array(
                'code' => 0,
                'message' => 'success',

            );
        } else {
            $return = array(
                'code' => -1,
                'message' => 'fail');
        }
        $this->ajaxReturn($return);
    }

    public function updateStatus()
    {
        $ids = $_POST['id'];
        $data['status'] = $_POST['status'];
        foreach ($ids as $key => $value) {
            # code...
            $map['memberid'] = $value;
            $res = M('member_info')->where($map)->save($data);
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

    public function getAgentInfo()
    {

        $memberId = I('post.memberid');

        if (!$memberId) {
            $this->ajaxReturn(array('list' => array()));
            return false;
        }

        //$memberId = $value['memberid'];
        $agent_info = M('agent_info');

        $data['list'] = $agentInfo = $agent_info->where(array('memberId' => $memberId))->select();

        $this->ajaxReturn($data);
    }

    public function getAgentSubInfo()
    {

        $agentId = I('post.agentId');

        if (!$agentId) {
            $this->ajaxReturn(array('list' => array()));
            return false;
        }

        //$memberId = $value['memberid'];
        $agentSub_info = M('agentsub_info');

        $data['list'] = $agentSub_info->where(array('agentId' => $agentId))->select();

        $this->ajaxReturn($data);
    }


}