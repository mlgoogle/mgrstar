<?php
namespace Home\Model;
use Think\Model;


class cgiModel extends Model{

    private $url;

    function __construct($uid=0){
        parent::__construct();
        $this->url = C('CGI_STAR_URl');

        $data = array('uid'=>$uid);
        $this->postCurl($this->url,$data);
    }

    private function postCurl($url,$data){
        $post_data = $data;

        $ch =curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)");
        curl_setopt($ch, CURLOPT_POST, 1);//
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HEADER,0);
        $rst=curl_exec($ch);
        curl_close($ch);

        exit(json_encode($rst));
    }
}


