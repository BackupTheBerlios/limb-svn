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
$taginfo =& new TagInfo('limb:USER', 'LimbUserTag');
$taginfo->setDefaultLocation(LOCATION_SERVER);
TagDictionary::registerTag($taginfo, __FILE__);

class LimbUserTag extends ServerDataComponentTag
{
  var $runtimeIncludeFile;
  var $runtimeComponentName = 'DataSourceComponent';

  function generateContents(&$code)
  {
    $this->tempvar = $code->getTempVarRef();

    $toolkit_var = $code->getTempVarRef();
    $user_var = $code->getTempVarRef();

    $code->writePHP($toolkit_var . " =& Limb :: toolkit();\n");
    $code->writePHP($user_var . " =& ". $toolkit_var . "->getUser();\n");

    $code->writePHP($this->getComponentRefCode() . '->registerDataSource(' . $user_var . ');');

    parent :: generateContents($code);
  }
}
?>
