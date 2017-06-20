<?php

namespace Home\Controller;
use Think\Controller;

/**
 * 轮播列表
 * @date finished at 2017-6-9
 *
 * Class StarController
 * @package Home\Controller
 */
class StarController extends Controller
{
    //软删除
    const DELETE_TRUE = 1;
    const DELETE_FALSE = 0;

    const UPLOADSDIR = '.' .DIRECTORY_SEPARATOR. 'Public'. DIRECTORY_SEPARATOR;             // ./Public/uploads/carousel/
    const STARDIR = 'uploads' . DIRECTORY_SEPARATOR . 'carousel' . DIRECTORY_SEPARATOR;     //  uploads/carousel/

    public function __construct()
    {
        parent::__construct();
        $this->assign('title', '轮播列表');
    }

    //模板显示
    public function carousel()
    {

        $model = M('star_bannerlist_new');
        $count = $model->where("`delete_flag` = ".self::DELETE_FALSE."")->count('id');

        $this->assign('count', $count);
        $this->display('star/carousel');
    }

    /**
     * 添加明星轮播图
     */
    public function addCarousel()
    {
        //接收过滤提交数据
        $starname = I('post.starname', '', 'strip_tags');
        $starname = trim($starname);

        $pic_url = I('post.pic_url', '', 'strip_tags');
        $pic_url = trim($pic_url);

        $starcode = (int)$_POST['starcode'];
        $sort = (int)$_POST['sort'];

        //非空提醒
        if (empty($starname) || empty($starcode)) {
            $return = array(
                'code' => -2,
                'message' => '请输入正确的明星名称和明星ID！'
            );
            return $this->ajaxReturn($return);
        }

        //唯一性判断
        $Model = M('star_bannerlist_new');
        $isExist = (int)$Model->where("`starname` = '{$starname}'")->count('id');
        if ($isExist) {
            $return = array(
                'code' => -2,
                'message' => '该明星信息已存在！'
            );
            return $this->ajaxReturn($return);
        }

        //数据入库
        $Model->starname = $starname;
        $Model->starcode = $starcode;
        $Model->pic_url = $pic_url;
        $Model->sort = $sort;
        $Model->add_time = time();
        $bool = ($Model->add()) ? 0 : 1;

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
        $dir = self::UPLOADSDIR . self::STARDIR;

        file_exists($dir) || (mkdir($dir, 0777, true) && chmod($dir, 0777));

        if (!is_array($_FILES['myfile']['name'])) {
            $fileName = date('ymdhis') . '.' . pathinfo($_FILES['myfile']['name'])['extension'];
            move_uploaded_file($_FILES['myfile']['tmp_name'], $dir . $fileName);
            $ret['file'] = $fileName;
        }

        echo json_encode($ret);
    }

    /**
     * 编辑信息
     * todo 设置图片的大小
     */
    public function editCarousel()
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
        $model = M('star_bannerlist_new');
        $item = $model->where("`id` = '{$id}'")->find();

        $pic_url = I('post.pic_url', '', 'strip_tags');
        $pic_url = trim($pic_url);
        if (!empty($pic_url)) {
            $model->pic_url = $pic_url;
        }

        if (count($item) > 0) {
            $sort = (int)$_POST['sort'];

            $model->id = $id;
            $model->sort = $sort;
            $model->modify_time = time();

            if ($model->save()) {
                $bool = 0;

                (!empty($pic_url) && $item['pic_url'] != $pic_url) ? @unlink(self::UPLOADSDIR . self::STARDIR . $item['pic_url']) : '';
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
    public function delCarousel()
    {
        //获取提交过来的ID值并进行分割 in 查询
        $ids = implode(',', $_POST['ids']);
        $model = M('star_bannerlist_new');
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
    public function searchCarousel()
    {
        $carousel = M('star_bannerlist_new');
        $pageNum = I('post.pageNum', 5, 'intval');
        $page = I('post.page', 1, 'intval');
        $map = array('delete_flag' => self::DELETE_FALSE);

        $count = $carousel->where($map)->count();// 查询满足要求的总记录数
        $list = $carousel->where($map)->page($page, $pageNum)->order('id desc')->select();//获取分页数据

        new \Think\Page($count, $pageNum);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $data['totalPages'] = $count;
        $data['pageNum'] = $pageNum;
        $data['page'] = $page;
        $data['totalPages'] = ceil($count / $pageNum);
        $data['list'] = $list;

        $this->ajaxReturn($data);
    }
}