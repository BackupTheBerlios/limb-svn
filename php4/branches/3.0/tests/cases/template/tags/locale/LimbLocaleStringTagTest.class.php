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

  function testDefaultFileUseChildContentAtRuntime()
  {
    $template = '<limb:locale:STRING>{$var}</limb:locale:STRING>';

    RegisterTestingTemplate('/limb/locale_string_default_at_runtime.html', $template);

    $page =& new Template('/limb/locale_string_default_at_runtime.html');
    $page->set('var', 'apply_filter');

    $this->assertEqual($page->capture(), 'Apply filter');
  }

  function testDefinedLocale()
  {
    $template = '<limb:locale:STRING file="test" locale="de">apply_filter</limb:locale:STRING>';

    RegisterTestingTemplate('/limb/locale_string_locale.html', $template);

    $page =& new Template('/limb/locale_string_locale.html');

    $this->assertEqual($page->capture(), 'Apply filter german');
  }

  function testFile()
  {
    $template = '<limb:locale:STRING file="error">cant_be_deleted</limb:locale:STRING>';

    RegisterTestingTemplate('/limb/locale_string_file.html', $template);

    $page =& new Template('/limb/locale_string_file.html');

    $this->assertEqual($page->capture(), 'Object can\'t be deleted');
  }

  // I can't test locale_type attribute since CONTENT_LOCALE_ID and MANAGEMENT_LOCALE_ID
  // are equal in test environment
}
?>
