<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: string.tag.php 916 2004-11-23 09:14:28Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/template/compiler/compiler_directive_tag.class.php');

class locale_currency_tag_info
{
  var $tag = 'locale:CURRENCY';
  var $end_tag = ENDTAG_FORBIDDEN;
  var $tag_class = 'locale_currency_tag';
}

register_tag(new locale_currency_tag_info());

class locale_currency_tag extends compiler_directive_tag
{
  function pre_generate(&$code)
  {
    $code->register_include(LIMB_DIR . '/core/lib/i18n/currency.class.php');
  }
  
  function generate_contents(&$code)
  {
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
      $locale_tmp = '$' . $code->get_temp_variable();

      $code->write_php(
        "{$locale_tmp} = " . $this->get_dataspace_ref_code() . '->get("' . $this->attributes['hash_id'] . '");');

      $code->write_php("echo currency :: locale_format({$locale_tmp}, constant('{$locale_constant}'));");
    }

    parent :: generate_contents($code);
  }


}

?>