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
require_once(LIMB_DIR . '/core/lib/db/db_factory.class.php');
require_once(LIMB_DIR . '/core/lib/db/util/sql/sql_statement_extractor.class.php');

class driver_test_manager
{
	var $conn;

	var $schema_statements;
	var $data_statements;

	var $dom_exchanges;
	var $xp_exchanges; 
	// Look for driver versions of the following classes, if found, add them.
	var $driver_classes = array(
													'connection', 
													'result_set', 
													'prepared_statement', 
													'callable_statement', 
													'id_generator', 
													'statement', 
													'db_info', 
													'table_info');
													
	function & instance()
	{
    static $instance;

    if ($instance === null)
    {
      $instance = new driver_test_manager();
      $instance->init();
    }

    return $instance;
	}

	function set_driver_classes($classes)
	{
		$self =& driver_test_manager :: instance();
		$self->driver_classes = $classes;
	} 

	function get_dsn()
	{
		$self =& driver_test_manager :: instance();
		return $self->conn->get_dsn();
	} 

	function get_connection()
	{
		$self =& driver_test_manager :: instance();
		return $self->conn;
	} 

	function connect()
	{
		$self =& driver_test_manager :: instance();
		$self->conn = db_factory::get_connection();
	} 

	function init()
	{
		$this->connect();
		$this->load_statements();
		$this->init_db($this->conn);
		
		if ($this->dom_exchanges === null)
		{
			$this->dom_exchanges = domxml_open_file(LIMB_DIR . '/tests/cases/db/etc/exchanges.xml');
			$this->xp_exchanges = $this->dom_exchanges->xpath_new_context();
		} 
	} 

	/**
	* Call this method to destroy and re-create the tables in the db.
	*/
	function restore()
	{
		$self =& driver_test_manager :: instance();
		$self->init_db($self->conn);
	} 

	/**
	* Loads & parses the schema SQL files.
	*/
	function load_statements()
	{
		$self =& driver_test_manager :: instance();
		$dsn = $self->conn->get_dsn();
		$schema = LIMB_DIR . '/tests/cases/db/etc/db/sql/' . $dsn['phptype'] . '/test-schema.sql';
		$data = LIMB_DIR . '/tests/cases/db/etc/db/sql/' . $dsn['phptype'] . '/test-data.sql';
		$self->schema_statements = sql_statement_extractor::extract_file($schema);
		$self->data_statements = sql_statement_extractor::extract_file($data);
	} 

	/**
	* Method that loads, parses, and executes the schema files.
	*/
	function init_db($conn)
	{
		$self =& driver_test_manager :: instance();
		$self->run_statements($self->schema_statements, $conn);
		$self->run_statements($self->data_statements, $conn);
	} 

	/**
	* Executes the passed SQL statements.
	*/
	function run_statements($statements, $conn)
	{
		$self =& driver_test_manager :: instance();
		$stmt = $conn->create_statement();
		foreach($statements as $sql)
		{ 
			if(is_error($e = $stmt->execute($sql)))
			{
				print "Error executing SQL: " . $sql . "\n";
				print $e->get_message() . "\n";
				print "... Attempting to continue ... \n";
			} 
		} 
	} 

	/**
	* Main worker function.  Adds any available tests to the passed in suite.
	*/
	function add_suite($parent_suite, $dsn)
	{
		$self =& driver_test_manager :: instance();
		$self->set_dsn($dsn); 
		// initialize db
		$self->init();

		$c = db_factory::get_new_connection($dsn); 
		// get just the first part of class name (e.g. my_sql from my_sqlconnection)
		//$camel_driver = str_replace('connection', '', get_class($c));

		//$suite = new PHPUnit_Framework_test_suite($camel_driver);

		foreach($self->driver_classes as $base_class)
		{ 
			// include the test class, based on driver name
			// do we want many?  Let's start by assuming that we'll fit all this in one class.
			$classname = $camel_driver . $base_class . 'Test';
			
			$path = '/drivers/' . $self->dsn['phptype'] . '/' . $classname . '.php';
			
			if (file_exists(LIMB_DIR . '/tests/cases/db/classes/' . $path))
			{
				include_once(LIMB_DIR . '/tests/cases/db/classes/' . $path);
				if (class_exists($classname))
				{
					//$suite->add_test_suite(new Reflection_Class($classname));
				} 
			} 
		} 

		$parent_suite->add_test($suite);
	} 

	/**
	* Get an "Exchange" -- which is a query + answer for current RDBMS.
	* 
	* @return db_exchange Populated db exchange object.
	*/
	function get_exchange($id)
	{
		$self =& driver_test_manager :: instance();
		$dsn = $self->conn->get_dsn();
		$matches = $self->xp_exchanges->xpath_eval("/exchanges/exchange[@id='" . $id . "']");

		if (!$matches)
		{
			print "XPath query matched no nodes: /exchanges/exchange[@id='" . $id . "']";
			return new sql_exception("XPath query matched no nodes: /exchanges/exchange[@id='" . $id . "']");
		} 
		// otherwise grab first match (there should be only one match)
		$result = is_array($matches) ? $matches[0] : $matches->nodeset[0];

		$exchange = new db_exchange(); // this is the object we send back 
		// We know there is a <sql> node
		$sql_nodes = $result->get_elements_by_tagname("sql"); 
		$sql_node = $sql_nodes[0];
		
		$sql = $sql_node->get_content(); // default is to use value of <sql> node. 
		// but there may also be a variant for current RDBMS ....
		$variant_nodes = $result->get_elements_by_tagname("sql-variant");
		if ($variant_nodes)
		{
			foreach($variant_nodes as $variant_node)
			{
				$attribute = $variant_node->get_attribute("id");
				if ($attribute->value() === $dsn['phptype'])
				{
					$sql = $variant_node->get_content();
					break;
				} 
			} 
		} 

		$exchange->set_sql($sql); 
		// now get the result, if any
		$result_nodes = $result->get_elements_by_tagname("result");
		if ($result_nodes)
		{ 
			$result_node = $result_nodes[0];
			if ($result_node)
			{
				$exchange->set_result($result_node->get_content());
			} 
		} 

		return $exchange;
	} 
} 

/**
* "Inner" class that encapsulates database exchange: query + answer.
*/
class db_exchange
{
	var $sql;
	var $res;

	function set_sql($sql)
	{
		$this->sql = $sql;
	} 

	function get_sql()
	{
		return $this->sql;
	} 

	function set_result($res)
	{
		$this->res = $res;
	} 

	function get_result()
	{
		return $this->res;
	} 
} 
