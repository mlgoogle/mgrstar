<?php

namespace Home\Controller;

use Think\Controller;

/**
 * 明星时间管理
 * @date started at 2017-6-15
 *
 * Class TimerController
 * @package Home\Controller
 */
class TimerController extends CTController
{
    //软删除    0上线 1下线 2软删除
    const DELETE_ONLINE = 0;
    const DELETE_OFF = 1;
    const DELETE_TRUE = 2;

    public function __construct()
    {
        parent::__construct();
        $this->assign('title', '明星时间管理');
    }

    //模板显示
    public function timer(){

        $this->display('timer/listing');
    }

    //分配
    public function distribute(){
        $this->display('timer/distribute');
    }

    //*
    public function avg()
    {
        $return['code'] = -2;
        $return['message'] = 'Error';

        $id = (int)$_POST['id'];
        $star = M('star_timer');
        $timer = $star->where("`id` = '{$id}'")->find();
        if (count($timer) == 0) {
            $return['message'] = '未找到该明星的发行时间';
            return $this->ajaxReturn($return);
        }
        $timerTotal = (int)$timer['micro'];

        $uids = I('post.uids', '', 'strip_tags');
        $uids = trim($uids);
        if (empty($uids)) {
            $return['message'] = '请输入要平均分配的用户ID';
            return $this->ajaxReturn($return);
        }
        $uidArr = explode(',', $uids);
        $uids = implode(',', $uidArr);
        $uModel = M('star_userinfo');
        $uList = $uModel->where(array('uid' => array('in', $uids)))->select();

        //真实存在的用户
        $userArr = array();
        $userNick = array();
        foreach ($uList as $list) {
            $userNick[$list['uid']] = $list['nickname'];
            $userArr[] = $list['uid'];
        }
        if (count($userArr) < 1) {
            $return['message'] = '请输入正确的用户ID';
            return $this->ajaxReturn($return);
        }

        //已拥有改明星的用户时间值
        $own = M('star_belongtime');
        $userTimeList = $own->where("`timerid` = '{$timer['id']}' AND `status` = 0")->select();
        $ownTimer = 0;

        //已分配过时间的用户
        $fansUidArr = array();
        foreach ($userTimeList as $user) {
            $fansUidArr[] = $user['belong_id'];
            $ownTimer += ((int)$user['star_time']);
        }

        //可用时间、可分配用户
        $free = $timerTotal - $ownTimer;
        $avgUids = array_diff($userArr, $fansUidArr);

        if ($free < 0) {
            $return['message'] = '没有多余的可分配时间可用';
            return $this->ajaxReturn($return);
        }

        if (count($avgUids) < 0) {
            $return['message'] = '输入的用户ID已分配过时间';
            return $this->ajaxReturn($return);
        }

        $fTime = 0;
        $bool = false;
        if ($free && $avgUids) {
            $fTime = $free/count($avgUids);
        }
        if (!is_int($fTime)) {
            $return['message'] = '无法整除可用时间:' . $free . ' 整除结果:' .$fTime . '--可分配用户id:' . implode('-', $avgUids) ;
            return $this->ajaxReturn($return);
        } else if ($fTime > 1){
            foreach ($avgUids as $u) {
                $own->timerid = $timer['id'];
                $own->belong_id = $u;
                //$own->nickname = $userNick[$u]['nickname'];
                $own->star_time = $fTime;
                //$own->add_time = date('Y-m-d H:i:s', time());
                $bool = $own->add();
            }
        } else {
            $return['message'] = '整除结果:' .$fTime . '--可分配用户id:' . implode('-', $avgUids) ;
            return $this->ajaxReturn($return);
        }

        $return['code'] = ($bool) ? 1 : -2;
        $return['message'] = ($bool) ? 'Success' : 'Error';
        return $this->ajaxReturn($return);
    }

