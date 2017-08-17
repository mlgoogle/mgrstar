<?php

namespace Home\Controller;

/**
 * 轮播列表
 * @date finished at 2017-6-9
 *
 * Class StarController
 * @package Home\Controller
 */
class StarController extends CTController
{
    //软删除
    const DELETE_TRUE = 1;
    const DELETE_FALSE = 0;

//const UPLOADSDIR = '\.' .DIRECTORY_SEPARATOR. 'Public'. DIRECTORY_SEPARATOR;             // ./Public/uploads/carousel/
//const STARDIR = 'uploads' . DIRECTORY_SEPARATOR . 'carousel' . DIRECTORY_SEPARATOR;     //  uploads/carousel/

	const UPLOADSDIR = "Public/uploads/";
    const STARDIR = "lucida/";
    const STARPIC = "pic/";
	//const STARDIR = "carousel/";

	public function __construct()
    {
        parent::__construct();


    }

    //模板显示
    public function carousel(){
        $this->assign('title', '轮播列表');
        $model = M('star_bannerlist');
        $count = $model->where("`delete_flag` = ".self::DELETE_FALSE)->count('id');

        $this->assign('count', $count);
        $this->display('star/carousel');
    }

    //明星帐号
    public function user(){
        $this->assign('title', '明星账号');
        $this->display('star/user');
    }

