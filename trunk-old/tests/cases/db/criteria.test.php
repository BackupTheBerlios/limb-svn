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
require_once(LIMB_DIR . '/core/lib/db/db_table.class.php');
require_once(LIMB_DIR . '/core/lib/db/builder/criteria.class.php');
require_once(LIMB_DIR . '/core/lib/db/builder/sql_builder.class.php');

class criteria_test_db_table extends db_table
{
  function _define_columns()
  {
  	return array(
      'cost' => array('type' => db_types::NUMERIC()),
      'bit' => array('type' => db_types::BOOLEAN()),
      'time' => array('type' => db_types::DATE()),
      'time' => array('type' => db_types::TIME()),
    );
  }
}

class test_criteria extends UnitTestCase
{
	var $criteria;

	function setUp()
	{
		$this->criteria = new criteria();
	} 

	function test_add_string()
	{
		$table = "myTable";
		$column = "myColumn";
		$value = "myValue"; 
		// Add the string
		$this->criteria->add($table . '.' . $column, $value); 
		// Verify that the key exists
		$this->assertTrue($this->criteria->contains_key($table . '.' . $column)); 
		// Verify that what we get out is what we put in
		$this->assertTrue($this->criteria->get_value($table . '.' . $column) === $value);
	} 

	/**
	* test various properties of Criterion and nested criterion
	*/
	function test_nested_criterion()
	{
		$table2 = "myTable2";
		$column2 = "myColumn2";
		$value2 = "myValue2";
		$key2 = "$table2.$column2";

		$table3 = "myTable3";
		$column3 = "myColumn3";
		$value3 = "myValue3";
		$key3 = "$table3.$column3";

		$table4 = "myTable4";
		$column4 = "myColumn4";
		$value4 = "myValue4";
		$key4 = "$table4.$column4";

		$table5 = "myTable5";
		$column5 = "myColumn5";
		$value5 = "myValue5";
		$key5 = "$table5.$column5";

		$crit2 = $this->criteria->get_new_criterion($key2, $value2, criteria::EQUAL());
		$crit3 = $this->criteria->get_new_criterion($key3, $value3, criteria::EQUAL());
		$crit4 = $this->criteria->get_new_criterion($key4, $value4, criteria::EQUAL());
		$crit5 = $this->criteria->get_new_criterion($key5, $value5, criteria::EQUAL());

		$t1 =& $crit2->add_and($crit3);
		$t1->add_or($crit4->add_and($crit5));
		$expect = "((myTable2.myColumn2=? "
		 . "AND myTable3.myColumn3=?) "
		 . "OR (myTable4.myColumn4=? "
		 . "AND myTable5.myColumn5=?))";

		$crit2->append_ps_to($sb = "", $params = array());

		$expect_params = array(
			array('table' => 'myTable2', 'column' => 'myColumn2', 'value' => 'myValue2'),
			array('table' => 'myTable3', 'column' => 'myColumn3', 'value' => 'myValue3'),
			array('table' => 'myTable4', 'column' => 'myColumn4', 'value' => 'myValue4'),
			array('table' => 'myTable5', 'column' => 'myColumn5', 'value' => 'myValue5'),
			);

		$this->assertEqual($expect, $sb);
		$this->assertEqual($expect_params, $params);

		$crit6 = $this->criteria->get_new_criterion($key2, $value2, criteria::EQUAL());
		$crit7 = $this->criteria->get_new_criterion($key3, $value3, criteria::EQUAL());
		$crit8 = $this->criteria->get_new_criterion($key4, $value4, criteria::EQUAL());
		$crit9 = $this->criteria->get_new_criterion($key5, $value5, criteria::EQUAL());

		$t2 =& $crit6->add_and($crit7);
		$t3 =& $t2->add_or($crit8);
		$t3->add_and($crit9);
		$expect = "(((myTable2.myColumn2=? "
		 . "AND myTable3.myColumn3=?) "
		 . "OR myTable4.myColumn4=?) "
		 . "AND myTable5.myColumn5=?)";

		$crit6->append_ps_to($sb = "", $params = array());

		$expect_params = array(
			array('table' => 'myTable2', 'column' => 'myColumn2', 'value' => 'myValue2'),
			array('table' => 'myTable3', 'column' => 'myColumn3', 'value' => 'myValue3'),
			array('table' => 'myTable4', 'column' => 'myColumn4', 'value' => 'myValue4'),
			array('table' => 'myTable5', 'column' => 'myColumn5', 'value' => 'myValue5'),
			);

		$this->assertEqual($expect, $sb);
		$this->assertEqual($expect_params, $params); 
		// should make sure we have tests for all possibilities
		$crita = $crit2->get_attached_criterion();

		$this->assertEqual($crit2, $crita[0]);
		$this->assertEqual($crit3, $crita[1]);
		$this->assertEqual($crit4, $crita[2]);
		$this->assertEqual($crit5, $crita[3]);

		$tables = $crit2->get_all_tables();

		$this->assertEqual($crit2->get_table(), $tables[0]);
		$this->assertEqual($crit3->get_table(), $tables[1]);
		$this->assertEqual($crit4->get_table(), $tables[2]);
		$this->assertEqual($crit5->get_table(), $tables[3]); 
		// simple confirmations that equality operations work
		$this->assertTrue($crit2->hash_code() === $crit2->hash_code());
	} 

