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
require_once(LIMB_DIR . 'class/lib/db/db_module.class.php');

class db_mysql extends db_module
{	
  function db_mysql($db_params)
  {
  	db_module :: db_module($db_params);
  }
  
  function _connect_db_operation($db_params)
  {
  	return mysql_connect($db_params['host'], $db_params['login'], $db_params['password']);
  }
  
  function _select_db_operation($db_name)
  {
  	return mysql_select_db($db_name, $this->_db_connection);
  }
  
  function _disconnect_db_operation($db_params)
  {
  	mysql_close($this->_db_connection);
  }

  function free_result()
  {
  	if($this->_sql_result)
  	{
    	mysql_free_result($this->_sql_result);
    	$this->_sql_result = null;
    }
  }
  
	function _sql_exec_operation($sql, $count=0, $start=0)
	{		
		if ($count)
			$sql .= "\nLIMIT $start, $count";
		
		return mysql_query($sql, $this->_db_connection);
	}
	  
  function make_select_string($table, $fields='*', $where='', $order='', $count=0, $start=0)
  {  		
  	$sql = parent :: make_select_string($table, $fields, $where, $order, $count, $start);
  	
		if ($count)
			$sql .= "\nLIMIT $start, $count";

    return $sql;
  }
    
  function get_affected_rows()
  {
  	return mysql_affected_rows($this->_db_connection);
  }

  function get_sql_insert_id()
	{		
		return mysql_insert_id($this->_db_connection);
	}

  function get_last_error()
	{
		return mysql_error();
	}
  
  function parse_batch_sql(&$ret, $sql, $release)
	{
    $sql          = trim($sql);
    $sql_len      = strlen($sql);
    $char         = '';
    $string_start = '';
    $in_string    = false;
    $time0        = time();

    for ($i = 0; $i < $sql_len; ++$i) 
    {
      $char = $sql[$i];

      // We are in a string, check for not escaped end of strings except for
      // backquotes that can't be escaped
      if ($in_string) 
      {
        for(;;) 
        {
          $i = strpos($sql, $string_start, $i);
          // No end of string found->add the current substring to the
          // returned array
          if (!$i) 
          {
            $ret[] = $sql;
            return true;
          }
          // Backquotes or no backslashes before quotes: it's indeed the
          // end of the string->exit the loop
          elseif ($string_start == '`' || $sql[$i-1] != '\\') 
          {
            $string_start      = '';
            $in_string         = false;
            break;
          }
          // one or more Backslashes before the presumed end of string...
          else 
          {
            // ... first checks for escaped backslashes
            $j                     = 2;
            $escaped_backslash     = false;
            while ($i-$j > 0 && $sql[$i-$j] == '\\') 
            {
              $escaped_backslash = !$escaped_backslash;
              $j++;
            }
            // ... if escaped backslashes: it's really the end of the
            // string->exit the loop
            if ($escaped_backslash) 
            {
              $string_start  = '';
              $in_string     = false;
              break;
            }
            else
            	$i++;
          }
      	}
      }
      // We are not in a string, first check for delimiter...
      elseif ($char == ';') 
      {
        // if delimiter found, add the parsed part to the returned array
        $ret[]      = substr($sql, 0, $i);
        $sql        = ltrim(substr($sql, min($i + 1, $sql_len)));
        $sql_len    = strlen($sql);
        if ($sql_len) 
        	$i      = -1;
        else
        	return true;
      }
      // ... then check for start of a string,...
      elseif (($char == '"') || ($char == '\'') || ($char == '`')) 
      {
        $in_string    = true;
        $string_start = $char;
      }
      // ... for start of a comment (and remove this comment if found)...
      elseif ($char == '#'
               || ($char == ' ' && $i > 1 && $sql[$i-2] . $sql[$i-1] == '--')) 
      {
        // starting position of the comment depends on the comment type
        $start_of_comment = (($sql[$i] == '#') ? $i : $i-2);
        // if no "\n" exits in the remaining string, checks for "\r"
        // (Mac eol style)
        $end_of_comment   = (strpos(' ' . $sql, "\012", $i+2))
                          ? strpos(' ' . $sql, "\012", $i+2)
                          : strpos(' ' . $sql, "\015", $i+2);
        if (!$end_of_comment) 
        {
          // no eol found after '#', add the parsed part to the returned
          // array if required and exit
          if ($start_of_comment > 0)
          	$ret[] = trim(substr($sql, 0, $start_of_comment));
              
          return true;
        } 
        else 
        {
          $sql = substr($sql, 0, $start_of_comment) . ltrim(substr($sql, $end_of_comment));
          $sql_len = strlen($sql);
          $i--;
        }
      }
      // ... and finally disactivate the "/*!...*/" syntax if MySQL < 3.22.07
      elseif ($release < 32270
               && ($char == '!' && $i > 1  && $sql[$i-2] . $sql[$i-1] == '/*'))
      	$sql[$i] = ' ';

      //send a fake header each 30 sec. to bypass browser timeout
      $time1 = time();
      if ($time1 >= $time0 + 30) 
      {
        $time0 = $time1;
        header('X-pmaPing: Pong');
      }
    }

    // add any rest to the returned array
    if (!empty($sql) && ereg('[^[:space:]]+', $sql))
    	$ret[] = $sql;

    return true;
	}

  function _fetch_assoc_result_row()
  {
  	return mysql_fetch_assoc($this->_sql_result);
  }
  
	function _result_num_fields()
	{
		return mysql_num_fields($this->_sql_result);
	}
	
  function _process_default_value($value)
  {
  	return "'{$value}'";
  }
  
	function escape($sql)
  {
  	return mysql_escape_string($sql);
  }
  
  function concat($values)
  {
  	$str = implode(',' , $values);
  	return " CONCAT({$str}) ";
  }
  
  function substr($string, $offset, $limit=null)
  {
		if ($limit === null)
			return " SUBSTRING({$string} FROM {$offset}) ";
		else 
			return " SUBSTRING({$string} FROM {$offset} FOR {$limit}) ";
  }
  
  function count_selected_rows()
  {
    return mysql_num_rows($this->_sql_result);
  }
  
  function _begin_operation()
  {
  	mysql_query('START TRANSACTION', $this->_db_connection);
  }
  
  function _commit_operation()
  {
  	mysql_query('COMMIT', $this->_db_connection);
  }
  
  function _rollback_operation()
  {
  	mysql_query('ROLLBACK', $this->_db_connection);
  }
}
?>