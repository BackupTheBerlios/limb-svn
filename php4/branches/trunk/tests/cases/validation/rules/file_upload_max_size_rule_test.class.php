<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/lib/util/dataspace.class.php');
require_once(LIMB_DIR . '/core/lib/validators/rules/file_upload_max_size_rule.class.php');
require_once(LIMB_DIR . '/tests/cases/validation/rules/single_field_rule_test.class.php');

class file_upload_max_size_rule_test extends single_field_rule_test
{
  function test_ok_default_maxsize_null()
  {
    $this->validator->add_rule(new file_upload_max_size_rule('testfield'));

    $data =& new dataspace();
    $data->set('testfield', array('error' => 0));

    $this->error_list->expectNever('add_error');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->is_valid());
  }

  function test_ok_maxsize()
  {
    $this->validator->add_rule(new file_upload_max_size_rule('testfield', 100));

    $data =& new dataspace();
    $data->set('testfield', array('size' => 99, 'error' => 0));

    $this->error_list->expectNever('add_error');

    $this->validator->validate($data);
    $this->assertTrue($this->validator->is_valid());
  }

  function test_error_ini_size_default_maxsize_null()
  {
    $this->validator->add_rule(new file_upload_max_size_rule('testfield'));

    $data =& new dataspace();
    $data->set('testfield', array('error' => UPLOAD_ERR_INI_SIZE));

    $this->error_list->expectOnce('add_error', array('testfield', 'FILEUPLOAD_MAX_SIZE', array()));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->is_valid());
  }

  function test_error_form_size_default_maxsize_null()
  {
    $this->validator->add_rule(new file_upload_max_size_rule('testfield'));

    $data =& new dataspace();
    $data->set('testfield', array('error' => UPLOAD_ERR_FORM_SIZE));

    $this->error_list->expectOnce('add_error', array('testfield', 'FILEUPLOAD_MAX_SIZE', array()));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->is_valid());
  }

  function test_error_size_exceeds_maxsize()
  {
    $this->validator->add_rule(new file_upload_max_size_rule('testfield', 100));

    $data =& new dataspace();
    $data->set('testfield', array('error' => 0, 'size' => 101));

    $this->error_list->expectOnce('add_error', array('testfield',
                                                     'FILEUPLOAD_MAX_USER_SIZE',
                                                     array('maxsize' => '100B')));

    $this->validator->validate($data);
    $this->assertFalse($this->validator->is_valid());
  }
}

?>