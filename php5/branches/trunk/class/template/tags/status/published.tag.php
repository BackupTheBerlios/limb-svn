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
class status_published_tag_info
{
  public $tag = 'status:PUBLISHED';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'status_published_tag';
}

register_tag(new status_published_tag_info());

/**
* Defines an action take, should a dataspace variable have been set at runtime.
* The opposite of the core_default_tag
*/
class status_published_tag extends compiler_directive_tag
{
  public function pre_generate($code)
  {
    parent::pre_generate($code);

    $value = 'true';
    if (isset($this->attributes['value']) && !(boolean)$this->attributes['value'])
      $value = 'false';

    $tempvar = $code->get_temp_variable();
    $actions_tempvar = $code->get_temp_variable();
    $code->write_php('$' . $actions_tempvar . ' = ' . $this->get_dataspace_ref_code() . '->get("actions");');

    $code->write_php('if (isset($' . $actions_tempvar . '["publish"]) && isset($' . $actions_tempvar . '["unpublish"])) {');
    $code->write_php('$' . $tempvar . ' = trim(' . $this->get_dataspace_ref_code() . '->get("status"));');
    $code->write_php('if ((boolean)(site_object :: STATUS_PUBLISHED & $' . $tempvar . ') === ' . $value . ') {');
  }

  public function post_generate($code)
  {
    $code->write_php('}');
    $code->write_php('}');
    parent::post_generate($code);
  }
}

?>