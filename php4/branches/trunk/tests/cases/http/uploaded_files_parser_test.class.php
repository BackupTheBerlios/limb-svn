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
require_once(LIMB_DIR . '/core/request/uploaded_files_parser.class.php');

class uploaded_files_parser_test extends LimbTestCase
{
  var $parser;

  function setUp()
  {
    $this->parser =& new uploaded_files_parser();
  }

  function test_empty()
  {
     $result = $this->parser->parse(array());
     $this->assertEqual($result, array());
  }

  function test_simple()
  {
     $files = array(
        'file1' => array(
                         'name' => 'file',
                         'type' => 'file_type',
                         'tmp_name' => 'file_tmp_name',
                         'size' => 'file_size',
                         'error' => 'file_err_code'
                         ),
        'file2' => array(
                         'name' => 'file',
                         'type' => 'file_type',
                         'tmp_name' => 'file_tmp_name',
                         'size' => 'file_size',
                         'error' => 'file_err_code'
                         ),
     );

     $result = $this->parser->parse($files);
     $this->assertEqual($result, $files);
  }

  function test_complex()
  {
     $files = array(
        'form' => array(
           'name' => array(
                           'file1' => 'file',
                           'file2' => 'file',
                           ),
           'type' => array(
                           'file1' => 'file_type',
                           'file2' => 'file_type',
                           ),
           'tmp_name' => array(
                               'file1' => 'file_tmp_name',
                               'file2' => 'file_tmp_name',
                               ),
           'size' => array(
                           'file1' => 'file_size',
                           'file2' => 'file_size',
                           ),
           'error' => array(
                           'file1' => 'file_err_code',
                           'file2' => 'file_err_code',
                           ),
         ),
     );

     $expected = array(
        'form' => array(
          'file1' => array(
             'name' => 'file',
             'type' => 'file_type',
             'tmp_name' => 'file_tmp_name',
             'size' => 'file_size',
             'error' => 'file_err_code'
           ),
          'file2' => array(
             'name' => 'file',
             'type' => 'file_type',
             'tmp_name' => 'file_tmp_name',
             'size' => 'file_size',
             'error' => 'file_err_code'
           ),
         ),
     );

     $result = $this->parser->parse($files);
     $this->assertEqual($result, $expected);
  }

  function test_mega_complex()
  {
     $files = array(
        'form' => array(
           'name' => array(
                           'file1' => array(
                                            '1' => 'file',
                                            '2' => 'file',
                                            ),
                           'file2' => 'file',
                           ),
           'type' => array(
                           'file1' => array(
                                            '1' => 'file_type',
                                            '2' => 'file_type',
                                            ),
                           'file2' => 'file_type',
                           ),
           'tmp_name' => array(
                               'file1' => array(
                                            '1' => 'file_tmp_name',
                                            '2' => 'file_tmp_name',
                                            ),
                               'file2' => 'file_tmp_name',
                               ),
           'size' => array(
                           'file1' => array(
                                            '1' => 'file_size',
                                            '2' => 'file_size',
                                            ),
                           'file2' => 'file_size',
                           ),
           'error' => array(
                           'file1' => array(
                                            '1' => 'file_err_code',
                                            '2' => 'file_err_code',
                                            ),
                           'file2' => 'file_err_code',
                           ),
         ),
     );

     $expected = array(
        'form' => array(
          'file1' => array(
            '1' => array(
               'name' => 'file',
               'type' => 'file_type',
               'tmp_name' => 'file_tmp_name',
               'size' => 'file_size',
               'error' => 'file_err_code'
             ),
            '2' => array(
               'name' => 'file',
               'type' => 'file_type',
               'tmp_name' => 'file_tmp_name',
               'size' => 'file_size',
               'error' => 'file_err_code'
             ),
          ),
          'file2' => array(
             'name' => 'file',
             'type' => 'file_type',
             'tmp_name' => 'file_tmp_name',
             'size' => 'file_size',
             'error' => 'file_err_code'
           ),
         ),
     );

     $result = $this->parser->parse($files);
     $this->assertEqual($result, $expected);
  }

  function test_mixed()
  {
     $files = array(
        'file1' => array(
                         'name' => 'file',
                         'type' => 'file_type',
                         'tmp_name' => 'file_tmp_name',
                         'size' => 'file_size',
                         'error' => 'file_err_code'
                         ),
        'form' => array(
           'name' => array(
                           'file1' => 'file',
                           'file2' => 'file',
                           ),
           'type' => array(
                           'file1' => 'file_type',
                           'file2' => 'file_type',
                           ),
           'tmp_name' => array(
                               'file1' => 'file_tmp_name',
                               'file2' => 'file_tmp_name',
                               ),
           'size' => array(
                           'file1' => 'file_size',
                           'file2' => 'file_size',
                           ),
           'error' => array(
                           'file1' => 'file_err_code',
                           'file2' => 'file_err_code',
                           ),
         ),
     );

     $expected = array(
        'file1' => array(
                         'name' => 'file',
                         'type' => 'file_type',
                         'tmp_name' => 'file_tmp_name',
                         'size' => 'file_size',
                         'error' => 'file_err_code'
                         ),
        'form' => array(
          'file1' => array(
             'name' => 'file',
             'type' => 'file_type',
             'tmp_name' => 'file_tmp_name',
             'size' => 'file_size',
             'error' => 'file_err_code'
           ),
          'file2' => array(
             'name' => 'file',
             'type' => 'file_type',
             'tmp_name' => 'file_tmp_name',
             'size' => 'file_size',
             'error' => 'file_err_code'
           ),
         ),
     );

     $result = $this->parser->parse($files);
     $this->assertEqual($result, $expected);
  }

}

?>