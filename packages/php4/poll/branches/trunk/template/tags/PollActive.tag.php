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

class PollActiveTagInfo
{
  var $tag = 'poll_active';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'poll_active_tag';
}

registerTag(new PollActiveTagInfo());

/**
* The parent compile time component for lists
*/
class PollActiveTag extends ServerComponentTag
{
  function PollActiveTag()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../components/poll_component';
  }

  function preGenerate($code)
  {
    parent::preGenerate($code);

    $code->writePhp($this->getComponentRefCode() . '->prepare();');
  }

  function generateContents($code)
  {
    $code->writePhp('if (' . $this->getComponentRefCode() . '->poll_exists()) {');
    parent :: generateContents($code);
    $code->writePhp('}');
  }

  function getDataspace()
  {
    return $this;
  }

  function getDataspaceRefCode()
  {
    return $this->getComponentRefCode();
  }
}

?>