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
$taginfo =& new TagInfo('limb:DAO', 'LimbDAOTag');
$taginfo->setDefaultLocation(LOCATION_SERVER);
$taginfo->setCompilerAttributes(array('target', 'class'));
TagDictionary::registerTag($taginfo, __FILE__);

class LimbDAOTag extends ServerComponentTag
{
  var $runtimeIncludeFile = '%LIMB_DIR%/core/template/components/dao/LimbDAOComponent.class.php';
  var $runtimeComponentName = 'LimbDAOComponent';

  function preParse()
  {
    $target = $this->getAttribute('target');
    if (empty($target))
    {
      $this->raiseCompilerError('MISSINGREQUIREATTRIBUTE',
                                array('attribute' => 'target'));
    }

    $class_path = $this->getAttribute('class');
    if (empty($class_path))
    {
      $this->raiseCompilerError('MISSINGREQUIREATTRIBUTE',
                                array('attribute' => 'class'));
    }

    return PARSER_REQUIRE_PARSING;
  }

  function generateContents(&$code)
  {
    parent :: generateContents($code);

    $code->writePhp($this->getComponentRefCode() . '->setClassPath("' . $this->getAttribute('class') .'");');

    $navigator = $this->getAttribute('navigator');
    if(!empty($navigator))
    {
      $code->writePhp($this->getComponentRefCode() . '->setNavigator("' . $navigator .'");');
    }

    $code->writePhp($this->getComponentRefCode() . '->setTargets("' . $this->getAttribute('target') .'");');
    $code->writePhp($this->getComponentRefCode() . '->process();');
  }

}

?>