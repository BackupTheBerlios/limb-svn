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
$taginfo =& new TagInfo('limb:DATASOURCE', 'LimbDatasourceTag');
$taginfo->setDefaultLocation(LOCATION_SERVER);
$taginfo->setCompilerAttributes(array('target', 'datasource_path'));
TagDictionary::registerTag($taginfo, __FILE__);

class LimbDatasourceTag extends ServerComponentTag
{
  var $runtimeIncludeFile = '%LIMB_DIR%/class/template/components/datasource/LimbDatasourceComponent.class.php';
  var $runtimeComponentName = 'LimbDatasourceComponent';

  function preParse()
  {
    $target = $this->getAttribute('target');
    if (empty($target))
    {
      $this->raiseCompilerError('MISSINGREQUIREATTRIBUTE',
                                array('attribute' => 'target'));
    }

    $path = $this->getAttribute('datasource_path');
    if (empty($path))
    {
      $this->raiseCompilerError('MISSINGREQUIREATTRIBUTE',
                                array('attribute' => 'datasource_path'));
    }

    $this->_checkOrderParameter();


    return PARSER_REQUIRE_PARSING;
  }

  function _checkOrderParameter()
  {
    if (!isset($this->attributes['order']))
      return;

    $order_items = explode(',', $this->getAttribute('order'));
    $order_pairs = array();
    foreach($order_items as $order_pair)
    {
      $arr = explode('=', $order_pair);

      if(!isset($arr[1]))
        continue;

      if(strtolower($arr[1]) != 'asc' &&
         strtolower($arr[1]) != 'desc' &&
        !strtolower($arr[1]) == 'rand()')
        $this->raiseCompilerError('INVALID_ATTRIBUTE_SYNTAX',
                                array('attribute' => 'order'));
    }
  }

  function generateContents(&$code)
  {
    parent :: generateContents($code);

    $code->writePhp($this->getComponentRefCode() . '->set_datasource_path("' . $this->getAttribute('datasource_path') .'");');

    $navigator = $this->getAttribute('navigator');
    if(!empty($navigator))
    {
      $code->writePhp($this->getComponentRefCode() . '->setup_navigator("' . $navigator .'");');
    }

    $code->writePhp($this->getComponentRefCode() . '->setup_targets("' . $this->getAttribute('target') .'");');
  }

}

?>