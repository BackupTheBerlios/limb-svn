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

require_once(LIMB_DIR . '/core/lib/error/error.inc.php');
require_once(LIMB_DIR . '/core/lib/db/sql_exception.class.php');

define('DB_ERROR',                     -1);
define('DB_ERROR_SYNTAX',              -2);
define('DB_ERROR_CONSTRAINT',          -3);
define('DB_ERROR_NOT_FOUND',           -4);
define('DB_ERROR_ALREADY_EXISTS',      -5);
define('DB_ERROR_UNSUPPORTED',         -6);
define('DB_ERROR_MISMATCH',            -7);
define('DB_ERROR_INVALID',             -8);
define('DB_ERROR_NOT_CAPABLE',         -9);
define('DB_ERROR_TRUNCATED',          -10);
define('DB_ERROR_INVALID_NUMBER',     -11);
define('DB_ERROR_INVALID_DATE',       -12);
define('DB_ERROR_DIVZERO',            -13);
define('DB_ERROR_NODBSELECTED',       -14);
define('DB_ERROR_CANNOT_CREATE',      -15);
define('DB_ERROR_CANNOT_DELETE',      -16);
define('DB_ERROR_CANNOT_DROP',        -17);
define('DB_ERROR_NOSUCHTABLE',        -18);
define('DB_ERROR_NOSUCHFIELD',        -19);
define('DB_ERROR_NEED_MORE_DATA',     -20);
define('DB_ERROR_NOT_LOCKED',         -21);
define('DB_ERROR_VALUE_COUNT_ON_ROW', -22);
define('DB_ERROR_INVALID_DSN',        -23);
define('DB_ERROR_CONNECT_FAILED',     -24);
define('DB_ERROR_EXTENSION_NOT_FOUND',-25);
define('DB_ERROR_ACCESS_VIOLATION',   -26);
define('DB_ERROR_NOSUCHDB',           -27);
define('DB_ERROR_CONSTRAINT_NOT_NULL',-29);

@ini_set('track_errors', true);

class	db_factory
{
  /**
   * Constant that indicates a connection object should be used.
   */
  function PERSISTENT() { return 1; }
  /**
   * Flag to pass to the connection to indicate that no case conversions
   * should be performed by result_set on keys of fetched rows.
   */
  function NO_ASSOC_LOWER() { return 16; }

  /**
  * Map of built-in drivers.
  * Change or add your own using register_driver()
  * @see register_driver()
  * @var array Hash mapping phptype => driver class (in unix-path notation, e.g. 'mysql' => '/drivers/mysql/mysql_connection').
  */
  var $driver_map = array
  (
    'mysql' => '/drivers/mysql/mysql_connection',
  );
  
  var $connection = null;
  
  var $db_map = null;

  /*
  * @private
  */
  function & instance()
  {
    static $instance;

    if ($instance === null)
      $instance = new db_factory();

    return $instance;
  }
  
  /**
  * Returns the class path to the driver registered for specified type.
  * @param string $phptype The phptype handled by driver (e.g. 'mysql', 'mssql', '*').
  * @return string The driver class in dot-path notation (e.g. db_factory.drivers.mssql.MSSQLconnection)
  *                  or NULL if no registered driver found.
  */
  function get_driver($phptype)
  {
    $self =& db_factory::instance();

    if (isset($self->driver_map[$phptype]))
    	return $self->driver_map[$phptype];
    else
    	return null;
  }
  
  /**
   * Parse a data source name.
   *
   * This isn't quite as powerful as DB::parse_dsn(); it's also a lot simpler, a lot faster,
   * and many fewer lines of code.
   *
   * A array with the following keys will be returned:
   *  phptype: Database backend used in PHP (mysql, odbc etc.)
   *  protocol: Communication protocol to use (tcp, unix etc.)
   *  hostspec: Host specification (hostname[:port])
   *  database: Database to use on the DBMS server
   *  username: User name for login
   *  password: Password for login
   *
   * The format of the supplied DSN is in its fullest form:
   *
   *  phptype://username:password@protocol+hostspec/database
   *
   * Most variations are allowed:
   *
   *  phptype://username:password@protocol+hostspec:110//usr/db_file.db
   *  phptype://username:password@hostspec/database_name
   *  phptype://username:password@hostspec
   *  phptype://username@hostspec
   *  phptype://hostspec/database
   *  phptype://hostspec
   *  phptype
   *
   * @param string $dsn Data Source Name to be parsed
   * @return array An associative array
   */
  function parse_dsn($dsn)
  {
    if (is_array($dsn))
    	return $dsn;

    $parsed = array(
	    'phptype'  => null,
	    'username' => null,
	    'password' => null,
	    'protocol' => null,
	    'hostspec' => null,
	    'port'     => null,
	    'socket'   => null,
	    'database' => null
    );

    $info = parse_url($dsn);

    if (count($info) === 1) // if there's only one element in result, then it must be the phptype
    { 
	    $parsed['phptype'] = array_pop($info);
	    return $parsed;
    }

    // some values can be copied directly
    $parsed['phptype'] = @$info['scheme'];
    $parsed['username'] = @$info['user'];
    $parsed['password'] = @$info['pass'];
    $parsed['port'] = @$info['port'];

    $host = @$info['host'];
    if (false !== ($pluspos = strpos($host, '+'))) 
    {
	    $parsed['protocol'] = substr($host,0,$pluspos);
	    if ($parsed['protocol'] === 'unix')
	    	$parsed['socket'] = substr($host,$pluspos+1);
	    else
	    	$parsed['hostspec'] = substr($host,$pluspos+1);

    } 
    else
    	$parsed['hostspec'] = $host;

    if (isset($info['path']))
    	$parsed['database'] = substr($info['path'], 1); // remove first char, which is '/'


    if (isset($info['query'])) 
    {
	    $opts = explode('&', $info['query']);
	    foreach ($opts as $opt) 
	    {
	      list($key, $value) = explode('=', $opt);
	      if (!isset($parsed[$key])) // don't allow params overwrite
	      	$parsed[$key] = urldecode($value);
	    }
    }

    return $parsed;
  }
  
