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

/**
* Server component tags have a corresponding server component which represents
* an API which can be used to manipulate the marked up portion of the template.
*/
class ServerComponentTag extends CompilerComponent
{
  var $runtime_component_path = '';

  function ServerComponentTag()
  {
    $this->runtime_component_path = LIMB_DIR . '/class/template/component';//???
  }

  /**
  * Returns a string of PHP code identifying the component in the hierarchy.
  */
  function getComponentRefCode()
  {
    $path = $this->parent->getComponentRefCode();
    return $path . '->children[\'' . $this->getServerId() . '\']';
  }

  /**
  * Calls the parent get_component_ref_code() method and writes it to the
  * compiled template, appending an add_child() method used to create
  * this component at runtime
  */
  function generateConstructor($code)
  {
    if (file_exists($this->runtime_component_path . '.class.php'))
      $code->registerInclude($this->runtime_component_path . '.class.php');
    else
      return throw(new FileNotFoundException('run time component file not found', $this->runtime_component_path));

    $component_class_name = end(explode('/', $this->runtime_component_path));

    if(!$component_class_name)
      return throw(new WactException('run time component file doesn\'t contains component class name',
                                array('file_path' => $this->runtime_component_path)));

    $code->writePhp($this->parent->getComponentRefCode() . '->add_child(new ' . $component_class_name . '(), \'' . $this->getServerId() . '\');' . "\n");

    parent::generateConstructor($code);
  }
}

?>