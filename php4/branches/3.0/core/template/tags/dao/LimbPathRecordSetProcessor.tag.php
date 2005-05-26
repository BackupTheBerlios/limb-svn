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
$taginfo =& new TagInfo('limb:RSProcessor:PATH', 'LimbPathRecordSetProcessorTag');
$taginfo->setEndTag(ENDTAG_FORBIDDEN);
$taginfo->setDefaultLocation(LOCATION_SERVER);
TagDictionary::registerTag($taginfo, __FILE__);

class LimbPathRecordSetProcessorTag extends ServerComponentTag
{
  var $runtimeIncludeFile = '%LIMB_DIR%/core/template/components/dao/PathRecordSetProcessorComponent.class.php';
  var $runtimeComponentName = 'PathRecordSetProcessorComponent';

   function preParse()
   {
      $source = $this->getAttribute('source');
      if (empty($source))
        $this->raiseCompilerError('MISSINGREQUIREATTRIBUTE',
                                array('attribute' => 'source'));

      return PARSER_REQUIRE_PARSING;
    }

  function generateContents(&$code)
  {
    $code->writePHP($this->getComponentRefCode() . '->setSource(\'' . $this->getAttribute('source') .'\');');
    $code->writePHP($this->getComponentRefCode() . '->process();');

    parent :: generateContents($code);
  }
}
?>
