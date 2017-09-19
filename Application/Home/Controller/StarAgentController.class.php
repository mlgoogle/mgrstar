<?php
namespace Home\Controller;
use Think\Exception;

/**
 * 明星经纪人
 * @date finished at 2017-8-28
 *
 * Class StarController
 * @package Home\Controller
 */
class StarAgentController extends CTController{

    const MAX_NUMBER = 0.1; // 比例值
    private $withdrawalsModel;

    public function __construct(){
        $this->withdrawalsModel = new \Home\Model\withdrawalsModel();
        parent::__construct();
    }

    public function agent(){
        $this->errorAddress();//权限

        $user = $this->user;

        $identity_id = $user['identity_id'];

        $this->assign('identityId', $identity_id);


        $this->assign('title', '明星经纪人账号');
        $this->display('starAgent/agent');
    }

    public function starAgentUser(){
        $this->errorAddress();//权限

        $this->getBankcardAdminInfo();
        $this->assign('title', '明星账号');
        $this->display('starAgent/starAgentUser');
    }

    public function starAgentList(){
        $user = $this->user;

        $identity_id = $user['identity_id'];

        $adminModel = M('admin_user');
        $agentsubModel = M('agentsub_info');
        $starUserModel = M('star_userinfo');

        $pageNum = I('post.pageNum', 5, 'intval');
        $page = I('post.page', 1, 'intval');

        $map['identity_id'] = -1;
        if($identity_id == -1){
            $map['id'] = $user['id'];
        }
        $count = $adminModel->where($map)->count();// 查询满足要求的总记录数
        $list = $adminModel->where($map)->page($page, $pageNum)->select();

        $agentSubIdArr = $adminIdArr = array();
        foreach ($list as $l){
            $agentSubIdArr[] = $l['agentSubId'];
            $adminIdArr[] = $l['id'];
        }

        $agentSubIdMap['id'] = array('in',$agentSubIdArr);

        $agentsubArr = $agentsubModel->where($agentSubIdMap)->select();

        foreach ($agentsubArr as $a){
            $agentSubIdData[$a['id']] = $a;
        }

        $starUserMap['adminId'] = array('in',$adminIdArr);
        $starUserArr = $starUserModel->field('count(adminId) as adminId_count , adminId ')->where($starUserMap)->group('adminId')->select();

        $adminIdCountArrr =  array();
        foreach ($starUserArr as $s){
            $adminIdCountArrr[$s['adminId']] = $s;
        }


        foreach ($list as $k=>$l){
            $list[$k]['nickname'] = $agentSubIdData[$l['agentSubId']]['nickname'];
            $list[$k]['mark'] = $agentSubIdData[$l['agentSubId']]['mark'];
            $list[$k]['phone'] = $agentSubIdData[$l['agentSubId']]['phone'];
            $list[$k]['number'] = isset($adminIdCountArrr[$l['id']])?$adminIdCountArrr[$l['id']]['adminId_count']:0;
        }

        new \Think\Page($count, $pageNum);// 实例化分页类 传入总记录数和每页显示的记录数(25)


        $data['totalPages'] = $count;
        $data['pageNum'] = $pageNum;
        $data['page'] = $page;
        $data['totalPages'] = ceil($count / $pageNum);
        $data['list'] = $list;
        return $this->ajaxReturn($data);

    }

    public function addStarAgent(){

        $user = $this->user;

        $identity_id = $user['identity_id'];

        if($identity_id != 1) return  false;

        $user = $this->user;
        $adminModel = M('admin_user');
        $agentsubModel = M('agentsub_info');

        $uname = I('post.uname','','strip_tags');
        $nickname = I('post.nickname','','strip_tags');
        $password = I('post.password','','strip_tags');
        $phone = I('post.phone',0,'int');

        if($adminModel->where(array('uname'=>$uname))->find()){
            $return = array(
                'code' => -2,
                'message' => "登录帐号已存在"
            );
            return $this->ajaxReturn($return);
        }


        //$agentNickname = I('post.agentNickname', '', 'strip_tags');

        $mark = 100000;

        $AutoIdArr = $agentsubModel->
        query('SELECT Auto_increment as autoId FROM information_schema.`TABLES` WHERE TABLE_NAME = \'agentsub_info\' AND TABLE_SCHEMA = \'star\' limit 1');
        $Auto = implode('',array_column($AutoIdArr,'autoId'));

        $AutoId = isset($Auto)?$Auto:1;
        $mark += $AutoId;

        $mark = $agentSubStr = sprintf('%03s', $mark);
        $dataAgent['mark'] = $mark;


        $dataAgent['nickname'] =  $nickname;
        $dataAgent['uid']      =  $user['id'];
        $dataAgent['phone']    =  $phone;
        $agentSubId = $agentsubModel->add($dataAgent);

        $data['uname']          = $uname;
        $data['nickname']       = $nickname;
        $data['pass']           = md5($password);
        $data['identity_id']    = -1; // 经纪人明星用户
        $data['registerTime']   = date('Y-m-d',time());
        $data['agentSubId']     = $agentSubId;

        if($adminModel->add($data)) {
            $return = array(
                'code' => 0,
                'message' => "添加成功"
            );
            return $this->ajaxReturn($return);
        }else{
            $return = array(
                'code' => -2,
                'message' => "添加失败"
            );
            return $this->ajaxReturn($return);
        }
    }

