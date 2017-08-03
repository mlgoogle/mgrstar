<?php
/**
 * Created by PhpStorm.
 * User: ctdone
 * Date: 2017/7/31
 * Time: 20:37
 */
namespace Home\Model;
use Think\Model;


class excelModel extends Model{

    protected $titles = array();
    protected $fields = array();
    protected $fileName = '';
    private $letterStyle = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N',
        'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');

    public function __construct($titles,$fields,$fileName='未知'){
        $this->titles = $titles;
        $this->fields = $fields;
        $this->letterStyle = $this->letterStyle;
        $this->fileName = $fileName.'报表';
    }

    public function excelFile($list){
        import("Org.Util.PHPExcel");
        import("Org.Util.PHPExcel.Worksheet.Drawing");
        import("Org.Util.PHPExcel.Writer.Excel2007");
        $objPHPExcel = new \PHPExcel();
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objActSheet = $objPHPExcel->getActiveSheet();// 水平居中（位置很重要，建议在最初始位置）

        $letterStyle = $this->letterStyle;

        foreach ($this->titles as $k=>$t){
            $letter = $letterStyle[$k];
            $objPHPExcel->setActiveSheetIndex(0)->getStyle($letter)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objActSheet->setCellValue($letter.'1', $t);
        }


       // $fields = $this->fields;
        $j= 0;
        foreach ($list as $key => $value) {
            foreach ($this->fields as $i=>$field) {
                $letterList = $letterStyle[$i];
                $objActSheet->setCellValue($letterList. ($j + 2), $value[$field]);
            }
            $j++;
        }


        foreach ($letterStyle as $l ){
            // 设置个表格宽度
            $objPHPExcel->getActiveSheet()->getColumnDimension($l)->setWidth(16);
        }

        foreach ($letterStyle as $l ){
            // 垂直居中
            $objPHPExcel->getActiveSheet()->getStyle($l)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        }

        $fileName = $this->fileName ;
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
}

