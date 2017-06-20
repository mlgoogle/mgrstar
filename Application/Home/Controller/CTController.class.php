<?php

namespace Home\Controller;
use Think\Controller;

/**
 * CT - CloudTop类
 * 继承自 TP 的 Controller 用于类扩展 eg:登录验证
 * @date finished at 2017-6-20
 *
 * Class CTController
 * @package Home\Controller
 */
class CTController extends Controller
{
    public function _initialize()
    {
        if (count(session('user')) > 0) {
            $this->redirect('login/login');
        }
    }
}