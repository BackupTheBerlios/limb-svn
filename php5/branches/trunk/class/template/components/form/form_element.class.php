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
require_once(LIMB_DIR . '/class/template/tag_component.class.php');
require_once(LIMB_DIR . '/class/validators/error_list.class.php');
require_once(LIMB_DIR . '/class/core/dataspace_registry.class.php');

/**
* Base class for concrete form elements
*/
abstract class form_element extends tag_component
{
  /**
  * Whether the form element has validated successfully (default TRUE)
  */
  protected $is_valid = true;
  /**
  * Name of the form element (for the name attribute)
  */
  protected $display_name;
  /**
  * CSS class attribute the element should display if there is an error
  */
  protected $error_class;
  /**
  * CSS style attribute the element should display if there is an error
  */
  protected $error_style;
  /**
  * Whether form name prefix is required
  */
  protected $attach_form_prefix = true;

  /**
  * Returns a value for the name attribute. If $this->display_name is not
  * set, returns either the title, alt or name attribute (in that order
  * of preference, defined for the tag
  */
  public function get_field_name()
  {
    if (isset($this->display_name))
    {
      return $this->display_name;
    }
    elseif (isset($this->attributes['title']))
    {
      return $this->attributes['title'];
    }
    elseif (isset($this->attributes['alt']))
    {
      return $this->attributes['alt'];
    }
    else
    {
      return str_replace('_', ' ', $this->attributes['name']);
    }
  }

  public function attach_form_prefix($state = true)
  {
    $prev = $this->attach_form_prefix;
    $this->attach_form_prefix = $state;
    return $prev;
  }

  /**
  * Returns true if the form element is in an error state
  */
  public function is_valid()
  {
    return !$this->is_valid;
  }

  /**
  * Puts the element into the error state and assigns the error class or
  * style attributes, if the corresponding member vars have a value
  */
  public function set_error()
  {
    $this->is_valid = false;
    if (isset($this->error_class))
    {
      $this->attributes['class'] = $this->error_class;
    }
    if (isset($this->error_style))
    {
      $this->attributes['style'] = $this->error_style;
    }
  }

  /**
  * Returns the value of the form element
  * (the contents of the value attribute)
  */
  public function get_value()
  {
    $form_component = $this->find_parent_by_class('form_component');

    $dataspace = dataspace_registry :: get($form_component->attributes['name']);

    if(!isset($this->attributes['name']))
      debug :: write_warning("form element 'name' attribute not set:" . $this->get_server_id());

    return $dataspace->get_by_index_string($this->_make_index_name($this->attributes['name']));
  }

  public function set_value($value)
  {
    $form_component = $this->find_parent_by_class('form_component');

    $dataspace = dataspace_registry :: get($form_component->attributes['name']);

    if(!isset($this->attributes['name']))
      debug :: write_warning("form element 'name' attribute not set:" . $this->get_server_id());

    $dataspace->set_by_index_string($this->_make_index_name($this->attributes['name']), $value);
  }

  public function render_errors()
  {
    $error_list = error_list :: instance();

    if($errors = $error_list->get_errors($this->id))
    {
      echo '<script language="javascript">';

      foreach($errors as $error_data)
      {
        echo "set_error('{$this->id}', '" . addslashes($error_data['error']) . "');";
      }

      echo '</script>';
    }
  }

  public function render_js_validation()
  {
    echo '';
  }

  protected function _make_index_name($name)
  {
    return preg_replace('/^([^\[\]]+)(\[.*\])*$/', "[\\1]\\2", $name);
  }

  protected function _process_name_attribute($value)
  {
    $form_component = $this->find_parent_by_class('form_component');

    $form_name = $form_component->attributes['name'];

    return $form_name . $this->_make_index_name($value);
  }

  public function render_attributes()
  {
    $this->_process_localized_value();

    foreach ($this->attributes as $attrib_name => $value)
    {
      if($this->attach_form_prefix && $attrib_name == 'name')
      {
        $value = $this->_process_name_attribute($value);
      }

      if (!is_null($value))
      {
        echo ' ';
        echo $attrib_name;

        echo '="';
        echo htmlspecialchars($value, ENT_QUOTES);
        echo '"';
      }
    }
  }

  protected function _process_localized_value()
  {
    if (!isset($this->attributes['locale_value']))
      return;

    if(isset($this->attributes['locale_type']))
    {
      if(strtolower($this->attributes['locale_type']) == 'content')
        $locale_constant = constant('CONTENT_LOCALE_ID');
      else
        $locale_constant = constant('MANAGEMENT_LOCALE_ID');

      unset($this->attributes['locale_type']);
    }
    else
      $locale_constant = constant('MANAGEMENT_LOCALE_ID');

    if(isset($this->attributes['locale_file']))
    {
      $this->attributes['value'] = strings :: get($this->attributes['locale_value'], $this->attributes['locale_file'], $locale_constant);
      unset($this->attributes['locale_file']);
    }
    else
      $this->attributes['value'] = strings :: get($this->attributes['locale_value'], 'common', $locale_constant);

    unset($this->attributes['locale_value']);
  }
}
?>