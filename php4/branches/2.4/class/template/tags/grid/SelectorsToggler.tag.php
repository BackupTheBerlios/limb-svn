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
class GridSelectorsTogglerTagInfo
{
  var $tag = 'grid:SELECTORS_TOGGLER';
  var $end_tag = ENDTAG_FORBIDDEN;
  var $tag_class = 'grid_selectors_toggler_tag';
}

registerTag(new GridSelectorsTogglerTagInfo());

class GridSelectorsTogglerTag extends CompilerDirectiveTag
{
  function checkNestingLevel()
  {
    if (!$this->findParentByClass('grid_list_tag'))
    {
      throw new WactException('missing enclosure',
          array('tag' => $this->tag,
          'enclosing_tag' => 'grid:LIST',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }

  function generateContents($code)
  {
    $md5id = substr(md5($this->getServerId()), 0, 5);

    if(isset($this->attributes['form_id']))
      $form_id = $this->attributes['form_id'];
    else
    {
      $grid = $this->findParentByClass('grid_list_tag');
      $form_id = 'grid_form_' . $grid->getServerId();
    }

    if(isset($this->attributes['selector_name']))
      $selector_name = $this->attributes['selector_name'];
    else
      $selector_name = 'ids';

    if(isset($this->attributes['img_src']))
      $img_src = $this->attributes['img_src'];
    else
      $img_src = '/shared/images/selected.gif';

    $js = "
    <script language='javascript'>
    window.toggle_mark_{$md5id} = 0;
    function selectors_toggle_{$md5id}()
    {
      if(window.toggle_mark_{$md5id} == 0)
        window.toggle_mark_{$md5id} = 1;
      else
        window.toggle_mark_{$md5id} = 0;

      form = document.getElementById('{$form_id}');

      if(!form)
        return;

      for (i = 0; i < form.elements.length; i++)
      {
       var item = form.elements[i];

       if (item.name.indexOf(form.name + '[{$selector_name}]') != -1)
        item.checked = toggle_mark_{$md5id};
      }
    }
    </script>";

    $code->writeHtml($js);

    $code->writeHtml("<img src='{$img_src}' onclick='selectors_toggle_{$md5id}()'>");

  }
}

?>