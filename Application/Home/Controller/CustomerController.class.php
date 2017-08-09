<?php

namespace Home\Controller;
use Think\Controller;

/**
 * 消费者
 * @date started at 2017-6-12
 *
 * Class CustomerController
 * @package Home\Controller
 */
class CustomerController extends CTController
{
    //软删除    0上线 1下线 2软删除
    const DELETE_ONLINE = 0;
    const DELETE_OFF = 1;
    const DELETE_TRUE = 2;

    private $excel;
    protected $titles = array();
    protected $excelModel;

    public function __construct(){
        parent::__construct();

            //$this->user = session('user');

        $this->excel = 1;

        $this->assign('title', '消费者列表');
    }

    //模板显示
    public function customer()
    {
        $this->display('Customer/listing');
    }

    /**
     * 添加
     */
    public function addCustomer()
    {
        //接收过滤提交数据
        $name = I('post.customername', '', 'strip_tags');
        $name = trim($name);
        $micro = (int)$_POST['micro'];

        //非空提醒
        if (empty($name) || empty($micro)) {
            $return = array(
                'code' => -2,
                'message' => '请填写正确的值！'
            );
            return $this->ajaxReturn($return);
        }

        //唯一性判断
        $model = M('customerment_type');
        $isExist = (int)$model->where("`customername` = '{$name}'")->count('id');
        if ($isExist) {
            $return = array(
                'code' => -2,
                'message' => '该类型已存在！'
            );
            return $this->ajaxReturn($return);
        }

        //数据入库
        $model->customername = $name;
        $model->micro = $micro;
        $model->add_time = date('Y-m-d H:i:s', time());
        $bool = ($model->add()) ? 0 : 1;

        //结果返回
        $return = array(
            'code' => $bool,
            'message' => 'success',
        );
        return $this->ajaxReturn($return);
    }

    /**
     * 编辑信息
     * todo 设置图片的大小
     */
    public function editCustomer()
    {
        $id = (int)$_POST['id'];
        if (!$id) {
            $return = array(
                'code' => -2,
                'message' => '未找到要更新的数据'
            );
            return $this->ajaxReturn($return);
        }

        $bool = 1;
        $model = M('customerment_type');
        $item = $model->where("`id` = '{$id}'")->find();

        if (count($item) > 0) {
            $micro = (int)$_POST['micro'];
            $name = I('post.customername', '', 'strip_tags');
            $name = trim($name);
            $model->customername = $name;

            $model->id = $id;
            $model->micro = $micro;
            $model->modify_time = date('Y-m-d H:i:s', time());

            $bool = ($model->save()) ? 0 : 1;
        }

        //结果返回
        $return = array(
            'code' => $bool,
            'message' => 'success',
        );
        return $this->ajaxReturn($return);
    }

    /**
     * 软删除
     */
    public function delCustomer()
    {
        //获取提交过来的ID值并进行分割 in 查询
        $ids = implode(',', $_POST['ids']);
        $model = M('customerment_type');
        $list = $model->where(array('id'=>array('in', $ids)))->select();

        //已查到的存在的数据
        $idArr = array();
        foreach ($list as $item) {
            $idArr[] = $item['id'];
        }
        $idIn = implode(',', $idArr);

        //数据更新
        $data = array(
            'status' => self::DELETE_TRUE,
            'modify_time' => date('Y-m-d H:i:s', time())
        );
        $bool = ($model->where(array('id'=>array('in', $idIn)))->save($data)) ? 0 : 1;

        //结果返回
        $return = array(
            'code' => $bool,
            'message' => 'success',
        );
        return $this->ajaxReturn($return);
    }

    /**
     * 上下线
     */
    public function status()
    {
        //获取提交过来的ID值并进行分割 in 查询
        $id = (int)$_POST['id'];
        $model = M('customerment_type');
        $item = $model->where("`id` = '{$id}'")->find();

        if (count($item) == 0) {
            $return = array(
                'code' => -2,
                'message' => '未找到数据',
            );
            return $this->ajaxReturn($return);
        }

        //数据更新
        $data = array(
            'id' => $item['id'],
            'status' => !$item['status'],
            'modify_time' => date('Y-m-d H:i:s', time())
        );
        $bool = ($model->save($data)) ? 0 : 1;

        //结果返回
        $return = array(
            'code' => $bool,
            'message' => 'success',
        );
        return $this->ajaxReturn($return);
    }

