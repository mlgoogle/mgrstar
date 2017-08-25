<?php
namespace Home\Controller;
use Think\Controller;
//use Think\Controller\restController;
class ProfitController extends CTController{

    const MAX_NUMBER = 1000; // 最大比值
    private $withdrawalsModel;

    public function __construct(){
        $this->withdrawalsModel = new \Home\Model\withdrawalsModel();
        parent::__construct();
    }

    //经纪人佣金
    public function commision(){
        $user = $this->user;
        $identityId = $user['identity_id'];

        if($identityId == 4) { //经纪人可访问
            $this->assign('title', '经纪人佣金');
            $this->getBankcardAdminInfo();
            $this->display('profit/commision');

        }else {
            $this->assign('title', '错误信息');
            $this->assign('message', '非经纪人不可访问');
            $this->display('err/error');
        }

    }

    //经销商(区域经纪人)佣金
    public function agentCommision(){
        $user = $this->user;
        $identityId = $user['identity_id'];

        if($identityId == 3) { //区域经纪人可访问
            $this->assign('title', '经销商佣金');
            $this->getBankcardAdminInfo();
            $this->display('profit/agentCommision');
        }else{
            $this->assign('title', '错误信息');
            $this->assign('message', '非经销商不可访问');
            $this->display('err/error');
        }
        return false;
    }

    //机构佣金
    public function memberCommision(){
        $user = $this->user;
        $identityId = $user['identity_id'];

        if($identityId == 2) { //区域经纪人可访问
            $this->assign('title', '机构佣金');
            $this->getBankcardAdminInfo();
            $this->display('profit/memberCommision');
        }else{
            $this->assign('title', '错误信息');
            $this->assign('message', '非机构不可访问');
            $this->display('err/error');
        }
        return false;
    }

    /**
     * @ 佣金管理-经纪人
     */
    public function subAgentProfit(){
        $channelModel = M('star_channel');
        $userinfoModel = M('star_userinfo');
        $orderlistModel = M('star_orderlist');

        $user = $this->user ;

        $agentSubId = $user['agentSubId'];
        $identityId = $user['identity_id'];

        $data['list'] = array();
        if($identityId == 4) {
            $map['id'] = $agentSubId;

            $list = M('agentsub_info')->where($map)->select();

            $channelMap['agentsubId'] = $agentSubId;

            $channelArr =  $channelModel->field('channel')->where($channelMap)->find();

            $userinfoMap['channel'] =  $channel = isset($channelArr['channel'])?trim($channelArr['channel']):'';
            if($channel) {
                $userInfoArr = $userinfoModel->field('uid')->where($userinfoMap)->find();
            }

            $orderlistMap['sell_uid'] =  $uid = isset($userInfoArr['uid'])?trim($userInfoArr['uid']):0;

            $order_num = $order_sum_price = 0;
            if($uid){
                $whereOreder['order_type'] = 2;
                $orderlistArr = $orderlistModel->where($orderlistMap)->select();
                foreach ($orderlistArr as $o){
                    $order_num  += $o['order_num'];
                    $order_sum_price += $o['order_num']*$o['order_price'];
                }

            }

            foreach ($list as $k=>$v) {
                $list[$k]['order_num'] = $order_num;
                $list[$k]['order_sum_price'] = $order_sum_price;
                $list[$k]['profit_price'] = $order_sum_price/self::MAX_NUMBER;
            }


            $data['list'] = $list;
        }

        $this->ajaxReturn($data);
    }


