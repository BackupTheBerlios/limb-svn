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

/**
* This is a utility class for holding criteria information for a query:
* sql_builder constructs SQL statements based on the values in this class.
* (inspired by propel project http://propel.phpdbg.org)
*/
class criteria
{
  function EQUAL()         { return ("=");             }
  function NOT_EQUAL()     { return ("<>");            }
  function ALT_NOT_EQUAL() { return ("!=");            }
  function GREATER_THAN()  { return (">");             }
  function LESS_THAN()     { return ("<");             }
  function GREATER_EQUAL() { return (">=");            }
  function LESS_EQUAL()    { return ("<=");            }
  function LIKE()          { return (" LIKE ");        }
  function NOT_LIKE()      { return (" NOT LIKE ");    }
  function ILIKE()         { return (" ILIKE ");       }
  function NOT_ILIKE()     { return (" NOT ILIKE ");   }
  function CUSTOM()        { return ("CUSTOM");        }
  function DISTINCT()      { return ("DISTINCT ");     }
  function IN ()           { return (" IN ");          }
  function NOT_IN()        { return (" NOT IN ");      }
  function ALL()           { return ("ALL ");          }
  function JOIN()          { return ("JOIN");          }
  function ASC()           { return ("ASC");           }
  function DESC()          { return ("DESC");          }
  function ISNULL()        { return (" IS NULL ");     }
  function ISNOTNULL()     { return (" IS NOT NULL "); }
  function CURRENT_DATE()  { return ("CURRENT_DATE");  }
  function CURRENT_TIME()  { return ("CURRENT_TIME");  }

	var $ignore_case = false;
	var $single_record = false;
	var $select_modifiers = array();
	var $select_columns = array();
	var $order_by_columns = array();
	var $group_by_columns = array();
	var $having = null;
	var $as_columns = array();
	var $join_l = null;
	var $join_r = null;

	var $left_join_l = null;

	/**
	* * The name of the database.
	*/
	var $db_name;

	/**
	* * The name of the database as given in the contructor.
	*/
	var $original_db_name;

	/**
	* To limit the number of rows to return.  <code>0</code> means return all
	* rows.
	*/
	var $limit = 0;

	/**
	* * To start the results at a row other than the first one.
	*/
	var $offset = 0; 
	// flag to note that the criteria involves a blob.
	var $blob_flag = null;

	var $aliases = null;

	var $use_transaction = false;

	/**
	* Primary storage of criteria data.
	* 
	* @var array 
	*/
	var $map = array();

	/**
	* Creates a new instance with the default capacity which corresponds to
	* the specified database.
	* 
	* @param db_name $ The dabase name.
	*/
	function criteria($db_name = null)
	{
		$this->db_name = $db_name;
		$this->original_db_name = $db_name;
	} 

	/**
	* Implementing SPL iterator_aggregate interface.  This allows
	* you to foreach() over a criteria object.
	*/
	function &get_iterator()
	{
		return new criterion_iterator($this);
	} 

	/**
	* Brings this criteria back to its initial state, so that it
	* can be reused as if it was new. Except if the criteria has grown in
	* capacity, it is left at the current capacity.
	* 
	* @return void 
	*/
	function clear()
	{
		$this->map = array();
		$this->ignore_case = false;
		$this->single_record = false;
		$this->select_modifiers = array();
		$this->select_columns = array();
		$this->order_by_columns = array();
		$this->group_by_columns = array();
		$this->having = null;
		$this->as_columns = array();
		$this->join_l = null;
		$this->join_r = null;
		$this->db_name = $this->original_db_name;
		$this->offset = 0;
		$this->limit = -1;
		$this->blob_flag = null;
		$this->aliases = null;
		$this->use_transaction = false;
	} 

	/**
	* Add an AS clause to the select columns. Usage:
	* 
	* <code>
	* 
	* criteria my_crit = new criteria();
	* my_crit.add_as_column("alias", "ALIAS("+my_peer.ID+")");
	* 
	* </code>
	* 
	* @param name $ wanted Name of the column
	* @param clause $ SQL clause to select from the table
	* 
	* If the name already exists, it is replaced by the new clause.
	* @return A modified criteria object.
	*/
	function &add_as_column($name, $clause)
	{
		$this->as_columns[$name] = $clause;
		return $this;
	} 

	/**
	* Get the column aliases.
	* 
	* @return array A Hashtable which map the column alias names
	* to the alias clauses.
	*/
	function &get_as_columns()
	{
		return $this->as_columns;
	} 

