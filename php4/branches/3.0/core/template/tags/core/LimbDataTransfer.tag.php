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
$taginfo =& new TagInfo('limb:DATA_TRANSFER', 'LimbDataTransferTag');
$taginfo->setEndTag(ENDTAG_FORBIDDEN);
TagDictionary::registerTag($taginfo, __FILE__);

class LimbDataTransferTag extends CompilerDirectiveTag
{
  function preParse()
  {
    $target = $this->getAttribute('target');
    if (empty($target))
    {
      $this->raiseCompilerError('MISSINGREQUIREATTRIBUTE',
                                array('attribute' => 'target'));
    }

    $from = $this->getAttribute('from');
    if (empty($from))
    {
      $this->raiseCompilerError('MISSINGREQUIREATTRIBUTE',
                                array('attribute' => 'from'));
    }

    return PARSER_REQUIRE_PARSING;
  }

  function generateContents(&$code)
  {
    $dataspace =& $this->getDataSourceRefCode();

    $target_name = $this->getAttribute('target');
    $from = $this->getAttribute('from');

    if (!empty($from) && !empty($target_name))
    {
      if($target =& $this->parent->findChild($target_name))
      {
        $code->writePhp($target->getComponentRefCode() . '->registerDataSet(new PagedArrayDataset(' . $dataspace . '->get("' . $from . '")));');
      }
    }

    parent :: generateContents($code);
  }
}

?>