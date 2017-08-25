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
class CrontabController extends CTController{
    public function __construct(){

    }

    public function addTrofitSummary(){
        $profitSummaryModel = M('profit_summary');

        $orderlistArr = M('star_orderlist')->field('sum(order_num) as sum_num, sum(order_price) as sum_price,sell_uid')->where(array('order_type'=>2))
            ->group('sell_uid')->select();


        $uidArr = array();

        foreach ($orderlistArr as $o){
            $uidArr[] = $o['sell_uid'];
        }

        $uidArr  = array_filter(array_unique($uidArr));

        $map['uid'] = array('in',$uidArr);
        $map['channel'] = array('exp','is not null');
        $map['channel'] = array('neq','');

        $userinfo = M('star_userinfo')->field('uid,channel')->where($map)->select();

        $userArr = array();
        foreach ($userinfo as $u){
            $userArr[$u['uid']] = $u['channel'];
        }

        $data = array();
        $i = 0 ;
        foreach ($orderlistArr as $k=>$o){
            if(isset($userArr[$o['sell_uid']])) {
                $data[$i]['sell_uid'] = $o['sell_uid'];
                $data[$i]['order_num'] = $o['sum_num'];
                $data[$i]['order_price'] = $o['sum_price'];
                $data[$i]['channel'] = $userArr[$o['sell_uid']];
                $data[$i]['create_time'] = date('Y-m-d', time());
                $i++;
            }
        }

        $profitSummaryModel->addAll($data);



        $newLog ='log_time:'.date('Y-m-d H:i:s');
        file_put_contents('./text.sql',$newLog.PHP_EOL, FILE_APPEND);

    }
}