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
require_once(LIMB_DIR . '/class/template/components/form/text_area_component.class.php');

if(!defined('RICHEDIT_DEFAULT_WIDTH'))
  define('RICHEDIT_DEFAULT_WIDTH', '600px');
if(!defined('RICHEDIT_DEFAULT_HEIGHT'))
  define('RICHEDIT_DEFAULT_HEIGHT', '400px');
if(!defined('RICHEDIT_DEFAULT_ROWS'))
  define('RICHEDIT_DEFAULT_ROWS', '30');
if(!defined('RICHEDIT_DEFAULT_COLS'))
  define('RICHEDIT_DEFAULT_COLS', '60');

class richedit_component extends text_area_component
{
  public function render_contents()
  {
    echo htmlspecialchars($this->get_value(), ENT_QUOTES);
  }

  protected function _load_js_script()
  {
    if (defined('HTMLAREA_SCRIPT_LOADED'))
      return;

    define('HTMLAREA_SCRIPT_LOADED', 1);

    echo "
    <script type='text/javascript'>
      var _editor_url = '/HTMLArea-3.0-rc1/';
      var _editor_lang = 'en';
    </script>

    <script type='text/javascript' src='/HTMLArea-3.0-rc1/htmlarea.js'></script>
    <script type='text/javascript'>
      HTMLArea.loadPlugin('TableOperations');
    </script>
    <script type='text/javascript' src='/shared/js/htmlarea_extension.js'></script>

    <script type='text/javascript'>
      function toggle_richedit_textarea()
      {
        c = get_cookie('use_textarea_instead_of_richedit');

        if(c == 1)
          remove_cookie('use_textarea_instead_of_richedit');
        else
          set_cookie('use_textarea_instead_of_richedit', 1);
      }
    </script>";
  }

  public function init_richedit()
  {
    $this->_load_js_script();

    $id = $this->get_attribute('id');

    if (isset($_COOKIE['use_textarea_instead_of_richedit']) && $_COOKIE['use_textarea_instead_of_richedit'])
      $caption = strings :: get('use_richedit_instead_of_textarea', 'common');
    else
      $caption = strings :: get('use_textarea_instead_of_richedit', 'common');

    echo "<table cellpadding=0 cellspacing=0>
          <tr>
            <td><button id='{$id}_button' class='button' onclick='toggle_richedit_textarea();window.location.reload();return false;' style='display:none'>{$caption}</button></td>
            <td nowrap> " . strings :: get('richedit_textarea_warning', 'common') . "</td>
          </tr>
          </table>";

    if ($this->get_attribute('mode') == 'light')
      $init_function = 'install_limb_lite_extension(editor.config);';
    else
      $init_function = 'install_limb_full_extension(editor.config);editor.registerPlugin(TableOperations);';

    if(!$this->get_attribute('rows'))
      $this->set_attribute('rows', RICHEDIT_DEFAULT_ROWS);

    if(!$this->get_attribute('cols'))
      $this->set_attribute('cols', RICHEDIT_DEFAULT_COLS);

    if(!$width = $this->get_attribute('width'))
      $width = RICHEDIT_DEFAULT_WIDTH;

    if(!$height = $this->get_attribute('height'))
      $height = RICHEDIT_DEFAULT_HEIGHT;

    echo "
    <script type='text/javascript' defer='defer'>

      function init_richedit_{$id}()
      {
        var editor = new HTMLArea('{$id}');

        if(typeof(editor.config) == 'undefined')
          return;

        {$init_function}

        editor.config.width = '{$width}';
        editor.config.height = '{$height}';

        c = get_cookie('use_textarea_instead_of_richedit');

        if(c != 1)
          editor.generate();

        document.getElementById('{$id}_button').style.display = 'block';
      }

      add_event(window, 'load', init_richedit_{$id});

    </script>";
  }
}

?>
