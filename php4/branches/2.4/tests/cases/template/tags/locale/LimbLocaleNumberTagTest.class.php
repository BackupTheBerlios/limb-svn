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
require_once(LIMB_DIR . '/core/LimbToolkit.interface.php');

Mock :: generate('LimbToolkit');

class LimbLocaleNumberTagTestCase extends LimbTestCase
{
  function LimbLocaleNumberTagTestCase()
  {
    parent :: LimbTestCase('limb:locale:NUMBER tag case');
  }

  function tearDown()
  {
    ClearTestingTemplates();
  }

  function testUseChildContent()
  {
    $template = '<limb:locale:NUMBER locale="en">100000</limb:locale:NUMBER>';

    RegisterTestingTemplate('/limb/locale_number_default.html', $template);

    $page =& new Template('/limb/locale_number_default.html');

    $this->assertEqual($page->capture(), '100,000.00');
  }

  function testUseOtherLocale()
  {
    $toolkit =& new MockLimbToolkit($this);
    
    $real_toolkit = Limb :: toolkit();
    $locale = $real_toolkit->getLocale('ru');
    $locale->fract_digits = 4;
    
    $toolkit->setReturnReference('getLocale', $locale, array('ru'));

    Limb :: registerToolkit($toolkit);
    
    $template = '<limb:locale:NUMBER locale="ru">100000</limb:locale:NUMBER>';

    RegisterTestingTemplate('/limb/locale_number_russian.html', $template);

    $page =& new Template('/limb/locale_number_russian.html');

    $this->assertEqual($page->capture(), '100,000.0000');

    $toolkit->tally();
    Limb :: popToolkit();
  }
  
  function testUseValue()
  {
    $template = '<limb:locale:NUMBER locale="en" value="100000"/>';

    RegisterTestingTemplate('/limb/locale_number_value.html', $template);

    $page =& new Template('/limb/locale_number_value.html');

    $this->assertEqual($page->capture(), '100,000.00');
  }

  function testUseFractDigits()
  {
    $template = '<limb:locale:NUMBER locale="en" value="100000" fract_digits="3"/>';

    RegisterTestingTemplate('/limb/locale_number_fract_digits.html', $template);

    $page =& new Template('/limb/locale_number_fract_digits.html');

    $this->assertEqual($page->capture(), '100,000.000');
  }

  function testUseDecimalSymbol()
  {
    $template = '<limb:locale:NUMBER locale="en" value="100000" decimal_symbol=","/>';

    RegisterTestingTemplate('/limb/locale_number_decimal_symbol.html', $template);

    $page =& new Template('/limb/locale_number_decimal_symbol.html');

    $this->assertEqual($page->capture(), '100,000,00');
  }

  function testUseThousandSeparator()
  {
    $template = '<limb:locale:NUMBER locale="en" value="100000" thousand_separator=" "/>';

    RegisterTestingTemplate('/limb/locale_number_thousand_separator.html', $template);

    $page =& new Template('/limb/locale_number_thousand_separator.html');

    $this->assertEqual($page->capture(), '100 000.00');
  }
  
  // I can't test locale_type attribute since CONTENT_LOCALE_ID and MANAGEMENT_LOCALE_ID 
  // are equal in test environment
}
?>
