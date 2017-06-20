<?php

namespace Home\Controller;

use Think\Controller;

//use Think\Controller\restController;
class AgentController extends Controller
{

    private $user;

    public function __construct()
    {

        $this->user = session('user');
        if (!session('user')) {
            //$this->ajaxReturn(array('code'=>-1,'message'=>'fail','data'=>'not login'));

        }
    }

    public function getList()
    {

        $agent_info = M('agent_info');
        $member_info = M('member_info');

        $map = $this->getUserIdentity();


        if (!empty($_POST['memberid'])) {
            $map['memberid'] = $_POST['memberid'];
        }
        if (!empty($_POST['nickname'])) {
            $map['nickname'] = array('like', "%" . $_POST['nickname'] . "%");
        }
        if (!empty($_POST['cellphone'])) {
            $map['phone'] = array('like', "%" . $_POST['phone'] . "%");
        }




        $pageNum = isset($_POST['pageNum']) ? $_POST['pageNum'] : 5;
        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $count = $agent_info->where($map)->count();// 查询满足要求的总记录数

        $list = $agent_info->where($map)->page($page, $pageNum)->select();//获取分页数据

        $memberArr = array();
        foreach ($list as $l) {
            $memberArr[] = $l['memberId'];
        }

        $memberArr = array_filter(array_unique($memberArr));


        $whereIn['memberid'] = array('IN', $memberArr);
        $memberInfo = $member_info->where($whereIn)->select();;

        foreach ($memberInfo as $m) {
            $memberRow[$m['memberid']] = $m;
        }


        foreach ($list as $key => $value) {
            $list[$key]['memberInfo'] = $memberRow[$value['memberId']];
        }

        $Page = new \Think\Page($count, $pageNum);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $data['totalPages'] = $count;
        $data['pageNum'] = $pageNum;
        $data['page'] = $page;
        $data['totalPages'] = ceil($count / $pageNum);
        $data['list'] = $list;
        $this->ajaxReturn($data);
    }

    public function add()
    {
        $member_info = M('agent_info');
        $data['memberId'] = $memberId = $_POST['memberid'];
        $data['mark'] = $mark = $_POST['mark'];
        $data['nickname'] = $name = $_POST['nickname'];
        $data['phone'] = $_POST['phone'];
        $data['status'] = 0;
        $data['verify'] = 0;
        $data['uid'] = $this->user['id'];

        if (!$memberId) {
            $return = array(
                'code' => -2,
                'message' => '请选择机构！',
            );
            $this->ajaxReturn($return);
            return false;
        }

        if (!$mark) {
            $return = array(
                'code' => -2,
                'message' => '请填写区域经纪人编号！',
            );
            $this->ajaxReturn($return);
            return false;
        }


        if (!$name) {
            $return = array(
                'code' => -2,
                'message' => '请填写区域经纪人名称！',
            );
            $this->ajaxReturn($return);
            return false;
        }

        $res = $member_info->add($data);

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

    public function updateStatus()
    {
        $id = $_POST['id'];
        $data['status'] = $_POST['status'];
        foreach ($id as $key => $value) {
            # code...
            $map['code'] = $value;
            $res = M('agent_info')->where($map)->save($data);

        }
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

    public function updateVerify()
    {
        $id = $_POST['code'];
        $data['verify'] = $_POST['verify'];
        # code...
        $map['code'] = $id;
        $res = M('agent_info')->where($map)->save($data);
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


    public function updateAgent()
    {


        $id = $_POST['id'];
        $data['memberId'] = $_POST['memberId'];
        $data['mark'] = $_POST['mark'];
        $data['nickname'] = $_POST['nickname'];
        $data['phone'] = $_POST['phone'];



        $map['id']= $id;
        $res = M('agent_info')->where($map)->save($data);

        
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

    public function del()
    {
        $id = $_POST['id'];

        foreach ($id as $key => $value) {
            # code...
            $map['code'] = $value;
            $res = M('agent_info')->where($map)->delete();

        }
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


    protected function getUserIdentity(){
        //判断用户类型
        $identity_id = $this->user['identity_id'];
        $map = array();
        if ($identity_id == 4) { //经纪人用户
            //$identity_id = 3;
            return false;
        }if ($identity_id == 3) { //区域经纪人
        //$identity_id = 3;

            return false;

        } else if ($identity_id == 2) { //机构用户
            // $identity_id = 2;
            $map['memberId'] = $this->user['memberId'];
            //$map['agentId'] = array('gt', 0);
        } else {  // 交易所用户
            //$identity_id = 1;

        }

        return $map;

    }

    public function getAgentFind(){
        $agentId = I('post.agentId');

        if (!$agentId) {
            $this->ajaxReturn(array('list' => array()));
            return false;
        }

        //$memberId = $value['memberid'];
        $agent_info = M('agent_info');

        $data['list'] = $agentInfo = $agent_info->where(array('id' => $agentId))->find();

        $this->ajaxReturn($data);
    }

    public function exceFile()
    {
        import("Org.Util.PHPExcel");
        import("Org.Util.PHPExcel.Worksheet.Drawing");
        import("Org.Util.PHPExcel.Writer.Excel2007");
        $objPHPExcel = new \PHPExcel();
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objActSheet = $objPHPExcel->getActiveSheet();// 水平居中（位置很重要，建议在最初始位置）
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('A')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('B1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('C')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('D')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('E')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('F')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $objActSheet->setCellValue('A1', '商品货号');
        $objActSheet->setCellValue('B1', '商品名称');
        $objActSheet->setCellValue('C1', '商品图');
        $objActSheet->setCellValue('D1', '商品条码');
        $objActSheet->setCellValue('E1', '商品属性');
        $objActSheet->setCellValue('F1', '报价(港币)');
        // 设置个表格宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(80);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);

        // 垂直居中
        $objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('E')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('F')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $fileName = '报价表';
        $date = date("Y-m-d", time());
        $fileName .= "_{$date}.xls";

        $fileName = iconv("utf-8", "gb2312", $fileName);
        //重命名表
        // $objPHPExcel->getActiveSheet()->setTitle('test');
        //设置活动单指数到第一个表,所以Excel打开这是第一个表
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content - Type: application / vnd . ms - excel');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache - Control: max - age = 0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); //文件通过浏览器下载

    }

}
