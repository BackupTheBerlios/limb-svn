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
class TabsTagInfo
{
  var $tag = 'tabs';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'tabs_tag';
}

registerTag(new TabsTagInfo());

class TabsTag extends CompilerDirectiveTag
{
  public
    $tabs = array(),
    $tabulator_class = 'class="tabulator"',
    $tab_class = 'class="tab"',
    $active_tab_class = 'class="active-tab"',
    $use_cookie = false;

  function prepare()
  {
    if(isset($this->attributes['active_tab']))
      $this->active_tab = $this->attributes['active_tab'];
    else
      $this->active_tab = null;

    if(isset($this->attributes['class']))
      $this->tabulator_class = 'class="' . $this->attributes['class'] . '"';

    if(isset($this->attributes['tab_class']))
      $this->tab_class = 'class="' . $this->attributes['tab_class'] . '"';

    if(isset($this->attributes['active_tab_class']))
      $this->active_tab_class = 'class="' . $this->attributes['active_tab_class'] . '"';

    if(isset($this->attributes['use_cookie']))
      $this->use_cookie = $this->attributes['use_cookie'];

    parent :: prepare();
  }

  function _loadTabsJsScript($code)
  {
    if (defined('TABS_SCRIPT_LOADED'))
      return;

    define('TABS_SCRIPT_LOADED', 1);

    $code->writeHtml("<script type='text/javascript' src='/shared/js/tabs.js'></script>");
  }

  function preGenerate($code)
  {
    $this->_loadTabsJsScript($code);

    parent :: preGenerate($code);
  }

  function postGenerate($code)
  {
    $js = '';

    if(isset($this->attributes['active_tab']))
      $active_tab = $this->attributes['active_tab'];
    else
      $active_tab = reset($this->tabs);

    if(!$this->tabs ||  !$active_tab ||  !in_array($active_tab, $this->tabs))
    {
      return throw(new WactException('invalid tabs declaration. Check your tabs settings',
          array('tag' => $this->tag,
          'file' => $this->source_file,
          'line' => $this->starting_line_no)));
    }

    foreach($this->tabs as $id)
     $js .= "var tab_data={'id':'{$id}'};\n tabs.registerTabItem(tab_data);\n";


    if ($this->use_cookie)
      $js .= "if (active_tab = get_cookie('active_tab'))\n
                tabs.activate(active_tab);\n
              else
                tabs.activate('{$active_tab}');\n";
    else
    $js .= "tabs.activate('{$active_tab}');\n";

    $code->writeHtml("
      <script type='text/javascript'>
        var tabs = new tabs_container();
        {$js}
      </script>");

    parent :: postGenerate($code);
  }
}

?>
