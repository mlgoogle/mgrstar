<?php
namespace Home\Model;
use Think\Model;

class withdrawalsModel extends Model{

    private $withdrawals_data;
    private $post_url;
    private $key;


    public function __construct(){
        $this->withdrawals_data = C('withdrawals_data');
        $this->post_url = C('post_url');
        $this->key      = C('key');
    }

    public function putWithdrawals($bankAccount=0,$bankSum=0,$accBankName=''){

        if(empty($bankAccount) || empty($bankSum)){
            $return = array(
                'code' => -2,
                'message' => '银行卡错误！',
            );
            return $return;
        }

        $withdrawalsData = $this->withdrawals_data;

        $subMerNo = $withdrawalsData['subMerNo'];
        $orderNo   = $this->addOrderNo($subMerNo);

        $wd = array(
            'subMerNo'      => $withdrawalsData['subMerNo'],
            'orderNo'       => $orderNo,
            'notifyUrl'     => $withdrawalsData['notifyUrl'],
            'transAmt'      => $bankSum,
            'isCompay'      => $withdrawalsData['isCompay'],
            'customerName'  => $withdrawalsData['customerName'],
            'acctNo'        => $withdrawalsData['acctNo'],
        );

        $paramData = array(
            'subMerNo'      => $withdrawalsData['subMerNo'],
            'orderNo'       => $orderNo,
            'notifyUrl'     => $withdrawalsData['notifyUrl'],
            'transAmt'      => $bankSum,
            'isCompay'      => $withdrawalsData['isCompay'],
            'customerName'  => $withdrawalsData['customerName'],
            'acctNo'        => $withdrawalsData['acctNo'],
            'sign'          => $this->createSign($wd),
            'accBankNo'     => $bankAccount,
            'accBankName'   => $accBankName
        );

        $url = $this->post_url;

        return $this->postCurl($url,$paramData);


    }

    public function notifyUrl(){
        dump(2323);exit;
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


        return $this->object_array(json_decode($rst));
    }

    private function  object_array($array){
        if(is_object($array)) {
            $array = (array)$array;
        }
        if(is_array($array)) {
            foreach($array as $key=>$value) {
                $array[$key] = $this->object_array($value);
            }
        }
        return $array;
    }




    private function addOrderNo($subMerNo){
        return $subMerNo . $this->serialNumber();
    }


    private function serialNumber(){
        return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }

    private function createSign($data){
        $stringA = '';
        $cars = array('subMerNo', 'orderNo', 'notifyUrl', 'transAmt', 'isCompay', 'customerName', 'acctNo');
        sort($cars);

        $clength = count($cars);
        $stringArr = array();
        for ($x = 0; $x < $clength; $x++) {
            //$stringA .= $cars[$x] . '=' . $data[$cars[$x]] . '&';

            $stringArr[] = $cars[$x] . '=' . $data[$cars[$x]];
        }

        $stringA = implode('&',$stringArr);

        $stringSignTemp = $stringA . "&key=".$this->key;
        return strtoupper(MD5($stringSignTemp)); //MD5签名用于信息摘要证验

    }


}


