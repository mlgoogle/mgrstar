<?php
namespace Home\Model;
use Think\Model\RelationModel;
class   userInfoModel extends RelationModel{
  protected $tableName = 'user_info'; 
  protected $_link = array(
  'agent'=>array(
      'mapping_type'  => self::HAS_ONE,
      'class_name'    => 'agent_info',
      'foreign_key'   => 'code',
      'as_fields' => 'nickname:agentname',
      ),
  );
}
?>
