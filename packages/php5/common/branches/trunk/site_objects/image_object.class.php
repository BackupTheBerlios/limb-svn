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
require_once(dirname(__FILE__) . '/media_object.class.php');
require_once(LIMB_DIR . 'class/lib/image/image_factory.class.php');
require_once(LIMB_DIR . 'class/etc/message_box.class.php');

class image_object extends media_object
{
	var $_image_library = null;
	
	function image_object()
	{
		parent :: media_object();
				
		$this->_image_library = image_factory :: create();
	}
	
	function _define_attributes_definition()
	{
		$ini =& get_ini('image_variations.ini');
		
		$image_variations = $ini->get_all();
		
		$definition = array();
		
		foreach(array_keys($image_variations) as $variation)
		{
			$definition['upload_' . $variation . '_max_size'] = array();
			$definition['generate_' . $variation . '_max_size'] = array();
			$definition[$variation . '_action'] = array();
			$definition[$variation . '_base_variation'] = array();
		}
		
		$definition['files_data'] = array();
		
		return complex_array :: array_merge(parent :: _define_attributes_definition(), $definition);
	}
	
	function _define_class_properties()
	{
		return array(
			'class_ordr' => 1,
			'can_be_parent' => 0,
			'controller_class_name' => 'image_object_controller'
		);
	}
	
	function create()
	{				
		if(($id = parent :: create()) === false)
			return false;
		
		if($this->get('files_data'))
		{
			if(!$this->create_variations())
				return false;
		}
							
		return $id;
	}
	
	function create_variations()
	{
		$image_variations = $this->_get_variations_ini_list();
		$result = array();
				
		foreach($image_variations as $variation => $variation_data)
		{
			$action = $this->get($variation . '_action');
			
			switch($action)
			{
				case 'generate':
					$this->_create_generate_operation($variation, $result);
					break;
				case 'upload':
					$this->_create_upload_operation($variation, $result);
					break;
			}
		}

		$this->_check_result($result);
		
		return true;//???
	}
	
	function _create_generate_operation($variation, &$result)
	{
		$files_data = $this->get('files_data', array());
		$output_file = tempnam(VAR_DIR, 'p');
		
		if(!isset($files_data['name'][$this->get($variation . '_base_variation')]))
		{
		  debug :: write_error('uploaded file not found', 
			  __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
			  array('variation' => $variation));
		  return false;
		}
		
		if(!$this->_resize_operation(
					$this->get($variation . '_base_variation'), 
					(int)$this->get('generate_' . $variation . '_max_size'),
					$output_file,
					$output_file_type))
		{
			unlink($output_file);
			$result[$variation] = array('saved' => false, 'resized' => false);
			return true;
		}
			
		$name_parts = explode('.', $files_data['name'][$this->get($variation . '_base_variation')]);
		if(is_array($name_parts) && sizeof($name_parts) > 1)
		{
			$arr_size = sizeof($name_parts);
			$name_parts[$arr_size] = $name_parts[$arr_size - 1];
			$name_parts[$arr_size - 1] = $variation;
			$fname = implode('.', $name_parts);
		}
		else
			$fname = 	$fname . '.' . $variation;
			
	  $this->_insert_variation(
	  	$variation, 
	  	$output_file,
	  	$fname,
	  	$this->_image_library->get_mime_type($output_file_type));
	  	
		unlink($output_file);
		
		$result[$variation] = array('saved' => true, 'resized' => true);
	  return true;
	}
	
	function _create_upload_operation($variation, &$result)
	{
		$files_data = $this->get('files_data', array());

		if(	!isset($files_data['name'][$variation]) ||
				!isset($files_data['tmp_name'][$variation]) ||
				!isset($files_data['type'][$variation]))
		{
		  debug :: write_error('uploaded file not found', 
			  __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
			  array('variation' => $variation));
			  
			$result[$variation] = array('error' => true);
			
		  return;
		}
		
		$this->_insert_variation(
			$variation, 
			$files_data['tmp_name'][$variation], 
			$files_data['name'][$variation], 
			$files_data['type'][$variation]
		);
		
		if($upload_max_size = $this->get('upload_' . $variation . '_max_size'))
		{
			$output_file = tempnam(VAR_DIR, 'p');
			
		  if(!$this->_resize_operation(
				  	$variation, 
				  	(int)$upload_max_size,
						$output_file,
						$output_file_type
					))
			{
				unlink($output_file);
				$result[$variation] = array('saved' => true, 'resized' => false);
			  return;
			}
		  	
		  $this->_update_variation(
		  	$variation, 
		  	$output_file,
		  	$files_data['name'][$variation],
		  	$this->_image_library->get_mime_type($output_file_type)
		  );

			unlink($output_file);
		  $result[$variation] = array('saved' => true, 'resized' => true);
		  return;
		}
		
	  $result[$variation] = array('saved' => true);
	  return;
	}
	
