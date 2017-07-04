<?php

namespace Home\Controller;
use Think\Controller;

/**
 * 咨询列表
 * @date  start at 2017-6-9
 *
 * Class InfoController
 * @package Home\Controller
 */
class InfoController extends Controller
{
    //软删除
    const DELETE_TRUE = 1;
    const DELETE_FALSE = 0;

    //const UPLOADSDIR = '.' .DIRECTORY_SEPARATOR. 'Public'. DIRECTORY_SEPARATOR;             // ./Public/uploads/info/
    //const STARDIR = 'uploads' . DIRECTORY_SEPARATOR . 'info' . DIRECTORY_SEPARATOR;         //  uploads/info/

	const UPLOADSDIR = "/Public/uploads/";
    const STARDIR = "info/";
    public function __construct()
    {
        parent::__construct();
        $this->assign('title', '资讯列表');
    }

    //模板显示
    public function listing()
    {
        $this->display('info/listing');
    }

    /**
     * 添加明星轮播图
     */
    public function addInfo()
    {
        //接收过滤提交数据
        $subject_name = I('post.subject_name', '', 'strip_tags');
        $subject_name = trim($subject_name);
        if (mb_strlen($subject_name,'utf8') > 16) {
            $return = array(
                'code' => -2,
                'message' => '标题过长'
            );
            return $this->ajaxReturn($return);
        }

        $local_pic = I('post.local_pic', '', 'strip_tags');
        $local_pic = trim($local_pic);

        $showpic_url = I('post.showpic_url', '', 'strip_tags');
        $showpic_url = trim($showpic_url);


        $remarks = I('post.remarks', '', 'strip_tags');
        $remarks = trim($remarks);
        if (mb_strlen($remarks,'utf8') > 40) {
            $return = array(
                'code' => -2,
                'message' => '资讯简介过长'
            );
            return $this->ajaxReturn($return);
        }

        $link_url = I('post.link_url', '', 'strip_tags');
        $link_url = trim($link_url);

        //非空提醒
        if (empty($subject_name) || empty($remarks)) {
            $return = array(
                'code' => -2,
                'message' => '请填写完整的信息'
            );
            return $this->ajaxReturn($return);
        }

        //唯一性判断
        $model = M('star_newsinfomation');
        $isExist = (int)$model->where("`subject_name` = '{$subject_name}'")->count('id');
        if ($isExist) {
            $return = array(
                'code' => -2,
                'message' => '该明星信息已存在！'
            );
            return $this->ajaxReturn($return);
        }



        //数据入库
        $model->subject_name = $subject_name;
        $model->showpic_url = $showpic_url;
        $model->local_pic = $local_pic;
        $model->link_url = $link_url;
        $model->remarks = $remarks;
        $model->news_time = date('Y-m-d H:i:s', time());
        $bool = ($model->add()) ? 0 : 1;

        //结果返回
        $return = array(
            'code' => $bool,
            'message' => 'success',
        );
        return $this->ajaxReturn($return);
    }

    /**
     * 接收的明星姓名查询明星对应信息
     */
    public function getStarInfo()
    {
        $model = M('star_starinfolist');
        $starname = I('post.starname', '', 'strip_tags');
        $starname = trim($starname);

        $item = $model->where("`star_name` = '{$starname}'")->find();

        $arr['star_code'] = '';
        $arr['star_name'] = "未找到 -{$starname}";
        if (count($item) > 0) {
            $arr['star_code'] = $item['star_code'];
            $arr['star_name'] = $item['star_name'];
        }

        return $this->ajaxReturn($arr);
    }

    /**
     * 图片上传
     */
    public function uploadFile()
    {
        $ret['file'] = '';
        $dir = './' . self::UPLOADSDIR . self::STARDIR;

        file_exists($dir) || (mkdir($dir, 0777, true) && chmod($dir, 0777));

        $hostUrl = 'http://'.$_SERVER['HTTP_HOST'];

        if (!is_array($_FILES['myfile']['name'])) {
			$path = pathinfo($_FILES['myfile']['name']);
			$fileName = date('ymdhis') . uniqid() . '.' . $path['extension'];
            move_uploaded_file($_FILES['myfile']['tmp_name'], $dir . $fileName);

            $ret['file'] =  $hostUrl . '/' . self::UPLOADSDIR . self::STARDIR . $fileName;
            $ret['local'] = $fileName;
        }

        echo json_encode($ret);
    }

