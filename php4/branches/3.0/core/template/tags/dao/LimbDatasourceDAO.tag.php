<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: LimbDAO.tag.php 1095 2005-02-08 13:13:22Z pachanga $
*
***********************************************************************************/
$taginfo =& new TagInfo('limb:DATASOURCE_DAO', 'LimbDatasourceDAOTag');
$taginfo->setDefaultLocation(LOCATION_SERVER);
$taginfo->setCompilerAttributes(array('target', 'class'));
TagDictionary::registerTag($taginfo, __FILE__);

class LimbDatasourceDAOTag extends ServerComponentTag
{
  var $runtimeIncludeFile = '%LIMB_DIR%/core/template/components/dao/LimbDatasourceDAOComponent.class.php';
  var $runtimeComponentName = 'LimbDatasourceDAOComponent';

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

    $code->writePhp($this->getComponentRefCode() . '->setTargets("' . $this->getAttribute('target') .'");');
    $code->writePhp($this->getComponentRefCode() . '->process();');
  }

}

?>