	function update_variations()
	{
		$image_variations = $this->_get_variations_ini_list();
		
		$result = array();

		foreach($image_variations as $variation => $variation_data)
		{
			$action = $this->get($variation . '_action');
			
			switch($action)
			{
				case 'generate':
					$this->_update_generate_operation($variation, $result);
					break;
				case 'upload':
					$this->_update_upload_operation($variation, $result);
					break;
			}
		}
		
		$this->_check_result($result);
	}

	function _update_generate_operation($variation, &$result)
	{
		$output_file = tempnam(VAR_DIR, 'p');
		
		if(!$this->_resize_operation(
					$this->get($variation . '_base_variation'), 
					(int)$this->get('generate_' . $variation . '_max_size'),
					$output_file,
					$output_file_type
				))
		{
			unlink($output_file);
		  $result[$variation] = array('saved' => false, 'resized' => false);
		  return;
		}
		
		if($media_data = $this->get_variation_media_data($variation))
		{
		  $this->_update_variation(
		  	$variation, 
		  	$output_file,
		  	$media_data['file_name'],
		  	$this->_image_library->get_mime_type($output_file_type)
		  );
		}
		else
		{
			$media_data = $this->get_variation_media_data($this->get($variation . '_base_variation'));
		  
		  $this->_insert_variation(
		  	$variation, 
		  	$output_file,
		  	$media_data['file_name'],
		  	$this->_image_library->get_mime_type($output_file_type));
		}

		unlink($output_file);
	  $result[$variation] = array('saved' => true, 'resized' => true);
	  return;
	}
	
	function _update_upload_operation($variation, &$result)
	{
		$files_data = $this->get('files_data');
		
		if(	!isset($files_data['name'][$variation]) ||
				!isset($files_data['tmp_name'][$variation]) ||
				!isset($files_data['type'][$variation]))
		{
		  debug :: write_error('uploaded file not found', 
			  __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
			  array('variation' => $variation));
			  
			$result[$variation] = array('error' => true);
			
		  return;
		}
		
		if($this->get_variation_media_data($variation))
		{
		  $this->_update_variation(
		  	$variation, 
				$files_data['tmp_name'][$variation], 
				$files_data['name'][$variation], 
				$files_data['type'][$variation]
			);
		}
		else
		{
			$this->_insert_variation(
				$variation, 
				$files_data['tmp_name'][$variation], 
				$files_data['name'][$variation], 
				$files_data['type'][$variation]
			);
		}
		
		if ($upload_max_size = $this->get('upload_' . $variation . '_max_size'))
		{
			$output_file = tempnam(VAR_DIR, 'p');
			
		  if(!$this->_resize_operation(
				  	$variation, 
				  	(int)$upload_max_size,
						$output_file,
						$output_file_type
					))
			{
				unlink($output_file);
			  $result[$variation] = array('saved' => true, 'resized' => false);
			  return;
			}
		  	
		  $this->_update_variation(
		  	$variation, 
		  	$output_file,
		  	$files_data['name'][$variation],
		  	$this->_image_library->get_mime_type($output_file_type)
		  );

			unlink($output_file);
		  $result[$variation] = array('saved' => true, 'resized' => true);
		  return;
		}

	  $result[$variation] = array('saved' => true);
	  return;
	}
	
	function _get_variations_ini_list()
	{
		$ini =& get_ini('image_variations.ini');
		
		return $ini->get_all();
	}
	
	function _insert_variation($variation_name, $tmp_file_path, $file_name, $mime_type)
	{
		$image_id = $this->get_id();
		
		if(($media_id = $this->_create_media_record($tmp_file_path, $file_name, $mime_type)) === false)
			return false;
		
		$size = getimagesize($tmp_file_path);
		$image_variation_data['image_id'] = $image_id;
		$image_variation_data['media_id'] = $media_id;
		$image_variation_data['width'] = $size[0];
		$image_variation_data['height'] = $size[1];
		$image_variation_data['variation'] = $variation_name;
		
		$image_variation_db_table =& db_table_factory :: instance('image_variation');
		
		$image_variation_db_table->insert($image_variation_data);
		
		return true;
	}
	
