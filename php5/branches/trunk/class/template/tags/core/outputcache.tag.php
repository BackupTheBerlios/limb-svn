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
class core_outputcache_tag_info
{
  public $tag = 'core:OUTPUTCACHE';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'core_outputcache_tag';
}

register_tag(new core_outputcache_tag_info());

class core_outputcache_tag extends server_component_tag
{
  public function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/outputcache_component';
  }

  public function generate_contents($code)
  {
    $v = '$' . $code->get_temp_variable();

    $code->write_php($this->get_component_ref_code() . '->prepare();');
    $code->write_php('if (!(' . $v . ' = ' . $this->get_component_ref_code() . '->get())) {');
    $code->write_php('ob_start();');

    parent::generate_contents($code);

    $code->write_php($this->get_component_ref_code() . '->write(ob_get_contents());ob_end_flush();');
    $code->write_php('}');
    $code->write_php('else echo ' . $v . ';');
  }
}

?>