<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
//inspired by EZpublish(http://ez.no), dir class

require_once(LIMB_DIR . '/class/lib/system/sys.class.php');

class fs
{
  const DIR_SEPARATOR_LOCAL   = 1;
  const DIR_SEPARATOR_UNIX    = 2;
  const DIR_SEPARATOR_DOS     = 3;

  const WIN32_NET_PREFIX      = '\\\\';

  static public function dirpath($path)
  {
    $path = self :: clean_path($path);

    if (($dir_pos = strrpos($path, self :: separator())) !== false )
      return substr($path, 0, $dir_pos);

    return $path;
  }

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

  /*
   Creates the directory $dir with permissions $perm.
   If $parents is true it will create any missing parent directories,
   just like 'mkdir -p'.
  */
  static public function mkdir($dir, $perm=0777, $parents=true)
  {
    $dir = self :: clean_path($dir);

    if(is_dir($dir))
      return;

    if(!$parents)
    {
      self :: _do_mkdir($dir, $perm);
      return;
    }

    $separator = self :: separator();

    $path_elements = self :: explode_path($dir);

    if(count($path_elements) == 0)
      return;

    $index = self :: _get_first_existent_path_index($path_elements, $separator);

    if($index === false)
    {
      throw new IOException('cant find first existent path', array('dir' => $dir));
    }

    $offset_path = '';
    for($i=0; $i < $index; $i++)
    {
      $offset_path .= $path_elements[$i] . $separator;
    }

    for($i=$index; $i < count($path_elements); $i++)
    {
      $offset_path .= $path_elements[$i] . $separator;
      self :: _do_mkdir($offset_path, $perm);
    }
  }

  static protected function _get_first_existent_path_index($path_elements, $separator)
  {
    for($i=count($path_elements); $i > 0; $i--)
    {
      $path = implode($separator, $path_elements);

      if(is_dir($path))
        return $i;

      array_pop($path_elements);
    }

    if(self :: is_absolute($path))
      return false;
    else
      return 0;
  }

  /*
   Creates the directory $dir with permission $perm.
  */
  static protected function _do_mkdir($dir, $perm)
  {
    if(is_dir($dir))
      return;

    if(self :: _has_win32_net_prefix($dir))
    {
      debug :: write_notice('win32 net path - cant check if it exists',
      __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
      array('dir' => $dir));

      return;
    }

    $oldumask = umask(0);
    if(!mkdir($dir, $perm))
    {
      umask($oldumask);
      throw new IOException('failed to create directory', array('dir' => $dir));
    }

    umask($oldumask);
  }

  static public function explode_path($path)
  {
    $path = self :: clean_path($path);

    $separator = self :: separator();

    $dir_elements = explode($separator, $path);

    if(sizeof($dir_elements) > 1 && $dir_elements[sizeof($dir_elements)-1] === '')
      array_pop($dir_elements);

    if(self :: _has_win32_net_prefix($path))
    {
      array_shift($dir_elements);
      array_shift($dir_elements);
      $dir_elements[0] = self :: WIN32_NET_PREFIX . $dir_elements[0];
    }

    return $dir_elements;
  }

  static public function chop($path)
  {
    $path = self :: clean_path($path);
    if(substr($path, -1) == self :: separator())
      $path = substr($path, 0, -1);

    return $path;
  }

  static public function rm($dir)
  {
    self :: _do_rm(self :: chop($dir), self :: separator());
    clearstatcache();
  }

