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


    public function addSummary(){
        $this->addStarTrofitSummary(); // 明星收益统计
        $this->addTrofitSummary(); // 用户交易收益统计
    }

    public function addStarTrofitSummary(){
        $profitStarSummaryModel = M('profit_star_summary');

        $openTime = time()-3600*24;

        $map = '';

       // $map = ' where order_date = ' . date('Y-m-d',$openTime);
        

//        $transStatisArr = M('TransStatis')->field('sum(order_price) as price_num, starcode')->where($map)
//            ->group('starcode')->select();

        $transStatisArr = M('TransStatis')->query('select sum(order_price) as price_num, starcode from TransStatis '. $map .' group by starcode ');

        $i = 0;
        foreach ($transStatisArr as $t){
            $starcode = $t['starcode'];
            $order_price = $t['price_num'];
            $data['create_time'] = $create_time = date('Y-m-d', time());

            $data['order_price']=array('exp','order_price+'.$order_price);

            if(!$p = $profitStarSummaryModel->where(array('starcode'=>$starcode))->save($data)){ //加$n
                $data['starcode'] = $starcode;
                $data['order_price'] = $order_price;
                $profitStarSummaryModel->add($data);
            }

            $i++;
        }

        $newLog ='log_time:'.date('Y-m-d H:i:s');
        file_put_contents('./transStatis.sql',$newLog.PHP_EOL, FILE_APPEND);
    }

    public function addTrofitSummary(){
        $profitSummaryModel = M('profit_summary');

        $map['order_type'] = 2;
        $openTime = time()-3600*24;
        $map['open_time']  = array('egt',$openTime);

        $orderlistArr = M('star_orderlist')->field('sum(order_num) as sum_num, sum(order_price) as sum_price,sell_uid')->where($map)
            ->group('sell_uid')->select();


        $uidArr = array();

        foreach ($orderlistArr as $o){
            $uidArr[] = $o['sell_uid'];
        }

        $uidArr  = array_filter(array_unique($uidArr));

        $userMap['uid'] = array('in',$uidArr);
        $userMap['channel'] = array('exp','is not null');
        $userMap['channel'] = array('neq','');

        $userinfo = M('star_userinfo')->field('uid,channel')->where($userMap)->select();

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