    /**
     * 添加明星轮播图
     */
    public function addCarousel(){

        //接收过滤提交数据
        $starname = I('post.starname', '', 'strip_tags');
        $starname = trim($starname);

        $pic_url = I('post.pic_url', '', 'strip_tags');
        $pic_url = trim($pic_url);

        $local_pic = I('post.local_pic', '', 'strip_tags');
        $local_pic = trim($local_pic);

        $starcode = (int)$_POST['starcode'];
        $sort = (int)$_POST['sort'];

//        if ($sort >= 5) {
//            $return = array(
//                'code' => -2,
//                'message' => '排序不能大于5'
//            );
//            return $this->ajaxReturn($return);
//        }
        //唯一性判断
        $model = M('star_starbrief');
        $map = array();

        $map['sort'] =  $sort;
        $map['is_arousel'] = 0;

        $count = $model->where($map)->count('id');
        if ($count) {
            $return = array(
                'code' => -2,
                'message' => '已有该排序'
            );
            return $this->ajaxReturn($return);
        }


        //非空提醒
        if (empty($starname) || empty($starcode)) {
            $return = array(
                'code' => -2,
                'message' => '请输入正确的明星名称和明星ID！'
            );
            return $this->ajaxReturn($return);
        }

        $starbrief = $model->where("`name` = '{$starname}' AND `is_arousel` = 1")->find();

        $uid = isset($starbrief['uid'])?$starbrief['uid']:0;

        //数据入库
        //$model->uid = $uid;
        //$model->name = $starname;
        //$model->code = $starcode;
        $data['pic1'] = $pic_url;
        $data['local_pic'] = $local_pic;
       // $model->local_pic = $local_pic;
        $data['sort'] = $sort;
        $data['is_arousel'] = 0;
        //$model->add_time = time();
        $bool = ($model->where(array('uid' => $uid))->save($data)) ? 0 : 1;

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
    public function getStarInfo(){
        $model = M('star_starbrief');
        $starname = I('post.starname', '', 'strip_tags');
        $starname = trim($starname);

        $item = $model->where("`name` = '{$starname}' AND status = 0 ")->find();// 默认 明星上线的才添加

        //$arr['star_code'] = '';
       // $arr['star_name'] = "未找到明星 -{$starname}";
        if (count($item) > 0) {
            $arr['star_code'] = $item['code'];
            $arr['star_name'] = $item['name'];
        }else{
            $return = array(
                'code' => -2,
                'message' => "未找到明星 -{$starname}"
            );
            return $this->ajaxReturn($return); exit();
        }

        return $this->ajaxReturn($arr);
    }

    /**
     * 图片上传
     */
    public function uploadFile()
    {
        $ret['file'] = '';
        $dir = './' . self::UPLOADSDIR . self::STARPIC;

        file_exists($dir) || (mkdir($dir, 0777, true) && chmod($dir, 0777));

        $hostUrl = 'http://'.$_SERVER['HTTP_HOST'];
        if (!is_array($_FILES['myfile']['name'])) {
            $path = pathinfo($_FILES['myfile']['name']);
			$fileName = date('ymdhis') . uniqid() . '.' . $path['extension'];
            move_uploaded_file($_FILES['myfile']['tmp_name'], $dir . $fileName);
            $ret['file'] =  $hostUrl . '/' . self::UPLOADSDIR . self::STARPIC . $fileName;
            $ret['local'] = $fileName;
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

        $sort = (int)$_POST['sort'];

        //唯一性判断
        $model = M('star_starbrief');


        $bool = 1;
        $item = $model->where("`uid` = '{$id}'")->find();

        $pic_url = I('post.pic_url', '', 'strip_tags');
        $pic_url = trim($pic_url);
        if (!empty($pic_url)) {
            $model->pic1 = $pic_url;
        }

        $local_pic = I('post.local_pic', '', 'strip_tags');
        $local_pic = trim($local_pic);

        if (!empty($local_pic)) {
            $model->local_pic = $local_pic;
        }

        if (count($item) > 0) {

            $model->uid = $id;
            $model->modify_time = date('Y-m-d H:i:s',time());
            $model->sort   = $sort;

            if ($model->save()) {
                $bool = 0;

                (!empty($pic_url) && $item['pic_url'] != $pic_url) ? @unlink('./' . self::UPLOADSDIR . self::STARDIR . $item['local_pic']) : '';
            }
        }


        //结果返回
        $return = array(
            'code' => $bool,
            'message' => '修改成功！',
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
        $model = M('star_bannerlist');
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
    public function searchCarousel(){
        $carousel = M('star_starbrief');
        $pageNum = I('post.pageNum', 10, 'intval');
        $page = I('post.page', 1, 'intval');
        $map = 'star_starbrief.is_arousel = 0 AND star_starbrief.status <> 2' ;

        $count = $carousel->where($map)->count();// 查询满足要求的总记录数

        $list = $carousel
            ->join('star_starinfolist ON star_starbrief.code = star_starinfolist.star_code','LEFT')->where($map)
            ->page($page, $pageNum)->order('star_starbrief.sort desc')->select();
       // $count = $carousel->where($map)->count();// 查询满足要求的总记录数
       // $list = $carousel->where($map)->page($page, $pageNum)->select();//获取分页数据->order('sort desc')

        new \Think\Page($count, $pageNum);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $data['totalPages'] = $count;
        $data['pageNum'] = $pageNum;
        $data['page'] = $page;
        $data['totalPages'] = ceil($count / $pageNum);
        $data['list'] = $list;

        $this->ajaxReturn($data);
    }

    public function getTimeStatus(){
        //0-预售 1-发售 2-流通
        $publish_arr = array('预售','发售','流通');

        $starcode = (int)$_POST['starcode'];

        $where['starcode'] = $starcode;

        $timerRow = M('star_timer')->where($where)->order('sort desc')->find();

        $publish_type = isset($timerRow['publish_type'])?(int)$timerRow['publish_type']:0;
        $return = array(
            'code' => 0,
            'publish_name' => $publish_arr[$publish_type],
        );

        return $this->ajaxReturn($return);

    }

    /**
     * 上下线
     */
    public function status(){
        //获取提交过来的ID值并进行分割 in 查询
        $code = (int)$_POST['code'];
        $model = M('star_starinfolist');
        $item = $model->where("`star_code` = '{$code}'")->find();

        if (count($item) == 0) {
            $return = array(
                'code' => -2,
                'message' => '未找到数据',
            );
            return $this->ajaxReturn($return);
        }



        if(!M('star_timer')->where('starcode = '.$code.' and status < 2' )->count()){

            $return = array(
                'code' => -2,
                'message' => '明星有发行时间才能上线！',
            );
            return $this->ajaxReturn($return);
        }

        //数据更新
        $data = array(
            'display_on_home' => !$item['display_on_home'],
            //'modify_time' => date('Y-m-d H:i:s', time())
        );
        $bool = ($model->where(array('star_code' => $item['star_code']))->save($data)) ? 0 : 1;

        //结果返回
        $return = array(
            'code' => $bool,
            'message' => 'success',
        );
        return $this->ajaxReturn($return);
    }

    //帐号相关内容


    public function userList(){
        $userInfo = M('star_userinfo');
        $pageNum = I('post.pageNum', 5, 'intval');
        $page = I('post.page', 1, 'intval');
        $map['starcode'] = array('exp','is not null');

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

        foreach ($list as $k=>$l){
            $list[$k]['starname'] = isset($nameArr[$l['starcode']])?$nameArr[$l['starcode']]:'';
        }

        new \Think\Page($count, $pageNum);// 实例化分页类 传入总记录数和每页显示的记录数(25)


        $data['totalPages'] = $count;
        $data['pageNum'] = $pageNum;
        $data['page'] = $page;
        $data['totalPages'] = ceil($count / $pageNum);
        $data['list'] = $list;
        return $this->ajaxReturn($data);
    }

    public function addUser(){

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
                'starcode' => $starcode
            );
            if(
                $userInfoModel->where("`phoneNum` = '{$phoneNum}'")->save($dataCode)){
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

    public function editUser(){
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

    /**
     * 接收的明星姓名查询明星对应信息
     */
    public function getStarUserInfo(){
        $model = M('star_starbrief');
        $starname = I('post.starname', '', 'strip_tags');
        $starname = trim($starname);

        $item = $model->where("`name` = '{$starname}' AND ( status = 0 OR status = 1 ) ")->find();// 默认 明星 没有删除的

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
