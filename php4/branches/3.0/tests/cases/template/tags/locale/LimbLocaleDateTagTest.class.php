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
require_once(LIMB_DIR . '/core/date/Date.class.php');

Mock :: generate('LimbToolkit');

class LimbLocaleDateTagTestCase extends LimbTestCase
{
  function LimbLocaleDateTagTestCase()
  {
    parent :: LimbTestCase('limb:locale:DATE tag case');
  }

  function tearDown()
  {
    ClearTestingTemplates();
  }

  function testUseChildContent()
  {
    $template = '<limb:locale:DATE locale="en">2002-02-20</limb:locale:DATE>';

    RegisterTestingTemplate('/limb/locale_date_default.html', $template);

    $page =& new Template('/limb/locale_date_default.html');

    $this->assertEqual($page->capture(), '02/20/2002');
  }

  function testUseRuntimeChildValue()
  {
    $template = '<limb:locale:DATE locale="en">{$var}</limb:locale:DATE>';

    RegisterTestingTemplate('/limb/locale_date_value_at_runtime.html', $template);

    $page =& new Template('/limb/locale_date_value_at_runtime.html');

    $page->set('var', '2002-02-20');

    $this->assertEqual($page->capture(), '02/20/2002');
  }

  function testStampValue()
  {
    $date = Date ::create($year = 2004, $month= 12, $day=20, $hour=10, $minute=15, $second=30);

    $template = '<limb:locale:DATE locale="en" date_type="stamp">'.
                $date->getStamp().
                '</limb:locale:DATE>';

    RegisterTestingTemplate('/limb/locale_date_stamp.html', $template);

    $page =& new Template('/limb/locale_date_stamp.html');

    $this->assertEqual($page->capture(), '12/20/2004');
  }

  function testFormatType()
  {
    $date = Date ::create($year = 2005, $month = 1, $day=20, $hour=10, $minute=15, $second=30);

    $template = '<limb:locale:DATE locale="en" date_type="stamp" format_type="date">'.
                $date->getStamp().
                '</limb:locale:DATE>';

    RegisterTestingTemplate('/limb/locale_date_format_type.html', $template);

    $page =& new Template('/limb/locale_date_format_type.html');

    $this->assertEqual($page->capture(), 'Thursday 20 January 2005');
  }

  function testDefinedFormat()
  {
    $date = Date ::create($year = 2005, $month = 1, $day=20, $hour=10, $minute=15, $second=30);

    $template = '<limb:locale:DATE locale="en" date_type="stamp" format="%Y %m %d">'.
                $date->getStamp().
                '</limb:locale:DATE>';

    RegisterTestingTemplate('/limb/locale_date_defined_format.html', $template);

    $page =& new Template('/limb/locale_date_defined_format.html');

    $this->assertEqual($page->capture(), '2005 01 20');
  }

  function testLocalizedFormatedValue()
  {
    $template = '<limb:locale:DATE locale="en" date_format="%A %d %B %Y" date_type="localized_string" format="%Y %m %d">'.
                'Thursday 20 January 2005'.
                '</limb:locale:DATE>';

    RegisterTestingTemplate('/limb/locale_date_localized_formated_value.html', $template);

    $page =& new Template('/limb/locale_date_localized_formated_value.html');

    $this->assertEqual($page->capture(), '2005 01 20');
  }

  function testTranslateFromOneLocaleToAnother()
  {
    $template = '<limb:locale:DATE date_locale="fr" locale="en" date_format="%A %d %B %Y" date_type="localized_string" format_type="date">'.
                'Jeudi 20 Janvier 2005'.
                '</limb:locale:DATE>';

    RegisterTestingTemplate('/limb/locale_date_transfer_between_locales.html', $template);

    $page =& new Template('/limb/locale_date_transfer_between_locales.html');

    $this->assertEqual($page->capture(), 'Thursday 20 January 2005');
  }

  // I can't test locale_type attribute since CONTENT_LOCALE_ID and MANAGEMENT_LOCALE_ID
  // are equal in test environment
}
?>
