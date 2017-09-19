<?php
namespace Home\Controller;

class SetController extends CTController{

    public function __construct(){
        parent::__construct();
    }

    public function version(){
        $this->errorAddress();//权限

        $this->assign('title', '版本设置');
        $this->display('set/version');
    }

    public function menu(){
        $this->errorAddress();//权限

        $this->assign('title', '菜单列表');
        $this->display('set/menu');
    }

    public function group(){
        $this->errorAddress();//权限

        $this->assign('title', '分组设置');
        $menuRow = $this->getMenu();

        $this->assign('menuRow',$menuRow);
        $this->display('set/group');
    }

    public function role(){
        $this->errorAddress();//权限

        $this->assign('title', '用户权限设置');

        $this->assign('userRow',$this->getUser());
        $this->assign('groupRow',$this->getGroup());
        $this->display('set/role');
    }

    public function menu_info(){

        $this->assign('title', '菜单设置');

        $versionMenuModel = M('version_menu');
        $id = I('get.id',0,'int');
        $map = array();

        $menuNameArr = $versionMenuModel->field('id,menu_name')->where(array('pid'=>0))->select();


        $list = array();
        if($id) {
            $map['id'] = $id;

            $list = $versionMenuModel->where($map)->find();

            $pid = isset($list['pid'])?(int)$list['pid']:0;
            if(empty($pid)){
                $menuNameArr = array();
            }

        }


        $this->assign('menuNameArr', $menuNameArr);
        $this->assign('list', $list);

        $this->display('set/menu_info');
    }

    public function versionList(){
        $versionInfoModel = M('version_info');
        $pageNum = I('post.pageNum', 5, 'intval');
        $page = I('post.page', 1, 'intval');

        $map = array();

        $versionInfoArr = $versionInfoModel->where($map)->select();

        foreach ($versionInfoArr as $v){
            $versionInfoArr[$v['ttype']] = $v;
        }


        $ttypeNameArr = array('IOS用户端','安卓用户端','IOS明星端','安卓明星端');

        for ($i=0;$i<4;$i++){
            $ttype = $versionInfoArr[$i]['ttype'];
            $versionInfoArr[$i]['typeName'] = isset($ttypeNameArr[$ttype])?$ttypeNameArr[$ttype]:'';
        }

        $data['list'] =  $versionInfoArr;
        $this->ajaxReturn($data);
    }

    public function versionLogList(){
        $versionLogModel = M('version_info_log');


       // $adminModel = M('admin_user');

        $pageNum = I('post.pageNum', 5, 'intval');
        $page = I('post.page', 1, 'intval');

        $map = array();

        $count = $versionLogModel->where($map)->count();// 查询满足要求的总记录数
        $list = $versionLogModel->where($map)->page($page, $pageNum)->select();

        $data['list'] =  $list;
        $this->ajaxReturn($data);
    }

    public function changeVersion(){
        $ttypeNameArr = array('IOS用户端','安卓用户端','IOS明星端','安卓明星端');
        $versionInfoModel = M('version_info');
        $versionLogModel = M('version_info_log');

        $ttype       = I('post.ttype',0,'int');
        $VersionName = I('post.VersionName','','string');
        $Size        = I('post.Size',0,'int');
        $Url         = I('post.Url','','string');
        $UpdateDesc  = I('post.UpdateDesc','','string');

        $where = array('ttype'=>$ttype);
        $data['VersionName'] = $VersionName;
        $data['Size'] = $Size;
        $data['Url'] = $Url;
        $data['UpdateDesc'] = $UpdateDesc;
        $data['VersionCode'] = date('Ymd',time());
        $data['ReleaseTime'] = date('Y-m-d',time());


        if($bool = $versionInfoModel->where($where)->save($data)){

            $dataLog = array();
            $dataLog['VersionName'] = $VersionName;
            $dataLog['Url']         = $Url;
            $dataLog['adminId']     = $this->user['id'];
            $dataLog['adminName']   = $this->user['uname'];
            $dataLog['ttypeName']   = $ttypeNameArr[$ttype];
            $dataLog['create_time'] = date('Y-m-d',time());

            $versionLogModel->add($dataLog);
        }


        if($bool) {
            $return = array(
                'code' => 0,
                'message' => "修改成功"
            );
            return $this->ajaxReturn($return);
        }else{
            $return = array(
                'code' => -2,
                'message' => "修改失败"
            );
        }

        return $this->ajaxReturn($return);

    }

    public function menuList(){
        $versionMenuModel = M('version_menu');
        $map = array();

        $pageNum = I('post.pageNum', 5, 'intval');
        $page = I('post.page', 1, 'intval');
        $pid = I('post.pid', 0, 'intval');

        $map['pid'] = $pid;

        if($pid){
            $pageNum = I('post.pageNum', 100, 'intval');
        }

        $count = $versionMenuModel->where($map)->count();// 查询满足要求的总记录数
        $list = $versionMenuModel->where($map)->page($page, $pageNum)->select();


        new \Think\Page($count, $pageNum);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $data['totalPages'] = $count;
        $data['pageNum'] = $pageNum;
        $data['page'] = $page;
        $data['totalPages'] = ceil($count / $pageNum);

        $data['list'] = $list;

        $this->ajaxReturn($data);

    }

