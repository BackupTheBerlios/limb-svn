<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: ini.class.php 418 2004-02-08 11:31:53Z server $
*
***********************************************************************************/ 

/*
 Has the date of the current cache code implementation as a timestamp,
 if this changes(increases) the cache files will need to be recreated.
*/
define('INI_CACHE_CODE_DATE', 1039545462);

require_once(LIMB_DIR . 'core/lib/system/dir.class.php');
require_once(LIMB_DIR . 'core/lib/debug/debug.class.php');

function get_ini_option($filename, $block_name, $var_name, $use_cache=null)
{			  		
	$ini =& get_ini($filename, $use_cache);
	
	return $ini->variable($block_name, $var_name);
}

/**
 * @return unknown
 * @param $filename unknown
 * @param $use_cache unknown
 * @desc Enter description here...
 */
function & get_ini($filename, $use_cache=null)
{
  if(file_exists(PROJECT_DIR . 'core/settings/' . $filename))
  	$root_dir = PROJECT_DIR . 'core/settings/';
  elseif(file_exists(LIMB_DIR . 'core/settings/' . $filename))
  	$root_dir = LIMB_DIR . 'core/settings/';
  else
  	error('ini file not found', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('file' => $filename));
  	
	if(!($ini =& ini :: instance($filename, $root_dir, $use_cache)))
		error('couldnt retrieve ini instance', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('file' => $filename));
		
	return $ini;
}

class ini
{
  // Variable to store the ini file values.
  var $block_values;

  // Stores the file_name
  var $file_name;

  // The root of all ini files
  var $root_dir;

  // Stores the path and file_name of the cache file
  var $cache_file;
  
  var $cached_dir = '';
  
  var $charset = 'utf8';
  
  function ini($file_name, $root_dir='', $use_cache=null)
  {
    if ($use_cache === null)
    	$use_cache = $this->is_cache_enabled();
    
    $this->file_name = $file_name;
    $this->root_dir = $root_dir;
    $this->use_cache = $use_cache;
    $this->cached_dir = CACHE_DIR;

    $this->load();
  }
  
  /*
   return true if the INI file $file_name exists in the root dir $root_dir.
   $file_name defaults to site.ini and root_dir to settings.
  */
  function exists($file_name, $root_dir)
  {
    return file_exists($root_dir . '/' . $file_name);
  }
  
  //Returns the current instance of the given .ini file
  function &instance($file_name, $root_dir='', $use_cache=null)
  {
  	$obj = null;
  	
  	$instance_name = "global_ini_instance_{$root_dir}_{$file_name}";
  	
  	if(isset($GLOBALS[$instance_name]))
			$obj =& $GLOBALS[$instance_name];
		
  	if(!$obj || get_class($obj) != 'ini')
  	{
  		$obj =& new ini($file_name, $root_dir, $use_cache);
  		$GLOBALS[$instance_name] =& $obj;
  	}
  	
  	return $obj;
  }

  //returns the file_name
  function file_name()
  {
  	return $this->file_name;
  }

  //returns true if INI cache is enabled globally, the default value is true.
  function is_cache_enabled()
  {
    return (!defined('CACHING_ENABLED') || (defined('CACHING_ENABLED') && constant('CACHING_ENABLED')));
  }

  /*
   Tries to load the ini file specified in the constructor or instance() function.
   If cache files should be used and a cache file is found it loads that instead.
   Set $reset to false if you don't want to reset internal data.
  */
  function load($reset = true)
  {
    if ($reset)
    	$this->reset();
    
    if ($this->use_cache)
    	$this->_load_cache();
    else
    	$this->_parse();
  }
  
  function find_input_files( &$input_files, &$ini_file )
  {
    $input_files = array();
    
    if($this->root_dir)
    	$ini_file = dir::path( array( $this->root_dir, $this->file_name ) );
    else
    	$ini_file = dir::path( array( $this->file_name ) );
    
    if ( file_exists( $ini_file . '.php' ) )
    	$ini_file .= '.php';
    if ( file_exists( $ini_file ) )
    	$input_files[] = $ini_file;
        
    $override_dirs = $this->_override_dirs();
    foreach ($override_dirs as $override_dir_item)
    {
      $override_dir = $override_dir_item[0];
      $is_global = $override_dir_item[1];
      
      if ($is_global)
      	$override_file = dir::path( array( $override_dir, $this->file_name ) );
      else
      	$override_file = dir::path( array( $this->root_dir, $override_dir, $this->file_name ) );
          
      if (file_exists($override_file . '.php'))
      {
	      $override_file .= '.php';
	      $input_files[] = $override_file;
      }
      elseif (file_exists($override_file))
      	$input_files[] = $override_file;

      if ($is_global)
      	$override_file = dir::path( array( $override_dir, $this->file_name . '.append' ) );
      else
      	$override_file = dir::path( array( $this->root_dir, $override_dir, $this->file_name . '.append' ) );
          
      if (file_exists( $override_file . '.php'))
      {
	      $override_file .= '.php';
	      $input_files[] = $override_file;
      }
      elseif (file_exists($override_file))
      	$input_files[] = $override_file;
    }
  }

