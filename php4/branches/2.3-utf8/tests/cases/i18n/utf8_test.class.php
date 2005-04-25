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
require_once(LIMB_DIR . '/core/lib/i18n/utf8.inc.php');

class utf8_test extends LimbTestCase
{
  function test_substr()
  {
    $this->assertEqual(utf8_substr("ääääσαφ", 1), "äääσαφ");
    $this->assertEqual(utf8_substr("ääääσαφ", 1, 4), "äääσ");
    $this->assertEqual(utf8_substr("ääääσαφ", -1), "φ");
    $this->assertEqual(utf8_substr("ääääσαφ", 0, -1), "ääääσα");
    $this->assertEqual(utf8_substr("ääääσαφ", 1, -1), "äääσα");
  }

  function test_explode()
  {
    $strings = utf8_explode(".", "ää.pх.σαφ");
    $this->assertEqual($strings[0], "ää");
    $this->assertEqual($strings[1], "pх");
    $this->assertEqual($strings[2], "σαφ");

    $strings = utf8_explode("λ", "τελευλτα");
    $this->assertEqual($strings[0], "τε");
    $this->assertEqual($strings[1], "ευ");
    $this->assertEqual($strings[2], "τα");
  }

  function test_rtrim()
  {
     $this->assertEqual(utf8_rtrim("τελευτατελ\0\n\n\t"), "τελευτατελ");
     $this->assertEqual(utf8_rtrim("τελευτατε?++?", "?+"), "τελευτατε");
     //intervals stuff not working yet
     //$this->assertEqual(utf8_rtrim("τελευτατε\n\t", "\0x00..\0x1F"), "τελευτατε");
  }

  function test_ltrim()
  {
     $this->assertEqual(utf8_ltrim("\0\n\n\tτελευτατελ"), "τελευτατελ");
     $this->assertEqual(utf8_ltrim("λτελευτατε", "λ"), "τελευτατε");
  }

  function test_trim()
  {
    $this->assertEqual(utf8_trim(" \n\t\0 τελευτατελ\0\n\n\t"), "τελευτατελ");
    $this->assertEqual(utf8_trim("pτελεpυτατελp", "p"), "τελεpυτατελ");
    $this->assertEqual(utf8_trim("pτελεpυτατελp", "pλ"), "τελεpυτατε");
  }

  function test_str_replace()
  {
    $this->assertEqual(utf8_str_replace("ελx", "", "τελxευτατελx"),
                       "τευτατ");
    $this->assertEqual(utf8_str_replace("τ", "υ", "τελευτατελ"),
                       "υελευυαυελ");
    $search = array("τ", "υ");
    $this->assertEqual(utf8_str_replace($search, "λ", "τελευτατελ"),
                       "λελελλαλελ");
    $replace = array("α", "ε");
    $this->assertEqual(utf8_str_replace($search, $replace, "τελευτατελ"),
                       "αελεεαααελ");
  }

  function test_strlen()
  {
    $this->assertEqual(utf8_strlen("τελευτατελ"), 10);
    $this->assertEqual(utf8_strlen("τ\nελευτα τελ "), 13);
  }

  function test_strpos()
  {
    $this->assertEqual(utf8_strpos("τελευτατελ", "τατ"), 5);
    $this->assertEqual(utf8_strpos("τελευτατελ", "ε"), 1);
    $this->assertEqual(utf8_strpos("τελευτατελ", "ε", 2), 3);
  }

  function test_strtolower()
  {
    $this->assertEqual(utf8_strtolower("ТЕСТ"), "тест");
    $this->assertEqual(utf8_strtolower("тЕсТ"), "тест");
  }

  function test_strtoupper()
  {
    $this->assertEqual(utf8_strtoupper("тест"), "ТЕСТ");
    $this->assertEqual(utf8_strtoupper("тЕсТ"), "ТЕСТ");
  }
}

?>