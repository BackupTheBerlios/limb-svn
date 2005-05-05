<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
define('DIR_SEPARATOR_LOCAL', 1);
define('DIR_SEPARATOR_UNIX', 2);
define('DIR_SEPARATOR_DOS', 3);
define('WIN32_NET_PREFIX', '\\\\');

require_once(LIMB_DIR . '/core/lib/system/sys.class.php');

class fs
{
  function safe_write($file, $content, $perm=0664)
  {
    $tmp = tempnam(VAR_DIR, '_');
    $fh = fopen($tmp, 'w');
    fwrite($fh, $content);
    fclose($fh);

    //just for safety
    @flock($file, LOCK_EX);

    if(@rename($tmp, $file) === false)
    {
      //this actually a win32 check
      @unlink($file);
      @rename($tmp, $file);
    }

    @flock($file, LOCK_UN);
    @chmod($file, $perm);
  }

  function is_absolute($path)
  {
    $path = fs :: normalize_path($path);
    $separator = fs :: separator();

    if($path{0} == $separator)
      return true;
    elseif(sys :: is_win32() && preg_match('~^[a-zA-Z]+:~', $path))
      return true;
    else
      return false;
  }

  function dirpath($path)
  {
    $path = fs :: normalize_path($path);

    if (($dir_pos = strrpos($path, fs :: separator())) !== false )
      return substr($path, 0, $dir_pos);

    return $path;
  }

  function mkdir($dir, $perm=0777, $parents=true)
  {
    if(is_dir($dir))
      return true;

    $dir = fs :: normalize_path($dir);

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
    $path = fs :: normalize_path($path);

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
    $path = fs :: normalize_path($path);
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
  function cp($src, $dest, $as_child = false, $include_regex = '', $exclude_regex = '', $include_hidden = false)
  {
    if (!is_dir($src))
    {
      debug :: write_error('no such a directory',
       __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
      array('dir' => $src));

      return false;
    }

    if(!fs :: mkdir($dest))
      return false;

    $src = fs :: normalize_path($src);
    $dest = fs :: normalize_path($dest);
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
    $items = fs :: find($src, 'df', $include_regex, $exclude_regex, false, $include_hidden);

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

          $new_items = fs :: find($full_path, 'df', $include_regex, $exclude_regex, $item, $include_hidden);

          $items = array_merge($items, $new_items);
          $total_items = array_merge($total_items, $new_items);

          unset($new_items);
        }
      }
    }
    if($total_items)
    {
      sort($total_items);
      clearstatcache();
    }

    return $total_items;
  }

  function ls($path)
  {
    if(!is_dir($path))
      return array();

    $files = array();
    $path = fs :: normalize_path($path);
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
    sort($files);
    return $files;
  }

  /*
  * return the separator used between directories and files according to $type.
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
  * converts any directory separators found in $path, in both unix and dos style, into
  * the separator type specified by $to_type and returns it.
  */
  function convert_separators($path, $to_type=DIR_SEPARATOR_UNIX)
  {
    $separator = fs :: separator($to_type);
    return preg_replace("#[/\\\\]#", $separator, $path);
  }

  /*
   Removes all unneeded directory separators and resolves any "."s and ".."s found in $path.

   For instance: "var/../lib/db" becomes "lib/db", while "../site/var" will not be changed.
   Will also convert separators
  */
  function normalize_path($path, $to_type=DIR_SEPARATOR_LOCAL)
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

  //obsolete
  function clean_path($path, $to_type=DIR_SEPARATOR_LOCAL)
  {
    return fs :: normalize_path($path, $to_type);
  }

  function _normalize_separators($path, $separator)
  {
    $normalize_path = preg_replace( "#$separator$separator+#", $separator, $path);

    if(fs :: _has_win32_net_prefix($path))
      $normalize_path = '\\' . $normalize_path;

    return $normalize_path;
  }

  function _has_win32_net_prefix($path)
  {
    if(sys :: is_win32() && strlen($path) > 2)
    {
      return (substr($path, 0, 2) == WIN32_NET_PREFIX);
    }
    return false;
  }

  /*
  * Creates a path out of all the dir and file items in the array $names
  * with correct separators in between them.
  */
  function path($names, $include_end_separator=false, $type=DIR_SEPARATOR_LOCAL)
  {
    $separator = fs :: separator($type);
    $path = implode($separator, $names);
    $path = fs :: normalize_path($path, $type);

    $has_end_separator = (strlen($path) > 0 && $path[strlen($path) - 1] == $separator);

    if ($include_end_separator && !$has_end_separator)
      $path .= $separator;
    elseif (!$include_end_separator && $has_end_separator)
      $path = substr($path, 0, strlen($path) - 1);

    return $path;
  }

  /*
  * Searchs items in the specific folder
  */
  function find($dir, $types = 'dfl', $include_regex = '', $exclude_regex = '', $add_path = true, $include_hidden = false)
  {
    $dir = fs :: normalize_path($dir);
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
        if ($include_regex && !preg_match($include_regex, $element))
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
    sort($items);
    return $items;
  }

  function &recursive_find($path, $types = 'dfl', $include_regex = '', $exclude_regex = '', $add_path = true, $include_hidden = false)
  {
    return fs :: walk_dir($path,
                          array('fs', '_do_recursive_find'),
                          array('types' => $types,
                               'include_regex' => $include_regex,
                               'exclude_regex' => $exclude_regex,
                               'add_path' => $add_path,
                               'include_hidden' => $include_hidden),
                          true);
  }

  function _do_recursive_find($dir, $file, $path, $params, &$return_params)
  {
    if(!is_dir($path))
      return;

    $items = fs :: find($path, $params['types'], $params['include_regex'], $params['exclude_regex'], $params['add_path'], $params['include_hidden']);
    foreach($items as $item)
    {
      $return_params[] = $item;
    }
  }

  function walk_dir($dir, $function_def, $params=array(), $include_first=false)
  {
    $return_params = array();

    $separator = fs :: separator();
    $dir = fs :: normalize_path($dir);
    $dir = fs :: chop($dir);

    $params['separator'] = $separator;

    fs :: _do_walk_dir($dir,
                       $separator,
                       $function_def,
                       $return_params,
                       $params,
                       $include_first);

    return $return_params;
  }

  function _do_walk_dir($item, $separator, $function_def, &$return_params, $params, $include_first, $level=0)
  {
    if($level > 0 || ($level == 0 && $include_first))
      call_user_func_array($function_def, array('dir' => dirname($item),
                                                'file' => basename($item),
                                                'path' => $item,
                                                'params' => $params,
                                                'return_params' => &$return_params));
    if(!is_dir($item))
      return;

    $handle = opendir($item);

    while(($file = readdir($handle)) !== false)
    {
      if (($file == '.') || ($file == '..'))
        continue;

      fs :: _do_walk_dir($item . $separator . $file,
                         $separator,
                         $function_def,
                         $return_params,
                         $params,
                         $level + 1);
    }
    closedir($handle);
  }
}
?>