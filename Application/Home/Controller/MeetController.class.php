<?php

namespace Home\Controller;

use Think\Controller;

/**
 * 约见管理
 * @date started at 2017-6-15
 *
 * Class MeetController
 * @package Home\Controller
 */
class MeetController extends CTController
{
    //1已约见    2已拒绝 3已完成
    const TTYPE_MEET = 1;
    const TYPE_REJ = 2;
    const TYPE_COM = 3;

    public function __construct()
    {
        parent::__construct();
        $this->assign('title', '约见管理');
    }

    //模板显示
    public function meet()
    {
        $this->display('meet/meet');
    }

    /**
     * 添加
     */
    public function addMeet()
    {
        //接收过滤提交数据
        $name = I('post.meentname', '', 'strip_tags');
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
        $model = M('meet_service_def');
        $isExist = (int)$model->where("`name` = '{$name}'")->count('mid');
        if ($isExist) {
            $return = array(
                'code' => -2,
                'message' => '该类型已存在！'
            );
            return $this->ajaxReturn($return);
        }

        //数据入库
        $model->name = $name;
        $model->price = $micro;
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
    public function editMeet()
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
        $model = M('meet_service_def');
        $item = $model->where("`mid` = '{$id}'")->find();

        if (count($item) > 0) {
            $micro = (int)$_POST['micro'];
            $name = I('post.name', '', 'strip_tags');
            $name = trim($name);
            $model->name = $name;

            $model->id = $id;
            $model->price = $micro;
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
    public function delMeet()
    {
        //获取提交过来的ID值并进行分割 in 查询
        $ids = implode(',', $_POST['ids']);
        $model = M('meet_service_def');
        $list = $model->where(array('mid' => array('in', $ids)))->select();

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
        $bool = ($model->where(array('mid' => array('in', $idIn)))->save($data)) ? 0 : 1;

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
        $model = M('meet_service_def');
        $item = $model->where("`mid` = '{$id}'")->find();

        if (count($item) == 0) {
            $return = array(
                'code' => -2,
                'message' => '未找到数据',
            );
            return $this->ajaxReturn($return);
        }

        //数据更新
        $data = array(
            'mid' => $item['mid'],
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

    public function mtype()
    {
        //获取提交过来的ID值并进行分割 in 查询
        $id = (int)$_POST['id'];
        $status = (int)$_POST['status'];

        $model = M('user_star_meetrel');
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
            'meet_type' => $status
        );

        $bool = $model->save($data);

        //结果返回
        $return = array(
            'code' => ($bool) ? 1: -2,
            'message' => ($bool) ? '修改成功！' : '修改失败！',
        );

        return $this->ajaxReturn($return);
    }

    /**
     * 列表
     * @todo 搜索
     */
    public function searchMeet()
    {
        $meent = M('user_star_meetrel');
        $pageNum = I('post.pageNum', 5, 'intval');
        $page = I('post.page', 1, 'intval');

        $severModel = M('meet_service_def');
        $serverList = $severModel->where("`status` != 2")->select();
        $sArr = array();

        foreach ($serverList as $sev) {
            $sArr[$sev['mid']] = $sev;
        }

        $count = $meent->count();// 查询满足要求的总记录数
        $list = $meent->page($page, $pageNum)->order('order_time desc')->select();//获取分页数据

        foreach ($list as $key => $item) {
            $list[$key]['username'] = M('star_userinfo')->where('uid = ' . (int)$item['uid'])->getField('nickname');
            $list[$key]['starname'] = M('star_starinfolist')->where('star_code = ' . (int)$item['starcode'])->getField('star_name');
            $list[$key]['status'] = self::getStatus($item['meet_type']);
            $list[$key]['active'] = (isset($sArr[$item['mid']]['name'])) ? $sArr[$item['mid']]['name'] : '';
            $list[$key]['price'] = (isset($sArr[$item['mid']]['price'])) ? $sArr[$item['mid']]['price'] : '';
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
            self::TTYPE_MEET => '已约见',
            self::TYPE_REJ => '已拒绝',
            self::TYPE_COM => '已完成'
        );

        return $arr[$status];
    }
}