	function _resize_operation($base_variation, $max_size=0, $output_file, &$output_file_type)
	{
		if(!$base_media_data = $this->get_variation_media_data($base_variation))
			return false;
		
		return $this->_resize_file_variation(
					$base_media_data['id'], 
					$base_media_data['mime_type'], 
					$max_size, 
					$output_file,
					$output_file_type);
	}
		
	function get_variation_media_data($variation)
	{
		$image_id = $this->get_id();
		
		$sql = "SELECT 				
						iv.image_id,
						iv.media_id, 
						iv.variation, 
						iv.width, 
						iv.height, 
						m.size, 
						m.mime_type, 
						m.file_name, 
						m.etag,
						m.id
						FROM image_variation iv, media m
						WHERE iv.image_id='{$image_id}' 
						AND iv.variation='{$variation}' 
						AND iv.media_id=m.id";
		
		$db =& db_factory :: instance();
		$db->sql_exec($sql);
		
		return $db->fetch_row();
	}
	
	function _resize_file_variation($base_media_id, $mime_type, $max_size, $output_file, &$output_file_type)
	{
		$input_file = MEDIA_DIR . $base_media_id . '.media';
		$input_file_type = $this->_image_library->get_image_type($mime_type);
		
		if (!$this->_image_library->set_input_file($input_file, $input_file_type))	
			return false;
		
		$output_file_type = $input_file_type;
		
		if (!$this->_image_library->set_output_file($output_file, $output_file_type))
			return false;
		
		if ($max_size)
		  $this->_image_library->resize(array('max_dimension' => $max_size));
		
		return $this->_image_library->commit();
	}
	
	function _update_variation($variation_name, $tmp_file_path, $file_name, $mime_type)
	{
  	$media_data = $this->get_variation_media_data($variation_name);
  	
		if(!$this->_update_media_record($media_data['id'], $tmp_file_path, $file_name, $mime_type))
		{
		  debug :: write_error('update media record failed', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array());
		  return false;
		}
		
		$size = getimagesize($tmp_file_path);
		
		$image_variation_db_table =& db_table_factory :: instance('image_variation');
		
		$image_id = $this->get_id();
		
		$image_variation_db_table->update(
			array('width' => $size[0], 'height' => $size[1]),
			array('image_id' => $image_id, 'variation' => $variation_name)
		);
		
		return true;
	}	
	
	function & fetch($params=array(), $sql_params=array())
	{
		if(!$records = parent :: fetch($params, $sql_params))
			return array();
		
		$images_ids = array();
		
		foreach($records as $record)
			$images_ids[] = "{$record['object_id']}";
			
		$ids = '('. implode(',', $images_ids) . ')';
			
		$sql = 
				"SELECT 
				iv.image_id,
				iv.media_id, 
				iv.variation, 
				iv.width, 
				iv.height, 
				m.size, 
				m.mime_type, 
				m.file_name, 
				m.etag,
				m.id
				FROM image_variation iv, media m
				WHERE iv.media_id = m.id AND 
				iv.image_id IN {$ids}";
		
		$db =& db_factory :: instance();
		
		$db->sql_exec($sql);
		
		if(!$images_variations = $db->get_array())
			return $records;
			
		foreach($images_variations as $variation_data)
		{
			foreach($records as $id => $record)
			{
				if($record['object_id'] == $variation_data['image_id'])
				{
					$records[$id]['variations'][$variation_data['variation']] = $variation_data;
					break;
				}
			}
		}
		
		return $records;
	}
	
	function _check_result($result)//it's not really the place for it...
	{
		$image_variations = $this->_get_variations_ini_list();
		
		foreach($image_variations as $variation => $variation_data)
		{
			$action = $this->get($variation . '_action');
			if($action == 'upload' || $action == 'generate')
			{
				if (!$result[$variation]['saved'])
				{
					message_box :: write_warning($variation . ' not saved');
				}
				
				if ($this->get($action . '_' . $variation . '_max_size') && !$result[$variation]['resized'])
				{
					message_box :: write_warning($variation . ' not resized');
				}
			}
		}
	}
}

?>
