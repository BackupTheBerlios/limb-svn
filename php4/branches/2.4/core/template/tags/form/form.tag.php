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


class form_tag_info
{
  var $tag = 'form';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'form_tag';
}

register_tag(new form_tag_info());

/**
* Compile time component for building runtime form_components
*/
class form_tag extends server_tag_component_tag
{
  var $runtime_component_path = '/core/template/components/form/form_component';

  /**
  * Returns the identifying server ID. It's value it determined in the
  * following order;
  * <ol>
  * <li>The XML id attribute in the template if it exists</li>
  * <li>The XML name attribute in the template if it exists</li>
  * <li>The value of $this->server_id</li>
  * <li>An ID generated by the get_new_server_id() function</li>
  * </ol>
  *
  * @see get_new_server_id
  * @return string value identifying this component
  * @access protected
  */
  function get_server_id()
  {
    if (!empty($this->attributes['id']))
    {
      return $this->attributes['id'];
    }
    elseif (!empty($this->attributes['name']))
    {
      return $this->attributes['name'];
    }
    elseif (!empty($this->server_id))
    {
      return $this->server_id;
    }
    else
    {
      $this->server_id = get_new_server_id();
      return $this->server_id;
    }
  }

  /**
  *
  * @return void
  * @access protected
  */
  function check_nesting_level()
  {
    if ($this->find_parent_by_class('form_tag'))
    {
      error('BADSELFNESTING', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }

    if (!isset($this->attributes['name']))
    {
      error('ATTRIBUTE_REQUIRED', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
          'attribute' => 'name',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }

  }

  /**
  *
  * @param code $ _writer
  * @return void
  * @access protected
  */
  function pre_generate(&$code)
  {
    parent :: pre_generate($code);

    $code->write_php($this->get_component_ref_code() . '->preserve_state("submitted", 1);');
    $code->write_php($this->get_component_ref_code() . '->render_state();');
    $code->write_php($this->get_dataspace_ref_code() . '->prepare();');
  }

  function generate_contents(&$code)
  {
    parent :: generate_contents($code);

    $v1 = '$' . $code->get_temp_variable();
    $v2 = '$' . $code->get_temp_variable();

    $code->write_php($v1 . ' =& request :: instance();');
    $code->write_php("if({$v2} = {$v1}->get_attribute('node_id')){");
    $code->write_php("echo \"<input type='hidden' name='node_id' value='{$v2}'>\";}");
  }

  /**
  *
  * @return form _tag this instance
  * @access protected
  */
  function &get_dataspace()
  {
    return $this;
  }

  /**
  *
  * @return string PHP runtime reference to object
  * @access protected
  */
  function get_dataspace_ref_code()
  {
    return $this->get_component_ref_code();
  }
}

?>