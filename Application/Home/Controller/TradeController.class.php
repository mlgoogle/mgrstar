<?php

namespace Home\Controller;

use Think\Controller;

//use Think\Controller\restController;
class TradeController extends Controller
{
    /*
    *
    *
    */
    public function __construct()
    {
        # code...
        if (!session('user')) {
            //$this->ajaxReturn(array('code'=>-1,'message'=>'fail','data'=>'not login'));

        }
    }

    //平仓记录
    public function closePosition()
    {
        $pageNum = isset($_POST['pageNum']) ? $_POST['pageNum'] : 5;
        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $userId = isset($_POST['uid']) ? $_POST['uid'] : 1;
        $timestart = date("Y-m-d H:i:s", strtotime($_POST['startTime']));
        $timeend = date("Y-m-d H:i:s", strtotime($_POST['endTime']));
        $map['uid'] = $_POST['userId'];
        // 操盘类型 buy_sell            	int(11)     	YES        NULL           	买卖方向（买入卖出）
        $map['uid'] = $userId;
        if (!empty($_POST['buySell'])) {
            $map['buy_sell'] = $_POST['buySell'];
        }
        if (!empty($_POST['codeId'])) {
            $map['code_id'] = $_POST['codeId'];
        }

        if (!empty($_POST['startTime']) || !empty($_POST['endTime'])) {
            $map['open_position_time '] = array(array('gt', "$timestart"), array('lt', "$timeend"));
        }
        $Trades = M('his_trades_record');
        $count = $Trades->where($map)->count();// 查询满足要求的总记录数
        $list = $Trades->where($map)->order('open_position_time desc')->page($page, $pageNum)->select();//获取分页数据
        foreach ($list as $key => $value) {
            $actualMap['id'] = $value['code_id'];
            $list[$key]['actaulInfo'] = M('actuals_goods')->where($actaulMap)->find();
            $list[$key]['close_position_time'] = date("Y-m-d H:i:s", $value['close_position_time']);

        }
        $Page = new \Think\Page($count, $pageNum);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $data['totalPages'] = $count;
        $data['pageNum'] = $pageNum;
        $data['page'] = $page;
        $data['totalPages'] = ceil($count / $pageNum);
        $data['list'] = $list;
        if ($data['list'] == null) {
            $data['list'] = array();
        }
        $this->ajaxReturn($data);
    }

    //充值记录
    public function keep(){
        $pageNum = isset($_POST['pageNum']) ? $_POST['pageNum'] : 5;
        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $userId = isset($_POST['userId']) ? $_POST['userId'] : 1;
        $timestart = date("Y-m-d H:i:s", strtotime($_POST['startTime']));
        $timeend = date("Y-m-d H:i:s", strtotime($_POST['endTime']));
        $map['uid'] = $userId;
        if (!empty($_POST['starTime']) || !empty($_POST['endTime'])) {
            $map['depositTime '] = array(array('gt', "$timestart"), array('lt', "$timeend"));
        }

        $Trades = M('recharge_info');
        $count = $Trades->where($map)->count();// 查询满足要求的总记录数
        $list = $Trades->where($map)->order('depositTime desc')->page($page, $pageNum)->select();//获取分页数据
        $Page = new \Think\Page($count, $pageNum);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $data['totalPages'] = $count;
        $data['pageNum'] = $pageNum;
        $data['page'] = $page;
        $data['totalPages'] = ceil($count / $pageNum);
        $data['list'] = $list;
        if ($data['list'] == null) {
            $data['list'] = array();
        }
        $this->ajaxReturn($data);


    }

    //出金记录
    public function out()
    {
        $pageNum = isset($_POST['pageNum']) ? $_POST['pageNum'] : 5;
        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $userId = isset($_POST['userId']) ? $_POST['userId'] : 1;
        $timestart = date("Y-m-d H:i:s", strtotime($_POST['startTime']));
        $timeend = date("Y-m-d H:i:s", strtotime($_POST['endTime']));
        $map['uid'] = $userId;
        if (!empty($_POST['starTime']) || !empty($_POST['endTime'])) {
            $map['handleTime'] = array(array('gt', "$timestart"), arry('lt', "$timeend"));
        }

        $Trades = M('user_withdraw');
        $count = $Trades->where($map)->count();// 查询满足要求的总记录数
        $list = $Trades->where($map)->order('handleTime ')->page($page, $pageNum)->select();//获取分页数据
        $Page = new \Think\Page($count, $pageNum);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $data['totalPages'] = $count;
        $data['pageNum'] = $pageNum;
        $data['page'] = $page;
        $data['totalPages'] = ceil($count / $pageNum);
        $data['list'] = $list;
        if ($data['list'] == null) {
            $data['list'] = array();
        }
        $this->ajaxReturn($data);

    }