    public function addMenu(){
        $versionMenuModel = M('version_menu');

        $pid       = I('post.pid',0,'int');

        $menuName = I('post.menu_name','','string');
        $menuFile        = I('post.menu_file','','string');
        $menuSummary         = I('post.menu_summary','','trim');


        $data['pid'] = $pid;
        $data['menu_name'] = $menuName;
        $data['menu_file'] = $menuFile;
        $data['menu_summary'] = $menuSummary;
        $data['create_time'] = date('Y-m-d',time());


        $bool = $versionMenuModel->add($data);

        if($bool) {
            $return = array(
                'code' => 0,
                'message' => "添加成功"
            );
            return $this->ajaxReturn($return);
        }else{
            $return = array(
                'code' => -2,
                'message' => "添加失败"
            );
        }

        return $this->ajaxReturn($return);

    }

    public function editMenu(){
        $versionMenuModel = M('version_menu');


        $id       = I('post.id',0,'int');

        $pid       = I('post.pid',0,'int');

        $menuName = I('post.menu_name','','string');
        $menuFile        = I('post.menu_file','','string');
        $menuSummary         = I('post.menu_summary','','trim');


        $data['pid'] = $pid;
        $data['menu_name'] = $menuName;
        $data['menu_file'] = $menuFile;
        $data['menu_summary'] = $menuSummary;
        $data['create_time'] = date('Y-m-d',time());


        $map = array('id'=>$id);

        $bool = $versionMenuModel->where($map)->save($data);

        if($bool) {
            $return = array(
                'code' => 0,
                'message' => "修改成功"
            );
            return $this->ajaxReturn($return);
        }else{
            $return = array(
                'code' => -2,
                'message' => "修改失败"
            );
        }

        return $this->ajaxReturn($return);
    }

    public function groupList(){
        $versionGroupModel = M('version_group');
        $versionWithGroupModel = M('version_with_group');
        $pageNum = I('post.pageNum', 5, 'intval');
        $page = I('post.page', 1, 'intval');

        $map = array();

        $count = $versionGroupModel->count();

        $versionGroupArr = $versionGroupModel->where($map)->page($page, $pageNum)->select();


        $groupIds = array();
        foreach ($versionGroupArr as $v){
            $groupIds[] = $v['id'];
        }

        $GroupMap['group_id'] = array('in',$groupIds);

        $groupArr = $versionWithGroupModel->where($GroupMap)->select();

        $groupRow = array();
        $menuIdRow = array();
        foreach ($groupArr as $g){
            $groupRow[$g['group_id']]['menu_name'][] = trim($g['menu_name']);
            $groupRow[$g['group_id']]['menu_id'][] = trim($g['menu_id']);
        }

        $list = array();
        foreach ($versionGroupArr as $k=>$v){
            $list[$k] = $v;
            $list[$k]['menu_names'] = implode(',',$groupRow[$v['id']]['menu_name']);
            $list[$k]['menu_ids'] = implode(',',$groupRow[$v['id']]['menu_id']);
        }


        new \Think\Page($count, $pageNum);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $data['totalPages'] = $count;
        $data['pageNum'] = $pageNum;
        $data['page'] = $page;
        $data['totalPages'] = ceil($count / $pageNum);

        $data['list'] = $list;

        $this->ajaxReturn($data);

    }

    public function addGroup(){
        $versionGroupModel = M('version_group');
        $versionWithGroupModel = M('version_with_group');


        $groupName = I('post.group_name','','trim');
        $summary = I('post.summary','','trim');

        $menuIds = I('post.menuIds',null);
        $menuNames = I('post.menuNames',null);

        if(empty($groupName)){
            $return = array(
                'code' => -2,
                'message' => "请填写分组名称"
            );
            return $this->ajaxReturn($return);
        }


        if(empty($menuIds)){
            $return = array(
                'code' => -2,
                'message' => "请选择菜单"
            );
            return $this->ajaxReturn($return);
        }

        $data['group_name'] = $groupName;
        $data['summary'] = $summary;
        $data['create_time'] = date('Y-m-d',time());


        if($id = $versionGroupModel->add($data)){
            $versionWithGroupModel->where(array('group_id'=>$id))->delete();
            $i = 0;
            if(is_array($menuIds)){
                foreach ($menuIds as $k=>$mid){
                    $groupData[$i]['group_id'] = $id;
                    $groupData[$i]['menu_id'] = $mid;
                    $groupData[$i]['menu_name'] = $menuNames[$k];
                    $i++;
                }
                $versionWithGroupModel->addAll($groupData);
            }

            $return = array(
                'code' => 0,
                'message' => "成功"
            );
            $this->ajaxReturn($return);

        }else{
            $return = array(
                'code' => -2,
                'message' => "失败"
            );
            $this->ajaxReturn($return);
        }

    }

