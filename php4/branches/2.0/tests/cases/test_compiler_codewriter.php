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


require_once(LIMB_DIR . 'core//template/template.class.php');
require_once(LIMB_DIR . 'core//template/compiler/codewriter.class.php');

if (! defined('data_space_test_case'))
	require_once TEST_CASES_DIR . '/test_dataspace.php';

class code_writer_test_case extends UnitTestCase
{
	function code_writer_test_case($name = 'code_writer_test_case')
	{
		$this->UnitTestCase($name);
	} 
	function setUp()
	{
		$this->writer = &new codewriter();
	} 
	function tearDown()
	{
		unset ($this->writer);
	} 
	function test_get_code()
	{
		$this->assertEqual($this->writer->get_code(), '');
	} 
	function test_write_php()
	{
		$this->writer->write_php('echo ("Hello World!");');
		$this->assertEqual($this->writer->get_code(), '<?php echo ("Hello World!"); ?>');
	} 
	function test_write_html()
	{
		$this->writer->write_html('<p>Hello World!</p>');
		$this->assertEqual($this->writer->get_code(), '<p>Hello World!</p>');
	} 
	function test_register_include()
	{
		$this->writer->register_include('test.php');
		$this->assertEqual($this->writer->get_code(), '<?php ' . "require_once('test.php');\n" . '?>');
	} 
	function test_begin_function()
	{
		$params = '($a,$b,$c)';
		$this->writer->begin_function($params);
		$this->assertEqual($this->writer->get_code(), '<?php function tpl1' . $params . "\n{\n ?>");
	} 
	function test_end_function()
	{
		$this->writer->end_function();
		$this->assertEqual($this->writer->get_code(), '<?php ' . "\n}\n" . ' ?>');
	} 
	function test_set_function_prefix()
	{
		$this->writer->set_function_prefix('Test');
		$params = '($a,$b,$c)';
		$this->writer->begin_function($params);
		$this->assertEqual($this->writer->get_code(), '<?php function tplTest1' . $params . "\n{\n ?>");
	} 
	function test_get_temp_variable()
	{
		$var = $this->writer->get_temp_variable();
		$this->assertWantedPattern('/[a-z][a-z0-9]*/i', $var);
	} 
	function test_get_second_temp_variable()
	{
		$A = $this->writer->get_temp_variable();
		$B = $this->writer->get_temp_variable();
		$this->assertNotEqual($A, $B);
	} 
	function test_get_temp_variables_many()
	{
		for ($i = 1; $i <= 30; $i++)
		{
			$var = $this->writer->get_temp_variable();
			$this->assertWantedPattern('/[a-z][a-z0-9]*/i', $var);
		} 
	} 
} 

?>