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
define( 'DIR_SEPARATOR_LOCAL', 1 );
define( 'DIR_SEPARATOR_UNIX', 2 );
define( 'DIR_SEPARATOR_DOS', 3 );
define( 'WIN32_NET_PREFIX', '\\\\' );

require_once(LIMB_DIR . '/core/lib/system/sys.class.php');

class dir
{
  function dir()
  {
  }

  /*
   Creates the directory $dir with permissions $perm.
   If $parents is true it will create any missing parent directories,
   just like 'mkdir -p'.
  */
  function mkdir($dir, $perm, $parents=true)
  {
    $dir = dir :: clean_path($dir);
    
    if(!$parents)
    	return dir :: _do_mkdir($dir, $perm);
    
    $separator = dir :: separator();
    	
    $dir_elements = dir :: explode_path($dir);
        
    if (count($dir_elements) == 0)
    	return true;
    
    if(!$dir_elements[0])
    {
    	array_shift($dir_elements);
    	$current_dir .= array_shift($dir_elements);
    }
    else
    {
    	$current_dir = array_shift($dir_elements);
    }
    
    if(!dir :: _do_mkdir($current_dir, $perm))
    	return false;
          	
    for ($i = 0; $i < count( $dir_elements ); $i++ )
    {
      $current_dir .= $separator . $dir_elements[$i];
			
      if (!dir :: _do_mkdir($current_dir, $perm))
      {	
      	return false;
      }
    }

  	return true;
  }
  
  /*
   Creates the directory $dir with permission $perm.
  */
  function _do_mkdir($dir, $perm)
  {
  	if(is_dir($dir))
  		return true;
  	  	  	
    $oldumask = umask(0);
    if(!mkdir($dir, $perm))
    {
      umask($oldumask);
      return false;
    }
    umask($oldumask);
    return true;
  }
  
  function explode_path($path)
  {
    $separator = dir :: separator();
    	
    $dir_elements = explode($separator, $path);
		
    if(dir :: _has_win32_net_prefix($path))
    {
    	array_shift($dir_elements);
    	array_shift($dir_elements);
    	$dir_elements[0] = WIN32_NET_PREFIX . $dir_elements[0];
    }
    	
		return $dir_elements;
  }
    
	function rm($dir)
	{
		$dir = dir :: clean_path($dir);
		
		if(!is_dir($dir))
			return;
		
		if($current_dir = @opendir($dir))
		{
			$separator = dir :: separator();
			
			while($entryname = readdir($current_dir))
			{
				if(is_dir("{$dir}{$separator}{$entryname}") && ($entryname != "." && $entryname!=".."))
					deldir("{$dir}{$separator}{$entryname}");
				elseif($entryname != "." && $entryname!="..")
					unlink("{$dir}{$separator}{$entryname}");
			}
			closedir($current_dir);
			rmdir($dir);
		}
	}
	
	function cp($src, $dest)
	{ 		
		dir :: mkdir($dest, 0777);
		$arr = dir :: ls($src);
		
		$separator = dir :: separator();
		
		foreach ($arr as $fn)
		{
			if($fn)
			{
				$fl = "{$src}{$separator}{$fn}";
				$flto = "{$dest}{$separator}{$fn}";
				if(is_dir($fl)) 
					dir :: cp($fl, $flto);
				else 
					copy(dir :: clean_path($fl), dir :: clean_path($flto));
			}
		}
	}
	
	function ls($wh)
	{
		$files = '';
		$wh = dir :: clean_path($wh);
		if($handle = opendir($wh)) 
		{
			while(($file = readdir($handle)) !== false) 
			{ 
				if($file != "." && $file != ".." ) 
				{ 
					if(!$files) 
						$files = "$file";
					else 
						$files = "$file\n$files"; 
				} 
			}
			closedir($handle); 
		}
		return explode("\n", $files);
	}

