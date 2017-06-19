<?php
namespace Home\Controller;
use Think\Controller;
//use Think\Controller\restControll
class AdminuserController extends Controller {
    /*
    * ****
    *
    */
    private $user;
    private $identitys = array(1,2,3,4); // 1 交易所 2 机构 3 经纪人 4 普通客户

    public function __construct(){
        # code...
        parent::__construct();
        $this->user = session('user');
        if(!session('user')){
           //$this->ajaxReturn(array('code'=>-1,'message'=>'fail','data'=>'not login'));

        }
    }

    //identity_id

    public function getIdentity(){
        $identity =M('admin_identity');
        $a = $identity->select();
        return $a;
    }

    public function getList(){
        $user_info =M('admin_user');
        $pageNum = isset($_POST['pageNum'])?$_POST['pageNum']:5;
        $page = isset($_POST['page'])?$_POST['page']:1;

        $timeStart = date("Y-m-d H:i:s",strtotime($_POST['starTime']));
        $timeEnd = date("Y-m-d H:i:s",strtotime($_POST['endTime']));

        if(!empty($_POST['nickname'])){
            $map['nickname'] = array('like',"%".$_POST['nickname']."%");
        }
        if(!empty($_POST['cellphone'])){
            $map['cellphone'] = array('like',"%".$_POST['cellphone']."%");
        }

        if(!empty($_POST['start_time']) || !empty($_POST['end_time'])){
            $map['registerTime'] = array(array('gt',"$timeStart"),array('lt',"$timeEnd")) ;
        }


        //判断用户类型
        $identity_id = $this->user['identity_id'];

        if($identity_id==3){ //经纪人用户
            //$identity_id = 3;
            $map['memberId'] = $this->user['memberId'];
            $map['agentId']  = $this->user['agentId'];
        }else if($identity_id==2){ //机构用户
            // $identity_id = 2;
            $map['memberId'] = $this->user['memberId'];
            $map['agentId'] = array('gt',0);
        }else{  // 交易所用户
            //$identity_id = 1;
            $map['pid'] = $this->user[id];
        }


        $count = $user_info->where($map)->count();// 查询满足要求的总记录数
        $list = $user_info->where($map)->page($page,$pageNum)->select();//获取分页数据

        $memberArr = array();
        foreach ($list as $l){
            $memberArr[] = $l['memberId'];
        }

        $memberArr = array_filter(array_unique($memberArr));

        $memberWhere['memberid'] = array('in',$memberArr);
        $memberInfo = M('member_info')->where($memberWhere)->select();

        $memberList = array();
        foreach ($memberInfo as $m){
            $memberList[$m['memberid']] = $m;
        }

        foreach ($list as $key=> $value){
            $list[$key]['memberInfo'] = $memberList[$value['memberId']];
            //$list[$key]['memberInfo']['name'] = isset($memberList[$value['memberId']])?$memberList[$value['memberId']]['name']:'';
            //$list[$key]['memberInfo']['cellphone'] = isset($memberList[$value['memberId']])?$memberList[$value['memberId']]['tel']:'';
            $list[$key]['cellphone'] = isset($memberList[$value['memberId']])?$memberList[$value['memberId']]['tel']:'';
            $list[$key]['orgType'] = isset($memberList[$value['memberId']])?$memberList[$value['memberId']]['type']:'';


            //tel
        }
        //dump($list);exit;

//        dump($memberInfo);exit;
//        foreach($list as $key =>$value){
//            $memberMap['memberid'] = $value['memberid'];
//            //$agentMap['code'] = $value['agentid'];
//            $memberInfo = M('member_info')->where($memberMap)->find();
//            //$agentInfo = M('agent_info')->where($agentMap)->find();
//            $list[$key]['memberInfo'] = $memberInfo;
//            //[$key]['agentInfo'] = $agentInfo;
//        }

        $Page       = new \Think\Page($count,$pageNum);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $data['totalPages'] = $count;
        $data['pageNum'] =$pageNum;
        $data['page'] = $page;
        $data['totalPages'] = ceil($count/$pageNum);
        $data['list'] = $list;
        $this->ajaxReturn($data);
    }