    /**
     * @ 佣金管理-经销商(区域经纪人)
     */
    public function agentProfit(){
        $channelModel = M('star_channel');
        $userinfoModel = M('star_userinfo');
        $orderlistModel = M('star_orderlist');
        $profitSummaryModel = M('profit_summary');

        $user = $this->user ;

        $agentId = $user['agentId'];
        $identityId = $user['identity_id'];

        $data['list'] = array();
        if($identityId == 3) {
            $map['agentId'] = $agentId;

            $list = M('agentsub_info')->where($map)->select();

            $agentSubIdArr = array();
            foreach ($list as $l){
                $agentSubIdArr[] = $l['id'];
            }

            $channelMap['agentsubId'] = array('in',$agentSubIdArr);

            $channelArr =  $channelModel->field('channel,agentsubId')->where($channelMap)->select();


            $channelNumberArr = $channelAgentsubIdArr=array();
            foreach ($channelArr as $c){
                $channelNumberArr[] = $c['channel'];
                $channelAgentsubIdArr[$c['agentsubId']] = $c['channel'];
            }


            foreach ($list as $k=>$l){
                $list[$k]['channel'] = $channelAgentsubIdArr[$l['id']];
            }


            $userinfoMap['channel'] =  array('in',$channelNumberArr);
            $profitSummaryArr = $profitSummaryModel->where($userinfoMap)->select();

            
            $profitSummaryChannelArr = array();
            foreach ($profitSummaryArr as $p){
                $order_num = isset($p['order_num'])?intval($p['order_num']):0;

                $order_price = isset($p['order_price'])?intval($p['order_price']):0;

                $profitSummaryChannelArr[$p['channel']]['channel'] = $p['channel'];
                $profitSummaryChannelArr[$p['channel']]['sum_num'][] = $order_num;
                $profitSummaryChannelArr[$p['channel']]['sum_price'][] = $order_price;
            }


            $profit_price = 0;
            foreach ($list as $k=>$v) {

                $sum_num = array_sum($profitSummaryChannelArr[$v['channel']]['sum_num']);
                $sum_price = array_sum($profitSummaryChannelArr[$v['channel']]['sum_price']);
                $order_sum_price = $sum_num* $sum_price;

                $list[$k]['order_num'] = $sum_num;
                $list[$k]['order_sum_price'] = $order_sum_price;
                $profit_price += $order_sum_price/self::MAX_NUMBER;
                $list[$k]['profit_price'] = $order_sum_price/self::MAX_NUMBER;

                $list[$k]['profit_sum_price'] = $profit_price;
            }


            $data['list'] = $list;
        }

        $this->ajaxReturn($data);
    }

    /**
     * @ 佣金管理-机构
     */
    public function memberProfit(){
        $channelModel = M('star_channel');
//        $userinfoModel = M('star_userinfo');
//        $orderlistModel = M('star_orderlist');
        $profitSummaryModel = M('profit_summary');

        $user = $this->user ;



        $memberId = $user['memberId'];
        $identityId = $user['identity_id'];

        $data['list'] = array();
        if($identityId == 2) {
            $map['memberId'] = $memberId;
            $list = M('agentsub_info')->where($map)->select();


            $agentSubIdArr = array();
            foreach ($list as $l){
                $agentSubIdArr[] = $l['id'];
            }

            $channelMap['agentsubId'] = array('in',$agentSubIdArr);

            $channelArr =  $channelModel->field('channel,agentsubId')->where($channelMap)->select();


            $channelNumberArr = $channelAgentsubIdArr=array();
            foreach ($channelArr as $c){
                $channelNumberArr[] = $c['channel'];
                $channelAgentsubIdArr[$c['agentsubId']] = $c['channel'];
            }


            foreach ($list as $k=>$l){
                $list[$k]['channel'] = $channelAgentsubIdArr[$l['id']];
            }


            $userinfoMap['channel'] =  array('in',$channelNumberArr);
            $profitSummaryArr = $profitSummaryModel->where($userinfoMap)->select();


            $profitSummaryChannelArr = array();
            foreach ($profitSummaryArr as $p){
                $order_num = isset($p['order_num'])?intval($p['order_num']):0;

                $order_price = isset($p['order_price'])?intval($p['order_price']):0;

                $profitSummaryChannelArr[$p['channel']]['channel'] = $p['channel'];
                $profitSummaryChannelArr[$p['channel']]['sum_num'][] = $order_num;
                $profitSummaryChannelArr[$p['channel']]['sum_price'][] = $order_price;
            }


            $profit_price = 0;
            foreach ($list as $k=>$v) {

                $sum_num = array_sum($profitSummaryChannelArr[$v['channel']]['sum_num']);
                $sum_price = array_sum($profitSummaryChannelArr[$v['channel']]['sum_price']);
                $order_sum_price = $sum_num* $sum_price;

                $list[$k]['order_num'] = $sum_num;
                $list[$k]['order_sum_price'] = $order_sum_price;
                $profit_price += $order_sum_price/self::MAX_NUMBER;
                $list[$k]['profit_price'] = $order_sum_price/self::MAX_NUMBER;

                $list[$k]['profit_sum_price'] = $profit_price;
            }


            $data['list'] = $list;
        }

        $this->ajaxReturn($data);
    }

