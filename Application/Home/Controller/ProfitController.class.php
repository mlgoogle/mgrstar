<?php
namespace Home\Controller;
use Think\Controller;
//use Think\Controller\restController;
class ProfitController extends CTController{

    public function __construct(){
        parent::__construct();
        $this->assign('title', '经纪人佣金');
    }

    //佣金
    public function commision(){
       $this->display('profit/commision');
    }
}


