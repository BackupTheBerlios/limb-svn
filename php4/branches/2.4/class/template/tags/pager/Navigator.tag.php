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
class PagerNavigatorTagInfo
{
  public $tag = 'pager:NAVIGATOR';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'pager_navigator_tag';
}

registerTag(new PagerNavigatorTagInfo());

/**
* Compile time component for root of a pager tag
*/
class PagerNavigatorTag extends ServerComponentTag
{
  function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/pager_component';
  }

  public function preGenerate($code)
  {
    parent::preGenerate($code);

    $code->writePhp($this->getComponentRefCode() . '->prepare();');
  }

  public function generateConstructor($code)
  {
    parent::generateConstructor($code);

    if (array_key_exists('items', $this->attributes))
    {
      $code->writePhp($this->getComponentRefCode() . '->items = \'' . $this->attributes['items'] . '\';');
    unset($this->attributes['items']);
    }
    if (array_key_exists('pages_per_section', $this->attributes))
    {
      $code->writePhp($this->getComponentRefCode() . '->pages_per_section = \'' . $this->attributes['pages_per_section'] . '\';');
    unset($this->attributes['pages_per_section']);
    }
  }

  public function getComponentRefCode()
  {
    if (isset($this->attributes['mirror_of']))
    {
      if($mirrored_pager = $this->parent->findChild($this->attributes['mirror_of']))
        return $mirrored_pager->getComponentRefCode();
      else
        throw new WactException('mirrowed component for pager not found',
          array('tag' => $this->tag,
          'mirror_of' => $this->attributes['mirror_of'],
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
    else
      return parent :: getComponentRefCode();
  }
}

?>