  static protected function _do_rm($dir, $separator)
  {
    if (is_dir($dir) && ($handle = opendir($dir)))
    {
      while(($file = readdir($handle)) !== false)
      {
        if(( $file == '.' ) || ( $file == '..' ))
          continue;

        if(is_dir( $dir . $separator . $file))
          self :: _do_rm($dir . $separator . $file, $separator);
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
  static public function cp($src, $dest, $as_child = false, $exclude_regex = '', $include_hidden = false)
  {
    $src = self :: clean_path($src);
    $dest = self :: clean_path($dest);

    if (!is_dir($src))
      throw new IOException('no such a directory', array('dir' => $src));

    self :: mkdir($dest);

    $separator = self :: separator();

    if ($as_child)
    {
      $separator_regex = preg_quote($separator);
      if (preg_match( "#^.+{$separator_regex}([^{$separator_regex}]+)$#", $src, $matches))
      {
        self :: _do_mkdir($dest . $separator . $matches[1], 0777);
        $dest .= $separator . $matches[1];
      }
      else
        return false;
    }
    $items = self :: find_subitems($src, 'df', $exclude_regex, false, $include_hidden);

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
          self :: _do_mkdir($dest . $separator . $item, 0777);

          $new_items = self :: find_subitems($full_path, 'df', $exclude_regex, $item, $include_hidden);

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

  static public function ls($path)
  {
    if(!is_dir($path))
      return array();

    $files = array();
    $path = self :: clean_path($path);
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
  */
  static public function separator($type = self :: DIR_SEPARATOR_LOCAL)
  {
    switch ($type)
    {
      case self :: DIR_SEPARATOR_LOCAL:
        return sys :: file_separator();
      case self :: DIR_SEPARATOR_UNIX:
        return '/';
      case self :: DIR_SEPARATOR_DOS:
        return "\\";
    }
    return null;
  }

  /*
   Converts any directory separators found in $path, in both unix and dos style, into
   the separator type specified by $to_type and returns it.
  */
  static public function convert_separators($path, $to_type = self :: DIR_SEPARATOR_UNIX)
  {
    $separator = self :: separator($to_type);
    return preg_replace("#[/\\\\]#", $separator, $path);
  }

  /*
   Removes all unneeded directory separators and resolves any "."s and ".."s found in $path.

   For instance: "var/../lib/db" becomes "lib/db", while "../site/var" will not be changed.
   Will also convert separators
  */
  static public function clean_path($path, $to_type = self :: DIR_SEPARATOR_LOCAL)
  {
    $path = self :: convert_separators($path, $to_type);
    $separator = self :: separator($to_type);

    $path = self :: _normalize_separators($path, $separator);

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

  static public function is_path_relative($file_path, $os_type = null)
  {
    return !fs :: is_path_absolute($file_path, $os_type);
  }

  static public function is_path_absolute($file_path, $os_type = null)
  {
    if($os_type === null)
      $os_type = sys :: os_type();

    if($os_type == 'win32')
      return preg_match('/^[a-zA-Z]+?:/', $file_path);
    else
    {
      $piece = substr($file_path, 0, 1);
      return ($piece  == '/' || $piece  == '\\');
    }
  }

  static protected function _normalize_separators($path, $separator)
  {
    $clean_path = preg_replace( "#$separator$separator+#", $separator, $path);

    if(self :: _has_win32_net_prefix($path))
      $clean_path = '\\' . $clean_path;

    return $clean_path;
  }

  static protected function _has_win32_net_prefix($path)
  {
    if(sys :: os_type() == 'win32' && strlen($path) > 2)
    {
      return (substr($path, 0, 2) == self :: WIN32_NET_PREFIX);
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
  static public function path($names, $include_end_separator=false, $type = self :: DIR_SEPARATOR_LOCAL)
  {
    $separator = self :: separator($type);
    $path = implode($separator, $names);
    $path = self :: clean_path($path, $type);

    $has_end_separator = (strlen($path) > 0 && $path[strlen($path) - 1] == $separator);

    if ($include_end_separator && !$has_end_separator)
      $path .= $separator;
    elseif (!$include_end_separator && $has_end_separator)
      $path = substr($path, 0, strlen($path) - 1);

    return $path;
  }

  static public function recursive_find($path, $regex)
  {
    $fs = new fs();
    return self :: walk_dir($path, array($fs, '_do_recursive_find'), array('regex' => $regex));
  }

  static protected function _do_recursive_find($dir, $file, $params, &$return_params)
  {
    if(preg_match( '/' . $params['regex'] . '$/', $file))
    {
      $return_params[] = $dir . $params['separator'] . $file;
    }
  }

  static public function walk_dir($dir, $function_def, $params=array())
  {
    $return_params = array();

    $separator = self :: separator();
    $dir = self :: clean_path($dir);
    $dir = self :: chop($dir);

    $params['separator'] = $separator;

    self :: _do_walk_dir($dir, $separator, $function_def, $return_params, $params);

    return $return_params;
  }

  static protected function _do_walk_dir($dir, $separator, $function_def, &$return_params, $params)
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
            self :: _do_walk_dir($dir . $separator . $file, $separator, $function_def, $return_params, $params);
        }
      }
      closedir($handle);
    }
  }

  /*
   Returns sub-items in the specific folder
  */
  static public function find_subitems($dir, $types = 'dfl', $exclude_regex = '', $add_path = true, $include_hidden = false)
  {
    $dir = self :: clean_path($dir);
    $dir = self :: chop($dir);

    $items = array();

    $separator = self :: separator();

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

  static public function find_subdirs($dir, $full_path = false, $include_hidden = false, $exclude_items = false)
  {
    return self :: find_subitems($dir, 'd', $full_path, $include_hidden, $exclude_items);
  }
}
?>