    public function editStarAgent(){
        $password = I('post.password', '', 'strip_tags');
        $phone = I('post.phone', '', 'int');

        $id = I('post.id', 0 ,'intval');

        if(!$password){
            $return = array(
                'code' => -2,
                'message' => '请输入密码！'
            );
            $this->ajaxReturn($return);
        }

        if(!$phone){
            $return = array(
                'code' => -2,
                'message' => '请输入手机号！'
            );
            $this->ajaxReturn($return);
        }

        $agentSubIdArr = M('admin_user')->field('agentSubId')->where(array('id'=>$id))->find();

        $agentSubId = isset($agentSubIdArr['agentSubId'])?$agentSubIdArr['agentSubId']:0;


        if($agentSubId) {
            $dataAgentsub['phone'] = $phone;
            M('agentsub_info')->where(array('id'=>$agentSubId))->save($dataAgentsub);
        }

        $dataUser['pass'] = md5($password);
        if(M('admin_user')->where(array('id'=>$id))->save($dataUser)){
            $return = array(
                'code' => 0,
                'message' => '成功'
            );
        }else{
            $return = array(
                'code' => -2,
                'message' => '失败'
            );
        }

        $this->ajaxReturn($return);

    }

    public function updateUserStatus(){

        $user = $this->user;

        $identity_id = $user['identity_id'];

        if($identity_id != 1) return  false;


        $idArr = I('post.id',0);
        $status = I('post.status',0);

        if($idArr){
            $map['id'] = array('in',$idArr);
            if(M('admin_user')->where($map)->save(array('status'=>$status))){
                $return = array(
                    'code' => 0,
                    'message' => '成功！'
                );
               return $this->ajaxReturn($return);
            }

            $return = array(
                'code' => -2,
                'message' => '失败！'
            );
        }else{
            $return = array(
                'code' => -2,
                'message' => '失败！'
            );
        }
        return $this->ajaxReturn($return);

    }

