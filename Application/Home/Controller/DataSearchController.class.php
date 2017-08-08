<?php

//namespace Home\Controller;
namespace Home\Controller;
use Think\Controller;
//use .\..\Home\Model ;

class DataSearchController extends Controller
{

    private $user;
    private $homeModel;
    private $excel;
    private $fileName;
    protected $titles = array();
    protected $excelModel;


    public function __construct(){
        parent::__construct();
        $user = $this->user = session('user');
        if(!$this->user){
            $this->display('Login/login');
        }

        $this->homeModel =  $article = new \Home\Model\homeModel();// D('Home/home');
        $this->excel = 1 ;

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

        $nickname = $_POST['nickname'];
        $phoneNum = $_POST['phoneNum'];

        /*
         搜索 机构等
         */
        $memberId = $_POST['memberMark'];
        $agentId = $_POST['agentMark'];
        $agentSubId = $_POST['agentSubMark'];


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

        if($nickname){
            $map['nickname'] = $nickname;
        }

        if($phoneNum){
            $map['phoneNum'] = $phoneNum;
        }

        $count = $user_info->where($map)->count();// 查询满足要求的总记录数

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

        foreach (M('user_balance')->field('uid,balance')->where($whereBalance)->select() as $bArr){
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
            $sell_price = $listAll[$key]['sell_info']['order_sum_price'];
            $sell_price = $listAll[$key]['sell_sum_price'] = isset($sell_price)?$sell_price:0;

            $listAll[$key]['buy_info']  = $buyPriceSum[$l['uid']];//买家 ：支出
            $buy_price = $listAll[$key]['buy_info']['order_sum_price'];
            $buy_price = $listAll[$key]['buy_sum_price'] = isset($buy_price)?$buy_price:0;


            $listAll[$key]['total_info'] = $totalSellArr[$l['uid']]; // 卖家时,没成功的为资产
            $total_sum_price = $listAll[$key]['total_info']['order_sum_price'];
            $total_sum_price = $listAll[$key]['total_sum_price'] = isset($total_sum_price)?$total_sum_price:0;

            $listAll[$key]['balance_info'] = $balaceArr[$l['uid']]; // 余额资金
            $balance = isset($listAll[$key]['balance_info']['balance'])?$listAll[$key]['balance_info']['balance']:0;
            $listAll[$key]['balance'] = is_float($balance)?sprintf('%.3f', $balance):$balance;

            $listAll[$key]['freeze_info'] = $freePriceSum[$l['uid']]; // 可用资金
            $order_sum_price = $listAll[$key]['freeze_info']['order_sum_price'];//冻结资金
            $order_sum_price = isset($order_sum_price)?$order_sum_price:0;
            $listAll[$key]['order_sum_price']  = sprintf('%.3f', $order_sum_price);


            $listAll[$key]['status_name'] = ((int)$sell_price -  (int)$buy_price < 0)?'亏':'赢';

            $total = $total_sum_price + $buy_price ;
            $listAll[$key]['total'] = is_float($total)?sprintf('%.3f', $total):$total;

    }


        if($this->excel) {
            $data['totalPages'] = $count;
            $data['pageNum'] = $pageNum;
            $data['page'] = $page;
            $data['totalPages'] = ceil($count / $pageNum);
            $data['list'] = $listAll;

            $this->ajaxReturn($data);
        }

        $this->excel = $listAll;
    }

    public function  getUserOrderinfo(){
        $map = array();
        $user_info = M('star_userinfo');

        $map = $this->getIdentity();

        $pageNum = isset($_POST['pageNum'])?$_POST['pageNum']:5;
        $page = isset($_POST['page'])?$_POST['page']:1;

        $nickname = $_POST['nickname'];
        $phoneNum = $_POST['phoneNum'];

        /*
         搜索 机构等
         */
        $memberId = $_POST['memberMark'];
        $agentId = $_POST['agentMark'];
        $agentSubId = $_POST['agentSubMark'];

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

            //$lists[$l['uid']]['finished_buy_price'] = $finishedbuyRows[$l['uid']];
            $starcode = $lists[$l['uid']]['starcode'] = $finishedbuyRows[$l['uid']]['starcode'];
            $starname =  $lists[$l['uid']]['starname'] = $finishedbuyRows[$l['uid']]['starname'];
            $lists[$l['uid']]['order_num'] = (int)$finishedbuyRows[$l['uid']]['order_num'];
            $lists[$l['uid']]['un_order_num'] = (int)$unfinishedBuyRows[$l['uid']]['un_order_num'];

            if($starcode || $starname) {
                $lists[$l['uid']]['starcodename'] = $starcode . ' / ' . $starname;
            }else{
                $lists[$l['uid']]['starcodename'] = '';
            }

            $type_member = isset($lists[$l['uid']]['member'])?$lists[$l['uid']]['member']['name']:'';
            $type_agent = isset($lists[$l['uid']]['agent'])?$lists[$l['uid']]['member']['nickname']:'';
            $agent_sub = isset($lists[$l['uid']]['agent_sub'])?$lists[$l['uid']]['agent_sub']['nickname']:'';

            if($type_member || $type_agent || $agent_sub){
                $lists[$l['uid']]['type_info'] = $type_member . ',' . $type_agent . ',' . $agent_sub;
            }else{
                $lists[$l['uid']]['type_info'] = '';
            }


            // $lists[$l['uid']]['unfinished_buy_price']= $unfinishedBuyRows[$l['uid']];
            //$lists[$l['uid']]['unfinished_buy_price']['nums'] = count($unfinishedBuyRows[$l['uid']]);
        }

