<?php

namespace Home\Controller;
use Think\Controller;

class AdminBackerController extends CTController
{
    public function index(){
        $this->display('AdminBacker/index');
    }

}