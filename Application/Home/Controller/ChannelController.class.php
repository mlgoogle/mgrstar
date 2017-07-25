<?php
/**
 * Created by PhpStorm.
 * User: ctdone
 * Date: 2017/7/24
 * Time: 17:47
 */

namespace Home\Controller;


class ChannelController extends CTController {

    public function __construct(){
        parent::__construct();
        $this->assign('title', '渠道管理');
    }

    public function listing(){
        $this->display('channel/listing');
    }

    public function getChannel(){
        $pageNum = I('post.pageNum', 10, 'intval');
        $page = I('post.page', 1, 'intval');

        $channelModel = M('star_channel');

        $count = $channelModel->count();// 查询满足要求的总记录数
        $list = $channelModel->page($page, $pageNum)->select();


        $agentSubIds =  array();
        foreach ($list as $l){
            $agentSubIds[] = $l['agentsubId'];

        }

        $agentSubIds = array_filter(array_unique($agentSubIds));

        $where['id'] = array('in',$agentSubIds);

        $agentSub = M('agentsub_info')->where($where)->select();

        $agentSubArr = array();
        foreach ($agentSub as $a){
            $agentSubArr[$a['id']] = $a;
        }

        foreach ($list as $k=>$l){
            $list[$k]['nickname'] = isset($agentSubArr[$l['agentsubId']]['nickname'])?$agentSubArr[$l['agentsubId']]['nickname']:'';
        }


        new \Think\Page($count, $pageNum);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $data['totalPages'] = $count;
        $data['pageNum'] = $pageNum;
        $data['page'] = $page;
        $data['totalPages'] = ceil($count / $pageNum);
        $data['list'] = $list;

        $this->ajaxReturn($data);
    }

    public function getProvince(){
        $provinceModel = M('area_province');

        $province = $provinceModel->select();
        $data['list'] = $province;
        $this->ajaxReturn($data);
    }

    public function getCity (){
        $cityModel = M('area_code');

        $pid= I('post.pid',0,'intval');

        $where['pid'] = $pid;
        $city = $cityModel->where($where)->select();
        $data['list'] = $city;
        $this->ajaxReturn($data);
    }


}