        if($this->excel) {
            $data['totalPages'] = $count;
            $data['pageNum'] = $pageNum;
            $data['page'] = $page;
            $data['totalPages'] = ceil($count / $pageNum);
            $data['list'] = $lists;

            $this->ajaxReturn($data);
        }

        $this->excel = $lists;

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

        /*
         搜索 机构等
         */
        $memberId = $_POST['memberMark'];
        $agentId = $_POST['agentMark'];
        $agentSubId = $_POST['agentSubMark'];

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

        $whereIds['status'] = 3; // 3-支付服务端成功

        $rechargeRows = $recharge_info->field('sum(amount) as amount_sum,uid,depositType')->where($whereIds)->group('uid')->select();

        //充值类型 1:是微信 2:是银联 3:是支付宝
        $depositArr = array('未知','微信','银联','支付宝');

        foreach ($rechargeRows as $w){
            $w['deposit_name'] = isset($depositArr[$w['depositType']])?$depositArr[$w['depositType']]:'未知';
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

            $lists[$l['uid']]['recharge']= $rechargeData[$l['uid']];
            $lists[$l['uid']]['deposit_name'] = isset($rechargeData[$l['uid']]['deposit_name'])?$rechargeData[$l['uid']]['deposit_name']:'未知';
            $lists[$l['uid']]['amount_sum'] = isset($rechargeData[$l['uid']]['amount_sum'])?$rechargeData[$l['uid']]['amount_sum']:0;


            $type_member = isset($lists[$l['uid']]['member'])?$lists[$l['uid']]['member']['name']:'';
            $type_agent  = isset($lists[$l['uid']]['agent'])?$lists[$l['uid']]['agent']['nickname']:'';
            $type_agent_sub  = isset($lists[$l['uid']]['agent_sub'])?$lists[$l['uid']]['agent_sub']['nickname']:'';

            if($type_member || $type_agent || $type_agent_sub){
                $lists[$l['uid']]['type_info'] =  $type_member . ',' .  $type_agent . ',' .  $type_agent_sub ;
            }else{
                $lists[$l['uid']]['type_info'] = '';
            }

        }