    /**
     * 分配时间
     */
    public function add(){
        $fans = I('post.fans', '', 'strip_tags');
        $fans = trim($fans);

        if (empty($fans)) {
            $return['code'] = -2;
            $return['message'] = '请输入粉丝昵称或UID';
            return $this->ajaxReturn($return);
        }

        $star_time = (int)$_POST['secs'];
        if($star_time < 1) {
            $return['code'] = -2;
            $return['message'] = '最低分配时间为1';
            return $this->ajaxReturn($return);
        }
        $id = (int)$_POST['id'];

        $star = M('star_timer');
        $timer = $star->where("`id` = '{$id}'")->find();

        $return['id'] = $id;
        $return['code'] = -2;
        $return['message'] = 'Error';

        if (count($timer) == 0) {
            $return['message'] = '未找到该明星的发行时间';
            return $this->ajaxReturn($return);
        }

        $model = M('star_userinfo');
        $userInfo = $model->where("`nickname` = '{$fans}' OR `uid` = '{$fans}'")->find();

        if (!$userInfo) {
            $return['message'] = '未找到该粉丝';
            return $this->ajaxReturn($return);
        }

        //输入过大的值
        $totalTime = (int)$timer['micro'];
        if ($star_time > $totalTime) {
            $return['message'] = '分配的时间过多';
            return $this->ajaxReturn($return);
        }

        //已拥有改明星的用户时间值
        $own = M('star_belongtime');
        $userTimeList = $own->where("`timerid` = '{$timer['id']}' AND `status` = 0")->select();
        $ownTimer = 0;
        foreach ($userTimeList as $user) {
            $ownTimer += ($user['star_time']);
        }

        $isExist = $own->where("`timerid` = '{$timer['id']}' AND `belong_id` = '{$userInfo['uid']}' AND `status` = 0")->count('id');
        if ($isExist) {
            $return['message'] = '粉丝已分配过时间';
            return $this->ajaxReturn($return);
        }

        //可用时间
        $free = (($totalTime - $ownTimer) <= 0) ? 0 : ($totalTime - $ownTimer);
        $free = (int)$free;
        if ($free == 0 || $star_time > $free) {
            $return['message'] = '可分配时间为 :' . $free;
            return $this->ajaxReturn($return);
        }

        $bool = false;

        if ($free >= $star_time) {
            $own->timerid = $timer['id'];
            $own->belong_id = $userInfo['uid'];
            //$own->nickname = $userInfo['nickname'];
            $own->star_time = $star_time;
            $own->star_code = $timer['starcode'];
            //$own->add_time = date('Y-m-d H:i:s', time());

            $return['fans'] = (!empty($userInfo['nickname'])) ? $userInfo['nickname'] : $userInfo['uid'];
            $return['secs'] = $star_time;

            $return['total'] = count($userTimeList) + 1;
            $return['free'] = $free - $star_time;

            $id = $bool = $own->add();
        }

        $return['id'] = $id;
        $return['code'] = ($bool) ? 1 : -2;
        $return['message'] = ($bool) ? '成功' : '失败';
        return $this->ajaxReturn($return);

    }

    /**
     * 手动输入用户昵称、ID
     */
    public function getFans()
    {
        $return['code'] = -2;
        $return['message'] = 'Error';
        $return['nickname'] = '';
        $return['belong_id'] = 0;

        $fans = I('post.fans', '', 'strip_tags');
        $fans = trim($fans);

        if (empty($fans)) {
            $return['message'] = '请输入粉丝昵称或UID';
            return $this->ajaxReturn($return);
        }

        $model = M('star_userinfo');
        $item = $model->where("`nickname` = '{$fans}' OR `uid` = '{$fans}'")->find();

        if (count($item) == 0) {
            $return['message'] = '未找到该粉丝信息';
            return $this->ajaxReturn($return);
        }

        if ($item) {
            $return['code'] = 0;
            $return['nickname'] = $item['nickname'];
            $return['belong_id'] = $item['uid'];
            $return['message'] = 'Success';
            return $this->ajaxReturn($return);
        }
    }

