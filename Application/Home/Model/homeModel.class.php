<?php
/**
 * Created by PhpStorm.
 * User: ctdone
 * Date: 2017/6/26
 * Time: 15:28
 */
namespace Home\Model;
use Think\Model;



class homeModel extends Model{

    private $identityArr = array(0,1,2,3,4);

    private $user;

    public function __construct(){
        $this->user = session('user');
    }

    public function getUserInfoIdentity($identity_id){

        if ($identity_id == 4) { //经纪人用户
            //$identity_id = 3;
            return false;
            $map['memberId'] = $this->user['memberId'];
            $map['agentId'] = $this->user['agentId'];
            $map['agentSubId'] = $this->user['agentSubId'];
        }
        if ($identity_id == 3) { //区域经纪人
            //$identity_id = 3;
            $map['memberId'] = $this->user['memberId'];
            $map['agentId'] = $this->user['agentId'];
            $map['agentSubId'] = array('gt', 0);
        } else if ($identity_id == 2) { //机构用户
            // $identity_id = 2;
            $map['memberId'] = $this->user['memberId'];
            $map['agentId'] = array('gt', 0);
        } else {  // 交易所用户
            //$identity_id = 1;

            $map['memberId'] = array('gt', 0);

        }
        return $map;
    }

}