    public function addBank(){
        $data = array();

        $bankcardAdminInfoModel = M('bankcard_admin_info');
        $data = $this->getBank();

        $bool = $bankcardAdminInfoModel->add($data);

        if($bool){
            $return = array(
                'code' => 0,
                'message' => '添加成功！',
            );
            return $this->ajaxReturn($return);
        }else{
            $return = array(
                'code' => -2,
                'message' => '添加失败！',
            );
            return $this->ajaxReturn($return);
        }
    }


    public function editBank(){
        $data = array();
        $bankcardAdminInfoModel = M('bankcard_admin_info');

        $id = I('post.id',0,'intval');

        if(empty($id)){
            $return = array(
                'code' => -2,
                'message' => '非法操作！',
            );
            return $this->ajaxReturn($return);
        }

        $data = $this->getBank();

        unset($data['adminId']);

        $bool = $bankcardAdminInfoModel->where(array('id'=>$id))->save($data);

        if($bool){
            $return = array(
                'code' => 0,
                'message' => '修改成功！',
            );
            return $this->ajaxReturn($return);
        }else{
            $return = array(
                'code' => -2,
                'message' => '修改失败！',
            );
            return $this->ajaxReturn($return);
        }


    }

    public function withdrawals(){
        $bankAccount = I('post.bankAccount',0,'intval');
        $bankSum = I('post.bankSum',0,'intval');
        $bankName = I('post.bankName','','string');

        if(empty($bankSum)){
            $return = array(
                'code' => -2,
                'message' => '提现金额不足一分！',
            );
            return $this->ajaxReturn($return);
        }

        if(empty($bankAccount)){
            $return = array(
                'code' => -2,
                'message' => '银行卡号不存在！',
            );
            return $this->ajaxReturn($return);
        }


        $returnAjax = $this->withdrawalsModel->putWithdrawals($bankAccount,$bankSum,$bankName);

        if($returnAjax['code'] == -2){
            return $this->ajaxReturn($returnAjax);
        }

        if(empty($returnAjax['respDesc']) ){
            $return = array(
                'code' => 0,
                'message' => '提现成功！',
                'withdrawals'=>$returnAjax,
            );
        }else{
            $return = array(
                'code' => -2,
                'message' => $returnAjax['respDesc'],
                'withdrawals'=>$returnAjax,
            );
        }

        return $this->ajaxReturn($return);
    }

    // 回调 地址
    public function notifyUrl(){
        return $this->ajaxReturn($this->withdrawalsModel->notifyUrl());
    }


    private function getBank(){
        $bankCardBinModel = M('bankcard_bin');
        $bankInfoModel = M('bank_info');

        $bankPersonName = I('post.bankPersonName','','trim'); //持卡人名字
        $bankAccount = I('post.bankAccount','','intval');//卡号

        $bankAccountBin = substr($bankAccount,0,6);

        $bankCardBinArr = $bankCardBinModel->field('bankId,bankcardName')->where(array('bin'=>$bankAccountBin))->find();

        if(empty($bankCardBinArr)){
            $return = array(
                'code' => -2,
                'message' => '非法银行卡！',
            );
            return $this->ajaxReturn($return);
        }



        $bankId = isset($bankCardBinArr['bankId'])?intval($bankCardBinArr['bankId']):0;
        $bankcardName = isset($bankCardBinArr['bankcardName'])?trim($bankCardBinArr['bankcardName']):''; //   支行名称

        if(empty($bankId)){
            $return = array(
                'code' => -2,
                'message' => '非法银行卡！',
            );
            return $this->ajaxReturn($return);
        }

        $bankInfoArr = $bankInfoModel->field('bankName')->where(array('bankId'=>$bankId))->find();

        $data['bankName']       = isset($bankInfoArr['bankName'])?trim($bankInfoArr['bankName']):'';
        $data['bankPersonName'] = $bankPersonName ;
        $data['bankAccount']    = $bankAccount ;
        $data['adminId']        = $this->user['id'];
        $data['bankId']         = $bankId ;

        return $data;
    }


    private function getBankcardAdminInfo(){
        $adminId = $this->user['id'];

        $bankcardAdminInfoModel = M('bankcard_admin_info');

        $bankAdminInfoArr = $bankcardAdminInfoModel->where(array('adminId'=>$adminId))->find();

        if($bankAdminInfoArr){
            $this->assign('bankAdminInfoArr',$bankAdminInfoArr);
            $this->assign('bankAdminTitle','更换银行卡');
        }else{
            $this->assign('bankAdminInfoArr',array());
            $this->assign('bankAdminTitle','绑定银行卡');
        }


    }

}


