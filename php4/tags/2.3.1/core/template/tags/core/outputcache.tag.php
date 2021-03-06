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
class core_outputcache_tag_info
{
  var $tag = 'core:OUTPUTCACHE';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'core_outputcache_tag';
}

register_tag(new core_outputcache_tag_info());

class core_outputcache_tag extends server_component_tag
{
  var $runtime_component_path = '/core/template/components/outputcache_component';

  function generate_contents(&$code)
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