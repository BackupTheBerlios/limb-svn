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
require_once(LIMB_DIR . '/core/lib/http/uri.class.php');

class uri_test extends LimbTestCase
{
  var $uri;

  function setUp()
  {
    $this->uri =& new uri();
  }

  function test_parse()
  {
    $url = 'http://admin:test@localhost:81/test.php/test?foo=bar#23';

    $this->uri->parse($url);

    $this->assertEqual($this->uri->get_protocol(), 'http');
    $this->assertEqual($this->uri->get_host(), 'localhost');
    $this->assertEqual($this->uri->get_user(), 'admin');
    $this->assertEqual($this->uri->get_password(), 'test');
    $this->assertEqual($this->uri->get_port(), '81');
    $this->assertEqual($this->uri->get_anchor(), '23');

    $this->assertEqual($this->uri->get_query_item('foo'), 'bar');
    $this->assertEqual($this->uri->count_query_items(), 1);

    $this->assertEqual($this->uri->get_path(), '/test.php/test');
    $this->assertEqual($this->uri->count_path(), 3);
    $this->assertEqual($this->uri->get_path_elements(), array('', 'test.php', 'test'));
    $this->assertEqual($this->uri->get_path_element(0), '');
    $this->assertEqual($this->uri->get_path_element(1), 'test.php');
    $this->assertEqual($this->uri->get_path_element(2), 'test');
  }

  function test_to_string_default()
  {
    $url = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $this->uri->parse($url);

    $this->assertEqual($this->uri->to_string(), $url);
  }

  function test_to_string_query_items_sort()
  {
    $url = 'http://localhost/test.php?b=1&a=2&c[1]=456';
    $expected_url = 'http://localhost/test.php?a=2&b=1&c[1]=456';

    $this->uri->parse($url);

    $this->assertEqual($this->uri->to_string(), $expected_url);
  }

  function test_to_string_no_protocol()
  {
    $url = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $this->uri->parse($url);

    $this->assertEqual(
      $this->uri->to_string(array('user', 'password', 'host', 'port', 'path', 'query', 'anchor')),
      'admin:test@localhost:81/test.php?foo=bar#23'
    );
  }

  function test_to_string_no_user()
  {
    $url = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $this->uri->parse($url);

    $this->assertEqual(
      $this->uri->to_string(array('protocol', 'password', 'host', 'port', 'path', 'query', 'anchor')),
      'http://localhost:81/test.php?foo=bar#23'
    );
  }

  function test_to_string_no_password()
  {
    $url = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $this->uri->parse($url);

    $this->assertEqual(
      $this->uri->to_string(array('protocol', 'user', 'host', 'port', 'path', 'query', 'anchor')),
      'http://admin@localhost:81/test.php?foo=bar#23'
    );
  }

  function test_to_string_no_host()
  {
    $url = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $this->uri->parse($url);

    $this->assertEqual(
      $this->uri->to_string(array('protocol', 'user', 'password', 'port', 'path', 'query', 'anchor')),
      '/test.php?foo=bar#23'
    );
  }

  function test_to_string_no_path()
  {
    $url = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $this->uri->parse($url);

    $this->assertEqual(
      $this->uri->to_string(array('protocol', 'user', 'password', 'host', 'port', 'query', 'anchor')),
      'http://admin:test@localhost:81?foo=bar#23'
    );
  }

  function test_to_string_no_query()
  {
    $url = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $this->uri->parse($url);

    $this->assertEqual(
      $this->uri->to_string(array('protocol', 'user', 'password', 'host', 'port', 'path', 'anchor')),
      'http://admin:test@localhost:81/test.php#23'
    );
  }

  function test_to_string_no_anchor()
  {
    $url = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $this->uri->parse($url);

    $this->assertEqual(
      $this->uri->to_string(array('protocol', 'user', 'password', 'host', 'port', 'path')),
      'http://admin:test@localhost:81/test.php'
    );
  }

  function test_set_query_string()
  {
    $url = 'http://localhost';

    $this->uri->parse($url);

    $this->uri->set_query_string('foo=bar&bar=foo');

    $this->assertEqual($this->uri->count_query_items(), 2);
    $this->assertEqual($this->uri->get_query_item('foo'), 'bar');
    $this->assertEqual($this->uri->get_query_item('bar'), 'foo');
  }

