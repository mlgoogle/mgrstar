<?php
namespace Home\Controller;
use Think\Controller;
//use Think\Controller\restController;
class MemberController extends Controller
{

    private $user;

    public function __construct()
    {
        # code...
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


        if($this->user['identity_id']==1) {

            $map['uid'] = $this->user['id'];

        }if($this->user['identity_id']==2){
            $map['memberid'] = $this->user['memberId'];
        }


        $count = $member_info->where($map)->count();// 查询满足要求的总记录数
        $list = $member_info->where($map)->page($page, $pageNum)->select();//获取分页数据

        foreach ($list as $key => $value) { //默认找第一个机构下的经纪人

            $memberId = $value['memberid'];
            $agent_info = M('agent_info');

            $agentInfo = $agent_info->where(array('memberid' => $memberId))->select();
            $list[$key]['agent_info'] = $agentInfo;
            break;
        }

        $Page = new \Think\Page($count, $pageNum);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $data['totalPages'] = $count;
        $data['pageNum'] = $pageNum;
        $data['page'] = $page;
        $data['totalPages'] = ceil($count / $pageNum);
        $data['list'] = $list;

        $data['code_identity'] = 0;
        if($this->user['identity_id']==1) {
            $data['code_identity'] = 1;
        }

        $this->ajaxReturn($data);

    }

    //获取某机构下的经纪人

    public function getAgentInfo()
    {
        $memberId = I('post.memberid');
        if (!$memberId) {
            $this->ajaxReturn(array('list' => array()));
            return false;
        }

        //$memberId = $value['memberid'];
        $agent_info = M('agent_info');

        $data['list'] = $agentInfo = $agent_info->where(array('memberid' => $memberId))->select();
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


    public function add(){

        $member_info = M('member_info');

        $name = $_POST['name']; //机构名称
        $mark = $_POST['mark'];
        $superMemberid = $_POST['superMemberid'];
        $type = $_POST['type'];
        $tel = $_POST['tel'];
        $phone = $_POST['phone'];

        $status = 0;//机构状态

        $data['name'] = $name;
        $data['mark'] = $mark;
        $data['superMemberid'] = $superMemberid;
        $data['type'] = $type;
        $data['tel'] = $tel;
        $data['phone'] = $phone;
        $data['status'] = $status;
        $data['uid'] = $this->user['id'];


        if (!$name) {
            $return = array(
                'code' => -2,
                'message' => '请填写机构名称！',
            );
            $this->ajaxReturn($return);
            return false;
        }else if(!$tel){
            $return = array(
                'code'=>-2,
                'message'=>'请输入手机号！',
            );
            $this->ajaxReturn($return);
            //  exit();
            return false;
        }else if(!preg_match('/^1[0-9]{10}$/',$tel)){
            $return = array(
                'code'=>-2,
                'message'=>'请正确的填写手机号！',
            );
            $this->ajaxReturn($return);
            return false;
        }
        /*
        else if(!$phone){
            $return = array(
                'code'=>-2,
                'message'=>'请输入固定电话！',
            );
            $this->ajaxReturn($return);
            //  exit();
            return false;
        }*/
        else {

            $res = $member_info->add($data);

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
    }


        //查询所有根机构
    public function getRootList(){
        $member_info =M('member_info');

        $where = array(
          'uid'=>$this->user['id'],
          'superMemberid'=>0
        );

        $data = $member_info->where($where)->select();//获取分页数据
        $this->ajaxReturn($data);
    }

    public function updateMember(){
      $member_info =M('member_info');

      $data['name'] = $name = $_POST['name'];
      $data['mark'] = $_POST['mark'];
      $data['superMemberid'] = $_POST['superMemberid'];
      $data['type'] = $_POST['type'];
      $data['tel']= $tel = $_POST['tel'];
      $data['phone'] = $_POST['phone'];//机构名称
      $map['memberid'] = $_POST['memberid'];


        if (!$name) {
            $return = array(
                'code' => -2,
                'message' => '请填写机构名称！',
            );
            $this->ajaxReturn($return);
            return false;
        }else if(!$tel){
            $return = array(
                'code'=>-2,
                'message'=>'请输入手机号！',
            );
            $this->ajaxReturn($return);
            //  exit();
            return false;
        }else if(!preg_match('/^1[0-9]{10}$/',$tel)){
            $return = array(
                'code'=>-2,
                'message'=>'请正确的填写手机号！',
            );
            $this->ajaxReturn($return);
            return false;
        }else {

            $res = $member_info->where($map)->save($_POST);

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

    }

    public function updateStatus(){
      $ids=$_POST['id'];
      $data['status'] = $_POST['status'];
      foreach ($ids as $key => $value) {
        # code...
          $map['memberid'] = $value;
          $res = M('member_info')->where($map)->save($data);
      }
      if($res){
      $return = array(
        'code'=>0,
        'message'=>'success',
      );
    }else{
      $return =array(
        'code'=>-1,
        'message'=>'fail',
      );
    }
      $this->ajaxReturn($return);

    }

}
