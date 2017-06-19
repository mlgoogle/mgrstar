<?php
namespace Home\Controller;
use Think\Controller;
//use Think\Controller\restController;
class UserController extends Controller {

    private $user;

     public function __construct(){
        # code...
        $this->user = session('user');
        parent::__construct();
        if(!session('user')){
            //$this->ajaxReturn(array('code'=>-1,'message'=>'fail','data'=>'not login'));

        }
    }

    public function getList(){

        $pageNum = isset($_POST['pageNum'])?$_POST['pageNum']:5;
        $page = isset($_POST['page'])?$_POST['page']:1;
        $timeStart = date("Y-m-d H:i:s",strtotime($_POST['startTime']));
        $timeEnd = date("Y-m-d H:i:s",strtotime($_POST['endTime']));


        //权限设置 访问
        /* 新需求
        $identity_id = $this->user['identity_id'];

        if($identity_id==2){ //机构访问
            $map['memberId'] = $this->user['memberId'];
        }else if($identity_id==3){// 经纪人访问
            $map['memberId'] = $this->user['memberId'];
            $map['agentId'] = $this->user['agentId'];
        }
        */

        //旧需求
        $memberId = $this->user['memberId'];
        if($memberId){
            $map['memberId'] = $memberId;
        }


        if(!empty($_POST['nickname'])){
            $map['nickname'] = array('like',"%".$_POST['nickname']."%");
        }
        if(!empty($_POST['phoneNum'])){
            $map['phoneNum'] = array('like',"%".$_POST['phoneNum']."%");
        }

        if(!empty($_POST['startTime']) || !empty($_POST['endTime'])){
            $map['registerTime'] = array(array('gt',"$timeStart"),array('lt',"$timeEnd")) ;
        }


        $user_info =M('userInfo');
        $count = $user_info->where($map)->count();// 查询满足要求的总记录数

        $list = $user_info->where($map)->page($page,$pageNum)->select();//获取分页数据
        //status

        foreach($list as $l){
            $memberIdArr[] = $l['memberId'];
            $agentIdArr[]  = $l['agentId'];
        }
        $memberIdArr = array_filter(array_unique($memberIdArr));
        $agentIdArr = array_filter(array_unique($agentIdArr));

        $memberInfo = $this->getMemberInfo($memberIdArr);
        $agentInfo = $this->getAgentInfo($agentIdArr);

        foreach ($memberInfo as $m){
           $memberNameArr[$m['memberid']] = $m['name'];
        }

        foreach ($agentInfo as $a){
            $agentNameArr[$a['code']] = $a['nickname'];
        }

        foreach ($list as $k => $l){
            $list[$k]['memberInfo']['name'] = isset($memberNameArr[$l['memberId']])?$memberNameArr[$l['memberId']]:'';
            $list[$k]['ageInfo']['nickname']  = isset($agentNameArr[$l['agentId']])?$agentNameArr[$l['agentId']]:'';
        }

        //dump($list);
        $Page       = new \Think\Page($count,$pageNum);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $data['totalPages'] = $count;
        $data['pageNum'] =$pageNum;
        $data['page'] = $page;
        $data['totalPages'] = ceil($count/$pageNum);
        $data['list'] = $list;
        $this->ajaxReturn($data);
    }

    public function getMemberInfo($arr){
        $whereIn['memberid']=array('in',$arr);
        return M('member_info')->where($whereIn)->select();
    }

    public function getAgentInfo($arr){
        $whereIn['code']=array('in',$arr);
        return M('agent_info')->where($whereIn)->select();

    }



    public function exceFile(){
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
        $date = date("Y-m-d",time());
        $fileName .= "_{$date}.xls";

        $fileName = iconv("utf-8", "gb2312", $fileName);
        //重命名表
        // $objPHPExcel->getActiveSheet()->setTitle('test');
        //设置活动单指数到第一个表,所以Excel打开这是第一个表
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); //文件通过浏览器下载

    }
    public function orgindex(){
        $this->display('accountManage/orgManage');
    }
}
