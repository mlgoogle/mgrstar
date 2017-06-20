<?php

namespace Home\Controller;
use Think\Controller;

class AdminBackerController extends Controller
{
    public function index()
    {
        $this->display('AdminBacker/index');
    }
}