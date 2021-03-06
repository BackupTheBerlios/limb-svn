<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/model/stats/search_engine_rules/search_engine_mailru_rule.class.php');

class search_engine_mailru_rule_test extends LimbTestCase
{
  var $rule = null;

  function search_engine_mailru_rule_test()
  {
    parent :: LimbTestCase();
  }

  function setUp()
  {
    $this->rule = new search_engine_mailru_rule();
  }

  function test_name()
  {
    $this->assertEqual('mail.ru', $this->rule->get_engine_name());
  }

  function test_match_ru_true()
  {
    $this->assertTrue($this->rule->match(urldecode('http://go.mail.ru/?qs=1&lfilter=yes&words=%EF%F0%E8%E2%E5%F2&change=2')));
    $this->assertEqual('������', $this->rule->get_matching_phrase());
  }

  function test_second_match_ru_true()
  {
    $this->assertTrue($this->rule->match(urldecode('http://go.mail.ru/index.phtml?lfilter=yes&hl=ru&q=%F1%E8%F1%F2%E5%EC%E0+LIMB+CMS&change=2')));
    $this->assertEqual('������� LIMB CMS', $this->rule->get_matching_phrase());
  }

  function test_match_eng_true()
  {
    $this->assertTrue($this->rule->match(urldecode('http://go.mail.ru/?qs=1&lfilter=yes&words=wow&change=2')));
    $this->assertEqual('wow', $this->rule->get_matching_phrase());
  }

  function test_second_match_eng_true()
  {
    $this->assertTrue($this->rule->match(urldecode('http://go.mail.ru/index.phtml?lfilter=yes&hl=ru&q=wow&change=2')));
    $this->assertEqual('wow', $this->rule->get_matching_phrase());
  }
}

?>