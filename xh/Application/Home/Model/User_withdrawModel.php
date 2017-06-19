<?php
namespace Home\Model;
use Think\Model;
class User_withdrawModel extends Model{
    protected $fields = array('id', 'uid','wid','money','charge','accountId','withdrawTime','status','handleTime','comment');
    protected $pk     = 'id';
}
?>
