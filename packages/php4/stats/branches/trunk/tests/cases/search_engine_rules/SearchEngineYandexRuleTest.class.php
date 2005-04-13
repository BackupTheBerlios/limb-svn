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
require_once(dirname(__FILE__) . '/../../../search_engine_rules/SearchEngineYandexRule.class.php');

class SearchEngineYandexRuleTest extends LimbTestCase
{
  var $rule = null;

  function searchEngineYandexRuleTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->rule = new SearchEngineYandexRule();
  }

  function testName()
  {
    $this->assertEqual('yandex', $this->rule->getEngineName());
  }

  function testMatchRuTrue()
  {
    $this->assertTrue($this->rule->match(urldecode('http://www.yandex.ru/yandsearch?text=%EF%F0%E8%E2%E5%F2&stype=www')));
    $this->assertEqual('привет', $this->rule->getMatchingPhrase());
  }

  function testMatchHighliteRuTrue()
  {
    $this->assertTrue($this->rule->match(urldecode('http://hghltd.yandex.ru/yandbtm?url=http://limb-project.com/root/ru/portfolio/gallery/565%3FPHPSESSID%3Df13597251e617971f3710bbca63a96bf&text=%E1%FE%F0%EE+%E8%ED%F4%EE%F0%EC%E0%F6%E8%EE%ED%ED%FB%F5+%F2%E5%F5%ED%EE%EB%EE%E3%E8%E9&dsn=39&d=128984')));
    $this->assertEqual('бюро информационных технологий', $this->rule->getMatchingPhrase());
  }

  function testYandpageMatchRuTrue()
  {
    $this->assertTrue($this->rule->match(urldecode('http://www.yandex.ru/yandpage?q=1051481360&p=1&ag=d&qs=%26text%3D%25D0%25D2%25C9%25D7%25C5%25D4')));
    $this->assertEqual('привет', $this->rule->getMatchingPhrase());
  }

  function testMatchEngTrue()
  {
    $this->assertTrue($this->rule->match(urldecode('http://www.yandex.ru/yandsearch?text=wow&stype=www')));
    $this->assertEqual('wow', $this->rule->getMatchingPhrase());
  }

  function testYandpageMatchEngTrue()
  {
    $this->assertTrue($this->rule->match(urldecode('http://www.yandex.ru/yandpage?q=1164101856&p=3&ag=d&qs=%26text%3Dwow')));
    $this->assertEqual('wow', $this->rule->getMatchingPhrase());
  }

}

?>