    /**
     *  发行时间列表
     */
    public function info()
    {
        if (!isset($_GET['id'])) {
            return $this->display('timer/info');
        }

        $id = (int)$_GET['id'];
        $model = M('star_timer');
        $item = $model->where("`id` = '{$id}'")->find();
        if (!$item) exit('非法输入');

        $microSumArr = $model->field('sum(micro) as microSum')->where("`starcode` = '{$item['starcode']}'")->find();

        if($model->where('sort > '.$item['sort'] .' AND starcode = ' . $item['starcode'])->find()){
            $this->assign('edit', 1);
        }else{
            $this->assign('edit', null);
        }


        $item['status'] = self::getStatus($item['status']);

        $timeMicro = M('star_time_micro')->where("`starcode` = '{$item['starcode']}'")->find();


        $item['last_time'] = $timeMicro['total_micro'] - $microSumArr['microSum']+$item['micro'];

        $item['publish_begin_time'] = date('Y-m-d',strtotime($item['publish_begin_time']));
        $item['publish_end_time'] = date('Y-m-d',strtotime($item['publish_end_time']));
        $this->assign('timeMicro', $timeMicro);
        $this->assign('item', $item);
        $this->display('timer/info');
    }

    /**
     * 分配时间列表
     */
    public function dis_info(){

        if (!isset($_GET['id'])) {
            return $this->display('timer/info');
        }

        $id = (int)$_GET['id'];
        $model = M('star_timer');
        $item = $model->where("`id` = '{$id}'")->find();
        if (!$item) exit('非法输入');

        $microSumArr = $model->field('sum(micro) as microSum')->where("`starcode` = '{$item['starcode']}'")->find();

        $item['status'] = self::getStatus($item['status']);

        $timeMicro = M('star_time_micro')->where("`starcode` = '{$item['starcode']}'")->find();


        $item['last_time'] = $timeMicro['total_micro'] - $microSumArr['microSum']+$item['micro'];

        $timer = M('star_belongtime')->where("`timerid` = '{$item['id']}' AND `status` = " . self::DELETE_ONLINE)->order('star_time desc')->select();

        foreach ($timer as $t){
            $belongIds[] = $t['belong_id'];
        }

        $star_time = 0;
        if(isset($belongIds)) {
            $whereUidArr['uid'] = array('in', $belongIds);

            $userInfo = M('star_userinfo')->field('uid,nickname')->where($whereUidArr)->select();

            foreach ($userInfo as $u){
                $user[$u['uid']] = $u['nickname'];
            }

            foreach ($timer as $key => $t) {
                $star_time += (int)$t['star_time'];
                $timer[$key]['nickname'] = $user[$t['belong_id']];
            }
        }
        $micro = (int)$item['micro'];
        $free = (($micro - $star_time < 0)) ? 0 : $micro - $star_time;

        $item['sort_name'] = $this->getSortName($item['sort']);


        $item['publish_begin_time'] = date('Y-m-d',strtotime($item['publish_begin_time']));
        $item['publish_end_time'] = date('Y-m-d',strtotime($item['publish_end_time']));
        $this->assign('timeMicro', $timeMicro);
        $this->assign('item', $item);
        $this->assign('timer', $timer);
        $this->assign('free', $free);
        $this->display('timer/dis_info');
    }

    /**
     * 软删除分配粉丝
     */
    public function fstatus()
    {
        //获取提交过来的ID值并进行分割 in 查询
        $id = (int)$_POST['id'];
        $model = M('star_belongtime');
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
            'status' => !$item['status'],
            //'modify_time' => date('Y-m-d H:i:s', time())
        );
        $bool = ($model->where('id =' . $item['id'])->save($data)) ? 0 : 1;

