<?php

namespace Home\Controller;
use Think\Controller;

/**
 * 明星列表
 * @date finished at 2017-6-13
 *
 * Class LucidaController
 * @package Home\Controller
 */
class LucidaController extends CTController
{
    //软删除    0上线 1下线 2软删除
    const DELETE_ONLINE = 0;
    const DELETE_OFF = 1;
    const DELETE_TRUE = 2;

    //const UPLOADSDIR = '.' .DIRECTORY_SEPARATOR. 'Public'. DIRECTORY_SEPARATOR;             // ./Public/uploads/carousel/
    //const STARDIR = 'uploads' . DIRECTORY_SEPARATOR . 'lucida' . DIRECTORY_SEPARATOR;     //  uploads/carousel/

    const UPLOADSDIR = "./Public/uploads/";
    const STARDIR = "lucida/";

    //明星经历
    const EXP_STATUS = 0;   //经历
    const ACH_STATUS = 1;   //成就
    const DEL_STATUS = 2;   //删除

    public function __construct()
    {
        parent::__construct();
        $this->assign('title', '明星列表');
    }

    //模板显示
    public function listing()
    {
        $this->display('lucida/listing');
    }

    /**
     * 添加
     */
    public function addLucida(){
        //接收过滤提交数据
        $name = I('post.name', '', 'strip_tags');
        $name = trim($name);

        $nationality = I('post.nationality', '', 'strip_tags');
        $nationality = trim($nationality);

        $birth = I('post.birth', '', 'strip_tags');
        $birth = trim($birth);

        $work = I('post.work', '', 'strip_tags');
        $work = trim($work);

        $colleage = I('post.colleage', '', 'strip_tags');
        $colleage = trim($colleage);

        $resident = I('post.resident', '', 'strip_tags');
        $resident = trim($resident);

        $worth = I('post.worth', '', 'strip_tags');
        $worth = trim($worth);

        //$appoint_id = (int)$_POST['appoint_id'];
        $appointIds = is_array($_POST['appointIds'])?$_POST['appointIds']:0;


        $weibo = (int)$_POST['weibo'];
        $code = (int)$_POST['code'];

        //非空提醒
        $flag = true;
        if (empty($name) || empty($code)) {
            $flag = false;
            $return = array(
                'code' => -2,
                'message' => '请输入正确的明星名称和明星ID！'
            );
            return $this->ajaxReturn($return);
        }

        //唯一性判断
        $model = M('star_starbrief');
        $isExist = $model->where("`name` = '{$name}'")->count('uid');
        $isCode = (int)$model->where("`code` = '{$code}'")->count('uid');

        if ($isExist || $isCode) {
            $flag = false;
            $return = array(
                'code' => -2,
                'message' => '该明星信息已存在！'
            );
            return $this->ajaxReturn($return);
        }

        $model->name = $name;
        $model->nationality = $nationality;
        $model->birth = $birth;
        $model->work = $work;
        $model->code = $code;

        $pic_flag = 0;
        $hostUrl = 'http://'.$_SERVER['HTTP_HOST'];

        $picArr = array();
        for ($i = 1; $i < 5; $i++) {
            $i = ($i > 5) ? 1 : $i;
            if (isset($_POST['pic'.$i])) {
                $pic_flag++;
                $key = 'pic' . $i;
                $pic = I("post.$key", '', 'strip_tags');
                $pic = trim($pic);
                $picArr[$key] = $model->$key = $hostUrl . "/Public/uploads/" . self::STARDIR . $pic;
            }
        }

        if ($pic_flag == 0) {
            $return = array(
                'code' => -2,
                'message' => '请上传图片！'
            );
            return $this->ajaxReturn($return);
        }

        if(!$appointIds){
            $return = array(
                'code' => -2,
                'message' => '至少选择一个时间使用范围！'
            );
            return $this->ajaxReturn($return);
        }


        $model->colleage = $colleage;
        $model->resident = $resident;
        $model->worth = $worth;
       // $model->appoint_id = $appoint_id;
        $model->weibo = $weibo;


        $model->add_time = date('Y-m-d H:i:s', time());
        $id = 0;


        if ($flag && $pic_flag) {
            $id = $model->add();

            //明星时间管理 对应插入
            $star_timer = M('star_timer');
            $star_timer->starname = $name;
            $star_timer->starcode = $code;
            $star_timer->add_time = date('Y-m-d H:i:s', time());
            $star_timer->add();

            // star_starinfolist 后期加这个表

            $startInfoList  = M('star_starinfolist');

            $startInfoList->star_code = $code;
            $startInfoList->star_name = $name;
            $startInfoList->star_phone = $code; // 暂时是明星的 code
            $startInfoList->star_pic = isset($picArr['pic1'])?$picArr['pic1']:'';
            $startInfoList->add();
            //

            if($id){
                //  时间使用范围 明星关联的约见类型添加
                $star_meet_servicerel = M('star_meet_servicerel');
                foreach ($appointIds as $mid) {
                    $meetDataList[] = array('starcode'=>$code,'mid'=>$mid);
                }

                $star_meet_servicerel->addAll($meetDataList);

            }



        }

        //结果返回
        $return = array(
            'id' => $id,
            'code' => ($id) ? 1 : -2,
            'message' => ($id) ? '添加成功！' : '添加失败！',
        );
        return $this->ajaxReturn($return);
    }