    public function DelStarAgent(){

        $user = $this->user;

        $identity_id = $user['identity_id'];

        if($identity_id != 1) return  false;

        $adminUser = M('admin_user');
        $idArr = I('post.ids',0);

        $map['id'] = array('in',$idArr);

        $res = $adminUser->where($map)->delete();

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

    //明星经纪人提现记录列表
    public function starLogList(){
        $user = $this->user;

        $identity_id = $user['identity_id'];

        if($identity_id != -1) return false;


        $profitStarLogModel = M('profit_star_log');
        $pageNum = I('post.pageNum', 5, 'intval');
        $page = I('post.page', 1, 'intval');

        $adminId = $user['id'];

        $map['adminId'] = $adminId;

        $count = $profitStarLogModel->where($map)->count();// 查询满足要求的总记录数
        $profitStarLogArr = $profitStarLogModel->where($map)->page($page, $pageNum)->order('id desc')->select();


        $data['totalPages'] = $count;
        $data['pageNum'] = $pageNum;
        $data['page'] = $page;
        $data['totalPages'] = ceil($count / $pageNum);
        $data['list'] = $profitStarLogArr;
        return $this->ajaxReturn($data);

    }

    public function agentUser(){
        $user = $this->user;

        $identity_id = $user['identity_id'];

        $adminId =  $user['id'];

        if($identity_id != -1) return false;


        $profitStarSummaryModel = M('profit_star_summary');

        $userInfo = M('star_userinfo');
        $pageNum = I('post.pageNum', 5, 'intval');
        $page = I('post.page', 1, 'intval');
        $map['starcode'] = array('exp','is not null');
        $map['adminId'] = $user['id'];

        $count = $userInfo->where($map)->count();// 查询满足要求的总记录数
        $list = $userInfo->where($map)->page($page, $pageNum)->select();
        $starcodeRow = array();
        foreach ($list as $l){
            $starcodeRow[] = $l['starcode'];
        }

        $starcodeRow = array_filter(array_unique($starcodeRow));

        $where['code'] = array('in',$starcodeRow);
        $brief = M('star_starbrief')->where($where)->select();

        $nameArr = array();
        foreach ($brief as $b){
            $nameArr[$b['code']] = $b['name'];
        }

        $profitStarMap['starcode'] = array('in',$starcodeRow);
        $profitStarSummary = $profitStarSummaryModel->where($profitStarMap)->select();

        $priceArr =  array();
        foreach ($profitStarSummary as $p){
            $priceArr[$p['starcode']] = $p['order_price'];
        }

        $profitStarcodeArr = array();
        //$profitSumPrice = 0;
        foreach ($list as $k=>$l){
            $list[$k]['starname'] = isset($nameArr[$l['starcode']])?$nameArr[$l['starcode']]:'';
            $list[$k]['star_price'] = $star_price =isset($priceArr[$l['starcode']])?$priceArr[$l['starcode']]:0;

            $profitStarcodeArr[] = $l['starcode'];
            //$profitSumPrice += $star_price;
        }

        new \Think\Page($count, $pageNum);// 实例化分页类 传入总记录数和每页显示的记录数(25)


        $profitSumPriceArr = $profitStarSummaryModel->field('sum(order_price) as sum_price')->where(array('adminId'=>$adminId))->find();

        $profitSumPrice = isset($profitSumPriceArr['sum_price'])?$profitSumPriceArr['sum_price']:0;


        $profitSumPrice = $profitSumPrice*self::MAX_NUMBER;

        F("profitSumPrice", $profitSumPrice*100); //缓存起来
       // F("profitStarcodeArr", $profitStarcodeArr);

        $data['totalPages'] = $count;
        $data['pageNum'] = $pageNum;
        $data['page'] = $page;
        $data['totalPages'] = ceil($count / $pageNum);
        $data['list'] = $list;
        $data['sum_price'] = sprintf('%0.2f',$profitSumPrice);
        return $this->ajaxReturn($data);
    }

    public function addAgentUser(){
        $user = $this->user;
        $userInfoModel = M('star_userinfo');
        $starname = I('post.starname', '', 'strip_tags');
        $starname = trim($starname);



        $starcode = I('post.starcode', 0, 'intval');

        $item = M('star_starbrief')->where("`code` = '{$starcode}' AND ( status = 0 OR status = 1 ) ")->find();// 默认 明星 没有删除的

        if (!$item) {
            $return = array(
                'code' => -2,
                'message' => "未找到明星 -{$starname}"
            );
            return $this->ajaxReturn($return);
        }


        $phoneNum = I('post.phoneNum',0,'strip_tags');

        $phoneNum = trim($phoneNum);

        if($userInfoModel->where("`starcode` = '{$starcode}'")->find()){  //`phoneNum` = '{$phoneNum}' OR
            $return = array(
                'code' => -2,
                'message' => '明星已关联账号！'
            );
            $this->ajaxReturn($return);
            return false;
        }


        if (!$phoneNum){
            $return = array(
                'code' => -2,
                'message' => '请输入帐号！'
            );
            $this->ajaxReturn($return);
            return false;
        }else {
            if (!preg_match('/^1[3-9][0-9]{9}$/', $phoneNum)) {
                $return = array(
                    'code' => -2,
                    'message' => '账号必须为手机号！'
                );
                $this->ajaxReturn($return);
                return false;
            }
        }
        if( $userInfoModel->where("`phoneNum` = '{$phoneNum}' and `starcode` is not null ")->find()){
            $return = array(
                'code' => -2,
                'message' => '账号已关联明星！'
            );
            $this->ajaxReturn($return);
            return false;
        }

        if( $userInfoModel->where("`phoneNum` = '{$phoneNum}'")->find()){
            $dataCode = array(
                'starcode' => $starcode,
                'adminId'  => $user['id']
            );
            if($userInfoModel->where("`phoneNum` = '{$phoneNum}'")->save($dataCode)){
                $return = array(
                    'code' => 0,
                    'message' =>'成功',
                );
            }else{
                $return = array(
                    'code' => -2,
                    'message' =>'失败',
                );
            }
            return $this->ajaxReturn($return);

//            $return = array(
//                'code' => -2,
//                'message' => '帐号已存在！'
//            );
//            $this->ajaxReturn($return);
            return false;
        }


        $data = array(
            'starcode'     => $starcode,
            'phoneNum'     => $phoneNum,
            'adminId'      => $user['id'],
            'passwd'       => md5(123456),
            'registerTime' => date('Y-m-d H:i:s',time())
        );

        $bool = -2;

        if($userInfoModel->add($data)){
            $bool = 0;
        }

        //结果返回
        $return = array(
            'code' => $bool,
            'message' => $bool?'失败':'成功',
        );

        return $this->ajaxReturn($return);

    }

    public function editAgentUser(){
        $password = I('post.password', '', 'strip_tags');
        $id = I('post.id', 0 ,'intval');

        $password = strtolower($password);
        if(!$password){
            $return = array(
                'code' => -2,
                'message' => '请输入密码！'
            );
            $this->ajaxReturn($return);
        }else{
            if (!preg_match('/^[a-z][a-z0-9]{5,14}$/', $password)) {
                $return = array(
                    'code' => -2,
                    'message' => '密码必须是英文字母开头6到15位！'
                );
                $this->ajaxReturn($return);
                return false;
            }
        }

        M('star_userinfo')->uid = $id;
        M('star_userinfo')->passwd  = md5($password);
        if(M('star_userinfo')->save()){
            $return = array(
                'code' => 0,
                'message' => '成功'
            );
        }else{
            $return = array(
                'code' => -2,
                'message' => '失败'
            );
        }

        $this->ajaxReturn($return);
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
        $bankAccount = I('post.bankAccount',0,'intval'); // 银行卡号
        $bankSum = F("profitSumPrice");//缓存的值 //I('post.bankSum',0,'intval');  // 提现金额
        $bankName = I('post.bankName','','string');
        $bankPersonName = I('post.bankPersonName','','string');

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
            F('profitSumPrice',NULL);//删除缓存数据

            if($this->withdrawalsFinish($bankAccount,$bankPersonName,$bankSum)) { //成功后的操作
                F("profitSumPrice", 0);
            }
        }else{
            $return = array(
                'code' => -2,
                'message' => $returnAjax['respDesc'],
                'withdrawals'=>$returnAjax,
            );
        }

        return $this->ajaxReturn($return);
    }