        //结果返回
        $return = array(
            'free' => (int)$_POST['free'] + $item['star_time'],
            'total' => (int)$_POST['total'] - 1,
            'code' => $bool,
            'message' => (!$bool) ? 'Success' : 'Error',
        );
        return $this->ajaxReturn($return);
    }

    /**
     * 添加
     */
    public function addTimer(){

        $data = array();

        $model = M('star_timer');

        //接收过滤提交数据
        if (isset($_POST['starname'])) {
            $name = I('post.starname', '', 'strip_tags');
            $name = trim($name);

            $isExist = (int)$model->where("`starname` = '{$name}' AND `status` !=".self::DELETE_TRUE)->count('id');
            if ($isExist) {
                $return = array(
                    'code' => -2,
                    'message' => '该明星已有值！'
                );
                return $this->ajaxReturn($return);
            }

            $starModel = M('star_starbrief');
            $item = $starModel->where("`name` = '{$name}' OR `uid`='{$name}'")->find();
            if (count($item) == 0) {
                $return = array(
                    'code' => -2,
                    'message' => '未找到该明星信息！'
                );
                return $this->ajaxReturn($return);
            }

            $starname = $item['name'];
            $starcode = $item['code'];
        }else{
            $return = array(
                'code' => -2,
                'message' => '请填写明星名称！'
            );
            return $this->ajaxReturn($return);
        }

        $total_micro = intval($_POST['total_micro']);
        if(!$total_micro || $total_micro < 100){
            $return = array(
                'code' => -2,
                'message' => '总发行数量最低为100！'
            );
            return $this->ajaxReturn($return);
        }

        $micro = is_array($_POST['micro'])?$_POST['micro']:array();

        foreach ($micro as $i=>$m){
            //非空提醒
            if(empty($m) || $m < 100){
                $return = array(
                    'code' => -2,
                    'message' => '消耗秒数最低为100！'
                );
                return $this->ajaxReturn($return);
            }

            $data[$i]['micro'] = $m;
            $data[$i]['publish_last_time'] = $m; // 剩余时间 添加时等于发售数量
        }

        $publish_begin_time = is_array($_POST['publish_begin_time'])?$_POST['publish_begin_time']:array();
        foreach ($publish_begin_time as $i=>$b){
            if(!$b){
                $return = array(
                    'code' => -2,
                    'message' => '请填写发售开始时间！'
                );
                return $this->ajaxReturn($return);
            }
            $data[$i]['publish_begin_time'] = $b;
        }

        $publish_end_time = is_array($_POST['publish_end_time'])?$_POST['publish_end_time']:array();
        foreach ($publish_end_time as $i=>$e){
            if(!$e){
                $return = array(
                    'code' => -2,
                    'message' => '请填写发售结束时间！'
                );
                return $this->ajaxReturn($return);
            }
            $data[$i]['publish_end_time'] = $e;
        }

        $publish_type = is_array($_POST['publish_type'])?$_POST['publish_type']:array();
        foreach ($publish_type as $i=>$t){
            if(!$e){
                $return = array(
                    'code' => -2,
                    'message' => '请填写发售结束时间！'
                );
                return $this->ajaxReturn($return);
            }
            $data[$i]['publish_type'] = $t;
        }

        $publish_price = is_array($_POST['publish_price'])?$_POST['publish_price']:array();
        foreach ($publish_price as $i=>$p){
            if(!$p){
                $return = array(
                    'code' => -2,
                    'message' => '请填写发售价格！'
                );
                return $this->ajaxReturn($return);
            }
            $data[$i]['publish_price'] = sprintf('%.2f',$p);
        }

        //$nameVal = array('第一期','第二期','第三期','第四期','第五期');

        $lastEndTime = 0;
        foreach ($data as $j=>$d){
            $data[$j]['starname'] = $starname;
            $data[$j]['starcode'] = $starcode;
            $data[$j]['sort'] = $j+1;

            $data[$j]['add_time'] = date('Y-m-d H:i:s', time());
            $beginTime = $d['publish_begin_time'];
            $endTime = $d['publish_end_time'];
            $data[$j]['publish_end_time'] = $endTime.' 23:59:59';

            if($beginTime>$endTime){
                $return = array(
                    'code' => -2,
                    'message' => $this->getSortName($j+1).'开始时间不能大于结束时间！' //$nameVal[$j]
                );
                return $this->ajaxReturn($return);
            }

            if($lastEndTime){
                if($beginTime<$lastEndTime){
                    $return = array(
                        'code' => -2,
                        'message' => getSortName($j+1).'开始时间不能小于上期结束时间！'
                    );
                    return $this->ajaxReturn($return);
                }
            }

            // 结束时间 保存
            $lastEndTime = $endTime;

        }

        //数据入库
        $id = 0;
        if($model->addAll($data)){
            //总秒数表
            $microData['total_micro'] = $total_micro;
            $microData['starcode'] = $starcode;
            $microData['add_time'] = date('Y-m-d H:i:s', time());
            $id = M(star_time_micro)->add($microData);
        }


        //结果返回
        $return = array(
            'id' => $id,
            'code' => ($id) ? 0 : 1,
            'message' => ($id) ? '成功！':'失败',
        );
        return $this->ajaxReturn($return);
    }

    /**
     * 编辑信息
     * todo 设置图片的大小
     */
    public function editTimer(){

        $return['code'] = -2;
        $return['message'] = 'Error';

        $id = (int)$_POST['id'];
        if (!$id) {
            $return['message'] = '非法操作';
            return $this->ajaxReturn($return);
        }


        $micro = (int)$_POST['micro'];
        if ($micro < 101) {
            $return['message'] = '输入的发售数量过低';
            return $this->ajaxReturn($return);
        }

        $model = M('star_timer');
        $item = $model->where("`id` = '{$id}'")->find();

        if($model->where('sort > '.$item['sort'] .' AND starcode = ' . $item['starcode'] )->find()){
            $return['message'] = '明星有新的发售，旧的不让修改！';
            return $this->ajaxReturn($return);
        }

        if (count($item) < 1) {
            $return['message'] = '未找到要更新的数据';
            return $this->ajaxReturn($return);
        }

        $totalMicroArr = M('star_time_micro')->field('total_micro')->where("`starcode` = '{$item['starcode']}'")->find();
        $totalMicro = isset($totalMicroArr['total_micro'])?$totalMicroArr['total_micro']:0;

        $microSumArr = M('star_timer')->field('sum(micro) as microSum')->where("`starcode` = '{$item['starcode']}'")->find();
        $microSum = isset($microSumArr['microSum'])?(int)$microSumArr['microSum']:0;
        $microSum = $totalMicro-($microSum - $item['micro']);
        if($micro>$microSum){
            $return['message'] = '发售数量大于剩余数量';
            return $this->ajaxReturn($return);
        }

        //$timer = M('star_belongtime')->where("`timerid` = '{$item['id']}' AND `status` = " . self::DELETE_ONLINE)->select();


        $id = $item['id'];
        $publish_begin_time = $_POST['publish_begin_time'];
        $publish_end_time = $_POST['publish_end_time'];

        if($publish_begin_time>$publish_begin_time){
            $return['message'] = '开始时间不能大于结束时间';
            return $this->ajaxReturn($return);
        }

        $publish_type = $_POST['publish_type'];
        $publish_price = $_POST['publish_price'];

        $publish_end_time = $publish_end_time. ' 23:59:59';

        unset($model->microSum);

        if (count($item) > 0) {
            $model->id = $id;
            $model->micro = $micro;
            $model->modify_time = date('Y-m-d H:i:s', time());
            $model->publish_begin_time = $publish_begin_time;
            $model->publish_end_time = $publish_end_time;
            $model->publish_type = $publish_type;
            $model->publish_price = $publish_price;
            $bool = $model->save();
        }


        //结果返回
        $return = array(
            'id' => $id,
            'code' => ($bool) ? 0 : 1,
            'message' => '修改成功',
        );
        return $this->ajaxReturn($return);
    }

    /**
     * 软删除
     */
    public function delTimer()
    {
        //获取提交过来的ID值并进行分割 in 查询
        $ids = implode(',', $_POST['ids']);
        $model = M('star_timer');
        $list = $model->where(array('id' => array('in', $ids)))->select();

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
        $bool = ($model->where(array('id' => array('in', $idIn)))->save($data)) ? 0 : 1;

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
        $model = M('star_timer');
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
            'status' => !$item['status'],
            'modify_time' => date('Y-m-d H:i:s', time())
        );
        $bool = ($model->where('id =' . $item['id'])->save($data)) ? 0 : 1;

        //结果返回
        $return = array(
            'code' => $bool,
            'message' => (!$bool) ? 'Success' : 'Error',
        );
        return $this->ajaxReturn($return);
    }

    /**
     * 列表
     * @todo 搜索
     */
    public function searchTimer(){
        $timer = M('star_timer');
        $pageNum = I('post.pageNum', 5, 'intval');
        $page = I('post.page', 1, 'intval');

        $count = $timer->where('status !=' . self::DELETE_TRUE)->count();// 查询满足要求的总记录数
        $list = $timer->where('status !=' . self::DELETE_TRUE)->page($page, $pageNum)->order('sort asc,id desc')->select();//获取分页数据

        foreach ($list as $l){
            $starcodeArr[] = $l['starcode'];
        }

        $starcodeArr = array_filter(array_unique($starcodeArr));

        $whereMicro['starcode'] = array('in',$starcodeArr);

        $timeMicro = M('star_time_micro')->where($whereMicro)->select();

        foreach($timeMicro as $tm){
            $dataMicro[$tm['starcode']] = $tm;
        }

        foreach ($list as $key => $item) {
            $list[$key]['total_micro'] = $dataMicro[$item['starcode']]['total_micro'];
            $list[$key]['status_type'] = $item['status'];
            $list[$key]['status'] = self::getStatus($item['status']);
        }


        new \Think\Page($count, $pageNum);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $data['totalPages'] = $count;
        $data['pageNum'] = $pageNum;
        $data['page'] = $page;
        $data['totalPages'] = ceil($count / $pageNum);
        $data['list'] = $list;

        $this->ajaxReturn($data);
    }

    /**
      分配列表
     */
    public function distributeList(){
        $timer = M('star_timer');
        $pageNum = I('post.pageNum', 5, 'intval');
        $page = I('post.page', 1, 'intval');

        $count = $timer->where('status !=' . self::DELETE_TRUE)->count();// 查询满足要求的总记录数
        $list = $timer->where('status !=' . self::DELETE_TRUE)->page($page, $pageNum)->order('sort asc,id desc')->select();//获取分页数据

        foreach ($list as $l){
            $starcodeArr[] = $l['starcode'];
        }

        $starcodeArr = array_filter(array_unique($starcodeArr));

        $whereMicro['starcode'] = array('in',$starcodeArr);

        $timeMicro = M('star_time_micro')->where($whereMicro)->select();

        foreach($timeMicro as $tm){
            $dataMicro[$tm['starcode']] = $tm;
        }

        //分配者
        $whereBelongtime['star_code'] = array('in',$starcodeArr);
        $whereBelongtime['status'] =  0;

        $belongtime = M('star_belongtime')->where($whereBelongtime)->select();

        foreach ($belongtime as $b){
            $belongIdArr[] = $b['belong_id'];
        }

        $whereUserInfo['uid'] = array('in',$belongIdArr);

        $userInfo = M('star_userinfo')->where($whereUserInfo)->select();

        foreach ($userInfo as $u){
            $userNameArr[$u['uid']] = $u['nickname'];
        }

        foreach ($belongtime as $belongKey=>$b){
            $belongtime[$belongKey]['nickname'] = $userNameArr[$b['belong_id']];
        }



        foreach ($belongtime as $nb){
            $nbTime[$nb['timerid']][] = $nb['nickname'];
        }


        foreach ($list as $key => $item) {
            $list[$key]['total_micro'] = $dataMicro[$item['starcode']]['total_micro'];
            $list[$key]['status_type'] = $item['status'];
            $nicknames = implode(',',$nbTime[$item['id']]);
            $list[$key]['nicknames'] = $nicknames;
            $list[$key]['status'] = self::getStatus($item['status']);
        }


        new \Think\Page($count, $pageNum);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $data['totalPages'] = $count;
        $data['pageNum'] = $pageNum;
        $data['page'] = $page;
        $data['totalPages'] = ceil($count / $pageNum);
        $data['list'] = $list;

        $this->ajaxReturn($data);
    }


    private function getStatus($status)
    {
        $arr = array(
            self::DELETE_ONLINE => '上线',
            self::DELETE_OFF => '下线',
            self::DELETE_TRUE => '删除'
        );

        return $arr[$status];
    }

    private function getSortName($key){
        $sortNames = array('第一期','第二期','第三期','第四期','第五期');

        return isset($sortNames[$key-1])?$sortNames[$key-1]:'未知';
    }
}