	/**
	* Allows one to specify an alias for a table that can
	* be used in various parts of the SQL.
	* 
	* @param alias $ a <code>String</code> value
	* @param table $ a <code>String</code> value
	* @return void 
	*/
	function add_alias($alias, $table)
	{
		if ($this->aliases === null)
		{
			$this->aliases = array();
		} 
		$this->aliases[$alias] = $table;
	} 

	/**
	* Returns the table name associated with an alias.
	* 
	* @param alias $ a <code>String</code> value
	* @return string A <code>String</code> value
	*/
	function get_table_for_alias($alias)
	{
		if ($this->aliases === null)
		{
			return null;
		} 
		return @$this->aliases[$alias];
	} 

	/**
	* Get the keys for the criteria map.
	* 
	* @return array 
	*/
	function keys()
	{
		return array_keys($this->map);
	} 

	/**
	* Does this criteria object contain the specified key?
	* 
	* @param string $column [table.]column
	* @return boolean True if this criteria object contain the specified key.
	*/
	function contains_key($column)
	{ 
		// must use array_key_exists() because the key could
		// exist but have a NULL value (that'd be valid).
		return array_key_exists($column, $this->map);
	} 

	/**
	* Will force the sql represented by this criteria to be executed within
	* a transaction.  This is here primarily to support the oid type in
	* postgresql.  Though it can be used to require any single sql statement
	* to use a transaction.
	* 
	* @return void 
	*/
	function set_use_transaction($v)
	{
		$this->use_transaction = (boolean) $v;
	} 

	/**
	* called by base_peer to determine whether the sql command specified by
	* this criteria must be wrapped in a transaction.
	* 
	* @return a <code>boolean</code> value
	*/
	function is_use_transaction()
	{
		return $this->use_transaction;
	} 

	/**
	* Method to return criteria related to columns in a table.
	* 
	* @return A criterion.
	*/
	function &get_criterion($column)
	{
		return @$this->map[$column];
	} 

	/**
	* Method to return criterion that is not added automatically
	* to this criteria.  This can be used to chain the
	* criterions to form a more complex where clause.
	* 
	* @param column $ String full name of column (for example TABLE.COLUMN).
	* @param mixed $value 
	* @param string $comparison 
	* @return A criterion.
	*/
	function &get_new_criterion($column, $value, $comparison = null)
	{
		return new criterion($this, $column, $value, $comparison);
	} 

	/**
	* Method to return a String table name.
	* 
	* @param name $ A String with the name of the key.
	* @return A String with the value of the object at key.
	*/
	function get_column_name($name)
	{
		$c = @$this->map[$name];
		$val = null;

		if ($c !== null)
		{
			$val = $c->get_column();
		} 

		return $val;
	} 

	/**
	* Shortcut method to get an array of columns indexed by table.
	* 
	* @return array array(table => array(table.column1, table.column2))
	*/
	function &get_tables_columns()
	{
		$tables = array();
		$keys = array_keys($this->map);
		foreach($keys as $key)
		{
			$t = substr($key, 0, strpos($key, '.')); 
			// this happens automatically, so if no notices
			// are raised, then leave it out:
			// if (!isset($tables[$t])) $tables[$t] = array();
			$tables[$t][] = $key;
		} 
		return $tables;
	} 

	/**
	* Method to return a comparison String.
	* 
	* @param string $key String name of the key.
	* @return string A String with the value of the object at key.
	*/
	function get_comparison($key)
	{
		$c = @$this->map[$key];
		$val = null;

		if ($c !== null)
		{
			$val = $c->get_comparison();
		} 

		return $val;
	} 

	/**
	* Get the Database(Map) name.
	* 
	* @return string A String with the Database(Map) name.
	*/
	function get_connection_name()
	{
		return $this->db_name;
	} 

	/**
	* Set the database_map name.  If <code>null</code> is supplied, uses value
	* provided by <code>Propel::get_default_db()</code>.
	* 
	* @param  $connectioname A String with the Database(Map) name.
	* @return void 
	*/
	function set_db_name($db_name = null)
	{
		$this->db_name = ($db_name === null ? DB_NAME : $db_name);
	} 

	/**
	* Method to return a String table name.
	* 
	* @param  $name A String with the name of the key.
	* @return string A String with the value of table for criterion at key.
	*/
	function get_table_name($name)
	{
		$c = @$this->map[$name];
		$val = null;
		if ($c !== null)
		{
			$val = $c->get_table();
		} 
		return $val;
	} 

	/**
	* Method to return the value that was added to criteria.
	* 
	* @param string $name A String with the name of the key.
	* @return mixed The value of object at key.
	*/
	function get_value($name)
	{
		$c = @$this->map[$name];
		$val = null;

		if ($c !== null)
		{
			$val = $c->get_value();
		} 

		return $val;
	} 

