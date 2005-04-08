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
require_once(WACT_ROOT . '/template/template.inc.php');

class LimbLocaleTagTestCase extends LimbTestCase
{
  function LimbLocaleTagTestCase()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function tearDown()
  {
    ClearTestingTemplates();
  }

  function testUsingContentLocaleIdByDefault()
  {
    $template = '<limb:LOCALE name="' . CONTENT_LOCALE_ID . '">Some text</limb:LOCALE>' .
                '<limb:LOCALE name="no_such_locale">Other text</limb:LOCALE>';

    RegisterTestingTemplate('/limb/locale_default.html', $template);

    $page =& new Template('/limb/locale_default.html');

    $this->assertEqual($page->capture(), 'Some text');
  }

  // This test is not good since CONTENT_LOCALE_ID and MANAGEMENT_LOCALE_ID are equal in test environment
  function testUsingManagementLocaleIdByDefault()
  {
    $template = '<limb:LOCALE name="no_such_locale" locale_type="management">Some text</limb:LOCALE>' .
                '<limb:LOCALE name="' . MANAGEMENT_LOCALE_ID . '" locale_type="management">Other text</limb:LOCALE>';

    RegisterTestingTemplate('/limb/locale_management.html', $template);

    $page =& new Template('/limb/locale_management.html');

    $this->assertEqual($page->capture(), 'Other text');
  }
}
?>