  /*
    Will load a cached version of the ini file if it exists,
    if not it will _parse the original file and create the cache file.
  */
  function _load_cache($reset = true)
  {
    if ($reset)
    	$this->reset();
    
    $cached_dir = $this->cached_dir;
    
    if (!is_dir($cached_dir))
    {
	    if (!dir :: mkdir($cached_dir, 0777, true))
	    	debug::write_error( "Couldn't create cache directory $cached_dir, perhaps wrong permissions", __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__ );
    }
		$input_files = array();
		$ini_file = '';
    $this->find_input_files($input_files, $ini_file);
    
    if ( count( $input_files ) == 0 )
    	return false;

    $md5_files = array();
    foreach ( $input_files as $input_file )
    	$md5_files[] = $input_file;
    
    $md5_input = implode( "\n", $md5_files );
    
    $file_name = md5( $md5_input ) . '.php';
    $cached_file = $cached_dir . $file_name;
    $this->cache_file = $cached_file;

    $input_time = false;
    // check for modifications
    foreach ( $input_files as $input_file )
    {
	    $file_time = filemtime( $input_file );
	    if ( $input_time === false || $file_time > $input_time )
	    	$input_time = $file_time;
    }

    $load_cache = false;
    $cache_time = false;
    if ( file_exists( $cached_file ) )
    {
	    $cache_time = filemtime( $cached_file );
	    $load_cache = true;
	    if ( $cache_time < $input_time )
	    	$load_cache = false;
    }

    $use_cache = false;
    if ( $load_cache )
    {
      $use_cache = true;
          
      $charset = null;
      $block_values = array();
      include( $cached_file );
      if ( !isset( $ini_cache_code_date ) or
           $ini_cache_code_date != INI_CACHE_CODE_DATE )
      {
        $this->reset();
        $use_cache = false;
      }
      else
      {
        $this->charset = $charset;
        $this->block_values = $block_values;
        $this->modified_block_values = array();
        unset( $block_values );
      }
    }
    if ( !$use_cache )
    {
	    $this->_parse( $input_files, $ini_file, false );
	    $this->save_cache( $cached_file );
    }
  }

  /*
   Stores the content of the INI object to the cache file $cached_file.
  */
  function save_cache( $cached_file )
  {
    if ( is_array( $this->block_values )  )
    {
      $fp = @fopen( $cached_file, 'w+' );
      if ( $fp === false )
      {
	      debug::write_error( "Couldn't create cache file '$cached_file', perhaps wrong permissions", __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__ );
	      return;
      }
      
      fwrite( $fp, "<?php\n\$ini_cache_code_date = " . INI_CACHE_CODE_DATE . ";\n" );
      fwrite( $fp, '$charset = "' . $this->charset .'";'."\n" );
      
      fwrite( $fp, '$block_values = ' . var_export($this->block_values, true) . ";\n");
               
      fwrite( $fp, "\n?>" );
      fclose( $fp );
    }
  }

  /*
    Parses either the override ini file or the standard file and then the append
    override file if it exists.
   */
  function _parse( $input_files = false, $ini_file = false, $reset = true )
  {
    if ( $reset )
    	$this->reset();
    if ( $input_files === false || $ini_file === false )
    	$this->find_input_files( $input_files, $ini_file );

    foreach ( $input_files as $input_file )
    {
	    if ( file_exists( $input_file ) )
	    	$this->_parse_file( $input_file );
    }
  }

