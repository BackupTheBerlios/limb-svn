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
class htmlspecialchars_tag_info
{
  var $tag = 'core:HTMLSPECIALCHARS';
  var $end_tag = ENDTAG_FORBIDDEN;
  var $tag_class = 'htmlspecialchars_tag';
}

register_tag(new htmlspecialchars_tag_info());

class htmlspecialchars_tag extends compiler_directive_tag
{
  function pre_parse()
  {
    if (! array_key_exists('hash_id', $this->attributes) ||
        empty($this->attributes['hash_id']))
    {
      error('MISSINGREQUIREATTRIBUTE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
        array('tag' => $this->tag,
              'attribute' => 'hash_id',
              'file' => $this->source_file,
              'line' => $this->starting_line_no));
    }
    return PARSER_FORBID_PARSING;
  }

  function generate_contents(&$code)
  {
    if(isset($this->attributes['hash_id']))
    {
      $tmp = '$' . $code->get_temp_variable();

      $code->write_php($tmp . ' = ' . $this->get_dataspace_ref_code() . '->get("' . $this->attributes['hash_id'] . '");');

      if(isset($this->attributes['addslashes']))
        $code->write_php("{$tmp} = addslashes({$tmp});");

      $code->write_php("echo htmlspecialchars({$tmp}, ENT_QUOTES);");
    }
  }
}

?>