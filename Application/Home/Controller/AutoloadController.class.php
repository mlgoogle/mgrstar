<?php
namespace Home\Controller;
use Think\Controller;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

require 'vendor/autoload.php';

class AutoloadController extends CTController{

    private $Controller;

    public function __construct(){
        // \Home\
        //$this->Controller = new \Think\Controller();
    }

    public function upload(){

        // 初始化签权对象
        $auth = new Auth(C('qn_ak'), C('qn_sk'));


        // 空间名  https://developer.qiniu.io/kodo/manual/concepts
        $bucket = 'starshareimage';

        // 生成上传Token
        $token = $auth->uploadToken($bucket);

        // 要上传文件的本地路径
        $filePath = $_FILES['myfile']['name'];


        // 上传到七牛后保存的文件名
        $key = $this->createUniqid($filePath);

        // 构建 UploadManager 对象
        $uploadMgr = new UploadManager();

        $fileServerPath = $_FILES['myfile']['tmp_name'];

        list($ret, $err) = $uploadMgr->putFile($token, $key, $fileServerPath);

        if ($err !== null) {
            $this->ajaxReturn(array("message" => $err, "status" => 0));
        } else {
            $this->ajaxReturn(array("path" =>  $ret['key'], "status" => 1));
        }

    }

    private function createUniqid($filePath){
        $path = pathinfo($filePath);
        return date('ymdhis') . uniqid() . '.' . $path['extension'];
    }
}