    public function openPosition()
    {
        $pageNum = isset($_POST['pageNum']) ? $_POST['pageNum'] : 5;
        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $userId = isset($_POST['uid']) ? $_POST['uid'] : 1;
        $timestart = date("Y-m-d H:i:s", strtotime($_POST['startTime']));
        $timeend = date("Y-m-d H:i:s", strtotime($_POST['endTime']));
        $map['uid'] = $_POST['userId'];
        if (!empty($_POST['buySell'])) {
            $map['buy_sell'] = $_POST['buySell'];
        }
        if (!empty($_POST['codeId'])) {
            $map['code_id'] = $_POST['codeId'];
        }

        if (!empty($_POST['startTime']) || !empty($_POST['endTime'])) {
            $map['open_position_time '] = array(array('gt', "$timestart"), arry('lt', "$timeend"));
        }
        $Trades = M('current_trades_record');

        $list = $Trades->where($map)->order('open_position_time desc')->page($page, $pageNum)->select();//获取分页数据
        foreach ($list as $key => $value) {
            $actualMap['id'] = $value['code_id'];
            $list[$key]['actaulInfo'] = M('actuals_goods')->where($actaulMap)->find();
            $list[$key]['close_position_time'] = date("Y-m-d H:i:s", $value['close_position_time']);

        }

        $data['list'] = $list;
        if ($data['list'] == null) {
            $data['list'] = array();
        }
        $this->ajaxReturn($data);
    }

    public function getgoodlist()
    {
        $actuals_good = M('actuals_goods');
        $res = $actuals_good->select();
        $this->ajaxReturn($res);
    }

    public function getopentradelist()
    {
        $pageNum = isset($_POST['pageNum']) ? $_POST['pageNum'] : 5;
        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $timestart = strtotime($_POST['startTime']);
        $timeend = strtotime($_POST['endTime']);
        if (!empty($_POST['id'])) {
            $map['code_id'] = $_POST['id'];
        }
        if (!empty($_POST['startTime']) || !empty($_POST['endTime'])) {
            $map['close_position_time '] = array(array('gt', "$timestart"), array('lt', "$timeend"));
        }
        $Trades = M('current_trades_record');
        $count = $Trades->where($map)->count();// 查询满足要求的总记录数
        $list = $Trades->where($map)->order('close_position_time desc')->page($page, $pageNum)->select();//获取分页数据

        foreach ($list as $key => $value) {
            $actualMap['id'] = $value['code_id'];
            $userMap['uid'] = $value['uid'];
            $list[$key]['actaulInfo'] = M('actuals_goods')->where($actaulMap)->find();
            $list[$key]['close_position_time'] = date("Y-m-d H:i:s", $value['close_position_time']);
            $list[$key]['userInfo'] = M('user_info')->where($userMap)->find();

        }
        $Page = new \Think\Page($count, $pageNum);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $data['totalPages'] = $count;
        $data['pageNum'] = $pageNum;
        $data['page'] = $page;
        $data['totalPages'] = ceil($count / $pageNum);
        $data['list'] = $list;
        if ($data['list'] == null) {

        }
        $this->ajaxReturn($data);
    }

