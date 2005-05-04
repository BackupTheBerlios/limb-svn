<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: LimbForm.tag.php 1013 2005-01-12 12:13:22Z pachanga $
*
***********************************************************************************/
$taginfo =& new TagInfo('limb:form:REFERER', 'LimbFormRefererTag');
$taginfo->setDefaultLocation(LOCATION_SERVER);
$taginfo->setEndTag(ENDTAG_FORBIDDEN);
TagDictionary::registerTag($taginfo, __FILE__);

class LimbFormRefererTag extends CompilerDirectiveTag
{
  function generateContents(&$code)
  {
    $ref = $code->getTempVarRef();
    $ds = $code->getTempVarRef();

    $code->writePHP($ds . ' =&' . $this->getDataSourceRefCode() . ';');

    $code->writePHP("if(!$ref = {$ds}->get('referer'))\n");

    if($this->getBoolAttribute('use_current'))
      $code->writePHP($ref . ' = isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : "";' . "\n");
    else
      $code->writePHP($ref . ' = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "";' . "\n");

    $code->writePHP("if($ref)");
    $code->writePHP('echo "<input type=\'hidden\' name=\'referer\' value=\'' . $ref . '\'>";');
  }
}
?>
