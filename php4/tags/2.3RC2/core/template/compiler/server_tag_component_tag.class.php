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

/**
* Server tag component tags are server_component_tags which also correspond to
* an HTML tag. Makes it easier to implement instead of extending from the
* server_component_tag class
*/
class server_tag_component_tag extends server_component_tag
{
  /**
  * Returns the XML tag name
  *
  * @return string
  * @access protected
  */
  function get_rendered_tag()
  {
    return $this->tag;
  }

  /**
  * Adds any additional XML attributes
  *
  * @param code $ _writer
  * @return void
  * @abstract
  * @access protected
  */
  function generate_extra_attributes($code)
  {
  }

  /**
  * Calls the parent pre_generate() method then writes the XML tag name
  * plus a PHP string which renders the attributes from the runtime
  * component.
  *
  * @param code $ _writer
  * @return void
  * @access protected
  * @todo compiler needs to detect XML to allow for empty tags
  */
  function pre_generate(&$code)
  {
    parent::pre_generate($code);
    $code->write_html('<' . $this->get_rendered_tag());
    $code->write_php($this->get_component_ref_code() . '->render_attributes();');
    // if this is an XML based document and this component is self closing,
    // (a condition we cannot yet detect.)
    // $code->write_html('/');
    $this->generate_extra_attributes($code);
    $code->write_html('>');
  }

  /**
  * Writes the closing tag string to the compiled template
  *
  * @param code $ _writer
  * @return void
  * @access protected
  */
  function post_generate(&$code)
  {
    if ($this->has_closing_tag)
    {
      $code->write_html('</' . $this->get_rendered_tag() . '>');
    }
    parent::post_generate($code);
  }

  /**
  * Writes the compiled template constructor from the runtime component,
  * assigning the attributes found at compile time to the runtime component
  * via a serialized string
  *
  * @param code $ _writer
  * @return void
  * @access protected
  */
  function generate_constructor(&$code)
  {
    parent::generate_constructor($code);
    $code->write_php($this->get_component_ref_code() . '->attributes = ' . var_export($this->attributes, true) . ';');
  }
}

?>