	/**
	* An alias to get_oalue() -- exposing a Hashtable-like interface.
	* 
	* @param string $key An Object.
	* @return mixed The value within the criterion (not the criterion object).
	*/
	function &get($key)
	{
		return $this->get_value($key);
	} 

	/**
	* Overrides Hashtable put, so that this object is returned
	* instead of the value previously in the criteria object.
	* The reason is so that it more closely matches the behavior
	* of the add() methods. If you want to get the previous value
	* then you should first criteria.get() it yourself. Note, if
	* you attempt to pass in an Object that is not a String, it will
	* throw a NPE. The reason for this is that none of the add()
	* methods support adding anything other than a String as a key.
	* 
	* @param string $key 
	* @param string $value 
	* @return Instance of self.
	*/
	function &put($key, $value)
	{
		return $this->add($key, $value);
	} 

	/**
	* Copies all of the mappings from the specified Map to this criteria
	* These mappings will replace any mappings that this criteria had for any
	* of the keys currently in the specified Map.
	* 
	* if the map was another criteria, its attributes are copied to this
	* criteria, overwriting previous settings.
	* 
	* @param mixed $t Mappings to be stored in this map.
	*/
	function put_all($t)
	{
		if (is_array($t))
		{
			$keys = array_keys($t);
			foreach ($keys as $key)
			{
				$val = &$t[$key];
				if (is_a($val, 'criterion'))
				{
					$this->map[$key] = &$val;
				} 
				else
				{ 
					$this->put($key, $val);
				} 
			} 
		} 
		elseif (is_a($t, 'criteria'))
		{
			$this->join_l = $t->join_l;
			$this->join_r = $t->join_r;
		} 
	} 

	/**
	* This method adds a new criterion to the list of criterias.
	* If a criterion for the requested column already exists, it is
	* replaced. If is used as follow:
	* 
	* <p>
	* <code>
	* criteria crit = new criteria();
	* $crit->add(&quot;column&quot;,
	*             &quot;value&quot;
	*             &quot;criterion.GREATER_THAN()&quot;);
	* </code>
	* 
	* Any comparison can be used.
	* 
	* The name of the table must be used implicitly in the column name,
	* so the Column name must be something like 'TABLE.id'. If you
	* don't like this, you can use the add(table, column, value) method.
	* 
	* @param string $crit_or_column The column to run the comparison on, or criterion object.
	* @param mixed $value 
	* @param string $comparison A String.
	* @return A modified criteria object.
	*/
	function add($p1, $value = null, $comparison = null)
	{
		if (is_a($p1, 'criterion'))
		{
			$c = $p1;
			$this->map[$c->get_table() . '.' . $c->get_column()] = $c;
		} 
		else
		{
			$column = $p1;
			$this->map[$column] = &new criterion($this, $column, $value, $comparison);
		} 

		return $this;
	} 

	/**
	* This is the way that you should add a straight (inner) join of two tables.  For
	* example:
	* 
	* <p>
	* AND PROJECT.PROJECT_ID=FOO.PROJECT_ID
	* <p>
	* 
	* left = PROJECT.PROJECT_ID
	* right = FOO.PROJECT_ID
	* 
	* @param string $left A String with the left side of the join.
	* @param string $right A String with the right side of the join.
	* @return criteria A modified criteria object.
	*/
	function &add_join($left, $right)
	{
		if ($this->join_l === null)
		{
			$this->join_l = array();
			$this->join_r = array();
		} 
		$this->join_l[] = $left;
		$this->join_r[] = $right;

		return $this;
	} 

	/**
	* get one side of the set of possible joins.  This method is meant to
	* be called by base_peer.
	* 
	* @return array 
	*/
	function &get_join_l()
	{
		return $this->join_l;
	} 

	/**
	* get one side of the set of possible joins.  This method is meant to
	* be called by base_peer.
	* 
	* @return array 
	*/
	function &get_join_r()
	{
		return $this->join_r;
	} 

	/**
	* Adds "ALL " to the SQL statement.
	* 
	* @return void 
	*/
	function set_all()
	{
		$this->select_modifiers[] = criteria::ALL();
	} 

	/**
	* Adds "DISTINCT " to the SQL statement.
	* 
	* @return void 
	*/
	function set_distinct()
	{
		$this->select_modifiers[] = criteria::DISTINCT();
	} 

	/**
	* Sets ignore case.
	* 
	* @param boolean $b True if case should be ignored.
	* @return A modified criteria object.
	*/
	function &set_ignore_case($b)
	{
		$this->ignore_case = (boolean) $b;
		return $this;
	} 

	/**
	* Is ignore case on or off?
	* 
	* @return boolean True if case is ignored.
	*/
	function is_ignore_case()
	{
		return $this->ignore_case;
	} 

