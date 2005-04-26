<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: version.tag.php 916 2004-11-23 09:14:28Z pachanga $
*
***********************************************************************************/
$taginfo =& new TagInfo('limb:VERSION', 'LimbVersionTag');
$taginfo->setEndTag(ENDTAG_FORBIDDEN);
TagDictionary::registerTag($taginfo, __FILE__);

class LimbVersionTag extends CompilerDirectiveTag
{
  function generateContents(&$code)
  {
    include_once(LIMB_DIR . '/version.php');

    if(!$this->hasAttribute('type'))
      $type = 'logo+text';
    else
      $type = $this->getAttribute('type');

    switch($type)
    {
      case 'logo':
        $this->_startHref($code);
        $this->_writeLogo($code);
        $this->_closeHref($code);
      break;

      case 'text':
        $this->_startHref($code);
        $this->_writeText($code);
        $this->_closeHref($code);
      break;

      case 'logo+text':
      default:
        $this->_startHref($code);
        $this->_writeText($code);
        $this->_writeLogo($code);
        $this->_closeHref($code);
      break;
    }
  }

  function _writeText(&$code)
  {
    $code->writeHtml(str_replace(' ', '&nbsp;', LIMB_FULL_NAME));
  }

  function _writeLogo(&$code)
  {
    $code->writeHtml('<img align="center" border="0" src="' . LIMB_LOGO . '" alt="' . LIMB_FULL_NAME . '" title="' . LIMB_FULL_NAME . '">');
  }

  function _startHref(&$code)
  {
    if($this->_needAttachHomePage())
      $code->writeHtml('<a href="' . LIMB_HOME . '" target="_blank">');
  }

  function _closeHref(&$code)
  {
    if($this->_needAttachHomePage())
      $code->writeHtml('</a>');
  }

  function _needAttachHomePage()
  {
    return $this->getBoolAttribute('dont_attach_home_page') == true;
  }
}

?>