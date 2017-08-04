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

    public function getUserInfoIdentity($T = false){ // 默认 mark

        $identity_id = $this->user['identity_id'];

        $memberId = $this->user['memberId'];
        $agentId = $this->user['agentId'];
        $agentSubId = $this->user['agentSubId'];
        $map = array();

        if($T){
            if ($identity_id == 4) { //经纪人用户
                //$identity_id = 3;
                // return false;
                $map['memberId'] = $this->user['memberId'];
                $map['agentId'] = $this->user['agentId'];
                $map['subagentId'] = $this->user['agentSubId'];
            }
            if ($identity_id == 3) { //区域经纪人
                //$identity_id = 3;
                $map['memberId'] = $this->user['memberId'];
                $map['agentId'] = $this->user['agentId'];
                $map['subagentId'] = array('gt', '0');
            } else if ($identity_id == 2) { //机构用户
                // $identity_id = 2;
                $map['memberId'] = $this->user['memberId'];
                $map['agentId'] = array('gt', '0');
            } else {  // 交易所用户
                //$identity_id = 1;

                //$map['memberId'] = array('gt', '0');

            }
        }else {

            if ($identity_id == 4) { //经纪人用户
                //$identity_id = 3;
                // return false;
                $map['memberId'] = $this->getMark('member_info', 'memberid', $memberId);
                $map['agentId'] = $this->getMark('agent_info', 'id', $agentId);
                $map['subagentId'] = $this->getMark('agentsub_info', 'id', $agentSubId);
            }
            if ($identity_id == 3) { //区域经纪人
                //$identity_id = 3;
                $map['memberId'] = $this->getMark('member_info', 'memberid', $memberId);
                $map['agentId'] = $this->getMark('agent_info', 'id', $agentId);
                $map['subagentId'] = array('gt', '0');
            } else if ($identity_id == 2) { //机构用户
                // $identity_id = 2;
                $map['memberId'] = $this->getMark('member_info', 'memberid', $memberId);
                $map['agentId'] = array('gt', '0');
            } else {  // 交易所用户
                //$identity_id = 1;

                //$map['memberId'] = array('gt', '0');

            }
        }

        return $map;
    }

    public function getMark($table,$key,$value){
        $markArr = M($table)->field('mark')->where($key . '=' .$value)->find();

        return empty($markArr['mark'])?'':$markArr['mark'];

    }

}
