<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

class core_data_transfer_tag_info
{
  var $tag = 'core:DATA_TRANSFER';
  var $end_tag = ENDTAG_FORBIDDEN;
  var $tag_class = 'core_data_transfer_tag';
}

register_tag(new core_data_transfer_tag_info());

class core_data_transfer_tag extends server_component_tag
{
  var $runtime_component_path = '/core/template/components/core_transfer_component';

  function check_nesting_level()
  {
    if (!isset($this->attributes['target']))
    {
      error('ATTRIBUTE_REQUIRED', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
          'attribute' => 'target',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }

  function generate_contents(&$code)
  {
    if (isset($this->attributes['hash_id']) && isset($this->attributes['target']))
    {
      $code->write_php($this->get_component_ref_code() .
                       '->make_transfer(\'' .$this->attributes['hash_id'] . '\', \''.
                                        $this->attributes['target'] . '\');');
    }

    parent :: generate_contents($code);
  }
}
?>