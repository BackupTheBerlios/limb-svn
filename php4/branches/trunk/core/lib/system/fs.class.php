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

class fs
{
  function is_absolute($path)
  {
    $path = fs :: clean_path($path);
    $separator = fs :: separator();

    if($path{0} == $separator)
      return true;
    elseif(sys :: os_type() == 'win32' && preg_match('~^[a-zA-Z]+:~', $path))
      return true;
    else
      return false;
  }

  function dirpath($path)
  {
    $path = fs :: clean_path($path);

    if (($dir_pos = strrpos($path, fs :: separator())) !== false )
      return substr($path, 0, $dir_pos);

    return $path;
  }

  /*
   Creates the directory $dir with permissions $perm.
   If $parents is true it will create any missing parent directories,
   just like 'mkdir -p'.
  */
  function mkdir($dir, $perm=0777, $parents=true)
  {
    $dir = fs :: clean_path($dir);

    if(is_dir($dir))
      return true;

    if(!$parents)
      return fs :: _do_mkdir($dir, $perm);

    $separator = fs :: separator();

    $path_elements = fs :: explode_path($dir);

    if (count($path_elements) == 0)
      return true;

    $index = fs :: _get_first_existing_path_index($path_elements, $separator);

    if($index === false)
    {
      //this is really a fatal case...
      return false;
    }

    $offset_path = '';
    for ($i=0; $i < $index; $i++)
    {
      $offset_path .= $path_elements[$i] . $separator;
    }

    for ($i=$index; $i < count($path_elements); $i++)
    {
      $offset_path .= $path_elements[$i] . $separator;

      if (!fs :: _do_mkdir($offset_path, $perm))
        return false;
    }

    return true;
  }

  function _get_first_existing_path_index($path_elements, $separator)
  {
    for ($i=count($path_elements); $i > 0; $i--)
    {
      $path = implode($separator, $path_elements);

      if(is_dir($path))
        return $i;

      array_pop($path_elements);
    }

    if(!fs :: is_absolute($path))
      return 0;
    else
      return false;
  }

  /*
   Creates the directory $dir with permission $perm.
  */
  function _do_mkdir($dir, $perm)
  {
    if(is_dir($dir))
      return true;

    if(fs :: _has_win32_net_prefix($dir))
    {
      debug :: write_notice('win32 net path - cant check if it exists',
      __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
      array('dir' => $dir));

      return true;
    }

    $oldumask = umask(0);
    if(!mkdir($dir, $perm))
    {
      umask($oldumask);

      debug :: write_error('failed to create directory',
       __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
      array('dir' => $dir));

      return false;
    }
    umask($oldumask);
    return true;
  }

  function explode_path($path)
  {
    $path = fs :: clean_path($path);

    $separator = fs :: separator();

    $dir_elements = explode($separator, $path);

    if(sizeof($dir_elements) > 1 && $dir_elements[sizeof($dir_elements)-1] === '')
      array_pop($dir_elements);

    if(fs :: _has_win32_net_prefix($path))
    {
      array_shift($dir_elements);
      array_shift($dir_elements);
      $dir_elements[0] = WIN32_NET_PREFIX . $dir_elements[0];
    }

    return $dir_elements;
  }

  function chop($path)
  {
    $path = fs :: clean_path($path);
    if(substr($path, -1) == fs :: separator())
      $path = substr($path, 0, -1);

    return $path;
  }

  function rm($dir)
  {
    fs :: _do_rm(fs :: chop($dir), fs :: separator());
    clearstatcache();
  }

  function _do_rm($dir, $separator)
  {
    if (is_dir($dir) && ($handle = opendir($dir)))
    {
      while(($file = readdir($handle)) !== false)
      {
        if(( $file == '.' ) || ( $file == '..' ))
          continue;

        if(is_dir( $dir . $separator . $file))
          fs :: _do_rm($dir . $separator . $file, $separator);
        else
          unlink($dir . $separator . $file);
      }

      closedir($handle);
      rmdir($dir);
    }
  }

  /*
   Copies a directory (and optionally all it's subitems) to another directory.
  */
  function cp($src, $dest, $as_child = false, $exclude_regex = '', $include_hidden = false)
  {
    $src = fs :: clean_path($src);
    $dest = fs :: clean_path($dest);

    if (!is_dir($src))
    {
      debug :: write_error('no such a directory',
       __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
      array('dir' => $src));

      return false;
    }

    if(!fs :: mkdir($dest))
      return false;

    $separator = fs :: separator();

    if ($as_child)
    {
      $separator_regex = preg_quote($separator);
      if (preg_match( "#^.+{$separator_regex}([^{$separator_regex}]+)$#", $src, $matches))
      {
        fs :: _do_mkdir($dest . $separator . $matches[1], 0777);
        $dest .= $separator . $matches[1];
      }
      else
        return false;//???
    }
    $items = fs :: find_subitems($src, 'df', $exclude_regex, false, $include_hidden);

    $total_items = $items;
    while (count($items) > 0)
    {
      $current_items = $items;
      $items = array();
      foreach ($current_items as $item)
      {
        $full_path = $src . $separator . $item;
        if (is_file( $full_path))
          copy($full_path, $dest . $separator . $item);
        elseif (is_dir( $full_path))
        {
          fs :: _do_mkdir($dest . $separator . $item, 0777);

          $new_items = fs :: find_subitems($full_path, 'df', $exclude_regex, $item, $include_hidden);

          $items = array_merge($items, $new_items);
          $total_items = array_merge($total_items, $new_items);

          unset($new_items);
        }
      }
    }
    if($total_items)
      clearstatcache();

    return $total_items;
  }