	/**
	* Set single record?  Set this to <code>true</code> if you expect the query
	* to result in only a single result record (the default behaviour is to
	* throw a propel_exception if multiple records are returned when the query
	* is executed).  This should be used in situations where returning multiple
	* rows would indicate an error of some sort.  If your query might return
	* multiple records but you are only interested in the first one then you
	* should be using set_limit(1).
	* 
	* @param b $ set to <code>true</code> if you expect the query to select just
	* one record.
	* @return A modified criteria object.
	*/
	function &set_single_record($b)
	{
		$this->single_record = (boolean) $b;
		return $this;
	} 

	/**
	* Is single record?
	* 
	* @return boolean True if a single record is being returned.
	*/
	function is_single_record()
	{
		return $this->single_record;
	} 

	/**
	* Set limit.
	* 
	* @param limit $ An int with the value for limit.
	* @return A modified criteria object.
	*/
	function &set_limit($limit)
	{
		$this->limit = $limit;
		return $this;
	} 

	/**
	* Get limit.
	* 
	* @return int An int with the value for limit.
	*/
	function get_limit()
	{
		return $this->limit;
	} 

	/**
	* Set offset.
	* 
	* @param int $offset An int with the value for offset.
	* @return A modified criteria object.
	*/
	function &set_offset($offset)
	{
		$this->offset = $offset;
		return $this;
	} 

	/**
	* Get offset.
	* 
	* @return An int with the value for offset.
	*/
	function get_offset()
	{
		return $this->offset;
	} 

	/**
	* Add select column.
	* 
	* @param name $ A String with the name of the select column.
	* @return A modified criteria object.
	*/
	function &add_select_column($name)
	{
		$this->select_columns[] = $name;
		return $this;
	} 

	/**
	* Get select columns.
	* 
	* @return array An array with the name of the select
	* columns.
	*/
	function get_select_columns()
	{
		return $this->select_columns;
	} 

	/**
	* Clears current select columns.
	* 
	* @return criteria A modified criteria object.
	*/
	function &clear_select_columns()
	{
		$this->select_columns = array();
		return $this;
	} 

	/**
	* Get select modifiers.
	* 
	* @return An array with the select modifiers.
	*/
	function get_select_modifiers()
	{
		return $this->select_modifiers;
	} 

	/**
	* Add group by column name.
	* 
	* @param string $group_by The name of the column to group by.
	* @return A modified criteria object.
	*/
	function &add_group_by_column($group_by)
	{
		$this->group_by_columns[] = $group_by;
		return $this;
	} 

	/**
	* Add order by column name, explicitly specifying ascending.
	* 
	* @param name $ The name of the column to order by.
	* @return A modified criteria object.
	*/
	function &add_ascending_order_by_column($name)
	{
		$this->order_by_columns[] = $name . ' ' . criteria::ASC();
		return $this;
	} 

	/**
	* Add order by column name, explicitly specifying descending.
	* 
	* @param string $name The name of the column to order by.
	* @return A modified criteria object.
	*/
	function &add_descending_order_by_column($name)
	{
		$this->order_by_columns[] = $name . ' ' . criteria::DESC();
		return $this;
	} 

	/**
	* Get order by columns.
	* 
	* @return A string_stack with the name of the order columns.
	*/
	function get_order_by_columns()
	{
		return $this->order_by_columns;
	} 

	/**
	* Get group by columns.
	* 
	* @return array .
	*/
	function get_group_by_columns()
	{
		return $this->group_by_columns;
	} 

	/**
	* Get Having criterion.
	* 
	* @return A criterion that is the having clause.
	*/
	function &get_having()
	{
		return $this->having;
	} 

	/**
	* Remove an object from the criteria.
	* 
	* @param string $key A String with the key to be removed.
	* @return mixed The removed value.
	*/
	function remove($key)
	{ 
		// looks like unset works just fine
		$foo = @$this->map[$key];
		unset($this->map[$key]);
		if (is_a($foo, 'criterion')) // $foo instanceof criterion) {
		{
			return $foo->get_value();
		} 
		return $foo;
	} 

	/**
	* Build a string representation of the criteria.
	* 
	* @return string A String with the representation of the criteria.
	*/
	function to_string()
	{ 
		$sb = "criteria:: ";
		foreach($this->keys() as $key)
		{
			$sb .= $key . "<=>" . $this->map[$key]->to_string() . ":  ";
		} 

		$params = array();
		$result = base_peer::create_select_sql($this, $params);

		$sb .= "\nCurrent Query SQL (may not be complete or applicable):\n" . $result . "\n";

		return $sb;
	} 

	/**
	* Returns the size (count) of this criteria.
	* 
	* @return int 
	*/
	function size()
	{
		return count($this->map);
	} 

