<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: LimbForm.tag.php 1028 2005-01-18 11:06:55Z pachanga $
*
***********************************************************************************/
$taginfo =& new TagInfo('limb:PRESERVE_STATE', 'LimbPreserveStateTag');
$taginfo->setEndTag(ENDTAG_FORBIDDEN);
$taginfo->setDefaultLocation(LOCATION_SERVER);
TagDictionary::registerTag($taginfo, __FILE__);

class LimbPreserveStateTag extends CompilerDirectiveTag
{
  function preParse()
  {
    $name = $this->getAttribute('name');
    if (empty($name))
    {
      $this->raiseCompilerError('MISSINGREQUIREATTRIBUTE',
                                array('attribute' => 'name'));
    }

    return PARSER_FORBID_PARSING;
  }

  function preGenerate(&$code)
  {
    $code->writePHP($this->getComponentRefCode() . '->preserveState("' . $this->getAttribute('name') . '");');

    parent :: preGenerate($code);
  }
}
?>
