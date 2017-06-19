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
class TimerController extends Controller
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
    public function timer()
    {
        $this->display('timer/listing');
    }

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
        $own = M('fans_own_timer');
        $userTimeList = $own->where("`timerid` = '{$timer['id']}' AND `status` = 0")->select();
        $ownTimer = 0;

        //已分配过时间的用户
        $fansUidArr = array();
        foreach ($userTimeList as $user) {
            $fansUidArr[] = $user['fansid'];
            $ownTimer += ((int)$user['secs']);
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
                $own->fansid = $u;
                $own->nickname = $userNick[$u]['nickname'];
                $own->secs = $fTime;
                $own->add_time = date('Y-m-d H:i:s', time());
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
    public function add()
    {
        $fans = I('post.fans', '', 'strip_tags');
        $fans = trim($fans);

        if (empty($fans)) {
            $return['message'] = '请输入粉丝昵称或UID';
            return $this->ajaxReturn($return);
        }

        $secs = (int)$_POST['secs'];
        if($secs < 1) {
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
        if (count($userInfo) == 0) {
            $return['message'] = '未找到该粉丝';
            return $this->ajaxReturn($return);
        }

        //输入过大的值
        $totalTime = (int)$timer['micro'];
        if ($secs > $totalTime) {
            $return['message'] = '分配的时间过多';
            return $this->ajaxReturn($return);
        }

        //已拥有改明星的用户时间值
        $own = M('fans_own_timer');
        $userTimeList = $own->where("`timerid` = '{$timer['id']}' AND `status` = 0")->select();
        $ownTimer = 0;
        foreach ($userTimeList as $user) {
            $ownTimer += ($user['secs']);
        }

        $isExist = $own->where("`timerid` = '{$timer['id']}' AND `fansid` = '{$userInfo['uid']}' AND `status` = 0")->count('id');
        if ($isExist) {
            $return['message'] = '粉丝已分配过时间';
            return $this->ajaxReturn($return);
        }

        //可用时间
        $free = (($totalTime - $ownTimer) <= 0) ? 0 : ($totalTime - $ownTimer);
        $free = (int)$free;
        if ($free == 0 || $secs > $free) {
            $return['message'] = '可分配时间为 :' . $free;
            return $this->ajaxReturn($return);
        }

        $bool = false;
        if ($free > $secs) {
            $own->timerid = $timer['id'];
            $own->fansid = $userInfo['uid'];
            $own->nickname = $userInfo['nickname'];
            $own->secs = $secs;
            $own->add_time = date('Y-m-d H:i:s', time());

            $return['fans'] = (!empty($userInfo['nickname'])) ? $userInfo['nickname'] : $userInfo['uid'];
            $return['secs'] = $secs;

            $return['total'] = count($userTimeList) + 1;
            $return['free'] = $free - $secs;

            $bool = $own->add();
        }

        $return['code'] = ($bool) ? 1 : -2;
        $return['message'] = ($bool) ? 'Success' : 'Error';
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
        $return['fansid'] = 0;

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
            $return['fansid'] = $item['uid'];
            $return['message'] = 'Success';
            return $this->ajaxReturn($return);
        }
    }

    /**
     * 分配时间列表
     */
    public function info()
    {
        $id = (int)$_GET['id'];
        $model = M('star_timer');
        $item = $model->where("`id` = '{$id}'")->find();
        if (!$item) exit('非法输入');

        $item['status'] = self::getStatus($item['status']);

        $timer = M('fans_own_timer')->where("`timerid` = '{$item['id']}' AND `status` = " . self::DELETE_ONLINE)->order('secs desc')->select();

        $secs = 0;
        foreach ($timer as $t) {
            $secs += (int)$t['secs'];
        }
        $micro = (int)$item['micro'];
        $free = (($micro - $secs < 0)) ? 0 : $micro - $secs;

        $this->assign('free', $free);
        $this->assign('timer', $timer);
        $this->assign('item', $item);
        $this->display('timer/info');
    }

    /**
     * 软删除分配粉丝
     */
    public function fstatus()
    {
        //获取提交过来的ID值并进行分割 in 查询
        $id = (int)$_POST['id'];
        $model = M('fans_own_timer');
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
            'free' => (int)$_POST['free'] + $item['secs'],
            'total' => (int)$_POST['total'] + 1,
            'code' => $bool,
            'message' => (!$bool) ? 'Success' : 'Error',
        );
        return $this->ajaxReturn($return);
    }

    /**
     * 添加
     */
    public function addTimer()
    {
        //接收过滤提交数据
        $name = I('post.timername', '', 'strip_tags');
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
        $model = M('star_timer');
        $isExist = (int)$model->where("`timername` = '{$name}'")->count('id');
        if ($isExist) {
            $return = array(
                'code' => -2,
                'message' => '该类型已存在！'
            );
            return $this->ajaxReturn($return);
        }

        //数据入库
        $model->timername = $name;
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
    public function editTimer()
    {
        $return['code'] = -2;
        $return['message'] = 'Error';

        $id = (int)$_POST['id'];
        if (!$id) {
            $return['message'] = '非法操作';
            return $this->ajaxReturn($return);
        }

        $micro = (int)$_POST['micro'];
        if ($micro < 601) {
            $return['message'] = '输入的时间过低';
            return $this->ajaxReturn($return);
        }

        $model = M('star_timer');
        $item = $model->where("`id` = '{$id}'")->find();
        if (count($item) < 1) {
            $return['message'] = '未找到要更新的数据';
            return $this->ajaxReturn($return);
        }
        $timer = M('fans_own_timer')->where("`timerid` = '{$item['id']}' AND `status` = " . self::DELETE_ONLINE)->select();
        $secs = 0;
        foreach ($timer as $t) {
            $secs += (int)$t['secs'];
        }
        if ($micro < $secs) {
            $return['message'] = '';
            return $this->ajaxReturn($return);
        }
        $id = $item['id'];
        if (count($item) > 0) {
            $model->id = $id;
            $model->micro = $micro;
            $model->modify_time = date('Y-m-d H:i:s', time());

            $bool = $model->save();
        }

        //结果返回
        $return = array(
            'id' => $id,
            'code' => ($bool) ? 0 : 1,
            'message' => 'Success',
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
    public function searchTimer()
    {
        $timer = M('star_timer');
        $pageNum = I('post.pageNum', 5, 'intval');
        $page = I('post.page', 1, 'intval');

        $count = $timer->count();// 查询满足要求的总记录数
        $list = $timer->page($page, $pageNum)->order('id desc')->select();//获取分页数据

        foreach ($list as $key => $item) {
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
}