<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: LimbFormTagTest.class.php 1013 2005-01-12 12:13:22Z pachanga $
*
***********************************************************************************/
require_once(WACT_ROOT . '/template/template.inc.php');

class LimbStringTagTestCase extends LimbTestCase
{
  function LimbStringTagTestCase()
  {
    parent :: LimbTestCase('limb:locale:STRING tag case');
  }

  function tearDown()
  {
    ClearTestingTemplates();
  }

  function testDefaultFileUseChildContent()
  {
    $template = '<limb:locale:STRING>apply_filter</limb:locale:STRING>';

    RegisterTestingTemplate('/limb/locale_string_default.html', $template);

    $page =& new Template('/limb/locale_string_default.html');

    $this->assertEqual($page->capture(), 'Apply filter');
  }

  function testUseValueAttribute()
  {
    $template = '<limb:locale:STRING name="apply_filter"/>';

    RegisterTestingTemplate('/limb/locale_string_value.html', $template);

    $page =& new Template('/limb/locale_string_value.html');

    $this->assertEqual($page->capture(), 'Apply filter');
  }
  
  function testDefinedLocale()
  {
    $template = '<limb:locale:STRING name="apply_filter" file="test" locale="de"/>';

    RegisterTestingTemplate('/limb/locale_string_locale.html', $template);

    $page =& new Template('/limb/locale_string_locale.html');

    $this->assertEqual($page->capture(), 'Apply filter german');
  }
  
  function testFile()
  {
    $template = '<limb:locale:STRING file="error" name="cant_be_deleted"/>';

    RegisterTestingTemplate('/limb/locale_string_file.html', $template);

    $page =& new Template('/limb/locale_string_file.html');

    $this->assertEqual($page->capture(), 'Object can\'t be deleted');
  }
  
  // I can't test locale_type attribute since CONTENT_LOCALE_ID and MANAGEMENT_LOCALE_ID 
  // are equal in test environment
}
?>