	/**
	* This method checks another criteria to see if they contain
	* the same attributes and hashtable entries.
	* 
	* @return boolean 
	*/
	function equals(&$crit)
	{
		$is_equiv = false;
		if ($crit === null || ! is_a($crit, 'criteria')) 
		{
			$is_equiv = false;
		} elseif ($this === $crit)
		{
			$is_equiv = true;
		} elseif ($this->size() === $crit->size())
		{ 
			// Important: nested criterion objects are checked
			$criteria = &$crit; // alias
			if ($this->offset === $criteria->get_offset() && 
					$this->limit === $criteria->get_limit() && 
					$this->ignore_case === $criteria->is_ignore_case() && 
					$this->single_record === $criteria->is_single_record() && 
					$this->db_name === $criteria->get_connection_name() && 
					$this->select_modifiers === $criteria->get_select_modifiers() && 
					$this->select_columns === $criteria->get_select_columns() && 
					$this->order_by_columns === $criteria->get_order_by_columns()
					)
			{
				$is_equiv = true;
				foreach($criteria->keys() as $key)
				{
					if ($this->contains_key($key))
					{
						$a = $this->get_criterion($key);
						$b = $criteria->get_criterion($key);
						if (!$a->equals($b))
						{
							$is_equiv = false;
							break;
						} 
					} 
					else
					{
						$is_equiv = false;
						break;
					} 
				} 
			} 
		} 
		return $is_equiv;
	} 

	/**
	* This method adds a prepared criterion object to the criteria as a having clause.
	* You can get a new, empty criterion object with the
	* get_new_criterion() method.
	* 
	* <p>
	* <code>
	* $crit = new criteria();
	* $c = $crit->get_new_criterion(base_peer::ID(), 5, criteria::LESS_THAN());
	* $crit->add_having($c);
	* </code>
	* 
	* @param having $ A criterion object
	* @return A modified criteria object.
	*/
	function add_having(&$having)
	{
		$this->having = &$having;
		return $this;
	} 

	/**
	* This method adds a new criterion to the list of criterias.
	* If a criterion for the requested column already exists, it is
	* "AND"ed to the existing criterion.
	* 
	* add_and(column, value, comparison)
	* <code>
	* $crit = $orig_crit->add_and(&quot;column&quot;,
	*                            &quot;value&quot;
	*                            &quot;criterion::GREATER_THAN&quot;);
	* </code>
	* 
	* add_and(column, value)
	* <code>
	* $crit = $orig_crit->add_and(&quot;column&quot;, &quot;value&quot;);
	* </code>
	* 
	* add_and(criterion)
	* <code>
	* $crit = new criteria();
	* $c = $crit->get_new_criterion(base_peer::ID(), 5, criteria::LESS_THAN());
	* $crit->add_and($c);
	* </code>
	* 
	* Any comparison can be used, of course.
	* 
	* @return criteria A modified criteria object.
	*/
	function &add_and($p1, $p2 = null, $p3 = null)
	{
		if ($p3 !== null)
		{ 
			// add_and(column, value, comparison)
			$oc = &$this->get_criterion($p1);
			$nc = &new criterion($this, $p1, $p2, $comparison);
			if ($oc === null)
			{
				$this->map[$p1] = &$nc;
			} 
			else
			{
				$oc->add_and($nc);
			} 
		}
		 elseif ($p2 !== null)
		{ 
			// add_and(column, value)
			$this->add_and($p1, $p2, criteria::EQUAL());
		} 
		elseif (is_a($p1, 'criterion'))
		{
			// add_and(criterion)
			$c = $p1;
			$oc = &$this->get_criterion($c->get_table() . '.' . $c->get_column());
			if ($oc === null)
			{
				$this->add($c);
			} 
			else
			{
				$oc->add_and($c);
			} 
		} 
		return $this;
	} 

