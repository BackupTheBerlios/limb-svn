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
abstract class server_component_tag extends compiler_component
{
  protected $runtime_component_path = '';

  function __construct()
  {
    $this->runtime_component_path = LIMB_DIR . '/class/template/component';//???
  }

  /**
  * Returns a string of PHP code identifying the component in the hierarchy.
  */
  public function get_component_ref_code()
  {
    $path = $this->parent->get_component_ref_code();
    return $path . '->children[\'' . $this->get_server_id() . '\']';
  }

  /**
  * Calls the parent get_component_ref_code() method and writes it to the
  * compiled template, appending an add_child() method used to create
  * this component at runtime
  */
  public function generate_constructor($code)
  {
    if (file_exists($this->runtime_component_path . '.class.php'))
      $code->register_include($this->runtime_component_path . '.class.php');
    else
      throw new FileNotFoundException('run time component file not found', $this->runtime_component_path);

    $component_class_name = end(explode('/', $this->runtime_component_path));

    if(!$component_class_name)
      throw new WactException('run time component file doesn\'t contains component class name',
                                array('file_path' => $this->runtime_component_path));

    $code->write_php($this->parent->get_component_ref_code() . '->add_child(new ' . $component_class_name . '(), \'' . $this->get_server_id() . '\');' . "\n");

    parent::generate_constructor($code);
  }
}

?>