    /**
     * 编辑信息
     * todo 设置图片的大小
     */
    public function editInfo()
    {
        $id = (int)$_POST['id'];
        if (!$id) {
            $return = array(
                'code' => -2,
                'message' => '未找到要更新的数据'
            );
            return $this->ajaxReturn($return);
        }

        //接收过滤提交数据
        $subject_name = I('post.subject_name', '', 'strip_tags');
        $subject_name = trim($subject_name);

        $showpic_url = I('post.showpic_url', '', 'strip_tags');
        $showpic_url = trim($showpic_url);

        $remarks = I('post.remarks', '', 'strip_tags');
        $remarks = trim($remarks);

        $link_url = I('post.link_url', '', 'strip_tags');
        $link_url = trim($link_url);

        //非空提醒
        if (empty($subject_name) || empty($remarks)) {
            $return = array(
                'code' => -2,
                'message' => '请填写完整的信息'
            );
            return $this->ajaxReturn($return);
        }
        if (mb_strlen($subject_name,'utf8') > 16) {
            $return = array(
                'code' => -2,
                'message' => '标题过长'
            );
            return $this->ajaxReturn($return);
        }

        if (mb_strlen($remarks,'utf8') > 40) {
            $return = array(
                'code' => -2,
                'message' => '资讯简介过长'
            );
            return $this->ajaxReturn($return);
        }

        $bool = 1;
        $model = M('star_newsinfomation');
        $item = $model->where("`id` = '{$id}'")->find();

        if (!empty($showpic_url)) {
            $model->showpic_url = $showpic_url;
        }

        $local_pic = I('post.local_pic', '', 'strip_tags');
        $local_pic = trim($local_pic);

        if (!empty($local_pic)) {
            $model->local_pic = $local_pic;
        }

        if (count($item) > 0) {
            //数据入库
            $model->id = $id;
            $model->subject_name = $subject_name;
            $model->link_url = $link_url;
            $model->remarks = $remarks;

            if ($model->save()) {
                $bool = 0;

                (!empty($showpic_url) && $item['link_url'] != $link_url) ? @unlink(self::UPLOADSDIR . self::STARDIR . $item['link_url']) : '';
            }
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
    public function delInfo()
    {
        //获取提交过来的ID值并进行分割 in 查询
        $ids = implode(',', $_POST['ids']);
        $model = M('star_newsinfomation');
        $list = $model->where(array('id'=>array('in', $ids)))->select();

        //已查到的存在的数据
        $idArr = array();
        foreach ($list as $item) {
            $idArr[] = $item['id'];
        }
        $idIn = implode(',', $idArr);

        //数据更新
        $data = array(
            'delete_flag' => self::DELETE_TRUE,
            'modify_time' => time()
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
     * 轮播列表
     * @todo 搜索
     */
    public function searchInfo()
    {
        $model = M('star_newsinfomation');
        $pageNum = I('post.pageNum', 5, 'intval');
        $page = I('post.page', 1, 'intval');
        //$map = array('delete_flag' => self::DELETE_FALSE);
        $map = array();

        $count = $model->where($map)->count();// 查询满足要求的总记录数
        $list = $model->where($map)->page($page, $pageNum)->order('id desc')->select();//获取分页数据
        foreach ($list as $key => $item) {
            $list[$key]['subject_name'] = mb_substr($item['subject_name'],0,6,'utf-8');
            $list[$key]['remarks'] = mb_substr($item['remarks'],0,8,'utf-8');
        }

        new \Think\Page($count, $pageNum);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $data['totalPages'] = $count;
        $data['pageNum'] = $pageNum;
        $data['page'] = $page;
        $data['totalPages'] = ceil($count / $pageNum);
        $data['list'] = $list;

        $this->ajaxReturn($data);
    }
}