    public function getclosetradelist()
    {
        $pageNum = isset($_POST['pageNum']) ? $_POST['pageNum'] : 5;
        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $timestart = date("Y-m-d H:i:s", strtotime($_POST['startTime']));
        $timeend = date("Y-m-d H:i:s", strtotime($_POST['endTime']));
        $map['code_id'] = $_POST['id'] ? $_POST['id'] : 10;

        if (!empty($_POST['startTime']) || !empty($_POST['endTime'])) {
            $map['close_position_time '] = array(array('gt', "$timestart"), array('lt', "$timeend"));
        }
        $Trades = M('his_trades_record');
        $count = $Trades->where($map)->count();// 查询满足要求的总记录数
        $list = $Trades->where($map)->order('close_position_time desc')->page($page, $pageNum)->select();//获取分页数据

        foreach ($list as $key => $value) {
            $actualMap['id'] = $value['code_id'];
            $userMap['uid'] = $value['uid'];
            $list[$key]['actaulInfo'] = M('actuals_goods')->where($actaulMap)->find();
            $list[$key]['close_position_time'] = date("Y-m-d H:i:s", $value['close_position_time']);
            $list[$key]['userInfo'] = M('user_info')->where($userMap)->find();

        }
        $Page = new \Think\Page($count, $pageNum);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $data['totalPages'] = $count;
        $data['pageNum'] = $pageNum;
        $data['page'] = $page;
        $data['totalPages'] = ceil($count / $pageNum);
        $data['list'] = $list;
        if ($data['list'] == null) {
            $data['list'] = array();
        }
        $this->ajaxReturn($data);
    }

    public function getouts()
    {
        $pageNum = isset($_POST['pageNum']) ? $_POST['pageNum'] : 5;
        $page = isset($_POST['page']) ? $_POST['page'] : 1;

        $timestart = date("Y-m-d H:i:s", strtotime($_POST['startTime']));
        $timeend = date("Y-m-d H:i:s", strtotime($_POST['endTime']));

        if (!empty($_POST['starTime']) || !empty($_POST['endTime'])) {
            $map['handleTime'] = array(array('gt', "$timestart"), array('lt', "$timeend"));
        }

        $Trades = M('user_withdraw');
        $count = $Trades->where($map)->count();// 查询满足要求的总记录数
        $list = $Trades->where($map)->order('handleTime')->page($page, $pageNum)->select();//获取分页数据

        $Page = new \Think\Page($count, $pageNum);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        foreach ($list as $key => $value) {
            # code...
            $userMap['uid'] = $value['uid'];
            $list[$key]['userInfo'] = M('user_info')->where($userMap)->find();
        }
        $data['totalPages'] = $count;
        $data['pageNum'] = $pageNum;
        $data['page'] = $page;
        $data['totalPages'] = ceil($count / $pageNum);
        $data['list'] = $list;
        if ($data['list'] == null) {
            $data['list'] = array();
        }
        $this->ajaxReturn($data);
    }

    public function getkeeps()
    {
        $pageNum = isset($_POST['pageNum']) ? $_POST['pageNum'] : 5;
        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $timestart = date("Y-m-d H:i:s", strtotime($_POST['startTime']));
        $timeend = date("Y-m-d H:i:s", strtotime($_POST['endTime']));
        if (!empty($_POST['starTime']) || !empty($_POST['endTime'])) {
            $map['depositTime '] = array(array('gt', "$timestart"), array('lt', "$timeend"));
        }

        $Trades = M('recharge_info');
        $count = $Trades->where($map)->count();// 查询满足要求的总记录数
        $list = $Trades->where($map)->order('depositTime desc')->page($page, $pageNum)->select();//获取分页数据
        foreach ($list as $key => $value) {
            # code...
            $userMap['uid'] = $value['uid'];
            $list[$key]['userInfo'] = M('user_info')->where($userMap)->find();
        }
        $Page = new \Think\Page($count, $pageNum);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $data['totalPages'] = $count;
        $data['pageNum'] = $pageNum;
        $data['page'] = $page;
        $data['totalPages'] = ceil($count / $pageNum);
        $data['list'] = $list;
        if ($data['list'] == null) {
            $data['list'] = array();
        }
        $this->ajaxReturn($data);


    }