	/**
	* This method adds a new criterion to the list of criterias.
	* If a criterion for the requested column already exists, it is
	* "OR"ed to the existing criterion.
	* 
	* Any comparison can be used.
	* 
	* Supports a number of different signatures:
	* 
	* add_or(column, value, comparison)
	* <code>
	* $crit = $orig_crit->add_or(&quot;column&quot;,
	*                            &quot;value&quot;
	*                            &quot;criterion::GREATER_THAN()&quot;);
	* </code>
	* 
	* add_or(column, value)
	* <code>
	* $crit = $orig_crit->add_or(&quot;column&quot;, &quot;value&quot;);
	* </code>
	* 
	* add_or(criterion)
	* 
	* @return criteria A modified criteria object.
	*/
	function &add_or($p1, $p2 = null, $p3 = null)
	{
		if ($p3 !== null)
		{ 
			// add_or(column, value, comparison)
			$oc = &$this->get_criterion($p1);
			$nc = &new criterion($this, $p1, $p2, $p3);
			if ($oc === null)
			{
				$this->map[$p1] = &$nc;
			} 
			else
			{
				$oc->add_or($nc);
			} 
		} 
		elseif ($p2 !== null)
		{ 
			// add_or(column, value)
			$this->add_or($p1, $p2, criteria::EQUAL());
		} 
		elseif (is_a($p1, 'criterion'))
		{
			// add_or(criterion)
			$c = &$p1;
			$oc = &$this->get_criterion($c->get_table() . '.' . $c->get_column());
			if ($oc === null)
			{
				$this->add($c);
			} 
			else
			{
				$oc->add_or($c);
			} 
		} 

		return $this;
	} 
}

define('CRITERION_AND', " AND ");
define('CRITERION_OR', " OR ");

/**
* This is an "inner" class that describes an object in the criteria.
* (inspired by propel project http://propel.phpdbg.org)
*/
class criterion
{
	/**
	* * Value of the CO.
	*/
	var $value;

	/**
	* * Comparison value.
	* 
	* @var sql_enum 
	*/
	var $comparison;

	/**
	* * Table name.
	*/
	var $table;

	/**
	* * Column name.
	*/
	var $column;

	/**
	* * flag to ignore case in comparision
	*/
	var $ignore_string_case = false;

	/**
	* The DBAdapter adaptor which might be used to get db specific
	* variations of sql.
	*/
	var $connection;

	/**
	* other connected criteria and their conjunctions.
	*/
	var $clauses = array();
	var $conjunctions = array();

	/**
	* * "Parent" criteria class
	*/
	var $parent;

	/**
	* Create a new instance.
	* 
	* @param criteria $parent The outer class (this is an "inner" class).
	* @param string $column TABLE.COLUMN format.
	* @param mixed $value 
	* @param string $comparison 
	*/
	function criterion(&$outer, $column, $value, $comparison = null)
	{
		$this->outer = &$outer;
		list($this->table, $this->column) = explode('.', $column);
		$this->value = $value;
		$this->comparison = ($comparison === null ? criteria::EQUAL() : $comparison);
	} 

	/**
	* Get the column name.
	* 
	* @return string A String with the column name.
	*/
	function &get_column()
	{
		return $this->column;
	} 

	/**
	* Set the table name.
	* 
	* @param name $ A String with the table name.
	* @return void 
	*/
	function set_table($name)
	{
		$this->table = $name;
	} 

	/**
	* Get the table name.
	* 
	* @return string A String with the table name.
	*/
	function &get_table()
	{
		return $this->table;
	} 

	/**
	* Get the comparison.
	* 
	* @return string A String with the comparison.
	*/
	function &get_comparison()
	{
		return $this->comparison;
	} 

	/**
	* Get the value.
	* 
	* @return mixed An Object with the value.
	*/
	function get_value()
	{
		return $this->value;
	} 

	/**
	* Get the value of db.
	* The DBAdapter which might be used to get db specific
	* variations of sql.
	* 
	* @return DBAdapter value of db.
	*/
	function &get_connection()
	{
		$connection= null;
		if ($this->connection=== null)
		{ 
			// db may not be set if generating preliminary sql for
			// debugging.
			if (($connection= &db_factory :: get_connection($this->outer->get_connection_name())) === null)
			{ 
				// we are only doing this to allow easier debugging, so
				// no need to throw up the exception, just make note of it.
			} 
		} 
		else
		{
			$connection = &$this->connection;
		} 

		return $connection;
	} 

	/**
	* Set the value of db.
	* The DBAdapter adaptor might be used to get db specific
	* variations of sql.
	* 
	* @param v $ Value to assign to db.
	* @return void 
	*/
	function set_connection(&$v)
	{
		$this->connection = &$v;
		for($i = 0, $_i = count($this->clauses); $i < $_i; $i++)
		{
			$this->clauses[$i]->set_connection($v);
		} 
	} 

	/**
	* Sets ignore case.
	* 
	* @param boolean $b True if case should be ignored.
	* @return criterion A modified criterion object.
	*/
	function set_ignore_case($b)
	{
		$this->ignore_string_case = $b;
		return $this;
	} 

	/**
	* Is ignore case on or off?
	* 
	* @return boolean True if case is ignored.
	*/
	function is_ignore_case()
	{
		return $this->ignore_string_case;
	} 

	/**
	* Get the list of clauses in this criterion.
	* 
	* @return array 
	* @private 
	*/
	function &get_clauses()
	{
		return $this->clauses;
	} 