  function test_set_query_string2()
  {
    $url = 'http://localhost';

    $this->uri->parse($url);
    $this->uri->set_query_string('foo[i1]=1&foo[i2]=2');

    $this->assertEqual($this->uri->count_query_items(), 1);
    $this->assertEqual($this->uri->get_query_item('foo'), array('i1' => '1', 'i2' => '2'));
  }

  function test_normalize_path()
  {
    $this->uri->parse('/foo/bar/../boo.php');
    $this->uri->normalize_path();
    $this->assertEqual($this->uri, new uri('/foo/boo.php'));

    $this->uri->parse('/foo/bar/../../boo.php');
    $this->uri->normalize_path();
    $this->assertEqual($this->uri, new uri('/boo.php'));

    $this->uri->parse('/foo/bar/../boo.php');
    $this->uri->normalize_path();
    $this->assertEqual($this->uri, new uri('/foo/boo.php'));
  }

  function test_add_query_item()
  {
    $url = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $this->uri->parse($url);

    $this->uri->add_query_item('bar', 'foo');
    $this->assertEqual($this->uri->get_query_string(), 'bar=foo&foo=bar');
  }

  function test_add_query_item2()
  {
    $url = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $this->uri->parse($url);

    $this->uri->add_query_item('foo', 'foo');
    $this->assertEqual($this->uri->get_query_string(), 'foo=foo');
  }

  function test_add_query_item3()
  {
    $url = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $this->uri->parse($url);

    $this->uri->add_query_item('foo', array('i1' => 'bar'));
    $this->uri->add_query_item('bar', 1);
    $this->assertEqual($this->uri->get_query_string(), 'bar=1&foo[i1]=bar');
  }

  function test_add_query_item4()
  {
    $url = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $this->uri->parse($url);

    $this->uri->add_query_item('foo', array('i1' => array('i2' => 'bar')));
    $this->uri->add_query_item('bar', 1);
    $this->assertEqual($this->uri->get_query_string(), 'bar=1&foo[i1][i2]=bar');
  }

  function test_add_query_item_urlencode()
  {
    $url = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $this->uri->parse($url);

    $this->uri->add_query_item('foo', ' foo ');
    $this->assertEqual($this->uri->get_query_string(), 'foo=+foo+');
  }

  function test_add_query_item_urlencode2()
  {
    $url = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $this->uri->parse($url);

    $this->uri->add_query_item('foo', array('i1' => ' bar '));
    $this->assertEqual($this->uri->get_query_string(), 'foo[i1]=+bar+');
  }

  function test_parse_default_80_port()
  {
    $url = 'http://admin:test@localhost/test.php?foo=bar#23';

    $this->uri->parse($url);

    $this->assertEqual($this->uri->get_port(), '80');
  }

  function test_compare_query_equal()
  {
    $url = 'http://admin:test@localhost2:81/test.php?bar=foo&foo=bar#23';

    $this->uri->parse($url);

    $this->assertTrue($this->uri->compare_query(
      new uri('http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23')
     ));
  }

  function test_compare_query_not_equal()
  {
    $url = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $this->uri->parse($url);

    $this->assertFalse($this->uri->compare_query(
      new uri('http://admin:test@localhost:81/test.php?bar=foo&foo=bar2#23')
     ));
  }

  function test_compare_query_not_equal2()
  {
    $url = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $this->uri->parse($url);

    $this->assertFalse($this->uri->compare_query(
      new uri('http://admin:test@localhost:81/test.php?bar=foo#23')
     ));
  }

  function test_compare_identical()
  {
    $url = 'http://admin:test@localhost:81/test.php?foo=bar#23';

    $this->uri->parse($url);

    $this->assertTrue($this->uri->compare(
      new uri('http://admin:test@localhost:81/test.php?foo=bar#23')));
  }

  function test_compare_equal()
  {
    $url = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $this->uri->parse($url);

    $this->assertTrue($this->uri->compare(
      new uri('http://admin:test@localhost:81/test.php?foo=bar&bar=foo#23')));
  }

  function test_compare_equal2()
  {
    $url = 'http://admin:test@localhost:81?';

    $this->uri->parse($url);

    $this->assertTrue($this->uri->compare(
      new uri('http://admin:test@localhost:81')
     ));
  }