  /*
   return the separator used between directories and files according to $type.

   Type can be one of the following:
   - DIR_SEPARATOR_LOCAL - Returns whatever is applicable for the current machine.
   - DIR_SEPARATOR_UNIX  - Returns a /
   - DIR_SEPARATOR_DOS   - Returns a \
  */
  function separator($type=DIR_SEPARATOR_LOCAL)
  {
    switch ($type)
    {
      case DIR_SEPARATOR_LOCAL:
      	return sys :: file_separator();
      case DIR_SEPARATOR_UNIX:
      	return '/';
      case DIR_SEPARATOR_DOS:
      	return "\\";
    }
    return null;
  }

  /*
   Converts any directory separators found in $path, in both unix and dos style, into
   the separator type specified by $to_type and returns it.
  */
  function convert_separators($path, $to_type = DIR_SEPARATOR_UNIX)
  {
    $separator = dir :: separator($to_type);
    return preg_replace("#[/\\\\]#", $separator, $path);
  }

  /*
   Removes all unneeded directory separators and resolves any "."s and ".."s found in $path.

   For instance: "var/../lib/db" becomes "lib/db", while "../site/var" will not be changed.
   Will also convert separators
  */
  function clean_path($path, $to_type=DIR_SEPARATOR_LOCAL)
  {
    $path = dir :: convert_separators($path, $to_type);
    $separator = dir :: separator($to_type);
		
		$path = dir :: _normalize_separators($path, $separator);
		        
    $path_elements= explode($separator, $path);
    $newpath_elements= array();
    
    foreach ($path_elements as $path_element)
    {
      if ( $path_element == '.' )
      	continue;
      if ( $path_element == '..' &&
           count( $newpath_elements) > 0 )
      	array_pop( $newpath_elements);
      else
      	$newpath_elements[] = $path_element;
    }
    if ( count( $newpath_elements) == 0 )
    	$newpath_elements[] = '.';
    	
    $path = implode($separator, $newpath_elements);
    return $path;
  }
  
  function _normalize_separators($path, $separator)
  {
  	$clean_path = preg_replace( "#$separator$separator+#", $separator, $path);
  	  	
    if(dir :: _has_win32_net_prefix($path))
    	$clean_path = '\\' . $clean_path;
    
    return $clean_path;
  }
  
  function _has_win32_net_prefix($path)
  {
    if(sys :: os_type() == 'win32' && strlen($path) > 2)
    {
    	return (substr($path, 0, 2) == WIN32_NET_PREFIX);
  	}
  	return false;
  }

  /*
   Creates a path out of all the dir and file items in the array $names
   with correct separators in between them.
   It will also remove unneeded separators.
   $type is used to determine the separator type, see dir::separator.
   If $include_end_separator is true then it will make sure that the path ends with a
   separator if false it make sure there are no end separator.
  */
  function path( $names, $include_end_separator = false, $type = DIR_SEPARATOR_UNIX )
  {
    $separator = dir :: separator( $type );
    $path = implode( $separator, $names );
    $path = dir :: clean_path( $path, $type );
    
    $has_end_separator = (strlen( $path ) > 0 && $path[strlen( $path ) - 1] == $separator);
                     
    if ( $include_end_separator && !$has_end_separator )
    	$path .= $separator;
    elseif ( !$include_end_separator && $has_end_separator )
    	$path = substr( $path, 0, strlen( $path ) - 1 );
    	
    return $path;
  }
  
	function walk_dir($dir, $function_def, $params=array())
	{
		static $separator = '';
		
		if(!$separator)
			$separator = dir :: separator();
		
		if(is_dir($dir))
		{
			$handle = opendir($dir);
			
			while (($file = readdir($handle))!==false) 
			{
				if (($file != '.') && ($file != '..')) 
				{
					if (is_dir($dir . $separator . $file))
						dir :: walk_dir($dir . $separator . $file . $separator, $function_def, $params);

					call_user_func_array($function_def, array('dir' => $dir, 'file' => $file, 'params' => $params));
				}
			}
			closedir($handle); 
		}
	}
}
?>