	/**
	* Get the list of conjunctions in this criterion
	* 
	* @return array 
	* @private 
	*/
	function &get_conjunctions()
	{
		return $this->conjunctions;
	} 

	/**
	* Append an AND criterion onto this criterion's list.
	*/
	function &add_and($criterion)
	{
		$this->clauses[] = &$criterion;
		$this->conjunctions[] = CRITERION_AND;
		return $this;
	} 

	/**
	* Append an OR criterion onto this criterion's list.
	* 
	* @return criterion 
	*/
	function &add_or($criterion)
	{
		$this->clauses[] = &$criterion;
		$this->conjunctions[] = CRITERION_OR;
		return $this;
	} 

	/**
	* Appends a Prepared Statement representation of the criterion
	* onto the buffer.
	* 
	* @param string $ &$sb The stringbuffer that will receive the Prepared Statement
	* @param array $params A list to which Prepared Statement parameters
	*                       will be appended
	* @return propel_exception If an error occures.
	*/
	function append_ps_to(&$sb, &$params)
	{
		if ($this->column === null)
		{
			return;
		} 

		$connection= &$this->get_connection();
		$clauses_length = count($this->clauses);

		for($j = 0; $j < $clauses_length; $j++)
		{
			$sb .= '(';
		} 

		if (criteria::CUSTOM() === $this->comparison)
		{
			if ($this->value !== '')
			{
				$sb .= (string) $value;
			} 
		} 
		else
		{
			if ($this->table === null)
			{
				$field = $this->column;
			} 
			else
			{
				$field = $this->table . '.' . $this->column;
			} 
			// There are several different types of expressions that need individual handling:
			// IN/NOT IN, LIKE/NOT LIKE, and traditional expressions.
			// OPTION 1:  table.column IN (?, ?) or table.column NOT IN (?, ?)
			if ($this->comparison === criteria::IN() || $this->comparison === criteria::NOT_IN())
			{
				$sb .= $field . $this->comparison;
				$values = (array) $this->value;
				for ($i = 0, $values_length = count($values); $i < $values_length; $i++)
				{
					$params[] = array('table' => $this->table, 'column' => $this->column, 'value' => $values[$i]);
				} 
				$in_string = '(' . substr(str_repeat("?,", $values_length), 0, -1) . ')';
				$sb .= $in_string;
			} 
			// OPTION 2:  table.column LIKE ? or table.column NOT LIKE ?  (or ILIKE for Postgres)
			elseif ($this->comparison === criteria::LIKE() || $this->comparison === criteria::NOT_LIKE() || $this->comparison === criteria::ILIKE() || $this->comparison === criteria::NOT_ILIKE())
			{ 
				// Handle LIKE, NOT LIKE (and related ILIKE, NOT ILIKE for Postgres)
				// If selection is case insensitive use ILIKE for postgre_sql or SQL
				// UPPER() function on column name for other databases.
				if ($this->ignore_string_case)
				{
					if (is_a($connection, 'db_postgres'))
					{
						if ($this->comparison === criteria::LIKE())
						{
							$this->comparison = criteria::ILIKE();
						} elseif ($comparison === criteria::NOT_LIKE())
						{
							$this->comparison = criteria::NOT_ILIKE();
						} 
					} 
					else
					{
						$field = $connection->ignore_case($field);
					} 
				} 

				$sb .= $field . $this->comparison; 
				// If selection is case insensitive use SQL UPPER() function
				// on criteria or, if Postgres we are using ILIKE, so not necessary.
				if ($this->ignore_string_case && !is_a($connection, 'db_postgres'))
				{
					$sb .= $connection->ignore_case('?');
				} 
				else
				{
					$sb .= '?';
				} 

				$params[] = array('table' => $this->table, 'column' => $this->column, 'value' => $this->value);
			} 
			// OPTION 3:  table.column = ? or table.column >= ? etc. (traditional expressions, the default)
			else
			{ 
				// NULL VALUES need special treatment because the SQL syntax is different
				// i.e. table.column IS NULL rather than table.column = null
				if ($this->value !== null)
				{ 
					// ANSI SQL functions get inserted right into SQL (not escaped, etc.)
					if ($this->value === criteria::CURRENT_DATE() || $this->value === criteria::CURRENT_TIME())
					{
						$sb .= $field . $this->comparison . $this->value;
					} 
					else
					{ 
						// default case, it is a normal col = value expression; value
						// will be replaced w/ '?' and will be inserted later using native db functions
						if ($this->ignore_string_case)
						{
							$sb .= $connection->ignore_case($field) . $this->comparison . $connection->ignore_case("?");
						} 
						else
						{
							$sb .= $field . $this->comparison . "?";
						} 
						// need to track the field in params, because
						// we'll need it to determine the correct setter
						// method later on (e.g. field 'review.DATE' => set_date());
						$params[] = array('table' => $this->table, 'column' => $this->column, 'value' => $this->value);
					} 
				} 
				else
				{ 
					// value is null, which means it was either not specified or specifically
					// set to null.
					if ($this->comparison === criteria::EQUAL() || $this->comparison === criteria::ISNULL())
					{
						$sb .= $field . criteria::ISNULL();
					} elseif ($this->comparison === criteria::NOT_EQUAL() || $this->comparison === criteria::ISNOTNULL())
					{
						$sb .= $field . criteria::ISNOTNULL();
					} 
					else
					{ 
						// for now throw an exception, because not sure how to interpret this
						return new exception(DB_ERROR, "Could not build SQL for expression: $field " . $this->comparison . " NULL");
					} 
				} 
			} 
		} 

		for($i = 0; $i < $clauses_length; $i++)
		{
			$sb .= $this->conjunctions[$i];
			$this->clauses[$i]->append_ps_to($sb, $params);
			$sb .= ')';
		} 
	} 

