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
class CoreDataTransferTagInfo
{
  var $tag = 'core:DATA_TRANSFER';
  var $end_tag = ENDTAG_FORBIDDEN;
  var $tag_class = 'core_data_transfer_tag';
}

registerTag(new CoreDataTransferTagInfo());

class CoreDataTransferTag extends CompilerDirectiveTag
{
  function preParse()
  {
    if (!isset($this->attributes['target']))
    {
      return throw(new WactException('missing required attribute',
          array('tag' => $this->tag,
          'attribute' => 'target',
          'file' => $this->source_file,
          'line' => $this->starting_line_no)));
    }

    return PARSER_REQUIRE_PARSING;
  }

  function generateContents($code)
  {
    $dataspace = $this->getDataspaceRefCode();

    if (isset($this->attributes['hash_id']) &&  isset($this->attributes['target']))
    {
      if($target = $this->parent->findChild($this->attributes['target']))
      {
        $code->writePhp($target->getComponentRefCode() . '->register_dataset(new array_dataset(' . $dataspace . '->get("' . $this->attributes['hash_id'] . '")))');
      }
    }

    parent :: generateContents($code);
  }
}

?>