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
require_once(LIMB_DIR . '/core/template/compiler/CompilerDirectiveTag.class.php');

class LocaleStringTagInfo
{
  var $tag = 'locale:STRING';
  var $end_tag = ENDTAG_FORBIDDEN;
  var $tag_class = 'locale_string_tag';
}

registerTag(new LocaleStringTagInfo());

class LocaleStringTag extends CompilerDirectiveTag
{
  function generateContents($code)
  {
    $file = 'common';

    if(isset($this->attributes['file']))
      $file = $this->attributes['file'];

    if(isset($this->attributes['locale_type']))
    {
      if(strtolower($this->attributes['locale_type']) == 'content')
        $locale_constant = 'CONTENT_LOCALE_ID';
      else
        $locale_constant = 'MANAGEMENT_LOCALE_ID';
    }
    else
      $locale_constant = 'MANAGEMENT_LOCALE_ID';

    if(isset($this->attributes['hash_id']))
    {
      $locale_tmp = '$' . $code->getTempVariable();

      $code->writePhp(
        "{$locale_tmp} = " . $this->getDataspaceRefCode() . '->get("' . $this->attributes['hash_id'] . '");');

      if(defined('DEBUG_TEMPLATE_I18N_ENABLED') &&  constant('DEBUG_TEMPLATE_I18N_ENABLED'))
      {
        $code->writePhp("
          echo '<img src=\'/shared/images/i.gif\' title=\'&#039;{$locale_tmp}&#039; from &#039;{$file}_???&#039; i18n file\'>';"
        );
      }

      $code->writePhp("echo strings :: get({$locale_tmp}, '{$file}', constant('{$locale_constant}'));");

    }
    elseif(isset($this->attributes['name']))
    {
      if(defined('DEBUG_TEMPLATE_I18N_ENABLED') &&  constant('DEBUG_TEMPLATE_I18N_ENABLED'))
      {
        $code->writePhp("
          echo '<img src=\'/shared/images/i.gif\' title=\'&#039;{$this->attributes['name']}&#039; from &#039;{$file}_???&#039; i18n file\'>';"
        );
      }

      $code->writePhp("echo strings :: get('{$this->attributes['name']}', '{$file}', constant('{$locale_constant}'));");
    }

    parent :: generateContents($code);
  }
}

?>