    //提现成功后的操作
    public function withdrawalsFinish($bankAccount,$bankPersonName,$profitPrice=0){
        try {
            $profitStarSummaryModel = M('profit_star_summary');
            $profitStarLogModel = M('profit_star_log');

            //$map['starcode'] = array('in', $starcodeArr);
            $map['adminId'] = $this->user['id'];
            $dataStarSummary['order_price'] = 0;
            $profitStarSummaryModel->where($map)->save($dataStarSummary);
            $profitPrice = sprintf('%0.2f',$profitPrice/100);

            $dataStarLog['adminId']         = $this->user['id'];
            $dataStarLog['profit_price']    = $profitPrice;
            $dataStarLog['bankAccount']     = $bankAccount;
            $dataStarLog['bankPersonName']  = $bankPersonName;
            $dataStarLog['create_time']     = date('Y-m-d', time());
            $profitStarLogModel->add($dataStarLog);
            return true;
        }catch (Exception $e){
            return false;
        }
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


    /**
     * 接收的明星姓名查询明星对应信息
     */
    public function getStarUserInfo(){
        $model = M('star_starbrief');
        $starname = I('post.starname', '', 'strip_tags');
        $starname = trim($starname);

        $item = $model->where("`name` = '{$starname}' AND ( status = 0 OR status = 1 ) ")->find();// 默认 明星 没有删除的

        dump($model->_sql());
        if (count($item) > 0) {
            $arr['star_code'] = $item['code'];
            $arr['star_name'] = $item['name'];
        }else{
            $return = array(
                'code' => -2,
                'message' => "未找到明星 -{$starname}"
            );
            return $this->ajaxReturn($return);
        }

        return $this->ajaxReturn($arr);
    }

}

