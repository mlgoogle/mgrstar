<?php

//namespace Home\Controller;
namespace Home\Controller;
use Think\Controller;
//use .\..\Home\Model ;

class DataSearchController extends Controller
{

    private $user;
    private $homeModel;

    public function __construct(){
        parent::__construct();
        $user = $this->user = session('user');
        if(!$this->user){
            $this->display('Login/login');
        }

        $this->homeModel =  $article = new \Home\Model\homeModel();// D('Home/home');

        $identity_id = $user['identity_id'];

        if($identity_id<2){
            $this->assign('identity_status', 1);
        }

        $this->assign('user',$this->user);
        $this->assign('action','dataSearch');
    }

    public function fundList(){
        $this->assign('actionUrl','fundList');

        $this->display('DataSearch/fundList');
    }

    public function position(){ // 持仓
        $this->assign('actionUrl','position');

        $this->display('DataSearch/position');
    }

    public function recharge(){ //充值
        $this->assign('actionUrl','recharge');

        $this->display('DataSearch/recharge');
    }

    //交易明细
    public function transaction(){
        $this->assign('actionUrl','transaction');

        $this->display('DataSearch/transaction');
    }

    //成交明细
    public function success(){
        $this->assign('actionUrl','success');

        $this->display('DataSearch/success');
    }

    //成交明细汇总
    public function success_total(){
        $this->assign('actionUrl','success_total');

        $this->display('DataSearch/success_total');
    }


    public function getUserInfo(){
        $map = array();

        $map = $this->getIdentity();

        $user_info = M('star_userinfo');

        $pageNum = isset($_POST['pageNum'])?$_POST['pageNum']:5;
        $page = isset($_POST['page'])?$_POST['page']:1;
        $count = $user_info->where($map)->count();// 查询满足要求的总记录数

        $nickname = $_POST['nickname'];
        $phoneNum = $_POST['phoneNum'];

        if($nickname){
            $map['nickname'] = $nickname;
        }

        if($phoneNum){
            $map['phoneNum'] = $phoneNum;
        }

        $list = $user_info->where($map)->page($page,$pageNum)->select();//获取分页数据
        foreach($list as $l){
            $userIds[] = $l['uid'];
        }


        $whereSellUids['sell_uid']  =  array('in',$userIds); // sell_type   buy_type
        $whereBuyUids['sell_type'] = $whereSellUids['sell_type'] =  2 ;// 卖方完成
        $whereBuyUids['sell_type'] = $whereSellUids['buy_type'] =  2 ;// 买方完成


        $sellRows = M('star_orderlist')->where($whereSellUids)->select(); //卖方   order_price  order_num


        $whereBuyUids['buy_uid']  =  array('in',$userIds);
        $buyRows = M('star_orderlist')->where($whereBuyUids)->select(); //买方


        $sellPriceSum = array();

        foreach ($sellRows as $s){
            //
            $sellPrice =  $s['order_num']* $s['order_price'];

            $sellOrderPrice[$s['sell_uid']]['order_price'][] = $sellPrice;
            $sellPriceSum[$s['sell_uid']] = $s;
            $sellPriceSum[$s['sell_uid']]['order_price'] = $sellOrderPrice[$s['sell_uid']]['order_price'];

        }


        foreach ($sellPriceSum as $id=>$sellArr){
            $sellPriceSum[$id]['order_sum_price'] = array_sum($sellArr['order_price']);
            unset($sellPriceSum[$id]['order_price']);
        }

        $buyPriceSum = array();

        foreach ($buyRows as $b){
            $buyPrice =  $b['order_num']* $b['order_price'];

            $buyOrderPrice[$b['buy_uid']]['order_price'][] = $buyPrice;
            $buyPriceSum[$b['buy_uid']] = $b;
            $buyPriceSum[$b['buy_uid']]['order_price'] = $buyOrderPrice[$b['buy_uid']]['order_price'];
        }


        foreach ($buyPriceSum as $id=>$buyArr){
            $buyPriceSum[$id]['order_sum_price'] = array_sum($buyArr['order_price']);
            unset($buyPriceSum[$id]['order_price']);
        }

        // 用户的余额
        $whereBalance['uid']  =  array('in',$userIds);
        $balaceArr = array();

        foreach (M('user_balance')->where($whereBalance)->select() as $bArr){
            $balaceArr[$bArr['uid']] = $bArr;
        }

        //
        //冻结资金

        $wheredongUids['buy_id']  =  array('in',$userIds); // 必须是 买家
        $dongUids['sell_type']    =  array('neq',2) ;// 卖方不完成
        $dongUids['buy_type']     =   2 ;// 买方完成
        $freezeRows = M('star_orderlist')->where($dongUids)->select(); //买方

        $freezePriceSum = array();

        foreach ($freezeRows as $f){
            $freezePrice =  $f['order_num']* $f['order_price'];

            //  dump($freezePrice);

            $freezeOrderPrice[$f['buy_uid']]['order_price'][] = $freezePrice;
            $freezePriceSum[$f['buy_uid']] = $f;
            $freezePriceSum[$f['buy_uid']]['order_price'] = $freezeOrderPrice[$f['buy_uid']]['order_price'];
        }


        foreach ($freezePriceSum as $id=>$freeArr){
            $freePriceSum[$id]['order_sum_price'] = array_sum($freeArr['order_price']);
            unset($freePriceSum[$id]['order_price']);
        }

        // 总资产
        $whereTotalUids['sell_uid']  =  array('in',$userIds); // 必须是 卖家
        // $whereTotalUids['sell_type'] =   array('neq',2) ;// 卖方不完成
        // $whereTotalUids['buy_type']  =  array('neq',2) ;// 买方完成
        $whereTotalUids['_string'] = 'sell_type <> 2 OR buy_type <> 2';

        $where = array();
        $totalRows = M('star_orderlist')->where($whereTotalUids)->select(); //买方

        foreach ($totalRows as $t){
            $totalPrice =  $t['order_num']* $t['order_price'];
            $totalOrderPrice[$t['sell_uid']]['order_price'][] = $totalPrice;

            $totalOrderPriceArr[$t['sell_uid']] = $t;
            $totalOrderPriceArr[$t['sell_uid']]['order_price'] = $totalOrderPrice[$t['sell_uid']]['order_price'];
        }


        foreach ($totalOrderPriceArr as $id=>$totalArr){
            $totalSellArr[$id]['order_sum_price'] = array_sum($totalArr['order_price']);
            // unset($sellPriceSum[$id]['order_price']);
        }



        foreach ($list as $key=>$l){
            $listAll[$key] = $l;
            $listAll[$key]['sell_info'] = $sellPriceSum[$l['uid']];// 卖家  ：收入
            $listAll[$key]['buy_info']  = $buyPriceSum[$l['uid']];//买家 ：支出

            $listAll[$key]['total_info'] = $totalSellArr[$l['uid']]; // 卖家时,没成功的为资产

            $listAll[$key]['balance_info'] = $balaceArr[$l['uid']]; // 余额资金

            $listAll[$key]['freeze_info'] = $freePriceSum[$l['uid']]; // 可用资金
        }


        $data['totalPages'] = $count;
        $data['pageNum'] =$pageNum;
        $data['page'] = $page;
        $data['totalPages'] = ceil($count/$pageNum);
        $data['list'] = $listAll;

        $this->ajaxReturn($data);
    }

