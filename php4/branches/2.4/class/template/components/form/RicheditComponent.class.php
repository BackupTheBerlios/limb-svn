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
require_once(LIMB_DIR . '/class/template/components/form/TextAreaComponent.class.php');

if(!defined('RICHEDIT_DEFAULT_WIDTH'))
  define('RICHEDIT_DEFAULT_WIDTH', '600px');
if(!defined('RICHEDIT_DEFAULT_HEIGHT'))
  define('RICHEDIT_DEFAULT_HEIGHT', '400px');
if(!defined('RICHEDIT_DEFAULT_ROWS'))
  define('RICHEDIT_DEFAULT_ROWS', '30');
if(!defined('RICHEDIT_DEFAULT_COLS'))
  define('RICHEDIT_DEFAULT_COLS', '60');

class RicheditComponent extends TextAreaComponent
{
  function renderContents()
  {
    echo htmlspecialchars($this->getValue(), ENT_QUOTES);
  }

  function _loadJsScript()
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

  function initRichedit()
  {
    $this->_loadJsScript();

    $id = $this->getAttribute('id');

    if (isset($_COOKIE['use_textarea_instead_of_richedit']) &&  $_COOKIE['use_textarea_instead_of_richedit'])
      $caption = Strings :: get('use_richedit_instead_of_textarea', 'common');
    else
      $caption = Strings :: get('use_textarea_instead_of_richedit', 'common');

    echo "<table cellpadding=0 cellspacing=0>
          <tr>
            <td><button id='{$id}_button' class='button' onclick='toggle_richedit_textarea();window.location.reload();return false;' style='display:none'>{$caption}</button></td>
            <td nowrap> " . Strings :: get('richedit_textarea_warning', 'common') . "</td>
          </tr>
          </table>";

    if ($this->getAttribute('mode') == 'light')
      $init_function = 'install_limb_lite_extension(editor.config);';
    else
      $init_function = 'install_limb_full_extension(editor.config);editor.registerPlugin(TableOperations);';

    if(!$this->getAttribute('rows'))
      $this->setAttribute('rows', RICHEDIT_DEFAULT_ROWS);

    if(!$this->getAttribute('cols'))
      $this->setAttribute('cols', RICHEDIT_DEFAULT_COLS);

    if(!$width = $this->getAttribute('width'))
      $width = RICHEDIT_DEFAULT_WIDTH;

    if(!$height = $this->getAttribute('height'))
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

      addEvent(window, 'load', init_richedit_{$id});

    </script>";
  }
}

?>
