<?php

namespace Home\Controller;
use Think\Controller;

class CustomerController extends Controller
{
    public function customerList()
    {
        $this->display('Customer/customerList');
    }
}