    public function  getUserOrderinfo(){
        $map = array();
        $user_info = M('star_userinfo');

        $map = $this->getIdentity();

        $pageNum = isset($_POST['pageNum'])?$_POST['pageNum']:5;
        $page = isset($_POST['page'])?$_POST['page']:1;

        $nickname = $_POST['nickname'];
        $phoneNum = $_POST['phoneNum'];

        if($nickname){
            $map['nickname'] = $nickname;
        }

        if($phoneNum){
            $map['phoneNum'] = $phoneNum;
        }

        $count = $user_info->where($map)->count();// 查询满足要求的总记录数

        $list = $user_info->where($map)->page($page,$pageNum)->select();//获取分页数据

        $userIds = array();

        foreach($list as $l){
            $userIds[]   = $l['uid'];
            $memberIds[] = $l['memberId']; // 机构 mark
            $agentSubIds[] = $l['agentId']; // 经纪人 mark

            $agentIds[] = $l['agentIdSub']; //区域经纪人 mark
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



        $whereOrderUids['buy_uid']  =  array('in',$userIds); //买方uids
        $whereOrderUids['sell_type'] =  2 ;// 卖方完成
        $whereOrderUids['buy_type'] =  2 ;// 买方完成

        $finishedbuyRows = $this->getBuyRows($whereOrderUids);

        $whereunfinishedUids['buy_uid']  =  array('in',$userIds);
        $whereunfinishedUids['_string'] = 'sell_type <> 2 OR buy_type <> 2';
        $unfinishedBuyRows = $this->getBuyRows($whereunfinishedUids);


        foreach ($list as $l){
            $lists[$l['uid']] = $l;


            $lMemberId = $l['memberId'];  //mark
            $lagentId = $l['agentId'];    // mark
            $lagentSubId = $l['subagentId'];    // mark

            $lists[$l['uid']]['member'] = $memberData[$lMemberId];
            $lists[$l['uid']]['agent'] = $agentSubData[$lagentId];

            $lists[$l['uid']]['agent_sub'] = $agentSubData[$lagentSubId];


            $lists[$l['uid']]['finished_buy_price'] = $finishedbuyRows[$l['uid']];
            //$lists[$l['uid']]['finished_buy_price']['nums'] = count($finishedbuyRows[$l['uid']]);
            //$lists[$l['uid']]['starcode'] = $finishedbuyRows[$l['uid']]['starcode'];

            foreach ($finishedbuyRows[$l['uid']] as $fd){
                $lists[$l['uid']]['finished_buy_price'][$fd['starcode']]['un_order_num'] =  $unfinishedBuyRows[$l['uid']][$fd['starcode']]['order_num'];
            }

            // $lists[$l['uid']]['unfinished_buy_price']= $unfinishedBuyRows[$l['uid']];
            //$lists[$l['uid']]['unfinished_buy_price']['nums'] = count($unfinishedBuyRows[$l['uid']]);
        }

        $data['totalPages'] = $count;
        $data['pageNum'] =$pageNum;
        $data['page'] = $page;
        $data['totalPages'] = ceil($count/$pageNum);
        $data['list'] = $lists;

        $this->ajaxReturn($data);

    }

    //充值
    public function getRechargeInfo(){
        $map = array();
        $user_info = M('star_userinfo');

        $map = $this->getIdentity();

        $pageNum = isset($_POST['pageNum'])?$_POST['pageNum']:5;
        $page = isset($_POST['page'])?$_POST['page']:1;

        $nickname = $_POST['nickname'];
        $phoneNum = $_POST['phoneNum'];

        if($nickname){
            $map['nickname'] = $nickname;
        }

        if($phoneNum){
            $map['phoneNum'] = $phoneNum;
        }

        $count = $user_info->where($map)->count();// 查询满足要求的总记录数

        $list = $user_info->where($map)->page($page,$pageNum)->select();//获取分页数据

        foreach ($list as $l){
            $userUids[] = $l['uid'];
            $memberIds[] = $l['memberId']; // 机构
            $agentSubIds[] = $l['agentId']; // 经纪人
            $agentIds[] = $l['agentIdSub']; //区域经纪人 mark
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


        $recharge_info = M('recharge_info');

        $whereIds['uid'] = array('in',$userUids);

        $whereIds['status'] = 1;

        $rechargeRows = $recharge_info->field('sum(amount) as amount_sum,uid,depositType')->where($whereIds)->group('uid')->select();

        //充值类型 1:微信 2:银行卡
        $depositArr = array('未知','微信','银行卡');

        foreach ($rechargeRows as $w){
            $w['deposit_name'] = $depositArr[$w['depositType']];
            $rechargeData[$w['uid']] = $w;

        }

        foreach ($list as $l) {
            $lists[$l['uid']] = $l;

            $lMemberId = $l['memberId'];  //mark
            $lagentId = $l['agentId'];    // mark
            $lagentSubId = $l['subagentId'];    // mark

            $lists[$l['uid']]['member'] = $memberData[$lMemberId];
            $lists[$l['uid']]['agent'] = $agentSubData[$lagentId];

            $lists[$l['uid']]['agent_sub'] = $agentSubData[$lagentSubId];

            $lists[$l['uid']]['recharge'] = $rechargeData[$w['uid']];
        }

        $data['totalPages'] = $count;
        $data['pageNum'] =$pageNum;
        $data['page'] = $page;
        $data['totalPages'] = ceil($count/$pageNum);
        $data['list'] = $lists;

        $this->ajaxReturn($data);
    }

    //交易  失败的订单

    public function getTransactionInfo(){

        //$this->getIdentity();

        $star_orderlist = M('star_orderlist');

        $status = (int)$_POST['status'];

        $whereOreder['order_type'] = -1;

        $pageNum = isset($_POST['pageNum'])?$_POST['pageNum']:5;
        $page = isset($_POST['page'])?$_POST['page']:1;

        $startTime = I('post.startTime');
        $endTime = I('post.endTime');
        if($startTime && $endTime) {
            $startTime = strtotime($startTime);
            $endTime = strtotime($endTime)+(24*3600);
            $whereOreder['close_time'] = array('between', $startTime . ',' . $endTime);
        }

        $count = $star_orderlist->where($whereOreder)->count();// 查询满足要求的总记录数


        $list = $star_orderlist->where($whereOreder)->page($page,$pageNum)->select();

        foreach ($list as $l){
            $buyUid[] = $l['buy_uid'];
            $sellUid[] = $l['sell_uid'];
        }


//        $uids = array_merge($sellUid,$buyUid);
//        $uids = array_filter(array_unique($uids));

        $map = array();

        $user_info = M('star_userinfo');

        if($status == 1){  //买方
            $uids = array_merge($buyUid);
            $uids = array_filter(array_unique($uids));
        }else if($status == 2){ // 卖方
            $uids = array_merge($sellUid);
            $uids = array_filter(array_unique($uids));
        }else{
            return false;
        }

        $map = $this->getIdentity();

        //dump($map);exit;
        $map['uid'] = array('in',$uids);


        $userRows = $user_info->where($map)->select();//获取分页数据

        foreach ($userRows as $u){
            $userUids[] = $u['uid'];
            $memberIds[] = $u['memberId']; // 机构 mark
            $agentIds[] = $u['agentId']; // 区域经纪人 mark
            $agentSubIds[] = $u['subagentId']; // 经纪人 mark

            $userInfo[$u['uid']] = $u;
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


        foreach ($list as $l){
            $lists[$l['id']] = $l;


            $sellUid = $l['sell_uid'];
            $buyUid  = $l['buy_uid'];

            if($status == 1){  //买方
                $listUid = $buyUid;
            }else if($status == 2){ // 卖方
                $listUid = $sellUid;
            }else{
                return false;
            }

            $lists[$l['id']]['name'] = isset($userInfo[$listUid]['nickname'])?$userInfo[$listUid]['nickname']:'';
            $lists[$l['id']]['phone'] = isset($userInfo[$listUid]['phoneNum'])?$userInfo[$listUid]['phoneNum']:'';

            //$lists[$l['id']]['buy_name'] = isset($userInfo[$buyUid]['nickname'])?$userInfo[$buyUid]['nickname']:'';
           // $lists[$l['id']]['buy_phone'] = isset($userInfo[$buyUid]['phoneNum'])?$userInfo[$buyUid]['phoneNum']:'';

            $lMemberId = $userInfo[$listUid]['memberId'];  //mark
            $lagentId = $userInfo[$listUid]['agentId'];    // mark
            $lagentSubId = $userInfo[$listUid]['subagentId'];    // mark

            $lists[$l['id']]['member'] = $memberData[$lMemberId];
            $lists[$l['id']]['agent'] = $agentSubData[$lagentId];

            $lists[$l['id']]['agent_sub'] = $agentSubData[$lagentSubId];
        }


        $data['totalPages'] = $count;
        $data['pageNum'] =$pageNum;
        $data['page'] = $page;
        $data['totalPages'] = ceil($count/$pageNum);
        $data['list'] = $lists;

        $data['status'] = $status;

        $this->ajaxReturn($data);

    }

    //交易成功

    public function getSuccessInfo(){

        $status = (int)$_POST['status'];
        $star_orderlist = M('star_orderlist');

        $whereOreder['order_type'] = 2;

        $pageNum = isset($_POST['pageNum'])?$_POST['pageNum']:5;
        $page = isset($_POST['page'])?$_POST['page']:1;

        $startTime = I('post.startTime');
        $endTime = I('post.endTime');
        if($startTime && $endTime) {
            $startTime = strtotime($startTime);
            $endTime = strtotime($endTime)+(24*3600);
            $whereOreder['close_time'] = array('between', $startTime . ',' . $endTime);
        }


        $count = $star_orderlist->where($whereOreder)->count();// 查询满足要求的总记录数

        $list = $star_orderlist->where($whereOreder)->page($page,$pageNum)->select();

        foreach ($list as $l){
            $buyUid[] = $l['buy_uid'];
            $sellUid[] = $l['sell_uid'];
        }



        if($status == 1){  //买方
            $uids = array_merge($buyUid);
            $uids = array_filter(array_unique($uids));
        }else if($status == 2){ // 卖方
            $uids = array_merge($sellUid);
            $uids = array_filter(array_unique($uids));
        }else{
            return false;
        }


        $map = array();
        $user_info = M('star_userinfo');

        $map = $this->getIdentity();

        $map['uid'] = array('in',$uids);

        $userRows = $user_info->where($map)->select();//获取分页数据

        foreach ($userRows as $u){
            $userUids[] = $u['uid'];
            $memberIds[] = $u['memberId']; // 机构 mark
            $agentIds[] = $u['agentId']; // 区域经纪人 mark
            $agentSubIds[] = $u['subagentId']; // 经纪人 mark

            $userInfo[$u['uid']] = $u;
        }


        foreach ($list as $l){
            $lists[$l['id']] = $l;
            $sellUid = $l['sell_uid'];
            $buyUid  = $l['buy_uid'];

            if($status == 1){  //买方
                $listUid = $buyUid;
            }else if($status == 2){ // 卖方
                $listUid = $sellUid;
            }else{
                return false;
            }

            $lists[$l['id']]['name'] = isset($userInfo[$listUid]['nickname'])?$userInfo[$listUid]['nickname']:'';
            $lists[$l['id']]['phone'] = isset($userInfo[$listUid]['phoneNum'])?$userInfo[$listUid]['phoneNum']:'';

            //dump($lists[$l['id']]['name']);dump($listUid);dump($lists);exit;

           // $lists[$l['id']]['buy_name'] = isset($userInfo[$buyUid]['nickname'])?$userInfo[$buyUid]['nickname']:'';
           // $lists[$l['id']]['buy_phone'] = isset($userInfo[$buyUid]['phoneNum'])?$userInfo[$buyUid]['phoneNum']:'';


            $lists[$l['id']]['order_total'] = $l['order_num']*$l['order_price'];

            $lists[$l['id']]['close_time'] = date('Y-m-d H:i:s',$l['close_time']);
        }



        $data['totalPages'] = $count;
        $data['pageNum'] =$pageNum;
        $data['page'] = $page;
        $data['totalPages'] = ceil($count/$pageNum);
        $data['list'] = $lists;
        $data['status'] = $status;

        $this->ajaxReturn($data);


    }

    //交易成功 汇总
    public function getSuccessTotalInfo(){  //1498103354
        $star_orderlist = M('star_orderlist');
        $whereOreder = array();

        $startTime = I('post.startTime');
        $endTime = I('post.endTime');
        if($startTime && $endTime) {
            $startTime = strtotime($startTime);
            $endTime = strtotime($endTime)+(24*3600);
            $whereOreder['close_time'] = array('between', $startTime . ',' . $endTime);
        }

        $whereOreder['order_type'] = 2;

        $pageNum = isset($_POST['pageNum'])?$_POST['pageNum']:5;
        $page = isset($_POST['page'])?$_POST['page']:1;

        $obj = $selectObj = $star_orderlist->field('id,FROM_UNIXTIME(close_time,\'%Y-%m-%d\') as days,count(id) as count_num,starcode,order_num,order_price')->where($whereOreder)->group('starcode,days');


       // $lister = $obj->order('days desc')->page($page,$pageNum)->select();


        $groupSql = $obj->buildSql();
        $count = D()->table("{$groupSql} as t")->count();

        $list = D()->table("{$groupSql} as t")->order('days desc')->page($page,$pageNum)->select();

        //dump(D()->table("{$groupSql} as t")->_sql());

        $data['totalPages'] = $count;
        $data['pageNum'] =$pageNum;
        $data['page'] = $page;
        $data['totalPages'] = ceil($count/$pageNum);
        $data['list'] = $list;

        $this->ajaxReturn($data);
    }

    private function getBuyRows($where){

        $orederRows = M('star_orderlist')->field(' sum(order_num) as nums , starcode,buy_uid ')->where($where)->group('starcode')->select(); //买方

        // dump($orederRows);

        foreach ($orederRows as $o){
            $rows[$o['buy_uid']][$o['starcode']]['order_num'] = $o['nums'];
            $rows[$o['buy_uid']][$o['starcode']]['starcode'] = $o['starcode'];
        }


        // $rowArr[$o['buy_uid']]['order_num'] = array_sum($rows[$o['buy_uid']]['order_num']);
        // $rowArr[$o['buy_uid']]['starcode'] = $rows[$o['buy_uid']]['starcode'];

        return $rows;
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

    private function getAgentSubName(){
        $agent_info = M('agentsub_info');
        return $agent_info->where($where)->select();
    }

    private function userInfoUids(){
        $user_info = M('star_userinfo');

        $pageNum = isset($_POST['pageNum'])?$_POST['pageNum']:5;
        $page = isset($_POST['page'])?$_POST['page']:1;
        $count = $user_info->where($map)->count();// 查询满足要求的总记录数

        $list = $user_info->where($map)->page($page,$pageNum)->select();//获取分页数据

        $userIds = array();

        foreach($list as $l){
            $userIds[] = $l['uid'];
        }

        return $userIds;
    }

    private function getIdentity($T = false){

        return $this->homeModel->getUserInfoIdentity($T);
    }

}