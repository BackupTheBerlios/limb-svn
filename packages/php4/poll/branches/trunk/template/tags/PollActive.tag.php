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
  public $tag = 'poll_active';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'poll_active_tag';
}

registerTag(new PollActiveTagInfo());

/**
* The parent compile time component for lists
*/
class PollActiveTag extends ServerComponentTag
{
  public function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../components/poll_component';
  }

  public function preGenerate($code)
  {
    parent::preGenerate($code);

    $code->writePhp($this->getComponentRefCode() . '->prepare();');
  }

  public function generateContents($code)
  {
    $code->writePhp('if (' . $this->getComponentRefCode() . '->poll_exists()) {');
    parent :: generateContents($code);
    $code->writePhp('}');
  }

  public function getDataspace()
  {
    return $this;
  }

  public function getDataspaceRefCode()
  {
    return $this->getComponentRefCode();
  }
}

?>