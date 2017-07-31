<?php

namespace Home\Controller;

use Think\Controller;

/**
 * 约见类型
 * @date started at 2017-6-12
 *
 * Class AppointController
 * @package Home\Controller
 */
class AppointController extends CTController
{
    //软删除    0上线 1下线 2软删除
    const DELETE_ONLINE = 0;
    const DELETE_OFF = 1;
    const DELETE_TRUE = 2;

    public function __construct()
    {
        parent::__construct();
        $this->assign('title', '约见类型管理');
    }

    //模板显示
    public function appoint()
    {
        $this->display('appoint/appoint');
    }

    /**
     * 添加
     */
    public function addAppoint(){
        //接收过滤提交数据

        $name = I('post.appointname', '', 'strip_tags');
        $name = trim($name);
        if (mb_strlen($name,'utf8') > 6) {
            $return = array(
                'code' => -2,
                'message' => '约见类型过长'
            );
            return $this->ajaxReturn($return);
        }

        $micro = (int)$_POST['micro'];
        if ($micro < 601) {
            $return = array(
                'code' => -2,
                'message' => '消耗秒数不能低于600秒'
            );
            return $this->ajaxReturn($return);
        }

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

        $url1  =  I('post.showpic_url', '', 'strip_tags');
        $url2  =  I('post.pic_url2', '', 'strip_tags');

        $local_pic = I('post.local_pic', '', 'strip_tags');

        if(empty($url1)) {
            $return = array(
                'code' => -2,
                'message' => '请上传选中约见配图！'
            );
            return $this->ajaxReturn($return);
        }

        if(empty($url2)) {
            $return = array(
                'code' => -2,
                'message' => '请上传未选中约见配图！'
            );
            return $this->ajaxReturn($return);
        }


        //数据入库
        $model->name = $name;
        $model->price = $micro;
        $model->url1 = $url1;
        $model->url2 = $url2;
        $model->local_pic = $local_pic;
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
     */
    public function editAppoint()
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

        $name = I('post.appointname', '', 'strip_tags');
        $name = trim($name);
        if (mb_strlen($name,'utf8') > 6) {
            $return = array(
                'code' => -2,
                'message' => '约见类型过长'
            );
            return $this->ajaxReturn($return);
        }

        $micro = (int)$_POST['micro'];
        if ($micro < 601) {
            $return = array(
                'code' => -2,
                'message' => '消耗秒数不能低于600秒'
            );
            return $this->ajaxReturn($return);
        }


        $url1  = I('post.showpic_url', '', 'strip_tags');
        $url2  = I('post.pic_url2', '', 'strip_tags');

        $local_pic = I('post.local_pic', '', 'strip_tags');

        if(empty($url1)) {
            $return = array(
                'code' => -2,
                'message' => '请上传选中约见配图！'
            );
            return $this->ajaxReturn($return);
        }

        if(empty($url2)) {
            $return = array(
                'code' => -2,
                'message' => '请上传未选中约见配图！'
            );
            return $this->ajaxReturn($return);
        }

        if (count($item) > 0) {
            $model->name = $name;

            $model->mid = $id;
            $model->price = $micro;

            $model->url1 = $url1;
            $model->url2 = $url2;
            $model->local_pic = $local_pic;

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
    public function delAppoint()
    {
        //获取提交过来的ID值并进行分割 in 查询
        $ids = implode(',', $_POST['ids']);
        $model = M('meet_service_def');
        $list = $model->where(array('mid' => array('in', $ids)))->select();

        //已查到的存在的数据
        $idArr = array();
        foreach ($list as $item) {
            $idArr[] = $item['mid'];
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

    /**
     * 列表
     * @todo 搜索
     */
    public function searchAppoint()
    {
        $appoint = M('meet_service_def');
        $pageNum = I('post.pageNum', 5, 'intval');
        $page = I('post.page', 1, 'intval');

        $count = $appoint->where('status !=' . self::DELETE_TRUE)->count();// 查询满足要求的总记录数
        $list = $appoint->where('status !=' . self::DELETE_TRUE)->page($page, $pageNum)->order('mid desc')->select();//获取分页数据

        foreach ($list as $key => $item) {
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