    /**
     * 列表
     * @todo 搜索
     */
    public function searchCustomer(){

        $customer = M('star_userinfo');
        $identity = M('identity_info');
        $pageNum = I('post.pageNum', 5, 'intval');
        $page = I('post.page', 1, 'intval');

        /*
         搜索 机构等
         */
        $memberId = $_POST['memberMark'];
        $agentId = $_POST['agentMark'];
        $agentSubId = $_POST['agentSubMark'];
        $map = array();
        if($memberId){
            $map['memberId'] = $memberId;
        }

        if($agentId){
            $map['agentId'] = $agentId;
        }

        if($agentSubId){
            $map['subagentId'] = $agentSubId;
        }

        /*
         end
         */
        $startTime = I('post.startTime');
        $endTime = I('post.endTime');
        if($startTime && $endTime) {
           // $startTime = strtotime($startTime);
           // $endTime = strtotime($endTime)+(24*3600);
            $map['registerTime'] = array(array('egt',$startTime),array('elt',$endTime));
        }


        $count = $customer->where($map)->count();// 查询满足要求的总记录数
        $list = $customer->where($map)->page($page, $pageNum)->order('uid desc')->select();//获取分页数据



        $channelArr = array();
        foreach ($list as $item) {
            $uidArr[] = $item['uid'];
            if($item['channel']) {
                $channelArr[] = $item['channel']; //渠道号
            }

            $memberIds[] = $item['memberId']; // 机构 mark
            $agentSubIds[] = $item['agentId']; // 经纪人 mark

            $agentIds[] = $item['agentIdSub']; //区域经纪人 mark
        }


        $channelArr = array_filter(array_unique($channelArr));

        $whereChannel['channel'] = array('in',$channelArr);

        $channelRow = M('star_channel')->where($whereChannel)->select();



        $channelData = array();
        foreach ($channelRow as $c){
            $channelData[$c['channel']] = $c;
        }


        //实名认证用户ID
        if (isset($uidArr) && count($uidArr) > 0) {
            $uidArr = implode(',', $uidArr);
            $users = $identity->where(array('uid'=>array('in', $uidArr)))->select();
            foreach ($users as $u) {
                $userList[$u['uid']] = $u;
            }
        }




        $memberIds = array_filter(array_unique($memberIds));
        $agentIds = array_filter(array_unique($agentIds));
        $agentSubIds = array_filter(array_unique($agentSubIds));

        $whereMemberIds['mark'] = array('in',$memberIds);
        $whereAgentIds['mark'] = array('in',$agentIds);
        $whereAgentSubIds['mark'] = array('in',$agentSubIds);

        $memberRows = $this->getMemberNmae($whereMemberIds);
        $agentRows  = $this->getAgentName($whereAgentIds);
        $agentSubRows  = $this->getAgentSubName($whereAgentSubIds);


        foreach ($memberRows as $m){
            $memberData[$m['mark']]['name'] = $m['name'];
        }

        foreach ($agentRows as $ag){
            $agentData[$ag['mark']]['nickname'] = $ag['nickname'];
        }

        foreach ($agentSubRows as $a){
            $agentSubData[$a['mark']]['nickname'] = $a['nickname'];
        }



        //用户与消费数据组装
        foreach ($list as $key => $val) {
            $list[$key]['realname'] = '';
            $list[$key]['isreal'] = '否';
            $list[$key]['idcards'] = '';

            //$recommand = $customer->where('uid = ' . (int)$val['recommend'])->getField('nickname');
            //$list[$key]['recommend'] =  ($recommand) ? $recommand : '';

            //推荐人 用昵称  经纪人昵称
            $agentsubName = isset($channelData[$val['channel']])?$channelData[$val['channel']]['agentsubName']:null;
            $list[$key]['agentsubName'] = isset($agentsubName)?$agentsubName:'';


            if (isset($userList[$val['uid']])) {
                $list[$key]['realname'] = $userList[$val['uid']]['realname'];
                $list[$key]['idcards'] = $userList[$val['uid']]['id_card'];
                $list[$key]['isreal'] = '是';
            }

            $lMemberId = $val['memberId'];  //mark
            $lagentId = $val['agentId'];    // mark
            $lagentSubId = $val['subagentId'];    // mark

            //dump($val);dump($lMemberId);dump($lagentId);exit;

            $list[$key]['member'] = $memberData[$lMemberId];
            $list[$key]['agent'] = $agentSubData[$lagentId];

            $list[$key]['agent_sub'] = $agentSubData[$lagentSubId];

            $type_member = isset($list[$key]['member'])?$list[$key]['member']['name']:'';
            $type_agent = isset($list[$key]['agent'])?$list[$key]['member']['nickname']:'';
            $agent_sub = isset($list[$key]['agent_sub'])?$list[$key]['agent_sub']['nickname']:'';


            if($type_member || $type_agent || $agent_sub){
                $list[$key]['type_info'] = $type_member . ',' . $type_agent . ',' . $agent_sub;
            }else{
                $list[$key]['type_info'] = '';
            }


        }

        if($this->excel) {
            new \Think\Page($count, $pageNum);// 实例化分页类 传入总记录数和每页显示的记录数(25)
            $data['totalPages'] = $count;
            $data['pageNum'] = $pageNum;
            $data['page'] = $page;
            $data['totalPages'] = ceil($count / $pageNum);
            $data['list']  = $list;
            $data['total'] = $count;

            $this->ajaxReturn($data);
        }
        $this->excel = $list;
    }



    public function downloadExcel(){
        $this->titles = $this->titlesArr();
        $fields = $this->fieldArr();
        $this->fileName = '消费者列表';
        $this->excelModel = new \Home\Model\excelModel($this->titles,$fields,$this->fileName);

        $this->excel = 0;
        $this->searchCustomer();
        $excel =$this->excel;
        $this->excelModel->excelFile($excel);
    }

    private function titlesArr(){
        return array(
            '序号',
            '创建时间',
            '手机号',
            '姓名',
            '昵称',
            '所属机构/区域/经纪人',
            '实名认证',
            '推荐人'
        );
    }

    private function fieldArr(){
        return array(
            'uid',
            'registerTime',
            'phoneNum',
            'realname',
            'nickname',
            'type_info',
            'isreal',
            'agentsubName'
        );
    }


    private function getMemberNmae($where){
        $member_info = M('member_info');
        $memberInfo = $member_info->where($where)->select();

        return $memberInfo;
    }


    private function getAgentName($where){
        $agent_info = M('agent_info');
        return $agent_info->where($where)->select();
    }

    private function getAgentSubName($where){
        $agent_info = M('agentsub_info');
        return $agent_info->where($where)->select();
    }

    
}