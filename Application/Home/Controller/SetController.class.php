<?php
namespace Home\Controller;

class SetController extends CTController{

    public function __construct(){
        parent::__construct();
    }

    public function version(){
        $this->assign('title', '版本设置');
        $this->display('set/version');
    }
}