    public function addUser(){


        $data = array();


        // 关联经纪人分类id
        $agentId = isset($_POST['agentId'])?intval($_POST['agentId']):0;
        //关联机构分类id
        $memberId = isset($_POST['memberId'])?intval($_POST['memberId']):0;

        if(!$memberId){
            $return =  array(
                'code' =>-2 ,
                'message'=>'请选择机构！'
            );
            $this->ajaxReturn($return);
            return false;
        }


        //判断现在登录用户的类型
        $loginIdentityId = $this->user['identity_id'];

        if($loginIdentityId==1){ //交易所登录用户

            if($agentId){
                //添加的是经纪人用户 用 memberId 查 uid
                $memberInfo = M('member_info');
                $where = array('memberid'=>$memberId);
                $memberRow = $memberInfo->where($where)->find();

                $pid = $this->user['id'];
                $first_id = (int)$memberRow['uid'];
                $second_id = 0;
            }else {

                $pid = $this->user['id'];
                $first_id = 0;
                $second_id = 0;
            }
        }else if($loginIdentityId==2){//机构登录用户
            $pid = $this->user['id'];
            $first_id = 0;
            $second_id = 0;
        }else{
            $return =  array(
                'code' =>-2 ,
                'message'=>'错误操作！'
            );
            $this->ajaxReturn($return);
            return false;
        }


        //判断添加是什么类型的用户
        if($agentId){ //经纪人用户
            //$identity_id = 3;
            $identity_id = $this->identitys[2];
        }else if($memberId){ //机构用户
            // $identity_id = 2;
            $identity_id = $this->identitys[1];
        }else{  // 交易所用户
            //$identity_id = 1;
            $identity_id = $this->identitys[0];
        }

        $uname = $_POST['uid']?$_POST['uid']:'';// 有点奇怪这是帐号名

        if(!$uname){
            $return =  array(
                'code' =>-2 ,
                'message'=>'请输入用户名！'
            );
            $this->ajaxReturn($return);
            return false;
        }

        if(M('admin_user')->where(array('uid'=>$uname))->find()) {//判断用户是否存在
            $return =  array(
                'code' =>-2 ,
                'message'=>'帐号已存在！'
            );
            $this->ajaxReturn($return);
            return false;
        }

        $data = array(
            'uid'          => $_POST['uid']?$_POST['uid']:'123', // 有点奇怪这是帐号名
            'pass'         => md5($_POST['password'])?md5($_POST['password']):123, // 密码
            'nickname'     => $_POST['nickname'],
            'memberId'     => $memberId,//关联机构分类id
            'agentId'      => $agentId,//关联经纪人分类id
            'registerTime' => date("Y-m-d H:i:s",time()),
            'pid'          => $pid,//上级交易所id
            'first_id'     => $first_id,//上级机构用户id
            'second_id'    => $second_id,//上级经纪人用户id
            'identity_id'  => $identity_id

        );
        //dump($data);exit;
        $adminUser = M('admin_user');
        $res = $adminUser->add($data);
        //echo $adminUser->getLastSql();获取sql语句

        if($res){
            $return = array(
                'code' => 0,
                'message' => 'success',

            );
        }else{
            $return =  array(
                'code' =>-1 ,
                'message'=>'fail'
            );
        }

        $this->ajaxReturn($return);
    }

    public function delUser(){
        $adminUser = M('admin_user');
        $id = $_POST['id'];
        foreach($id as $key=>$value){
            $map['id'] = $value;
            $res = $adminUser->where("id= $value")->delete();

        }
        if($res) {
            $return = array(
                'code' => 0,
                'message' => 'success',

            );
        }else{
            $return = array(
                'code' => -1,
                'message' => 'fail',

            );
        }
        $this->ajaxReturn($return);
    }

    public function  updateUser()
    {
        $adminUser = M('admin_user');
        $id = $_POST['id'];
        //$data['uid'] = $uname = $_POST['uid'];
        $data['pass'] =  md5($_POST['password']);
        $data['nickname'] = $_POST['nickname'];
        $data['memberId'] = $memberId = $_POST['memberId'];
        $data['agentId']  = $agentId  = $_POST['agentId'];
        $password = $_POST['password'];


        if(!$password){
            $return =  array(
                'code' =>-2 ,
                'message'=>'请输入密码！'
            );
            $this->ajaxReturn($return);
            return false;
        }
        /*
        else if(M('admin_user')->where(array('uid'=>$uname))->find()) {//判断用户是否存在
            $return =  array(
                'code' =>-2 ,
                'message'=>'帐号已存在！'
            );
            $this->ajaxReturn($return);
            return false;
        }
        */
        //判断添加是什么类型的用户

        if($agentId){ //经纪人用户
            //$identity_id = 3;
            $identity_id = $this->identitys[2];
        }else if($memberId){ //机构用户
        // $identity_id = 2;
        $identity_id = $this->identitys[1];
        }else{  // 交易所用户
            //$identity_id = 1;
            $identity_id = $this->identitys[0];
        }

        $data['identity_id'] = $identity_id;


        $res = $adminUser->where("id = $id")->save($data);



        if($res){
            $return =  array(
              'code'=>0,
              'message'=>'success',
            );
        }else{
             $return =  array(
               'code' =>-1 ,
               'message'=>'fail'
             );
        }
        $this->ajaxReturn($return);
    }

    public function updateStatus(){
      $adminUser = M('admin_user');

      $map = $_POST['id'];
      $data['status'] = $_POST['status'];
      foreach($map as $key=>$value){
        $adminUser->where("id= $value")->save($data);

      }
      $return =  array(
        'code'=>0,
        'message'=>'success',

      );
      $this->ajaxReturn($return);
    }

    public function resetPassword(){
        $adminUser = M('admin_user');

        $id = $_POST['id'];
        $data['pass'] = md5($_POST['password']);
        $res =  $adminUser->where("id= $id")->save($data);
        if($res){
          $return =  array(
            'code'=>0,
            'message'=>'success',

          );
        }else{
          $return =  array(
            'code'=>-1,
            'message'=>'fail',

          );
        }
        $this->ajaxReturn($return);
    }

    //修改手续费
    public function editFee(){
        $adminUser = M('admin_user');
        $id =  I('post.id');

        //$id = $_POST['id'];

        $percentFee = (int)I('post.percentFee');


        if($percentFee>100 || $percentFee<1){

            $return =  array(
                'code'=>-2,
                'message'=>'手续费在1~100之间！',
            );

            $this->ajaxReturn($return);

            return false;
        }

        $data['percentFee'] = $percentFee;

        $res =  $adminUser->where("id= $id")->save($data);
        if($res){
            $return =  array(
                'code'=>0,
                'message'=>'success',

            );
        }else{
            $return =  array(
                'code'=>-1,
                'message'=>'fail',

            );
        }
        $this->ajaxReturn($return);
    }

}