  /*
    Will _parse the INI file and store the variables in the variable $this->block_values
   */
  function _parse_file( $file )
  {        	
    $fp = @fopen( $file, 'r' );
    if ( !$fp )
        return false;
        
    $size = filesize( $file );
    $contents =& fread( $fp, $size );
    fclose( $fp );
    
    $lines =& preg_split( "#\r\n|\r|\n#", $contents );
    unset( $contents );
      
    if ( $lines === false )
    {
      debug::write_error( "Failed opening file '$file' for reading", __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__ );
      return false;
    }
		
    $current_block = 'default';
    
    if ( count( $lines ) > 0 )
    {
      // check for charset
      if ( preg_match( "/#\?ini(.+)\?/", $lines[0], $ini_arr ) )
      {
        $args = explode( ' ', trim( $ini_arr[1] ) );
        foreach ( $args as $arg )
        {
          $vars = explode( '=', trim( $arg ) );
          if ( $vars[0] == 'charset' )
          {
            $val = $vars[1];
            
            if ( $val{0} == '"' &&
                 strlen( $val ) > 0 &&
                 $val{strlen($val)-1} == '"' )
            	$val = substr( $val, 1, strlen($val) - 2 );
            	
            $this->charset = $val;
          }
        }
      }
    }

    foreach ($lines as $line)
    {    	  	
      if(trim( $line ) == '')
      	continue;
     	
     	//removing comments #, not "\#"
      //$line = ereg_replace('([^"#]+|"(.*)")|(#[^#]*)', "\\1", $line);
      $line = preg_replace('/([^"#]+|"(.*)")|(#[^#]*)/', "\\1", $line);
      
      // check for new block
      if (preg_match("#^\[(.+)\]\s*$#", $line, $new_block_name_array))
      {
        $new_block_name = trim($new_block_name_array[1]);
        $current_block = $new_block_name;
        $this->block_values[$current_block] = array();
        continue;
      }

      // check for variable
      if (preg_match("#^([a-zA-Z0-9_-]+)(\[([a-zA-Z0-9_-]*)\]){0,1}(\s*)=(.*)$#", $line, $value_array ) )
      {
        $var_name = trim($value_array[1]);

        $var_value = trim($value_array[5]);

        if(preg_match('/^"(.*)"$/', $var_value, $m))
        	$var_value = $m[1];
                               
        if ($value_array[2])
        {
          if ($value_array[3])
          {
            $key_name = $value_array[3];
            
            if ( isset( $this->block_values[$current_block][$var_name] ) &&
                 is_array( $this->block_values[$current_block][$var_name] ) )
            	$this->block_values[$current_block][$var_name][$key_name] = $var_value;
            else
            	$this->block_values[$current_block][$var_name] = array( $key_name => $var_value );
          }
          else
          {
            if ( isset( $this->block_values[$current_block][$var_name] ) &&
                 is_array( $this->block_values[$current_block][$var_name] ) )
            	$this->block_values[$current_block][$var_name][] = $var_value;
            else
            	$this->block_values[$current_block][$var_name] = array( $var_value );
          }
        }
        else
        {
        	$this->block_values[$current_block][$var_name] = $var_value;
        }
      }
    }
  }

  //removes the cache file if it exists.
  function reset_cache()
  {
    if (file_exists($this->cache_file))
    	unlink($this->cache_file);
  }

  /*
    Saves the file to disk.
    If file_name is given the file is saved with that name if not the current name is used.
  */
  function save( $file_name = false, $use_backup=false, $suffix = false, $use_override = false, $only_modified = false )
  {
    $line_separator = sys :: line_separator();
    $path_array = array();
    
    if ( $file_name === false )
        $file_name = $this->file_name;
        
    $path_array[] = $this->root_dir;
    
    if ( $use_override )
    {
    	$path_array[] = 'override';
    }
    if ( is_string( $use_override ) && $use_override == 'append' )
    	$file_name .= '.append';
        
    if ( $suffix !== false )
    	$file_name .= $suffix;
        
    $originalfile_name = $file_name;
    $backupfile_name = $originalfile_name . sys :: backup_filename();
    $file_name .= '.tmp';

    $file_path = dir :: path( array_merge( $path_array, $file_name ) );
    $original_file_path = dir :: path( array_merge( $path_array, $originalfile_name ) );
    $backup_file_path = dir :: path( array_merge( $path_array, $backupfile_name ) );

    $fp = @fopen( $file_path, 'w+');
    if ( !$fp )
    {
      debug::write_error( "Failed opening file '$file_path' for writing", __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__ );
      return false;
    }

    $write_ok = true;
    $written = fwrite( $fp, "<?php /* #?ini charset=\"" . $this->charset . "\"?$line_separator$line_separator" );
    
    if ( $written === false )
    	$write_ok = false;
    	
    $i = 0;
    if ( $write_ok )
    {
      foreach( array_keys( $this->block_values ) as $block_name )
      {
        if ( $only_modified )
        {
          $group_has_modified = false;
          if ( isset( $this->modified_block_values[$block_name] ) )
          {
            foreach ( $this->modified_block_values[$block_name] as $modified_value )
            {
              if ( $modified_value )
              	$group_has_modified = true;
            }
          }
          if ( !$group_has_modified )
          	continue;
        }
        $written = 0;
        if ( $i > 0 )
        	$written = fwrite( $fp, "$line_separator" );
        if ( $written === false )
        {
          $write_ok = false;
          break;
        }
        
        $written = fwrite( $fp, "[$block_name]$line_separator" );
        if ( $written === false )
        {
          $write_ok = false;
          break;
        }
        foreach( array_keys( $this->block_values[$block_name] ) as $block_var )
        {
          if ( $only_modified )
          {
            if ( !isset( $this->modified_block_values[$block_name][$block_var] ) ||
                 !$this->modified_block_values[$block_name][$block_var] )
            	continue;
          }
          $var_key = $block_var;
          $var_value = $this->block_values[$block_name][$block_var];
          if ( is_array( $var_value ) )
          {
            if ( count( $var_value ) > 0 )
            {
              foreach ( $var_value as $var_array_key => $var_array_value )
              {
                if ( is_string( $var_array_key ) )
                	$written = fwrite( $fp, "$var_key" . "[$var_array_key]=$var_array_value$line_separator" );
                else
                	$written = fwrite( $fp, "$var_key" . "[]=$var_array_value$line_separator" );
                	
                if ( $written === false )
                	break;
              }
            }
            else
            	$written = fwrite( $fp, "$var_key" . "[]$line_separator" );
          }
          else
          	$written = fwrite( $fp, "$var_key=$var_value$line_separator" );
          	
          if ( $written === false )
          {
            $write_ok = false;
            break;
          }
        }
        if ( !$write_ok )
        	break;
        ++$i;
      }
    }
    if ( $write_ok )
    {
      $written = fwrite( $fp, "*/ ?>" );
      if ( $written === false )
      	$write_ok = false;
    }
    @fclose( $fp );
    if ( !$write_ok )
    {
      unlink( $file_path );
      return false;
    }

    if ( file_exists( $backup_file_path ) )
    	unlink( $backup_file_path );
    	
    if ( $use_backup && file_exists( $original_file_path ))
    {
      if ( !rename( $original_file_path, $backup_file_path ) )
      	return false;
    }
    
    unlink( $original_file_path );
    
    if ( !rename( $file_path, $original_file_path ) )
    {
    	if($use_backup)
      	rename( $backup_file_path, $original_file_path );
      return false;
    }
    		
    return true;
  }

