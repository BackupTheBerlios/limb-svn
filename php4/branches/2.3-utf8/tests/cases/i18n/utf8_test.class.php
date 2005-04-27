<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: fs_test.class.php 916 2004-11-23 09:14:28Z pachanga $
*
***********************************************************************************/

SimpleTestOptions::ignore('utf8_test');

class utf8_test extends LimbTestCase
{
  function _create_utf8_imp()
  {
    return null;
  }

  function test_substr()
  {
    $imp = $this->_create_utf8_imp();
    $this->assertEqual($imp->utf8_substr("это просто тест", 1), "то просто тест");
    $this->assertEqual($imp->utf8_substr("ääääσαφ", 1, 4), "äääσ");
    $this->assertEqual($imp->utf8_substr("ääääσαφ", -1), "φ");
    $this->assertEqual($imp->utf8_substr("ääääσαφ", 0, -1), "ääääσα");
    $this->assertEqual($imp->utf8_substr("ääääσαφ", 1, -1), "äääσα");
  }

  function test_explode()
  {
    $imp = $this->_create_utf8_imp();
    $strings = $imp->utf8_explode(".", "ää.pх.σαφ");
    $this->assertEqual($strings[0], "ää");
    $this->assertEqual($strings[1], "pх");
    $this->assertEqual($strings[2], "σαφ");

    $strings = $imp->utf8_explode("λ", "τελευλτα");
    $this->assertEqual($strings[0], "τε");
    $this->assertEqual($strings[1], "ευ");
    $this->assertEqual($strings[2], "τα");
  }

  function test_rtrim()
  {
    $imp = $this->_create_utf8_imp();
    $this->assertEqual($imp->utf8_rtrim("τελευτατελ\0\n\n\t"), "τελευτατελ");
    $this->assertEqual($imp->utf8_rtrim("τελευτατε?++.*?", ".*?+"), "τελευτατε");
    //intervals stuff not working yet, and it's not clear how it should work
    //$this->assertEqual($imp->utf8_rtrim("τελευτατε\n\t", "\0x00..\0x1F"), "τελευτατε");
  }

  function test_ltrim()
  {
    $imp = $this->_create_utf8_imp();
    $this->assertEqual($imp->utf8_ltrim("\0\n\n\tτελευτατελ"), "τελευτατελ");
    $this->assertEqual($imp->utf8_ltrim("λτελευτατε", "λ"), "τελευτατε");
    $this->assertEqual($imp->utf8_ltrim("?+.*+?τελευτατε", "?.*+"), "τελευτατε");
  }

  function test_trim()
  {
    $imp = $this->_create_utf8_imp();
    $this->assertEqual($imp->utf8_trim(" \n\t\0 τελευτατελ\0\n\n\t"), "τελευτατελ");
    $this->assertEqual($imp->utf8_trim("pτελεpυτατελp", "p"), "τελεpυτατελ");
    $this->assertEqual($imp->utf8_trim("pτελεpυτατελp", "pλ"), "τελεpυτατε");
    $this->assertEqual($imp->utf8_trim("?*++?τελευτατε?+.+?", "?.+*"), "τελευτατε");
  }

  function test_str_replace()
  {
    $imp = $this->_create_utf8_imp();
    $this->assertEqual($imp->utf8_str_replace("ελx", "", "τελxευτατελx"),
                       "τευτατ");
    $this->assertEqual($imp->utf8_str_replace("τ", "υ", "τελευτατελ"),
                       "υελευυαυελ");
    $search = array("τ", "υ");
    $this->assertEqual($imp->utf8_str_replace($search, "λ", "τελευτατελ"),
                       "λελελλαλελ");
    $replace = array("α", "ε");
    $this->assertEqual($imp->utf8_str_replace($search, $replace, "τελευτατελ"),
                       "αελεεαααελ");
  }

  function test_strlen()
  {
    $imp = $this->_create_utf8_imp();
    $this->assertEqual($imp->utf8_strlen("τελευτατελ"), 10);
    $this->assertEqual($imp->utf8_strlen("τ\nελευτα τελ "), 13);
  }

  function test_strpos()
  {
    $imp = $this->_create_utf8_imp();
    $this->assertEqual($imp->utf8_strpos("τελευτατελ", "τατ"), 5);
    $this->assertEqual($imp->utf8_strpos("τελευτατελ", "ε"), 1);
    $this->assertEqual($imp->utf8_strpos("τελευτατελ", "ε", 2), 3);
  }

  function test_strtolower()
  {
    $imp = $this->_create_utf8_imp();
    $this->assertEqual($imp->utf8_strtolower("ТЕСТ"), "тест");
    $this->assertEqual($imp->utf8_strtolower("тЕсТ"), "тест");
  }

  function test_strtoupper()
  {
    $imp = $this->_create_utf8_imp();
    $this->assertEqual($imp->utf8_strtoupper("тест"), "ТЕСТ");
    $this->assertEqual($imp->utf8_strtoupper("тЕсТ"), "ТЕСТ");
  }
}

?>