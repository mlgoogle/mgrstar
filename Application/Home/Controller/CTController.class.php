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
    protected $user;
    protected   $hostUrl;
    private     $menu;

    public function _initialize(){

        $this->hostUrl = C('qn_domain'); //图片域名

        $sessionName = C('user');

        $user = $this->user = session($sessionName);


        if(!$this->user){
            $this ->redirect('login/login',Null,0);
        }

        $menu = $this->getAdminMenu();
        foreach ($menu as $m){
            $menuRow[] = $m['menu_id'];
        }

        $this->menu = new \Home\Model\menuModel($menuRow);


        $this->assign('menu',$this->menu->menuRow());



        $identity_id = $user['identity_id'];

        if($identity_id<2){
            $this->assign('identity_status', 1);
        }




    }

    public function errorAddress(){
        $groupId = $this->user['group_id'];

        if(empty($groupId)){
            return false;
        }
        $menu = $this->getAdminMenu();
        foreach ($menu as $m){
            $menuRow[] = $m['menu_id'];
        }

        $array = $this->getMenuAll();

        $menuFile = strtolower(CONTROLLER_NAME.'/'.ACTION_NAME);

        $pid = isset($array[$menuFile])?intval($array[$menuFile]):0;
        if(!in_array($pid,$menuRow)){
            $this->assign('title', '错误信息');
            $this->assign('message', '没有权限');
            $this->display('err/error');
            exit;
            return false;
        }
    }

    public function getAdminMenu(){
        $versionWithGroupModel = M('version_with_group');

        $groupId = $this->user['group_id'];
        $map['group_id'] = $groupId;

        $menuIdArr = $versionWithGroupModel->field('menu_id')->where($map)->select();
        return $menuIdArr;
    }

    public function getMenuAll(){
        $versionMenuModel = M('version_menu');
        $map = array();
        if(!$versionMenuArr=F('version_menu_all')) {
            $versionMenuArr = $versionMenuModel->field('id,pid,menu_file')->where($map)->order('id asc')->select();
            F('version_menu_all', $versionMenuArr);
        }

        $array = array();
        foreach ($versionMenuArr as $v){
            $menuFile =   strtolower($v['menu_file']);
            $array[$menuFile] = $v['pid'];
        }

        return $array;
    }



}