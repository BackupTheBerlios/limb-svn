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
require_once(LIMB_DIR . '/class/core/ArrayDataset.class.php');
require_once(LIMB_DIR . '/class/template/TagComponent.class.php');
require_once(LIMB_DIR . '/class/validators/ErrorList.class.php');
require_once(LIMB_DIR . '/class/core/DataspaceRegistry.class.php');
require_once(LIMB_DIR . '/class/core/EmptyDataset.class.php');

/**
* The form_component provide a runtime API for control the behavior of a form
*/
class FormComponent extends TagComponent
{
  /**
  * Switch to identify whether the form has errors or not
  */
  var $is_valid = true;
  /**
  * An indexed array of variable names used to build hidden form fields which
  * are passed on in the next POST request
  */
  var $state_vars = array();
  /**
  * Determined whether the form has errors.
  */
  function isValid()
  {
    return $this->is_valid;
  }

  function setValidStatus($status)
  {
    $this->is_valid = $status;
  }

  /**
  * Returns the error_list if it exists or an empty_error_list if not
  */
  function getErrorDataset()
  {
    $inst =& ErrorList :: instance();
    $errors = $inst->export();

    if (!sizeof($errors))
      return new EmptyDataset();

    $array = array();
    foreach($errors as $field_name => $errors_array)
    {
      foreach($errors_array as $error)
      {
        if($child = $this->findChild($field_name))
        {
          if(!$label = $child->getAttribute('label'))
            $label = $child->getServerId();

          $array[] = array('label' => $label, 'error_message' => $error['error']);
        }
      }
    }

    return new ArrayDataset($array);
  }

  /**
  * Identify a variable stored in the dataspace of the component, which
  * should be passed as a hidden form field in the form post.
  */
  function preserveState($variable, $value=null)
  {
    $this->state_vars[$variable] = $value;
  }

  function isFirstTime()
  {
    if(isset($this->attributes['name']))
    {
      $dataspace = DataspaceRegistry :: get($this->attributes['name']);

      return $dataspace->get('submitted') ? false : true;
    }
    else
    {
      $toolkit =& Limb :: toolkit();
      $request =& $toolkit->getRequest();
      return $request->hasAttribute('submitted');
    }
  }

  /**
  * Renders the hidden fields for variables which should be preserved
  */
  function renderState()
  {
    foreach ($this->state_vars as $var => $value)
    {
      echo '<input type="hidden" name="';
      echo $this->attributes['name'] . '[' . $var . ']';
      echo '" value="';

      if(!$value)
        echo htmlspecialchars($this->getAttribute($var), ENT_QUOTES);
      else
        echo htmlspecialchars($value, ENT_QUOTES);

      echo '">';
    }
  }

  function renderAttributes()
  {
    if(!isset($this->attributes['action']))
    {
      $this->attributes['action'] = $_SERVER['PHP_SELF'];

      $toolkit =& Limb :: toolkit();
      $request =& $toolkit->getRequest();
      if($request->hasAttribute('popup'))
        $this->attributes['action'] .= '?popup=1';
    }

    parent :: renderAttributes();
  }

}

?>