	/**
	* Tests &lt;= and =&gt;.
	*/
	function test_between_criterion()
	{
		$cn1 = $this->criteria->get_new_criterion("criteria_test.cost",
			1000,
			criteria::GREATER_EQUAL());

		$cn2 = $this->criteria->get_new_criterion("criteria_test.cost",
			5000,
			criteria::LESS_EQUAL());
			
		$this->criteria->add($cn1->add_and($cn2));
		$expect = "SELECT  FROM criteria_test WHERE "
		 . "(criteria_test.cost>=? AND criteria_test.cost<=?)";

		$expect_params = array(array('table' => 'criteria_test', 'column' => 'cost', 'value' => 1000),
			array('table' => 'criteria_test', 'column' => 'cost', 'value' => 5000),
			);

		$result = sql_builder :: create_select_sql($this->criteria, $params = array(), $conn = DB_USE_DEFAULT_CONNECTION);

		$this->assertEqual($expect, $result);
		$this->assertEqual($expect_params, $params);
	}
	
	function test_equals()
	{
		$cn1_1 = $this->criteria->get_new_criterion("criteria_test.cost",
			1000,
			criteria::GREATER_EQUAL());

		$cn1_2 = $this->criteria->get_new_criterion("criteria_test.cost",
			5000,
			criteria::LESS_EQUAL());
			
		$this->criteria->add($cn1_1->add_and($cn1_2));
		
		$criteria2 = new criteria();

		$cn2_1 = $criteria2->get_new_criterion("criteria_test.cost",
			1000,
			criteria::GREATER_EQUAL());

		$cn2_2 = $criteria2->get_new_criterion("criteria_test.cost",
			5000,
			criteria::LESS_EQUAL());
			
		$criteria2->add($cn2_1->add_and($cn2_2));
		
		$this->assertTrue($criteria2->equals($this->criteria), 'criterias must be equal');
	}

	/**
	* Verify that AND and OR criterion are nested correctly.
	*/
	function test_precedence()
	{
		$cn1 = $this->criteria->get_new_criterion("criteria_test.cost", "1000", criteria::GREATER_EQUAL());
		$cn2 = $this->criteria->get_new_criterion("criteria_test.cost", "2000", criteria::LESS_EQUAL());
		$cn3 = $this->criteria->get_new_criterion("criteria_test.cost", "8000", criteria::GREATER_EQUAL());
		$cn4 = $this->criteria->get_new_criterion("criteria_test.cost", "9000", criteria::LESS_EQUAL());
		$this->criteria->add($cn1->add_and($cn2));
		$this->criteria->add_or($cn3->add_and($cn4));

		$expect = "SELECT  FROM criteria_test WHERE "
		 . "((criteria_test.cost>=? AND criteria_test.cost<=?) "
		 . "OR (criteria_test.cost>=? AND criteria_test.cost<=?))";

		$expect_params = array(array('table' => 'criteria_test', 'column' => 'cost', 'value' => '1000'),
			array('table' => 'criteria_test', 'column' => 'cost', 'value' => '2000'),
			array('table' => 'criteria_test', 'column' => 'cost', 'value' => '8000'),
			array('table' => 'criteria_test', 'column' => 'cost', 'value' => '9000'),
			);

		$result = sql_builder::create_select_sql($this->criteria, $params = array(), $conn = DB_USE_DEFAULT_CONNECTION);

		$this->assertEqual($expect, $result);
		$this->assertEqual($expect_params, $params);
	} 

	/**
	* Tests actually nothing right now....
	*/
	function test_criterion_ignore_case()
	{
		$my_criterion = $this->criteria->get_new_criterion("TABLE.COLUMN", "FoObAr", criteria::LIKE());
		$my_criterion->append_ps_to($sb = "", $params = array());

		$ignore_criterion = $my_criterion->set_ignore_case(true);
		$ignore_criterion->append_ps_to($sb = "", $params = array());
	} 

	/**
	* Test that true is evaluated correctly.
	*/
	function test_boolean()
	{
		$this->criteria->add("criteria_test.bit", true);

		$expect = "SELECT  FROM criteria_test WHERE criteria_test.bit=?";
		$expect_params = array(
			array('table' => 'criteria_test', 'column' => 'bit', 'value' => true),
		);
		
		$result = sql_builder::create_select_sql($this->criteria, $params = array(), $conn = DB_USE_DEFAULT_CONNECTION);

		$this->assertEqual($expect, $result, "Boolean test failed.");
		$this->assertEqual($expect_params, $params);
	} 

	function test_current_date()
	{
		$this->criteria = new criteria();
		$this->criteria->add("criteria_test.time", criteria::CURRENT_TIME());
		$this->criteria->add("criteria_test.date", criteria::CURRENT_DATE());

		$expect = "SELECT  FROM criteria_test WHERE criteria_test.time=CURRENT_TIME AND criteria_test.date=CURRENT_DATE";

		$result = sql_builder::create_select_sql($this->criteria, $params = array(), $conn = DB_USE_DEFAULT_CONNECTION);

		$this->assertEqual($expect, $result, "Current date test failed!");
	} 

	function test_count_aster()
	{
		$this->criteria->add_select_column("COUNT(*)");
		$this->criteria->add("criteria_test.time", criteria::CURRENT_TIME());
		$this->criteria->add("criteria_test.date", criteria::CURRENT_DATE());

		$expect = "SELECT COUNT(*) FROM criteria_test WHERE criteria_test.time=CURRENT_TIME AND criteria_test.date=CURRENT_DATE";

		$result = sql_builder::create_select_sql($this->criteria, $params = array(), $conn = DB_USE_DEFAULT_CONNECTION);

		$this->assertEqual($expect, $result);
	} 
} 