  function test_compare_not_equal_schema()
  {
    $url = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $this->uri->parse($url);

    $this->assertFalse($this->uri->compare(
      new uri('https://admin:test@localhost:81/test.php?bar=foo&foo=bar#23')
     ));
  }

  function test_compare_not_equal_user()
  {
    $url = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $this->uri->parse($url);

    $this->assertFalse($this->uri->compare(
      new uri('http://admin1:test@localhost:81/test.php?bar=foo&foo=bar#23')
     ));
  }

  function test_compare_not_equal_password()
  {
    $url = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $this->uri->parse($url);

    $this->assertFalse($this->uri->compare(
      new uri('http://admin:test1@localhost:81/test.php?bar=foo&foo=bar#23')
     ));
  }

  function test_compare_not_equal_host()
  {
    $url = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $this->uri->parse($url);

    $this->assertFalse($this->uri->compare(
      new uri('http://admin:test@localhost1:81/test.php?bar=foo&foo=bar#23')
     ));
  }

  function test_compare_not_equal_port()
  {
    $url = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $this->uri->parse($url);

    $this->assertFalse($this->uri->compare(
      new uri('http://admin:test@localhost/test.php?bar=foo&foo=bar#23')
     ));
  }

  function test_compare_not_equal_path()
  {
    $url = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $this->uri->parse($url);

    $this->assertFalse($this->uri->compare(
      new uri('http://admin:test@localhost:81/test.php/test?bar=foo&foo=bar#23')
     ));
  }

  function test_compare_not_equal_path2()
  {
    $url = 'http://admin:test@localhost:81/test.php/test?bar=foo&foo=bar#23';

    $this->uri->parse($url);

    $this->assertFalse($this->uri->compare(
      new uri('http://admin:test@localhost:81/test.php/test1?bar=foo&foo=bar#23')
     ));
  }

  function test_compare_anchor_doesnt_matter()
  {
    $url = 'http://admin:test@localhost:81/test.php?bar=foo&foo=bar#23';

    $this->uri->parse($url);

    $this->assertTrue($this->uri->compare(
      new uri('http://admin:test@localhost:81/test.php?bar=foo&foo=bar#32')
     ));
  }

  function test_compare_path_equal()
  {
    $url = 'http://localhost/test.php/test';

    $this->uri->parse($url);

    $this->assertEqual(0,
      $this->uri->compare_path(
        new uri('http://localhost2/test.php/test')
      )
    );
  }

  function test_compare_path_contains()
  {
    $url = 'http://localhost/test.php/test';

    $this->uri->parse($url);

    $this->assertEqual(1,
      $this->uri->compare_path(
        new uri('http://localhost2/test.php')
      )
    );
  }

  function test_compare_path_is_contained()
  {
    $url = 'http://localhost/test.php/test';

    $this->uri->parse($url);

    $this->assertEqual(-1,
      $this->uri->compare_path(
        new uri('http://localhost2/test.php/test/test2')
      )
    );
  }

  function test_compare_path_not_equal()
  {
    $url = 'http://localhost/test.php/test/test1';

    $this->uri->parse($url);

    $this->assertIdentical(false,
      $this->uri->compare_path(
        new uri('http://localhost2/test.php/test/test2')
      )
    );
  }

  function test_remove_query_item()
  {
    $url = 'http://localhost/test.php?foo=bar&bar=foo';

    $this->uri->parse($url);

    $this->uri->remove_query_item('bar');

    $this->assertEqual('foo=bar', $this->uri->get_query_string());
    $this->assertEqual('http://localhost/test.php?foo=bar', $this->uri->to_string());
  }

  function test_remove_query_items()
  {
    $url = 'http://localhost/test.php?foo=bar&bar=foo';

    $this->uri->parse($url);

    $this->uri->remove_query_items();

    $this->assertEqual('', $this->uri->get_query_string());
    $this->assertEqual('http://localhost/test.php', $this->uri->to_string());
  }

  function test_is_absolute()
  {
    $url = '/test.php';

    $this->uri->parse($url);

    $this->assertTrue($this->uri->is_absolute());
  }

  function test_is_relative()
  {
    $url = '../../test.php';

    $this->uri->parse($url);

    $this->assertTrue($this->uri->is_relative());
  }

}

?>