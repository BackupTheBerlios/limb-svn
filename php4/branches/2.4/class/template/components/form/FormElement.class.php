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
require_once(LIMB_DIR . '/class/template/TagComponent.class.php');
require_once(LIMB_DIR . '/class/validators/ErrorList.class.php');
require_once(LIMB_DIR . '/class/core/DataspaceRegistry.class.php');

/**
* Base class for concrete form elements
*/
class FormElement extends TagComponent
{
  /**
  * Whether the form element has validated successfully (default TRUE)
  */
  var $is_valid = true;
  /**
  * Name of the form element (for the name attribute)
  */
  var $display_name;
  /**
  * CSS class attribute the element should display if there is an error
  */
  var $error_class;
  /**
  * CSS style attribute the element should display if there is an error
  */
  var $error_style;
  /**
  * Whether form name prefix is required
  */
  var $attach_form_prefix = true;

  /**
  * Returns a value for the name attribute. If $this->display_name is not
  * set, returns either the title, alt or name attribute (in that order
  * of preference, defined for the tag
  */
  function getFieldName()
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

  function attachFormPrefix($state = true)
  {
    $prev = $this->attach_form_prefix;
    $this->attach_form_prefix = $state;
    return $prev;
  }

  /**
  * Returns true if the form element is in an error state
  */
  function isValid()
  {
    return !$this->is_valid;
  }

  /**
  * Puts the element into the error state and assigns the error class or
  * style attributes, if the corresponding member vars have a value
  */
  function setError()
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
  function getValue()
  {
    $form_component = $this->findParentByClass('form_component');

    $dataspace = DataspaceRegistry :: get($form_component->attributes['name']);

    if(!isset($this->attributes['name']))
      Debug :: writeWarning("form element 'name' attribute not set:" . $this->getServerId());

    return $dataspace->getByIndexString($this->_makeIndexName($this->attributes['name']));
  }

  function setValue($value)
  {
    $form_component = $this->findParentByClass('form_component');

    $dataspace = DataspaceRegistry :: get($form_component->attributes['name']);

    if(!isset($this->attributes['name']))
      Debug :: writeWarning("form element 'name' attribute not set:" . $this->getServerId());

    $dataspace->setByIndexString($this->_makeIndexName($this->attributes['name']), $value);
  }

  function renderErrors()
  {
    $error_list = ErrorList :: instance();

    if($errors = $error_list->getErrors($this->id))
    {
      echo '<script language="javascript">';

      foreach($errors as $error_data)
      {
        echo "set_error('{$this->id}', '" . addslashes($error_data['error']) . "');";
      }

      echo '</script>';
    }
  }

  function renderJsValidation()
  {
    echo '';
  }

  function _makeIndexName($name)
  {
    return preg_replace('/^([^\[\]]+)(\[.*\])*$/', "[\\1]\\2", $name);
  }

  function _processNameAttribute($value)
  {
    $form_component = $this->findParentByClass('form_component');

    $form_name = $form_component->attributes['name'];

    return $form_name . $this->_makeIndexName($value);
  }

  function renderAttributes()
  {
    $this->_processLocalizedValue();

    foreach ($this->attributes as $attrib_name => $value)
    {
      if($this->attach_form_prefix &&  $attrib_name == 'name')
      {
        $value = $this->_processNameAttribute($value);
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

  function _processLocalizedValue()
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
      $this->attributes['value'] = Strings :: get($this->attributes['locale_value'], $this->attributes['locale_file'], $locale_constant);
      unset($this->attributes['locale_file']);
    }
    else
      $this->attributes['value'] = Strings :: get($this->attributes['locale_value'], 'common', $locale_constant);

    unset($this->attributes['locale_value']);
  }
}
?>