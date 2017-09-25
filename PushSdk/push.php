<?php
/*
 * 2017.9.20  个推
 */
header("Content-Type: text/html; charset=utf-8");

require_once(dirname(__FILE__) . '/' . 'IGt.Push.php');
require_once(dirname(__FILE__) . '/' . 'igetui/IGt.AppMessage.php');
require_once(dirname(__FILE__) . '/' . 'igetui/IGt.APNPayload.php');
require_once(dirname(__FILE__) . '/' . 'igetui/template/IGt.BaseTemplate.php');
require_once(dirname(__FILE__) . '/' . 'IGt.Batch.php');
require_once(dirname(__FILE__) . '/' . 'igetui/utils/AppConditions.php');
//http的域名
define('HOST','http://sdk.open.api.igexin.com/apiex.htm');


//定义常量, appId、appKey、masterSecret 采用本文档 "第二步 获取访问凭证 "中获得的应用配置
define('APPKEY','i2RtWjHUYs7sju2VQXcMV7');
define('APPID','HA9wao0Mxy7BPUu18po5zA');
define('MASTERSECRET','njsL7Wzwl6AI3FFGmMqgM2');

//define('BEGINTIME','2015-03-06 13:18:00');
//define('ENDTIME','2015-03-06 13:24:00');



pushMessageToApp();
//群推接口案例
function pushMessageToApp(){
    $igt = new IGeTui(HOST,APPKEY,MASTERSECRET);
    //定义透传模板，设置透传内容，和收到消息是否立即启动启用
    $template = IGtTransmissionTemplateDemo();
    //$template = IGtLinkTemplateDemo();
    // 定义"AppMessage"类型消息对象，设置消息内容模板、发送的目标App列表、是否支持离线发送、以及离线消息有效期(单位毫秒)
    $message = new IGtAppMessage();
    $message->set_isOffline(true);
    $message->set_offlineExpireTime(10 * 60 * 1000);//离线时间单位为毫秒，例，两个小时离线为3600*1000*2
    $message->set_data($template);

    $appIdList=array(APPID);
    $phoneTypeList=array('ANDROID');
    $provinceList=array('浙江');
    $tagList=array('haha');
    //用户属性
    //$age = array("0000", "0010");


    //$cdt = new AppConditions();
    // $cdt->addCondition(AppConditions::PHONE_TYPE, $phoneTypeList);
    // $cdt->addCondition(AppConditions::REGION, $provinceList);
    //$cdt->addCondition(AppConditions::TAG, $tagList);
    //$cdt->addCondition("age", $age);

    $message->set_appIdList($appIdList);
    //$message->set_conditions($cdt->getCondition());

    $rep = $igt->pushMessageToApp($message,"星云");

    if($rep['result'] == 'ok'){
        $array = array(
            'code' => 0,
            'message' =>'推送成功'
        );
    }else{
        $array = array(
            'code' => 0,
            'message' =>'推送失败'
        );
    }

    exit(json_encode($array));
    return false;
}


function IGtLinkTemplateDemo(){

    $starId = isset($_POST['starId'])?intval($_POST['starId']):0;

    if(!empty($starId)){
        return IGtTransmissionTemplateDemo();
    }

    $title = isset($_POST['title'])?trim($_POST['title']):'';
    $text = isset($_POST['text'])?trim($_POST['text']):'';
    $logo = isset($_POST['logo'])?trim($_POST['logo']):'';
    $logoURL = isset($_POST['logoURL'])?trim($_POST['logoURL']):'';
    $url = isset($_POST['url'])?trim($_POST['url']):'';


    if(empty($title)){
        $array = array(
            'code' => -2,
            'message' =>'请输入标题'
        );
        exit(json_encode($array));
        return false;
    }

    if(empty($text)){
        $array = array(
            'code' => -2,
            'message' =>'请输入内容'
        );
        exit(json_encode($array));
        return false;
    }

    $template =  new IGtLinkTemplate();
    $template ->set_appId(APPID);                  //应用appid
    $template ->set_appkey(APPKEY);                //应用appkey
    $template ->set_title($title);       //通知栏标题
    $template ->set_text($text);        //通知栏内容
    $template->set_logo($logo);                       //通知栏logo
    $template->set_logoURL($logoURL);                    //通知栏logo链接
    $template ->set_isRing(true);                  //是否响铃
    $template ->set_isVibrate(true);               //是否震动
    $template ->set_isClearable(true);             //通知栏是否可清除
    $template ->set_url($url); //打开连接地址
    //$template->set_duration(BEGINTIME,ENDTIME); //设置ANDROID客户端在此时间区间内展示消息
    $template->set_pushInfo($title, 0, $text, "sound", "", "", "", "");

//    //       APN高级推送
//    $apn = new IGtAPNPayload();
//    $alertmsg=new DictionaryAlertMsg();
//    $alertmsg->body="星云的内容";
//    $alertmsg->actionLocKey="ActionLockey";
//    $alertmsg->locKey="LocKey";
//    $alertmsg->locArgs=array("locargs");
//    $alertmsg->launchImage="launchimage";
////        iOS8.2 支持
//    $alertmsg->title="星云";
//    $alertmsg->titleLocKey="TitleLocKey";
//    $alertmsg->titleLocArgs=array("TitleLocArg");
//
//    $apn->alertMsg=$alertmsg;
//    $apn->badge=1;
//    $apn->sound="default";
//    $apn->add_customMsg("payload","星云的内容");
//    $apn->contentAvailable=-1;
//    $apn->category="ACTIONABLE";
//    $template->set_apnInfo($apn);


    return $template;
}


function IGtTransmissionTemplateDemo(){

    $starId = isset($_POST['starId'])?intval($_POST['starId']):0;
    $title = isset($_POST['title'])?trim($_POST['title']):'';
    $text = isset($_POST['text'])?trim($_POST['text']):'';

    $url = isset($_POST['url'])?trim($_POST['url']):'';

    if(empty($title)){
        $array = array(
            'code' => -2,
            'message' =>'请输入标题'
        );
        exit(json_encode($array));
        return false;
    }

    if(empty($text)){
        $array = array(
            'code' => -2,
            'message' =>'请输入内容'
        );
        exit(json_encode($array));
        return false;
    }

//    if(empty($starId)){
//        $array = array(
//            'code' => -2,
//            'message' =>'请选择用户'
//        );
//        exit(json_encode($array));
//        return false;
//    }

    $starIdArr = array('starId'=>$starId,'starUrl'=>$url);
    $content = json_encode($starIdArr);


    $template =  new IGtTransmissionTemplate();
    //应用appid
    $template->set_appId(APPID);
    //应用appkey
    $template->set_appkey(APPKEY);
    //透传消息类型
    $template->set_transmissionType(1);
    //透传内容
    $template->set_transmissionContent($content);
    //$template->set_duration(BEGINTIME,ENDTIME); //设置ANDROID客户端在此时间区间内展示消息
    //这是老方法，新方法参见iOS模板说明(PHP)*/
    //$template->set_pushInfo("actionLocKey","badge","message",
    //"sound","payload","locKey","locArgs","launchImage");

    $template->set_pushInfo($title, 0, $text, "sound", "", "", "", "");



    return $template;
}


