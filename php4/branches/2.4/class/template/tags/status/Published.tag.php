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
class StatusPublishedTagInfo
{
  var $tag = 'status:PUBLISHED';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'status_published_tag';
}

registerTag(new StatusPublishedTagInfo());

/**
* Defines an action take, should a dataspace variable have been set at runtime.
* The opposite of the core_default_tag
*/
class StatusPublishedTag extends CompilerDirectiveTag
{
  function preGenerate($code)
  {
    parent::preGenerate($code);

    $value = 'true';
    if (isset($this->attributes['value']) &&  !(boolean)$this->attributes['value'])
      $value = 'false';

    $tempvar = $code->getTempVariable();
    $actions_tempvar = $code->getTempVariable();
    $code->writePhp('$' . $actions_tempvar . ' = ' . $this->getDataspaceRefCode() . '->get("actions");');

    $code->writePhp('if (isset($' . $actions_tempvar . '["publish"]) && isset($' . $actions_tempvar . '["unpublish"])) {');
    $code->writePhp('$' . $tempvar . ' = trim(' . $this->getDataspaceRefCode() . '->get("status"));');
    $code->writePhp('if ((boolean)(site_object :: STATUS_PUBLISHED & $' . $tempvar . ') === ' . $value . ') {');
  }

  function postGenerate($code)
  {
    $code->writePhp('}');
    $code->writePhp('}');
    parent::postGenerate($code);
  }
}

?>