  function & get_connection($dsn = null, $flags = 0)
  {
  	$self =& db_factory::instance();
  	
  	if(!$self->connection)
  	{
  		$self->connection =& db_factory::get_new_connection($dsn, $flags);
  	}
  	
  	return $self->connection;
  }
  
  function get_database_map()
  {
  	$self =& db_factory::instance();
  	
  	if($this->db_map === null)
  	{
  		$this->init_database_map();
  	}
  		
  	return $this->db_map;
  }
  
  function init_database_map()
  {
    $self =& db_factory::instance();
    
    $conn = $self->get_connection();
    $dsn = $conn->get_dsn();
    
    $self->db_map =& new db_map($dsn['database']);
    
    return $map;
  }

	
  /**
   * Create a new db connection object and connect to the specified
   * database
   *
   * @param mixed $dsn "data source name", see the self::parse_dsn
   * method for a description of the dsn format.  

   * @param int $flags connection flags (e.g. PERSISTENT).
   *
   * @return object Newly created connection object or an sql_exception object on error
   * @see self::parse_dsn()
   */
  function & get_new_connection($dsn = null, $flags = 0)
  { 
  	if(is_null($dsn))
  	{
			$dsninfo['phptype'] = DB_TYPE;
			$dsninfo['hostspec'] = DB_HOST;
			$dsninfo['username'] = DB_LOGIN;
			$dsninfo['password'] = DB_PASSWORD;
			$dsninfo['database'] = DB_NAME;
			
			if(defined('DB_PROTOCOL'))
				$dsninfo['protocol'] = DB_PROTOCOL;

			if(defined('DB_PORT'))
				$dsninfo['port'] = DB_PORT;
				
			if(defined('DB_SOCKET'))
				$dsninfo['socket'] = DB_SOCKET;
  	}
    elseif (is_array($dsn))
    	$dsninfo = $dsn;
    elseif(is_string($dsn))
    	$dsninfo = db_factory::parse_dsn($dsn);

    $self =& db_factory::instance();

    // support "catchall" drivers which will themselves handle the details of connecting
    // using the proper RDBMS driver.
    if (isset($self->driver_map['*']))
    	$type = '*';
    else
    {
      $type = $dsninfo['phptype'];
      if (! isset($self->driver_map[$type]))
        return new sql_exception(DB_ERROR_NOT_FOUND, "No driver has been registered to handle connection type: $type");
    }
    
    $clazz = db_factory::import($self->driver_map[$type]);

    if (is_error($clazz))
    	return $clazz;

    $obj = new $clazz();

    if (! is_a($obj, 'connection'))
    	return new sql_exception(DB_ERROR_NOT_FOUND, "Class does not implement db_factory connection interface: $clazz");

    if (($e = $obj->connect($dsninfo, $flags)) !== true) 
    {
      $e->set_user_info($dsninfo);
      return $e;
    }

    return $obj;
  }
  
  function import($path)
  {
    $pos = strrpos($path, '/');
    if ($pos !== false)
   		$class = substr($path, $pos + 1);
   	else
   		return new sql_exception(DB_ERROR_NOT_FOUND, "Unable to find driver class in path: " . $path);
  	
    if (!class_exists($class))
    {
      $ret = @include_once(LIMB_DIR . '/core/lib/db/' . $path . '.class.php');
      if ($ret === false)
      	return new sql_exception(DB_ERROR_NOT_FOUND, "Unable to load driver class: " . $class);

      if (!class_exists($class))
      	return new sql_exception(DB_ERROR_NOT_FOUND, "Unable to find loaded class: $class (Hint: make sure classname matches filename)");
    }
    return $class;
  }
}

function start_user_transaction()
{
	$connection = & db_factory :: get_connection();
	$connection->begin();
}

function commit_user_transaction()
{
	$connection = & db_factory :: get_connection();
	$connection->commit();
}

function rollback_user_transaction()
{
	$connection = & db_factory :: get_connection();
	$connection->rollback();
}
?>