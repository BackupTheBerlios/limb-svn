<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

class SysParam
{
  var $_db_table = null;
  var $_types = array("char", "int", "blob", "float");

  function sysParam()
  {
    $toolkit =& Limb :: toolkit();
    $this->_db_table =& $toolkit->createDBTable('SysParam');
  }

  function & instance()
  {
    if (!isset($GLOBALS['SysParamGlobalInstance']) || !is_a($GLOBALS['SysParamGlobalInstance'], 'SysParam'))
      $GLOBALS['SysParamGlobalInstance'] =& new SysParam();

    return $GLOBALS['SysParamGlobalInstance'];
  }

  function saveParam($identifier, $type, $value, $force_new = true)
  {
    if(!in_array($type, $this->_types))
    {
      return throw(new LimbException('trying to save undefined type in sys_param',
        array('type' => $type, 'param' => $identifier)));
    }

    $params = $this->_db_table->getList("identifier='{$identifier}'", '', '', 0, 1);

    if(empty($value))//?
    {
      if ($type == 'int' ||  $type == 'float')
        $value = (int) $value;
      else
        $value = (string) $value;
    }

    if(is_array($params) &&  count($params))
    {
      $param = current($params);

      $data = array(
          'type' => $type,
          "{$type}_value" => $value,
      );

      if($force_new)
      {
        foreach($this->_types as $type_name)
          if($type_name != $type)
              $data["{$type_name}_value"] =  NULL;

      }
      return $this->_db_table->updateById($param['id'], $data);

    }
    else
    {
      $data = array(
          "id" => null,
          'identifier' => $identifier,
          'type' => $type,
          "{$type}_value" => $value,
      );

      $this->_db_table->insert($data);

      if(catch('Exception', $e))
        return throw($e);

      return $this->_db_table->getLastInsertId();
    }
  }

  function getParam($identifier, $type='')
  {
    if(!empty($type) &&  !in_array($type, $this->_types))
    {
      return throw(new LimbException('trying to get undefined type in sys_param',
        array('type' => $type, 'param' => $identifier)));
    }

    $params = $this->_db_table->getList("identifier='{$identifier}'", '', '', 0, 1);

    if(!is_array($params) ||  !count($params))
      return null;

    $param = current($params);

    if (empty($type))
      $type = $param['type'];

    return $param["{$type}_value"];
  }
}
?>