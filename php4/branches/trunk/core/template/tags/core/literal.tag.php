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


class core_literal_tag_info
{
  var $tag = 'core:LITERAL';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'core_literal_tag';
}

register_tag(new core_literal_tag_info());

/**
* Prevents a section of the template from being parsed, placing the contents
* directly into the compiled template
*/
class core_literal_tag extends compiler_directive_tag
{
  /**
  *
  * @return void
  * @access protected
  */
  function check_nesting_level()
  {
    if ($this->find_parent_by_class('core_literal_tag'))
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
    return PARSER_FORBID_PARSING;
  }
}

?>