<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: LimbPreserveState.tag.php 1159 2005-03-14 10:10:35Z pachanga $
*
***********************************************************************************/
$taginfo =& new TagInfo('limb:DS_processor:GroupActions', 'LimbGroupActionsDatasourceProcessorTag');
$taginfo->setEndTag(ENDTAG_FORBIDDEN);
$taginfo->setDefaultLocation(LOCATION_SERVER);
TagDictionary::registerTag($taginfo, __FILE__);

class LimbGroupActionsDatasourceProcessorTag extends ServerComponentTag
{
  var $runtimeIncludeFile = '%LIMB_DIR%/core/template/components/dao/GroupActionsDatasourceProcessorComponent.class.php';
  var $runtimeComponentName = 'GroupActionsDatasourceProcessorComponent';

  function preParse()
  {
    $target = $this->getAttribute('group_name');
    if (empty($target))
    {
      $this->raiseCompilerError('MISSINGREQUIREATTRIBUTE',
                                array('attribute' => 'group_name'));
    }

    return PARSER_REQUIRE_PARSING;
  }

  function generateContents(&$code)
  {
    $code->writePhp($this->getComponentRefCode() . '->setGroupName("' . $this->getAttribute('group_name') .'");');

    $code->writePHP($this->getComponentRefCode() . '->process();');

    parent :: generateContents($code);
  }
}
?>
