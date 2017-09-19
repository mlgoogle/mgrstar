<?php
namespace Home\Model;
use Think\Model;

class menuModel extends Model{

    private  $menu;

    public function __construct($menu){
        $this->menu = $menu;
        $this->user = session('user');
    }

    public function menuRow(){

        $starArr = array('Star', 'Info', 'Lucida', 'Meet', 'Appoint', 'Timer');
        $cusArr = array( 'Customer');
        $starCtr = array('carousel', 'listing', 'meet', 'appoint');

        $timerArr = array('timer', 'info');

        $versionMenuModel = M('version_menu');

        $pid = I('post.pid', 0, 'intval');
        $map = array();

//        foreach ($this->menu as $m){
//            $menuRow[] = $m['menu_id'];
//        }

        $menuRow = $this->menu;


        $versionMenuArr = $versionMenuModel->where($map)->order('id asc')->select();
        

        $menuArr = array();
        $i = 0;
        foreach ($versionMenuArr as $v){
            $pid = isset($v['pid'])?intval($v['pid']):0;
            $id = isset($v['id'])?intval($v['id']):0;

            if(empty($pid)){
                if($menuRow) {
                    if (in_array($id, $menuRow)) {
                        $menuArr[$id] = $v;
                    }
                }else{
                    $menuArr[$id] = $v;
                }
            }else{
                $menuArr[$pid][$i] = $v;
                $menuArr[$pid][$i]['pid_file'] = $menuArr[$pid]['menu_file'];
                $i++;
            }

        }


        $menuHtml = '<div class="sidebar"> <ul class="nav-list">';


        foreach ($menuArr as $k=>$m){

            $menuFiles = $m['menu_file'];
            $menuNamees = $menuFiles ;

            if($menuNamees == 'AdminBacker/index'){
                $mainClass = (CONTROLLER_NAME == 'AdminBacker') ? 'class="main"' : '';
            } else if($menuNamees == 'Star'){
                $mainClass = in_array(CONTROLLER_NAME ,  $starArr)?'class="main"':'';
            }else {
                $mainClass = (CONTROLLER_NAME == $menuNamees) ? 'class="main"' : '';
            }


            if($k==1){
                $href ='/index.php/Home/'.$m['menu_file'];
               // $mainClass = 'class="active"';
            }else{
                $href ='javascript:;';
            }
            $menuHtml .= '<li class="pli">';

            $menuHtml .= '<a href="' . $href . '" ' . $mainClass . '>';
            $menuHtml .= '<strong>' .$m['menu_name'] .'</strong></a>';
            $menuHtml .= '<ul class="submenu" >';
            foreach ($m as $s){
                $menuFile = isset($s['menu_file'])?trim($s['menu_file']):0;
                if($menuFile) {
                    $menuNameLenght = strpos($menuFile, '/');
                    $menuName = substr($menuFile,$menuNameLenght+1);



                    $actClass = (ACTION_NAME == $menuName) ? 'class="act"' : '';

                    $timerId = '';

                    if($menuName == 'timer'){
                        $timerId = ' id ="timerId"';
                    }else if($menuName == 'distribute'){
                        $timerId = ' id ="distributeId"';
                    } else if($menuName == 'listing'){
                        $timerId = ' id ="lucidaId"';
                    }


                    $menuHtml .= '<li>';
                    $menuHtml .= '<a href="/index.php/Home/' . $menuFile .'" ' . $actClass . $timerId .'>' . $s['menu_name'] . '</a>';
                    $menuHtml .= '</li>';
                }

            }

            $menuHtml .= '</ul>';
            $menuHtml .= '</li>';
        }
        $menuHtml .= '</ul>';
        $menuHtml .= '</div>';

        unset($menuArr);

        return $menuHtml;
    }
}