    /**
     * 添加经历、成就
     */
    public function addExp ()
    {
        $bool = 1;
        $id = (int)$_POST['uid'];

        $model = M('star_starbrief');
        $item = $model->where("`code` = '{$id}'")->find();

        if (!isset($item['uid'])) {
            $return = array(
                'code' => -2,
                'message' => '未找到对应的明星信息'
            );
            return $this->ajaxReturn($return);
        }

        $key = I('post.key', '', 'strip_tags');
        $key = trim($key);

        $val = I('post.val', '', 'strip_tags');
        $val = trim($val);

        $val = I('post.val', '', 'strip_tags');
        $val = trim($val);

        if (empty($key) || empty($val)) {
            $return = array(
                'code' => -2,
                'message' => '请检查-数据为空'
            );
            return $this->ajaxReturn($return);
        }

        $expModel = M('star_experience');
        $status = ($key == 'exp') ? 0 : 1;

        $expModel->star_code = $item['code'];

        $expModel->star_experience = $val;

        $expModel->status = $status;

        $bool = ($expModel->add()) ? 0 : 1;

        //结果返回
        $return = array(
            'code' => $bool,
            'message' => !($bool) ? '成功' : '失败',
        );
        return $this->ajaxReturn($return);
    }

    /**
     * 删除经历
     */
    public function exp()
    {
        //获取提交过来的ID值并进行分割 in 查询
        $id = (int)$_POST['id'];
        $model = M('star_experience');
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
            'status' => 2
        );
        $bool = ($model->save($data)) ? 0 : 1;

