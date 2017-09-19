<?php

namespace Home\Controller;
use Think\Controller;

class AdminBackerController extends CTController
{
    public function index(){
        $this->errorAddress();//权限

        $this->display('AdminBacker/index');
    }

}