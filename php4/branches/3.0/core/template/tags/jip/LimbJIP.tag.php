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
$taginfo =& new TagInfo('limb:JIP', 'LimbJIPTag');
$taginfo->setDefaultLocation(LOCATION_SERVER);
TagDictionary::registerTag($taginfo, __FILE__);

class LimbJIPTag extends ServerComponentTag
{
  var $runtimeIncludeFile = '%LIMB_DIR%/core/template/components/JIPComponent.class.php';
  var $runtimeComponentName = 'JIPComponent';

  function generateContents(&$code)
  {
    $code->writePHP($this->getComponentRefCode() . '->process();');

    parent :: generateContents($code);
  }
}
?>
