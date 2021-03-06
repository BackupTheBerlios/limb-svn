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
* Ancester tag class for input controls
*/
abstract class ControlTag extends ServerTagComponentTag
{
  public function getServerId()
  {
    if (!empty($this->attributes['id']))
    {
      return $this->attributes['id'];
    }
    elseif (!empty($this->server_id))
    {
      return $this->server_id;
    }
    else
    {
      $this->server_id = getNewServerId();
      return $this->server_id;
    }
  }

  public function checkNestingLevel()
  {
    if ($this->findParentByClass(get_class($this)))
    {
      throw new WactException('bad self nesting',
          array('tag' => $this->tag,
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
    if (!$this->findParentByClass('form_tag'))
    {
      throw new WactException('missing enclosure',
          array('tag' => $this->tag,
          'enclosing_tag' => 'form',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }

  public function generateConstructor($code)
  {
    parent :: generateConstructor($code);

    if (array_key_exists('display_name', $this->attributes))
    {
      $code->writePhp($this->getComponentRefCode() . '->display_name = \'' . $this->attributes['display_name'] . '\';');
    unset($this->attributes['display_name']);
    }
  }

  public function postGenerate($code)
  {
    parent :: postGenerate($code);

    $code->writePhp($this->getComponentRefCode() . '->render_js_validation();');
    $code->writePhp($this->getComponentRefCode() . '->render_errors();');
  }
}

?>