  function ls($path)
  {
    if(!is_dir($path))
      return array();

    $files = array();
    $path = fs :: clean_path($path);
    if($handle = opendir($path))
    {
      while(($file = readdir($handle)) !== false)
      {
        if($file != '.' && $file != '..' )
        {
          $files[] = $file;
        }
      }
      closedir($handle);
    }
    return $files;
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
    $separator = fs :: separator($to_type);
    return preg_replace("#[/\\\\]#", $separator, $path);
  }

  /*
   Removes all unneeded directory separators and resolves any "."s and ".."s found in $path.

   For instance: "var/../lib/db" becomes "lib/db", while "../site/var" will not be changed.
   Will also convert separators
  */
  function clean_path($path, $to_type=DIR_SEPARATOR_LOCAL)
  {
    $path = fs :: convert_separators($path, $to_type);
    $separator = fs :: separator($to_type);

    $path = fs :: _normalize_separators($path, $separator);

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

    if(fs :: _has_win32_net_prefix($path))
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
   $type is used to determine the separator type, see fs::separator.
   If $include_end_separator is true then it will make sure that the path ends with a
   separator if false it make sure there are no end separator.
  */
  function path($names, $include_end_separator=false, $type=DIR_SEPARATOR_LOCAL)
  {
    $separator = fs :: separator($type);
    $path = implode($separator, $names);
    $path = fs :: clean_path($path, $type);

    $has_end_separator = (strlen($path) > 0 && $path[strlen($path) - 1] == $separator);

    if ($include_end_separator && !$has_end_separator)
      $path .= $separator;
    elseif (!$include_end_separator && $has_end_separator)
      $path = substr($path, 0, strlen($path) - 1);

    return $path;
  }

  function &recursive_find($path, $regex)
  {
    $dir =& new fs();

    return $dir->walk_dir($path, array(&$dir, '_do_recursive_find'), array('regex' => $regex));
  }

  function _do_recursive_find($dir, $file, $params, &$return_params)
  {
    if(preg_match('/' . $params['regex'] . '$/', $file))
    {
      $return_params[] = $dir . $params['separator'] . $file;
    }
  }

  function walk_dir($dir, $function_def, $params=array())
  {
    $return_params = array();

    $separator = fs :: separator();
    $dir = fs :: clean_path($dir);
    $dir = fs :: chop($dir);

    $params['separator'] = $separator;

    fs :: _do_walk_dir($dir, $separator, $function_def, $return_params, $params);

    return $return_params;
  }

  function _do_walk_dir($dir, $separator, $function_def, &$return_params, $params)
  {
    if(is_dir($dir))
    {
      $handle = opendir($dir);

      while(($file = readdir($handle)) !== false)
      {
        if (($file != '.') && ($file != '..'))
        {
          call_user_func_array($function_def, array('dir' => $dir, 'file' => $file, 'params' => $params, 'return_params' => &$return_params));

          if (is_dir($dir . $separator . $file))
            fs :: _do_walk_dir($dir . $separator . $file, $separator, $function_def, $return_params, $params);
        }
      }
      closedir($handle);
    }
  }

  /*
   Returns sub-items in the specific folder
  */
  function find_subitems($dir, $types = 'dfl', $exclude_regex = '', $add_path = true, $include_hidden = false)
  {
    $dir = fs :: clean_path($dir);
    $dir = fs :: chop($dir);

    $items = array();

    $separator = fs :: separator();

    if ($handle = opendir($dir))
    {
      while(($element = readdir($handle)) !== false)
      {
        if ($element == '.' || $element == '..')
          continue;
        if (!$include_hidden && $element[0] == '.')
          continue;
        if ($exclude_regex && preg_match($exclude_regex, $element))
          continue;
        if (is_dir($dir . $separator . $element) && strpos($types, 'd') === false)
          continue;
        if (is_link($dir . $separator . $element) && strpos($types, 'l') === false)
          continue;
        if (is_file( $dir . $separator . $element ) && strpos($types, 'f') === false)
          continue;

        if ($add_path)
        {
          if (is_string($add_path))
            $items[] = $add_path . $separator . $element;
          else
            $items[] = $dir . $separator . $element;
        }
        else
          $items[] = $element;
      }
      closedir($handle);
    }
    return $items;
  }

  function find_subdirs($dir, $full_path = false, $include_hidden = false, $exclude_items = false)
  {
    return fs :: find_subitems($dir, 'd', $full_path, $include_hidden, $exclude_items);
  }
}
?>