        //结果返回
        $return = array(
            'code' => $bool,
            'message' => !($bool) ? '成功' : '失败',
        );
        return $this->ajaxReturn($return);
    }

    /**
     * 接收的明星姓名查询明星对应信息
     */
    public function getStarInfo()
    {
        $model = M('star_starinfolist');
        $name = I('post.starname', '', 'strip_tags');
        $name = trim($name);

        $item = $model->where("`star_name` = '{$name}'")->find();

        $arr['star_code'] = '';
        $arr['star_name'] = "未找到 -{$name}";
        if (count($item) > 0) {
            $arr['star_code'] = $item['star_code'];
            $arr['star_name'] = $item['star_name'];
        }

        return $this->ajaxReturn($arr);
    }

    /**
     * 图片上传
     * @todo 只能上传5张
     */
    public function uploadFile()
    {
        $ret = array();
        $dir = self::UPLOADSDIR . self::STARDIR;
        file_exists($dir) || (mkdir($dir, 0777, true) && chmod($dir, 0777));

        $files = $_FILES['myfile']['name'];

        for ($i = 0; $i < count($files); $i++) {
            $path = pathinfo($_FILES['myfile']['name'][$i]);
			$fileName = date('ymdhis') . uniqid() .'.' . $path['extension'];

            move_uploaded_file($_FILES['myfile']['tmp_name'][$i], $dir . $fileName);
            $ret[] = $fileName;
        }

        echo json_encode($ret);
    }

    /**
     * 编辑信息
     * @todo 设置图片的大小
     * @todo 已有4张每上传一张将第一张替换，而非填充第五章
     */
    public function editLucida()
    {
        $bool = 1;
        $id = (int)$_POST['uid'];

        $model = M('star_starbrief');
        $item = $model->where("`uid` = '{$id}'")->find();

        if (!isset($item['uid'])) {
            $return = array(
                'code' => -2,
                'message' => '未找到要更新的数据'
            );
            return $this->ajaxReturn($return);
        }

        //接收过滤提交数据
      //  $name = I('post.name', '', 'strip_tags');
      //  $name = trim($name);

        $nationality = I('post.nationality', '', 'strip_tags');
        $nationality = trim($nationality);

        $birth = I('post.birth', '', 'strip_tags');
        $birth = trim($birth);

        $work = I('post.work', '', 'strip_tags');
        $work = trim($work);

        $colleage = I('post.colleage', '', 'strip_tags');
        $colleage = trim($colleage);

        $resident = I('post.resident', '', 'strip_tags');
        $resident = trim($resident);

        $worth = I('post.worth', '', 'strip_tags');
        $worth = trim($worth);

        //$appoint_id = (int)$_POST['appoint_id'];
        $appointIds = is_array($_POST['appointIds'])?$_POST['appointIds']:0;

        $weibo = (int)$_POST['weibo'];
       // $code = (int)$_POST['code'];

        //非空提醒
//        if (empty($name) || empty($code)) {
//            $return = array(
//                'code' => -2,
//                'message' => '请输入正确的明星名称和明星ID！'
//            );
//            return $this->ajaxReturn($return);
//        }

        //唯一性判断
//        $isExist = false;(int)$model->where("`name` = '{$name}' AND `uid` !={$item['uid']}")->count('uid');
//        $isCode = (int)$model->where("`code` = '{$code}' AND `uid` !={$item['uid']}")->count('uid');
//
//        if ($isExist || $isCode) {
//            $return = array(
//                'code' => -2,
//                'message' => '该明星信息已存在！'
//            );
//            return $this->ajaxReturn($return);
//        }

        if(!$appointIds){
            $return = array(
                'code' => -2,
                'message' => '至少选择一个时间使用范围！'
            );
            return $this->ajaxReturn($return);
        }


        if (count($item) > 0) {
            $model->uid = $item['uid'];
           // $model->name = $name;
            $model->nationality = $nationality;
            $model->birth = $birth;
            $model->work = $work;
          //  $model->code = $code;

            $pic_flag = 0;
            $hostUrl = 'http://'.$_SERVER['HTTP_HOST'];
           // dump($_POST['pic'.$i]);exit;

            $picArr = $_POST['pic'];
            for ($i = 1; $i <= 5; $i++) {
                if (isset($_POST['pic'.$i])) {
                    $pic_flag++;
                    $key = 'pic' . $i;
                    $pic = I("post.$key", '', 'strip_tags');
                    $pic = trim($pic);
                    $model->$key = $hostUrl. "/Public/uploads/" . self::STARDIR . $pic;
                    if (!empty($pic) && $item[$key] != $pic) {
                        @unlink(self::UPLOADSDIR . self::STARDIR . $item[$key]);
                    }
                }

//                if($picArr){ //修改
//                    $pic = trim($picArr[$i-1]);
//                    $key = 'pic' . $i;
//                    $model->$key = $hostUrl. "/Public/uploads/" . self::STARDIR . $pic;
//                }
//
//                dump($picArr);
//                dump($key);
//                dump($model->$key);

                if (!empty($item['pic1']) || !empty($item['pic2']) || !empty($item['pic3']) || !empty($item['pic4']) || !empty($item['pic5'])) {
                    $pic_flag = 1;
                }
            }


            if ($pic_flag == 0) {
                $return = array(
                    'code' => -2,
                    'message' => '请上传图片！'
                );
                return $this->ajaxReturn($return);
            }


            $model->colleage = $colleage;
            $model->resident = $resident;
            $model->worth = $worth;
           // $model->appoint_id = $appoint_id;
            $model->weibo = $weibo;


            $model->modify_time = date('Y-m-d H:i:s', time());

            if ($model->save()) {
                $bool = 1;

                if($id){

                    $star_meet_servicerel = M('star_meet_servicerel');
                    //修改先删除以前的
                    $star_meet_servicerel ->where(array('starcode'=>$code))->delete();
                    //  时间使用范围 明星关联的约见类型添加
                    foreach ($appointIds as $mid) {
                        $meetDataList[] = array('starcode'=>$code,'mid'=>$mid);
                    }

                    $star_meet_servicerel->addAll($meetDataList);

                }

            }else{
                $return = array(
                    //'id' => $item['uid'],
                    'code' => -2,
                    'message' => '修改失败！',
                );
                return $this->ajaxReturn($return);
            }
        }

        //结果返回
        $return = array(
            'id' => $item['uid'],
            'code' => $bool,
            'message' => '修改成功！',
        );
        return $this->ajaxReturn($return);
    }

    /**
     * 软删除
     */
    public function delLucida()
    {
        //获取提交过来的ID值并进行分割 in 查询
        $ids = implode(',', $_POST['ids']);
        $model = M('star_starbrief');
        $list = $model->where(array('uid'=>array('in', $ids)))->select();

        //已查到的存在的数据
        $idArr = array();
        foreach ($list as $item) {
            $idArr[] = $item['uid'];
        }
        $idIn = implode(',', $idArr);

        //数据更新
        $data = array(
            'status' => self::DELETE_TRUE,
            'modify_time' => date('Y-m-d H:i:s', time())
        );
        $bool = ($model->where(array('uid'=>array('in', $idIn)))->save($data)) ? 0 : 1;

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
        $model = M('star_starbrief');
        $item = $model->where("`uid` = '{$id}'")->find();

        if (count($item) == 0) {
            $return = array(
                'code' => -2,
                'message' => '未找到数据',
            );
            return $this->ajaxReturn($return);
        }

        //数据更新
        $data = array(
            'uid' => $item['uid'],
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
     * 明星列表
     * @todo 搜索
     */
    public function searchLucida()
    {
        $carousel = M('star_starbrief');
        $pageNum = I('post.pageNum', 5, 'intval');
        $page = I('post.page', 1, 'intval');

        $count = $carousel->where('status !=' . self::DELETE_TRUE)->count();// 查询满足要求的总记录数
        $list = $carousel->where('status !=' . self::DELETE_TRUE)->page($page, $pageNum)->order('uid desc')->select();//获取分页数据

        $i = 1;
        foreach ($list as $key => $item) {
           // $i = ($i > 5) ? 1 : $i;
            $path = pathinfo($item['pic'.$i]);
            //$path = $item['pic'.$i];
            $list[$key]['pic'.$i] = $path['basename'];
           // $i++;
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

    public function info()
    {
        $appoints = M('meet_service_def')->where('status =' . self::DELETE_ONLINE)->select();

        if (!isset($_GET['id'])) {
            $this->assign('appoints', $appoints);
            return $this->display('lucida/info');
        }

        $uid = (int)$_GET['id'];
        $model = M('star_starbrief');
        $item = $model->where("`uid` = '{$uid}'")->find();

        if (count($item) == 0) {
            exit('非法操作');
        }
        for ($i = 0; $i < 5; $i++) {
           // $path = pathinfo($item['pic'.$i]);
            $item['pic'.$i] = $item['pic'.$i];//$path['basename'];
        }

        $expList = M('star_experience')->where('star_code =' . $item['code'])->select();

        $meetList = M('star_meet_servicerel')->field('mid')->where('starcode =' . $item['code'])->select();

        $meetData = array();
        foreach ($meetList as $k=>$m){
            $meetData[] = isset($m['mid'])?intval($m['mid']):0;
        }

        foreach ($appoints as $k=>$a){
            if(in_array($a['mid'],$meetData)){
                $appoints[$k]['checked'] = 1;
            }else{
                $appoints[$k]['checked'] = 0;
            }
        }

        $experiences = array();
        $achieve = array();

        foreach ($expList as $val) {
            if ($val['status'] == self::EXP_STATUS) {
                $experiences[] = $val;
            } else if ($val['status'] == self::ACH_STATUS) {
                $achieve[] = $val;
            }
        }
        $pics = array();

        for ($i = 1; $i < 6; $i++) {
            if (!empty($item['pic'.$i])) {
                $pics['pic'.$i] = $item['pic'.$i];
                $path = pathinfo($item['pic'.$i]);
                $pathPics['pic'.$i] = $path['basename'];
            }
        }

        $this->assign('appoints', $appoints);
        $this->assign('pics', $pics);
        $this->assign('pathPics', $pathPics);
        $this->assign('item', $item);
        $this->assign('exp', $experiences);
        //$this->assign('meet', $meetList);
        $this->assign('ach', $achieve);
        $this->display('lucida/info');
    }

    public function rvpic()
    {
        $uid = (int)$_POST['uid'];
        $key = I('post.key', '', 'strip_tags');
        $key = trim($key);

        $model = M('star_starbrief');
        $item = $model->where("`uid` = '{$uid}'")->find();
        if (count($item) == 0) {
            exit('非法操作');
        }

        $bool = false;
        if (isset($item[$key])) {
            $data = array(
                'uid' => $item['uid'],
                $key => ''
            );
            $bool = $model->save($data);
        }

        //结果返回
        $return = array(
            'id' => $item['uid'],
            'code' => $bool,
            'message' => ($bool) ? 1 : -2,
        );
        return $this->ajaxReturn($return);
    }

    /**
     * 数据状态
     * @param $status
     * @return mixed
     */
    private function getStatus($status)
    {
        $arr = array(
            self::DELETE_ONLINE => '上架',
            self::DELETE_OFF => '下架',
            self::DELETE_TRUE => '删除'
        );

        return $arr[$status];
    }
}
