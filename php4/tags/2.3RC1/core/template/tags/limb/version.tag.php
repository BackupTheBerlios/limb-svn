<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
class limb_version_tag_info
{
  var $tag = 'limb:VERSION';
  var $end_tag = ENDTAG_FORBIDDEN;
  var $tag_class = 'limb_version_tag';
}

register_tag(new limb_version_tag_info());

class limb_version_tag extends compiler_directive_tag
{
  function generate_contents(&$code)
  {
    include_once(LIMB_DIR . '/version.php');

    if(!isset($this->attributes['type']))
      $type = 'logo+text';
    else
      $type = $this->attributes['type'];

    switch($type)
    {
      case 'logo':
        $this->_start_href($code);

        $this->_write_logo($code);

        $this->_close_href($code);
      break;

      case 'text':
        $this->_start_href($code);

        $this->_write_text($code);

        $this->_close_href($code);
      break;

      case 'logo+text':
      default:
        $this->_start_href($code);

        $this->_write_text($code);
        $this->_write_logo($code);

        $this->_close_href($code);
      break;
    }
  }

  function _write_text(&$code)
  {
    $code->write_html(str_replace(' ', '&nbsp;', LIMB_FULL_NAME));
  }

  function _write_logo(&$code)
  {
    $code->write_html('<img align="center" border="0" src="' . LIMB_LOGO . '" alt="' . LIMB_FULL_NAME . '" title="' . LIMB_FULL_NAME . '">');
  }

  function _start_href(&$code)
  {
    if($this->_need_attach_home_page())
      $code->write_html('<a href="' . LIMB_HOME . '" target="_blank">');
  }

  function _close_href(&$code)
  {
    if($this->_need_attach_home_page())
      $code->write_html('</a>');
  }

  function _need_attach_home_page()
  {
    return (!isset($this->attributes['attach_home_page']) ||
            (isset($this->attributes['attach_home_page']) &&
             ($this->attributes['attach_home_page'] == 1 || $this->attributes['attach_home_page'] == "true")));
  }
}

?>