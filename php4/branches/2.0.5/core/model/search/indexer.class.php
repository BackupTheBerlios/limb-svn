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


require_once(LIMB_DIR . 'core/lib/db/db_factory.class.php');
require_once(LIMB_DIR . 'core/lib/system/objects_support.inc.php');

class indexer
{
	var $db = null;
		
	function indexer()
	{
		$this->db = db_factory :: instance();
	} 
	
	function & instance()
	{
		$obj =&	instantiate_object('indexer');
		return $obj;
	}
	
	function add(&$site_object)
	{
		$indexer =& indexer :: instance();
		
		$indexer->remove($site_object);
		
		$index_array = array();
		$index_array_only_words = array();
		
		$attributes =& $site_object->export_attributes();
		
		$keys = array_keys($attributes);
		
		foreach($keys as $attribute_name)
		{
			$definition = $site_object->get_attribute_definition($attribute_name);
			
			if (!isset($definition['search']) || !$definition['search'])
				continue;

			$text =& indexer :: normalize_string($attributes[$attribute_name]);
			
			$words_array =& explode(' ', $text);

			for($i=0, $j=1, $max_count = 1000; $i < sizeof($words_array); $i++)
			{
				if (!($word = trim($words_array[$i])))
					continue;

				$index_array_only_words[] = $word;
					
				if($i > ($j * $max_count) || $i == (sizeof($words_array) - 1))
				{
					$index_array_only_words =& array_values(array_unique($index_array_only_words));
					
					$indexer->_update_db($site_object, $index_array_only_words, $attribute_name);

					$j++;
					$index_array = array();
					$index_array_only_words = array();
				}
			} 
		}
	}
	
	function _update_db(&$site_object, &$index_array_only_words, $attribute_name)
	{
		$existing_words =& $this->_get_existing_words($index_array_only_words);
		
		$word_ids_array = array();
		
		foreach($existing_words as $word_data)
			$word_ids_array[] = $word_data['id'];

		$this->_update_existing_words($word_ids_array);
		
		$new_words =& $this->_insert_new_words(array_diff(
				$index_array_only_words, 
				complex_array :: get_column_values('word', $existing_words)
			));

		foreach($new_words as $word_data)
			$word_ids_array[] = $word_data['id'];
		
		$this->_insert_word_link($site_object, $word_ids_array, $attribute_name);
	
	}
	
	function & _get_existing_words(&$index_array_only_words)
	{
		$words_string = implode('\',\'', $index_array_only_words);
		
		$this->db->sql_exec("SELECT * FROM sys_word WHERE word IN ( '$words_string' ) "); 
		
		$existing_words =& $this->db->get_array();
		
		return $existing_words;
	}
	
	function _update_existing_words(&$word_ids_array)
	{						
		if (count($word_ids_array) > 0)
		{
			$word_ids_string = implode(',', $word_ids_array);
			$this->db->sql_exec("
				UPDATE sys_word 
				SET object_count=(object_count+1) 
				WHERE id IN ($word_ids_string)");
		}
	}
	
	function &_insert_new_words(&$new_word_ids_array)
	{
		if (!count ($new_word_ids_array) > 0)
			return array();

		$new_word_string =& implode("', '1' ), ('", $new_word_ids_array);
		$this->db->sql_exec("INSERT INTO sys_word ( word, object_count ) VALUES ('$new_word_string', '1' )");

		$new_word_string =& implode("','", $new_word_ids_array);
		$this->db->sql_exec("SELECT id, word FROM sys_word WHERE word IN ( '$new_word_string' ) ");
		
		return $this->db->get_array();
	}
	
	function _insert_word_link(&$site_object, &$word_ids_array, $attribute_name)
	{
		$object_id = $site_object->get_id();
		$class_id = $site_object->get_class_id();
		
		$values_string_list = array();
		for ($i=0; $i < count($word_ids_array); $i++)
			$values_string_list[] = " ('{$word_ids_array[$i]}', '$object_id', '$class_id', '$attribute_name')";

		if (count($values_string_list) > 0)
		{
			$values_string = implode(',', $values_string_list);
			$this->db->sql_exec("INSERT INTO sys_word_link
                      ( word_id,
                        object_id,
                        class_id,
                        attribute_name)
                        VALUES $values_string");
		} 

	}
		
	function remove(&$site_object)
	{
		$indexer =& indexer :: instance();
		
		$indexer->db =& db_factory :: instance();
		$object_id = $site_object->get_id();
		
		$indexer->db->sql_exec("SELECT word_id FROM sys_word_link WHERE object_id='$object_id'");
		$word_array =& $indexer->db->get_array();
		
		$word_ids_array = complex_array :: get_column_values('word_id', $word_array);
		
		if (count($word_ids_array) > 0)
		{
			$word_id_string = implode(',', $word_ids_array);
			if (count($word_ids_array) > 0)
				$indexer->db->sql_exec("UPDATE sys_word SET object_count=( object_count - 1 ) WHERE id in ( $word_id_string )");
				
			$indexer->db->sql_exec("DELETE FROM sys_word WHERE object_count='0'");
			$indexer->db->sql_exec("DELETE FROM sys_word_link WHERE object_id='$object_id'");
		} 
	}
	 
	function & normalize_string(&$content)
	{
		$content = strtolower($content);
    $content = str_replace("\n", ' ', $content );
    $content = str_replace("\t", ' ', $content );
    $content = str_replace("\r", ' ', $content );
    
    $search = array (
    						"'<script[^>]*?>.*?</script>'si",  // Strip out javascript 
                "'<[\/\!]*?[^<>]*?>'si",           // Strip out html tags 
                "'([\r\n])[\s]+'"                 // Strip out white space 
              );

		$replace = array ('', 
		                 ' ', 
		                 ' '); 

		$content = preg_replace ($search, $replace, $content); 

    $content = preg_replace( "#(\.){2,}#", ' ', $content );
    $content = preg_replace( "#^\.#", ' ', $content );
    $content = preg_replace( "#\s\.#", ' ', $content );
    $content = preg_replace( "#\.\s#", ' ', $content );
    $content = preg_replace( "#\.$#", ' ', $content );
		
		$content = str_replace("'", ' ', $content );
		$content = str_replace("\"", ' ', $content );
		$content = str_replace("”", ' ', $content );
		$content = str_replace("“", ' ', $content );
		$content = str_replace("`", ' ', $content );
		$content = str_replace("&nbsp;", ' ', $content );
    $content = str_replace(":", ' ', $content );
    $content = str_replace(",", ' ', $content );
    $content = str_replace(";", ' ', $content );
    $content = str_replace("(", ' ', $content );
    $content = str_replace(")", ' ', $content );
    $content = str_replace("-", ' ', $content );
    $content = str_replace("+", ' ', $content );
    $content = str_replace("/", ' ', $content );
    $content = str_replace("!", ' ', $content );
    $content = str_replace("?", ' ', $content );
    $content = str_replace("[", ' ', $content );
    $content = str_replace("]", ' ', $content );
    $content = str_replace("$", ' ', $content );
    $content = str_replace("\\", ' ', $content );
    $content = str_replace("<", ' ', $content );
    $content = str_replace(">", ' ', $content );
    $content = str_replace("*", ' ', $content );

    $content = trim(preg_replace("(\s+)", ' ', $content));

    return $content;
	}
} 

?>