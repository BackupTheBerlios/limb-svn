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

class LimbStringFilterTestCase extends LimbTestCase
{
  function LimbStringFilterTestCase()
  {
    parent :: LimbTestCase('LimbI18NString filter case');
  }

  function tearDown()
  {
    ClearTestingTemplates();
  }

  function testDefault()
  {
    $template = '{$"apply_filter"|LimbI18NString}';

    RegisterTestingTemplate('/limb/locale_string_filter_default.html', $template);

    $page =& new Template('/limb/locale_string_filter_default.html');

    $this->assertEqual($page->capture(), 'Apply filter');
  }

  function testFile()
  {
    $template = '{$"error_float"|LimbI18NString:"error", "",""}';

    RegisterTestingTemplate('/limb/locale_string_filter_file.html', $template);

    $page =& new Template('/limb/locale_string_filter_file.html');

    $this->assertEqual($page->capture(), 'Only float type data allowed');
  }

  function testDefinedLocale()
  {
    $template = '{$"apply_filter"|LimbI18NString:"test","","de"}';

    RegisterTestingTemplate('/limb/locale_string_filter_locale.html', $template);

    $page =& new Template('/limb/locale_string_filter_locale.html');

    $this->assertEqual($page->capture(), 'Apply filter german');
  }

  function testDefaultDBE()
  {
    $template = '{$var|LimbI18NString}';

    RegisterTestingTemplate('/limb/locale_string_filter_dbe.html', $template);

    $page =& new Template('/limb/locale_string_filter_dbe.html');

    $page->set('var', 'apply_filter');

    $this->assertEqual($page->capture(), 'Apply filter');
  }

  function testFileDBE()
  {
    $template = '{$var|LimbI18NString:"error"}';

    RegisterTestingTemplate('/limb/locale_string_filter_file_dbe.html', $template);

    $page =& new Template('/limb/locale_string_filter_file_dbe.html');

    $page->set('var', 'error_float');

    $this->assertEqual($page->capture(), 'Only float type data allowed');
  }

  function testLocaleDBE()
  {
    $template = '{$var|LimbI18NString:"test", "", "de"}';

    RegisterTestingTemplate('/limb/locale_string_filter_locale_dbe.html', $template);

    $page =& new Template('/limb/locale_string_filter_locale_dbe.html');

    $page->set('var', 'apply_filter');

    $this->assertEqual($page->capture(), 'Apply filter german');
  }

  function testDefaultDBEForAttribute()
  {
    $template = '<form id="test_form" name="test_form" runat="server">'.
                '<input id="test_input" type="text" value="{$^var|LimbI18NString|uppercase}">' .
                '</form>';

    RegisterTestingTemplate('/limb/locale_string_filter_dbe_for_attribute.html', $template);

    $page =& new Template('/limb/locale_string_filter_dbe_for_attribute.html');

    $page->set('var', 'apply_filter');

    $expected = '<form id="test_form" name="test_form">'. //please note the second value attribute!
                '<input id="test_input" type="text" name="test_input" value="" value="APPLY FILTER">' .
                '</form>';

    $this->assertEqual($page->capture(), $expected);
  }

  // I can't test locale_type attribute since CONTENT_LOCALE_ID and MANAGEMENT_LOCALE_ID
  // are equal in test environment
}
?>
