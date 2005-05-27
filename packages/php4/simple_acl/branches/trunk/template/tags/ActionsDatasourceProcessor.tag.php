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
$taginfo =& new TagInfo('limb:DSProcessor:Actions', 'ActionsDatasourceProcessorTag');
$taginfo->setEndTag(ENDTAG_FORBIDDEN);
$taginfo->setDefaultLocation(LOCATION_SERVER);
TagDictionary::registerTag($taginfo, __FILE__);

class ActionsDatasourceProcessorTag extends ServerComponentTag
{
  var $runtimeIncludeFile = '%LIMB_SIMPLE_ACL_DIR%/template/components/ActionsDatasourceProcessorComponent.class.php';
  var $runtimeComponentName = 'ActionsDatasourceProcessorComponent';

  function generateContents(&$code)
  {
    $code->writePHP($this->getComponentRefCode() . '->process();');

    parent :: generateContents($code);
  }
}
?>