  /*
   Removes all read data from .ini files.
  */
  function reset()
  {
  	$this->block_values = array();
  }

  /*
   return the root directory from where all .ini and override files are read.
   This is set by the instance() or INI() functions.
  */
  function root_dir()
  {
  	return $this->root_dir;
  }
  
  /*
   return the override directories, if no directories has been set "override" is returned.
   The override directories are relative to the rootDir().
  */
  function _override_dirs()
  {
	  $dirs =& $GLOBALS['ini_override_dir_list'];
	  if ( !isset( $dirs ) || !is_array( $dirs ) )
	  	$dirs = array();

	  return $dirs;
  }

  /*
    Reads a variable from the ini file and puts it in the parameter $variable.
    $variable is not modified if the variable does not exist
  */
  function assign($block_name, $var_name, &$variable)
  {
    if ($this->has_variable($block_name, $var_name))
    	$variable = $this->variable($block_name, $var_name);
    else
    	return false;
    return true;
  }

  /*!
    Reads a variable from the ini file.
    false is returned if the variable was not found.
  */
  function variable($block_name, $var_name)
  {    
    if (!isset($this->block_values[$block_name]))
    {
    	debug::write_notice('undefined block', 
    		__FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
    		array(
    			'ini' => $this->file_name,
    			'block' => $block_name,
    			'variable' => $var_name)
    	);
    }
    elseif (isset($this->block_values[$block_name][$var_name]))
    {
    	return $this->block_values[$block_name][$var_name];
    }
    else
    {
    	debug::write_notice('undefined variable', 
    		__FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
    		array(
    			'ini' => $this->file_name,
    			'block' => $block_name,
    			'variable' => $var_name)
    	);
    }
    
    return '';
  }

  /*
    Checks if a variable is set. Returns true if the variable exists, false if not.
  */
  function has_variable($block_name, $var_name)
  {
  	return isset($this->block_values[$block_name][$var_name]);
  }

  //Checks if group $block_name is set. Returns true if the group exists, false if not.
  function has_group($block_name)
  {
  	return isset($this->block_values[$block_name]);
  }

  //Fetches a variable group and returns it as an associative array.
  function group($block_name)
  {
    if (isset($this->block_values[$block_name]))
    	return $this->block_values[$block_name];
    	
		debug::write_notice(
			'unknown block', 
			__FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
			array(
				'ini' => $this->file_name,
				'block_name' => $block_name
			)
		);
		return null;
  }

  //Sets an INI file variable.
  function set_variable($block_name, $var_name, $var_value)
  {
  	$this->block_values[$block_name][$var_name] = $var_value;
  }
  
  //Returns block_values, which is a nicely named Array
  function get_named_array()
  {
  	return $this->block_values;
  }
}
?>