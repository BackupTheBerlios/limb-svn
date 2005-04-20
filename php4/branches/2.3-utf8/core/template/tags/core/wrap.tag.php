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

class core_wrap_tag_info
{
  var $tag = 'core:WRAP';
  var $end_tag = ENDTAG_FORBIDDEN;
  var $tag_class = 'core_wrap_tag';
}

register_tag(new core_wrap_tag_info());

/**
* Merges the current template with a wrapper template, the current
* template being inserted into the wrapper at the point where the
* wrap tag exists.
*/
class core_wrap_tag extends compiler_directive_tag
{
  var $resolved_source_file;

  /**
  * List of tag names of the children of the wrap tag
  *
  * @var array
  * @access private
  */
  var $keylist;

  /**
  *
  * @return void
  * @access protected
  */
  function check_nesting_level()
  {
    if ($this->find_parent_by_class('core_wrap_tag'))
    {
      error('BADSELFNESTING', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }

  /**
  *
  * @return int PARSER_FORBID_PARSING
  * @access protected
  */
  function pre_parse()
  {
    global $tag_dictionary;
    $file = $this->attributes['file'];
    if (empty($file))
    {
      error('MISSINGENCLOSURE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
          'attribute' => 'file',
          'enclosing_tag' => 'pager:navigator',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }

    if (!$this->resolved_source_file = resolve_template_source_file_name($file))
    {
      error('MISSINGFILE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
          'srcfile' => $file,
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }

    $sfp =& new source_file_parser($this->resolved_source_file, $tag_dictionary);
    $sfp->parse($this);
    return PARSER_FORBID_PARSING;
  }

  /**
  *
  * @return void
  * @access protected
  */
  function prepare()
  {
    $this->parent->wrapping_component =& $this;

    parent :: prepare();
  }

  /**
  *
  * @return void
  * @access protected
  */
  function generate_wrapper_prefix(&$code)
  {
    $this->keylist = array_keys($this->children);
    $name = $this->attributes['placeholder'];
    reset($this->keylist);
    while (list(, $key) = each($this->keylist))
    {
      $child = &$this->children[$key];
      if ($child->get_server_id() == $name)
      {
        break;
      }
      $child->generate($code);
    }
  }

  /**
  *
  * @param code $ _writer
  * @return void
  * @access protected
  */
  function generate_wrapper_postfix(&$code)
  {
    while (list(, $key) = each($this->keylist))
    {
      $this->children[$key]->generate($code);
    }
  }

  /**
  * By the time this is called we have already called generate
  * on all of our children, so does nothing
  *
  * @param code $ _writer
  * @return void
  * @access protected
  */
  function generate(&$code)
  {
    // By the time this is called we have already called generate
    // on all of our children.
  }
}

?>