	/**
	* Build a string representation of the criterion.
	* 
	* @return string A String with the representation of the criterion.
	*/
	function to_string()
	{ 
		// it is alright if value == null
		
		if ($this->column == null)
		{
			return '';
		} 

		$expr = '';
		$params = array('', '');
		$this->append_ps_to($expr, $params);
		return $expr;
	} 

	/**
	* This method checks another criteria to see if they contain
	* the same attributes and hashtable entries.
	* 
	* @return boolean 
	*/
	function equals(&$obj)
	{
		if ($this === $obj)
		{
			return true;
		} 

		if (($obj === null) || !(is_a($obj, 'criterion')))
		{
			return false;
		} 

		$crit = &$obj;

		$is_equiv = ((($this->table === null && $crit->get_table() === null) || 
									($this->table !== null && $this->table === $crit->get_table())) && 
									$this->column === $crit->get_column() && 
									$this->comparison === $crit->get_comparison()); 
		// we need to check for value equality
		if ($is_equiv)
		{
			$is_equiv &= ($value === $b);
		} 
		// check chained criterion
		$clauses_length = count($this->clauses);
		$is_equiv &= (count($crit->get_clauses()) == $clauses_length);
		$crit_conjunctions = $crit->get_conjunctions();
		$crit_clauses = $crit->get_clauses();
		for ($i = 0; $i < $clauses_length; $i++)
		{
			$is_equiv &= ($this->conjunctions[$i] === $crit_conjunctions[$i]);
			$is_equiv &= ($this->clauses[$i] === $crit_clauses[$i]);
		} 

		return $is_equiv;
	} 

	/**
	* Returns a hash code value for the object.
	*/
	function &hash_code()
	{
		$h = crc32(serialize($this->value)) ^ crc32($this->comparison);

		if ($this->table !== null)
		{
			$h ^= crc32($this->table);
		} 

		if ($this->column !== null)
		{
			$h ^= crc32($this->column);
		} 

		$clauses_length = count($this->clauses);
		for($i = 0; $i < $clauses_length; $i++)
		{
			$h ^= crc32($this->clauses[$i]->to_string());
		} 

		return $h;
	} 

	/**
	* Get all tables from nested criterion objects
	* 
	* @return array 
	*/
	function &get_all_tables()
	{
		$tables = array();
		$this->add_criterion_table($this, $tables);
		return $tables;
	} 

	/**
	* method supporting recursion through all criterions to give
	* us a string array of tables from each criterion
	* 
	* @return void 
	* @private 
	*/
	function add_criterion_table($c, &$s)
	{
		$s[] = $c->get_table();
		$clauses = $c->get_clauses();
		$clauses_length = count($clauses);
		for($i = 0; $i < $clauses_length; $i++)
		{
			$this->add_criterion_table($clauses[$i], $s);
		} 
	} 

	/**
	* get an array of all criterion attached to this
	* recursing through all sub criterion
	* 
	* @return array criterion[]
	*/
	function &get_attached_criterion()
	{
		$crits = array();
		$this->traverse_criterion($this, $crits);
		return $crits;
	} 

	/**
	* method supporting recursion through all criterions to give
	* us an array of them
	* 
	* @param criterion $c 
	* @param array $ &$a
	* @return void 
	* @private 
	*/
	function traverse_criterion($c, &$a)
	{
		$a[] = $c;
		$clauses = $c->get_clauses();
		$clauses_length = count($clauses);
		for($i = 0; $i < $clauses_length; $i++)
		{
			$this->traverse_criterion($clauses[$i], $a);
		} 
	} 
} 

?>