    public function editGroup(){
        $versionGroupModel = M('version_group');
        $versionWithGroupModel = M('version_with_group');


        $groupName = I('post.group_name','','trim');
        $id = I('post.id',0,'int');
        $summary = I('post.summary','','trim');

        $menuIds = I('post.menuIds',null);
        $menuNames = I('post.menuNames',null);

        if(empty($id)){
            return false;
        }

        if(empty($groupName)){
            $return = array(
                'code' => -2,
                'message' => "请填写分组名称"
            );
            return $this->ajaxReturn($return);
        }


        if(empty($menuIds)){
            $return = array(
                'code' => -2,
                'message' => "请选择菜单"
            );
            return $this->ajaxReturn($return);
        }

        $data['group_name'] = $groupName;
        $data['summary'] = $summary;
        $data['create_time'] = date('Y-m-d',time());


        $versionGroupModel->where(array('id'=>$id))->save($data);


        $versionWithGroupModel->where(array('group_id'=>$id))->delete();
        $i = 0;
        if(is_array($menuIds)){
            foreach ($menuIds as $k=>$mid){
                $groupData[$i]['group_id'] = $id;
                $groupData[$i]['menu_id'] = $mid;
                $groupData[$i]['menu_name'] = $menuNames[$k];
                $i++;
            }
            $versionWithGroupModel->addAll($groupData);
        }

        $return = array(
            'code' => 0,
            'message' => "成功"
        );
        $this->ajaxReturn($return);



    }

    public function adminList(){
        $adminUserModel = M('admin_user');
        $versionGroupModel = M('version_group');

        $map['identity_id'] = array('gt',1);
        $map['group_id'] = array('gt',0);

        $adminUserArr = $adminUserModel->field('id,uname,group_id')->where($map)->select();

        $groupIdArr = array();
        foreach ($adminUserArr as $a){
            $groupIdArr[] = $a['group_id'];
        }

        $mapGroup['version_group.id'] = array('in',$groupIdArr);

        $versionGroupArr = $versionGroupModel->field('version_group.id,version_group.group_name,version_group.summary,V.menu_name')->join('version_with_group V ON V.group_id=version_group.id', 'LEFT')->where($mapGroup)->select();


        $groupArr =  array();
        foreach ($versionGroupArr as $v){
            $group_name = isset($v['group_name'])?trim($v['group_name']):'';
            $summary = isset($v['summary'])?trim($v['summary']):'';
            $menu_name = isset($v['menu_name'])?trim($v['menu_name']):'';

            $groupArr[$v['id']]['group_name'] = $group_name;
            $groupArr[$v['id']]['summary'] = $summary;
            $groupArr[$v['id']]['menu_name'][] = $menu_name;
        }

        $list = array();
        foreach ($adminUserArr as $d){
            $list[$d['group_id']] = $d;
            $list[$d['group_id']]['group_name'] = $groupArr[$d['group_id']]['group_name'];
            $list[$d['group_id']]['summary'] = $groupArr[$d['group_id']]['summary'];
            $menu_nameArr = $groupArr[$d['group_id']]['menu_name'];

            $list[$d['group_id']]['menu_name'] = implode(',',$menu_nameArr);
        }


        $data['list'] = $list;

        $this->ajaxReturn($data);

    }

    public function editAdmin(){
        $adminUserModel = M('admin_user');


        $id = I('post.id',0,'int');
        $group_id = I('post.group_id',0,'int');;

        if(empty($id)){
            $return = array(
                'code' => -2,
                'message' => "请填写账号"
            );
            return $this->ajaxReturn($return);
        }

        if(empty($group_id)){
            $return = array(
                'code' => -2,
                'message' => "请填写分组"
            );
            return $this->ajaxReturn($return);
        }



        $data['group_id'] = $group_id;


        $adminUserModel->where(array('id'=>$id))->save($data);


        $return = array(
            'code' => 0,
            'message' => "成功"
        );
        $this->ajaxReturn($return);



    }

    private function getMenu(){
        $versionMenuModel = M('version_menu');
        $id = I('get.id',0,'int');
        $map = array('pid'=>$id);

        $menuNameArr = $versionMenuModel->field('id,menu_name')->where($map)->select();

        return $menuNameArr;
    }

    private function getGroup(){
        $versionGroupModel = M('version_group');
        $id = I('get.id',0,'int');
        $map = array();

        $menuGroupArr = $versionGroupModel->field('id,group_name')->where($map)->select();

        return $menuGroupArr;
    }

    private function getUser(){
        $adminUserModel = M('admin_user');

        $map['identity_id'] = array('gt',1);

        $adminUserArr = $adminUserModel->field('id,uname')->where($map)->select();

        return $adminUserArr;
    }

}