        if($this->excel) {
            $data['totalPages'] = $count;
            $data['pageNum'] = $pageNum;
            $data['page'] = $page;
            $data['totalPages'] = ceil($count / $pageNum);
            $data['list'] = $lists;

            $this->ajaxReturn($data);
        }else{
            $this->excel = $lists;
        }
    }

    //交易  失败的订单

    public function getTransactionInfo(){
        $status = (int)$_POST['status'];
        /*
         搜索 机构等
         */
        $memberId = $_POST['memberMark'];
        $agentId = $_POST['agentMark'];
        $agentSubId = $_POST['agentSubMark'];

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

        $user_info = M('star_userinfo');

        $uidArr = $user_info->field('uid')->where($map)->select();
        foreach ($uidArr as $uid){
             $newUids[] = $uid['uid'];
        }

        $newUids = array_merge($newUids);
        $newUids = array_filter(array_unique($newUids));

        if($status == 1){  //买方

            $whereOreder['buy_uid'] = array('in',$newUids);
        }else if($status == 2){ // 卖方
            $whereOreder['sell_uid'] = array('in',$newUids);
        }

        //$this->getIdentity();

        $star_orderlist = M('star_orderlist');

        $whereOreder['order_type'] = -1;

        $pageNum = isset($_POST['pageNum'])?$_POST['pageNum']:10;
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


        $map = array();


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

            $type_member = isset($lists[$l['id']]['member']['name'])?$lists[$l['id']]['member']['name']:'';
            $type_agent = isset($lists[$l['id']]['agent']['nickname'])?$lists[$l['id']]['agent']['nickname']:'';
            $type_agent_sub = isset($lists[$l['id']]['type_agent_sub']['nickname'])?$lists[$l['id']]['type_agent_sub']['nickname']:'';

            if($type_member || $type_agent || $type_agent_sub){
                $lists[$l['id']]['type_info'] = $type_member . ',' . $type_agent . ',' . $type_agent_sub;
            }else{
                $lists[$l['id']]['type_info'] = '';
            }


        }

        if($this->excel) {
            $data['totalPages'] = $count;
            $data['pageNum'] = $pageNum;
            $data['page'] = $page;
            $data['totalPages'] = ceil($count / $pageNum);
            $data['list'] = $lists;

            $data['status'] = $status;

            $this->ajaxReturn($data);
        }else{
            $this->excel =  $lists;
        }

    }

    //交易成功

    public function getSuccessInfo(){

        $status = (int)$_POST['status'];

        /*
         搜索 机构等
         */
        $memberId = $_POST['memberMark'];
        $agentId = $_POST['agentMark'];
        $agentSubId = $_POST['agentSubMark'];

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

        $uidArr = M('star_userinfo')->field('uid')->where($map)->select();
        foreach ($uidArr as $uid){
            $newUids[] = $uid['uid'];
        }

        $newUids = array_merge($newUids);
        $newUids = array_filter(array_unique($newUids));

        if($status == 1){  //买方

            $whereOreder['buy_uid'] = array('in',$newUids);
        }else if($status == 2){ // 卖方
            $whereOreder['sell_uid'] = array('in',$newUids);
        }


        $star_orderlist = M('star_orderlist');

        $whereOreder['order_type'] = 2;

        $pageNum = isset($_POST['pageNum'])?$_POST['pageNum']:10;
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
                $statusValue = '买入';
            }else if($status == 2){ // 卖方
                $listUid = $sellUid;
                $statusValue = '买出';
            }else{
                return false;
            }

            $lists[$l['id']]['name'] = isset($userInfo[$listUid]['nickname'])?$userInfo[$listUid]['nickname']:'';
            $lists[$l['id']]['phone'] = isset($userInfo[$listUid]['phoneNum'])?$userInfo[$listUid]['phoneNum']:'';


           // $lists[$l['id']]['buy_name'] = isset($userInfo[$buyUid]['nickname'])?$userInfo[$buyUid]['nickname']:'';
           // $lists[$l['id']]['buy_phone'] = isset($userInfo[$buyUid]['phoneNum'])?$userInfo[$buyUid]['phoneNum']:'';
            $lists[$l['id']]['statusValue'] = $statusValue;

            $lists[$l['id']]['order_total'] = $l['order_num']*$l['order_price'];

            $lists[$l['id']]['close_time'] = date('Y-m-d H:i:s',$l['close_time']);
        }


        if($this->excel){

            $data['totalPages'] = $count;
            $data['pageNum'] = $pageNum;
            $data['page'] = $page;
            $data['totalPages'] = ceil($count / $pageNum);
            $data['list'] = $lists;
            $data['status'] = $status;

            $this->ajaxReturn($data);
        }else{
            $this->excel = $lists;
        }


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


        $data['totalPages'] = $count;
        $data['pageNum'] =$pageNum;
        $data['page'] = $page;
        $data['totalPages'] = ceil($count/$pageNum);
        $data['list'] = $list;

        $this->ajaxReturn($data);
    }

    private function getBuyRows($where){

        $orederRows = M('star_orderlist')->field(' sum(order_num) as nums , starcode,buy_uid ')->where($where)->group('starcode')->select(); //买方


        foreach ($orederRows as $o){
            $codeArr[] = $o['starcode'];
        }

        $codeWhere['code']  = array('in',$codeArr);
        $nameArr = M('star_starbrief')->field('code,name')->where($codeWhere)->select();

        $nameRow = array();
        foreach ($nameArr as $n){
            $nameRow[$n['code']] = $n['name'];
        }

        foreach ($orederRows as $o){
            $rows[$o['buy_uid']]['order_num'] = $o['nums'];
            $rows[$o['buy_uid']]['starcode'] = $o['starcode'];
            $rows[$o['buy_uid']]['starname'] = isset($nameRow[$o['starcode']])?$nameRow[$o['starcode']]:'';
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

    private function getAgentSubName($where){
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

    private function excelModels($fields){
        return new \Home\Model\excelModel($this->titles,$fields,$this->fileName);
    }


    public function downloadExcelFund(){
        $this->titles = $this->titlesFundArr();
        $fields = $this->fieldFundArr();
        $this->fileName = '资金查询';
        $this->excelModel =  $this->excelModels($fields);

        $this->excel = 0;
        $this->getUserInfo();
        $excel =$this->excel;
        $this->excelModel->excelFile($excel);
    }

    private function titlesFundArr(){
        return array(
            '序号',
            '消费者名称',
            '收入',
            '支出',
            '冻结资金',
            '可用资金',
            '浮动盈亏',
            '资产总值'
        );
    }

    private function fieldFundArr(){
        return array(
            'uid',
            'nickname',
            'sell_sum_price',
            'buy_sum_price',
            'order_sum_price',
            'balance',
            'status_name',
            'total',
        );
    }

    public function downloadExcelPosition(){
        $this->titles = $this->titlesPositionArr();
        $this->fileName = '持仓汇总';
        $fields = $this->fieldPositionArr();
        $this->excelModel =  $this->excelModels($fields);

        $this->excel = 0;
        $this->getUserOrderinfo();
        $excel =$this->excel;
        $this->excelModel->excelFile($excel);
    }

    private function titlesPositionArr(){
        return array(
            '序号',
            '明星编号／明星',
            '消费者姓名',
            '消费者手机号',
            '持仓量（持单个明星总秒数）',
            '冻结量（未交易成功的秒数）',
            '所属机构、所属区域、所属经纪人',
        );
    }

    private function fieldPositionArr(){
        return array(
            'uid',
            'starcodename',
            'nickname',
            'phoneNum',
            'order_num',
            'un_order_num',
            'type_info',
        );
    }

    public function downloadExcelRecharge(){
        $this->titles = $this->titlesRechargeArr();
        $this->fileName = '充值金额';
        $fields = $this->fieldRechargeArr();
        $this->excelModel =  $this->excelModels($fields);

        $this->excel = 0;
        $this->getRechargeInfo();
        $excel =$this->excel;
        $this->excelModel->excelFile($excel);
    }

    private function titlesRechargeArr(){
        return array(
            '序号',
            '消费者手机号',
            '消费者姓名',
            '所属机构、所属区域、所属经纪人',
            '充值方式',
            '充值金额'
        );
    }

    private function fieldRechargeArr(){
        return array(
            'uid',
            'phoneNum',
            'nickname',
            'type_info',
            'deposit_name',
            'amount_sum'
        );
    }

    public function downloadExcelTransaction(){
        $status = (int)$_POST['status'];
        if($status == 1){
            $name = '买家';
        }else if($status == 2){
            $name = '卖家';
        }else{
            $name = '未知';
        }

        $this->titles = $this->titlesTransactionArr($name);
        $this->fileName = '交易额明细';

        $fields = $this->fieldTransactionArr();
        $this->excelModel =  $this->excelModels($fields);

        $this->excel = 0;
        $this->getTransactionInfo();
        $excel =$this->excel;
        $this->excelModel->excelFile($excel);
    }

    private function titlesTransactionArr($name='买家'){
        return array(
            '序号',
            $name.'手机号',
            $name.'姓名',
            '所属机构、所属区域、所属经纪人',
            '成交数量',
            '成交金额'
        );
    }

    private function fieldTransactionArr(){
        return array(
            'id',
            'phone',
            'name',
            'type_info',
            'order_num',
            'order_price'
        );
    }


    public function downloadExcelSuccess(){
        $status = (int)$_POST['status'];
        if($status == 1){
            $name = '买家';
        }else if($status == 2){
            $name = '卖家';
        }else{
            $name = '未知';
        }

        $this->titles = $this->titlesSuccessArr($name);
        $this->fileName = '成交明细';

        $fields = $this->fieldTSuccessArr();
        $this->excelModel =  $this->excelModels($fields);

        $this->excel = 0;
        $this->getSuccessInfo();
        $excel =$this->excel;
        $this->excelModel->excelFile($excel);
    }

    private function titlesSuccessArr($name='买家'){
        return array(
            '序号',
            '成交日期时间',
            '成交订单编号',
            $name.'姓名',
            $name.'手机号',
            '交易类型',
            '明星名称／代码',
            '成交数量',
            '成交价格',
            '成交金额'
        );
    }

    private function fieldTSuccessArr(){
        return array(
            'id',
            'close_time',
            'order_id',
            'name',
            'phone',
            'statusValue',
            'starcode',
            'order_num',
            'order_price',
            'order_total'
        );
    }



}