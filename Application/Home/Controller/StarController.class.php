<?php

namespace Home\Controller;
use Think\Controller;

class StarController extends Controller
{
    public function carousel()
    {
        $this->assign('title','轮播列表');
        $this->display('star/carousel');
    }
}