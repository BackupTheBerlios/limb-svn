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

class LimbLocaleNumberFilterTestCase extends LimbTestCase
{
  function LimbLocaleNumberFilterTestCase()
  {
    parent :: LimbTestCase('limb:locale:NUMBER filter case');
  }

  function tearDown()
  {
    ClearTestingTemplates();
  }

  function testUseDefault()
  {
    $template = '{$"100000"|LimbI18NNumber}';

    RegisterTestingTemplate('/limb/locale_number_filter_default.html', $template);

    $page =& new Template('/limb/locale_number_filter_default.html');

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

    $template = '{$"100000"|LimbI18NNumber:"","ru"}';

    RegisterTestingTemplate('/limb/locale_number_filter_russian.html', $template);

    $page =& new Template('/limb/locale_number_filter_russian.html');

    $this->assertEqual($page->capture(), '100,000.0000');

    $toolkit->tally();
    Limb :: popToolkit();
  }

  function testUseFractDigits()
  {
    $template = '{$"100000"|LimbI18NNumber:"","en","3"}';

    RegisterTestingTemplate('/limb/locale_number_filter_fract_digits.html', $template);

    $page =& new Template('/limb/locale_number_filter_fract_digits.html');

    $this->assertEqual($page->capture(), '100,000.000');
  }

  function testUseDecimalSymbol()
  {
    $template = '{$"100000"|LimbI18NNumber:"","en","",","}';

    RegisterTestingTemplate('/limb/locale_number_filter_decimal_symbol.html', $template);

    $page =& new Template('/limb/locale_number_filter_decimal_symbol.html');

    $this->assertEqual($page->capture(), '100,000,00');
  }

  function testUseThousandSeparator()
  {
    $template = '{$"100000"|LimbI18NNumber:"","en","",""," "}';

    RegisterTestingTemplate('/limb/locale_number_filter_thousand_separator.html', $template);

    $page =& new Template('/limb/locale_number_filter_thousand_separator.html');

    $this->assertEqual($page->capture(), '100 000.00');
  }

  // I can't test locale_type attribute since CONTENT_LOCALE_ID and MANAGEMENT_LOCALE_ID
  // are equal in test environment
}
?>
