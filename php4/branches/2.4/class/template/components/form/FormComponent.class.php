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
  protected $is_valid = true;
  /**
  * An indexed array of variable names used to build hidden form fields which
  * are passed on in the next POST request
  */
  protected $state_vars = array();
  /**
  * Determined whether the form has errors.
  */
  public function isValid()
  {
    return $this->is_valid;
  }

  public function setValidStatus($status)
  {
    $this->is_valid = $status;
  }

  /**
  * Returns the error_list if it exists or an empty_error_list if not
  */
  public function getErrorDataset()
  {
    $errors = ErrorList :: instance()->export();

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
  public function preserveState($variable, $value=null)
  {
    $this->state_vars[$variable] = $value;
  }

  public function isFirstTime()
  {
    if(isset($this->attributes['name']))
    {
      $dataspace = DataspaceRegistry :: get($this->attributes['name']);

      return $dataspace->get('submitted') ? false : true;
    }
    else
    {
      return Limb :: toolkit()->getRequest()->hasAttribute('submitted');
    }
  }

  /**
  * Renders the hidden fields for variables which should be preserved
  */
  public function renderState()
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

  public function renderAttributes()
  {
    if(!isset($this->attributes['action']))
    {
      $this->attributes['action'] = $_SERVER['PHP_SELF'];

      $request = Limb :: toolkit()->getRequest();
      if($request->hasAttribute('popup'))
        $this->attributes['action'] .= '?popup=1';
    }

    parent :: renderAttributes();
  }

}

?>