    public function getSum()
    {


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
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('B')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('C')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('D')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('E')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


        $objActSheet->setCellValue('A1', '交易用户名称');
        $objActSheet->setCellValue('B1', '交易商品名称');
        $objActSheet->setCellValue('C1', '买卖方向');
        $objActSheet->setCellValue('D1', '收益');
        $objActSheet->setCellValue('E1', '交易时间');
        //填充数据
        $user_info = M('userInfo');
        if (!empty($_GET['nickname'])) {
            $usermap['nickname'] = array('like', "%" . $_GET['nickname'] . "%");
        }
        if (!empty($_GET['phoneNum'])) {
            $usermap['phoneNum'] = array('like', "%" . $_GET['phoneNum'] . "%");
        }
        if ($usermap) {
            $userInfos = M('user_info')->where($usermap)->select();

            foreach ($userInfos as $key => $value) {
                # code...
                $ids[] = $value['uid'];
            }
            $map['uid'] = array('in', $ids);
        }
        $timestart = strtotime($_GET['startTime']);
        $timeend = strtotime($_GET['endTime']);
        if (!empty($_GET['startTime']) || !empty($_GET['endTime'])) {
            $map['close_position_time '] = array(array('gt', "$timestart"), array('lt', "$timeend"));
        }


        $Trades = M('his_trades_record');

        $list = $Trades->where($map)->order('close_position_time desc')->select();//获取分页数据

        foreach ($list as $key => $value) {
            $userMap['uid'] = $value['uid'];
            $actualMap['id'] = $value['code_id'];
            $userInfo = M('user_info')->where($userMap)->find();
            $actualInfo = M('actuals_goods')->where($actualMap)->find();

            $objActSheet->setCellValue("A" . ($key + 2), $userInfo['phoneNum']);
            $objActSheet->setCellValue("B" . ($key + 2), $actualInfo['name']);
            if ($value['buy_sell'] == 1) {
                $buyName = '买入';
            } else {
                $buyName = '卖出';
            }
            $objActSheet->setCellValue('C' . ($key + 2), $buyName);


            $objActSheet->setCellValue('D' . ($key + 2), $value['result'] * $value['gross_profit']);
            $objActSheet->setCellValue('E' . ($key + 2), date("Y-m-d H:i:s", $value['close_position_time']));


        }
        // 设置个表格宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(16);

        // 垂直居中
        $objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('E')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('F')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $fileName = '用户交易时间报表';
        $date = date("Y-m-d", time());
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

    public function report()
    {
        $pageNum = $_POST['pageNum'] ? $_POST['pageNum'] : 5;
        $page = $_POST['page'] ? $_POST['page'] : 1;
        $user_info = M('userInfo');
        if (!empty($_POST['nickname'])) {
            $usermap['nickname'] = array('like', "%" . $_POST['nickname'] . "%");
        }
        if (!empty($_POST['phoneNum'])) {
            $usermap['phoneNum'] = array('like', "%" . $_POST['phoneNum'] . "%");
        }
        if ($usermap) {
            $userInfos = M('user_info')->where($usermap)->select();

            foreach ($userInfos as $key => $value) {
                # code...
                $ids[] = $value['uid'];
            }
            $map['uid'] = array('in', $ids);
        }
        $timestart = strtotime($_POST['startTime']);
        $timeend = strtotime($_POST['endTime']);
        if (!empty($_POST['startTime']) || !empty($_POST['endTime'])) {
            $map['close_position_time '] = array(array('gt', "$timestart"), array('lt', "$timeend"));
        }
        $Trades = M('his_trades_record');
        $count = $Trades->where($map)->count();// 查询满足要求的总记录数

        $list = $Trades->where($map)->order('open_position_time desc')->page($page, $pageNum)->select();//获取分页数据
        foreach ($list as $key => $value) {
            $actualMap['id'] = $value['code_id'];
            $userMap['uid'] = $value['uid'];
            $list[$key]['actaulInfo'] = M('actuals_goods')->where($actaulMap)->find();
            $list[$key]['userInfo'] = M('user_info')->where($userMap)->find();
            $list[$key]['close_position_time'] = date("Y-m-d H:i:s", $value['close_position_time']);

        }
        $Page = new \Think\Page($count, $pageNum);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $data['totalPages'] = $count;
        $data['pageNum'] = $pageNum;
        $data['page'] = $page;
        $data['totalPages'] = ceil($count / $pageNum);
        if (!$list) {
            $list = [array();
        }
        $data['list'] = $list;
        //s$data['from'] ='$Page->';

        $this->ajaxReturn($data);
    }
}
