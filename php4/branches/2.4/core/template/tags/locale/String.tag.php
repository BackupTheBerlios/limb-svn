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
$taginfo =& new TagInfo('limb:locale:STRING', 'LimbLocaleStringTag');
$taginfo->setDefaultLocation(LOCATION_SERVER);
TagDictionary::registerTag($taginfo, __FILE__);

class LimbLocaleStringTag extends CompilerDirectiveTag
{
	function preParse() 
  {
		return PARSER_FORBID_PARSING;
	}

	function CheckNestingLevel() 
  {
		if ($this->findParentByClass('LimbLocaleStringTag')) 
      $this->raiseCompilerError('BADSELFNESTING');
	}
  
  function generateContents(&$code)
  {
    $code->registerInclude(LIMB_DIR . '/core/i18n/Strings.class.php');
    
    $file = 'common';

    if(!$file = $this->getAttribute('file'))
      $file = 'common';

    $locale = $this->_getLocale();
    
    if($text_node = $this->findChildByClass('TextNode'))
    {
      $content =& $text_node->contents; 
      
      $code->writePhp("echo strings :: get('{$content}', '{$file}', '$locale');");
      
    }
    elseif($name = $this->getAttribute('name'))
    {
       $code->writePhp("echo strings :: get('{$name}', '{$file}', '{$locale}');");
    }
    
    $this->removeChildren();
  }
  
  function _getLocale()
  {
    if($locale = $this->getAttribute('locale'))
      return $locale;
    
    if($locale_type = $this->getAttribute('locale_type'))
    {
      if(strtolower($locale_type) == 'management')
        $locale_constant = 'MANAGEMENT_LOCALE_ID';
      else
        $locale_constant = 'CONTENT_LOCALE_ID';
    }
    else
      $locale_constant = 'CONTENT_LOCALE_ID';
    
    return constant($locale_constant);
  }
  
	function removeChildren() 
  {
    $this->children = array();
	}  
}

?>