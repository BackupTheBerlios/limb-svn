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
require_once(LIMB_DIR . '/class/lib/system/Sys.class.php');

define('FS_SEPARATOR_LOCAL', 1);
define('FS_SEPARATOR_UNIX', 2);
define('FS_SEPARATOR_DOS',  3);
define('FS_WIN32_NET_PREFIX', '\\\\');

class Fs
{
  function dirpath($path)
  {
    $path = Fs :: cleanPath($path);

    if (($dir_pos = strrpos($path, Fs :: separator())) !== false )
      return substr($path, 0, $dir_pos);

    return $path;
  }

  function isAbsolute($path)
  {
    $path = Fs :: cleanPath($path);
    $separator = Fs :: separator();

    if($path{0} == $separator)
      return true;
    elseif(Sys :: osType() == 'win32' &&  preg_match('~^[a-zA-Z]+:~', $path))
      return true;
    else
      return false;
  }

  /*
   Creates the directory $dir with permissions $perm.
   If $parents is true it will create any missing parent directories,
   just like 'mkdir -p'.
  */
  function mkdir($dir, $perm=0777, $parents=true)
  {
    $dir = Fs :: cleanPath($dir);

    if(is_dir($dir))
      return;

    if(!$parents)
    {
      Fs :: _doMkdir($dir, $perm);
      return;
    }

    $separator = Fs :: separator();

    $path_elements = Fs :: explodePath($dir);

    if(count($path_elements) == 0)
      return;

    $index = Fs :: _getFirstExistentPathIndex($path_elements, $separator);

    if($index === false)
    {
      return new IOException('cant find first existent path', array('dir' => $dir));
    }

    $offset_path = '';
    for($i=0; $i < $index; $i++)
    {
      $offset_path .= $path_elements[$i] . $separator;
    }

    for($i=$index; $i < count($path_elements); $i++)
    {
      $offset_path .= $path_elements[$i] . $separator;
      Fs :: _doMkdir($offset_path, $perm);
    }
  }

  function _getFirstExistentPathIndex($path_elements, $separator)
  {
    for($i=count($path_elements); $i > 0; $i--)
    {
      $path = implode($separator, $path_elements);

      if(is_dir($path))
        return $i;

      array_pop($path_elements);
    }

    if(Fs :: isAbsolute($path))
      return false;
    else
      return 0;
  }

  /*
   Creates the directory $dir with permission $perm.
  */
  function _doMkdir($dir, $perm)
  {
    if(is_dir($dir))
      return;

    if(Fs :: _hasWin32NetPrefix($dir))
    {
      Debug :: writeNotice('win32 net path - cant check if it exists',
      __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
      array('dir' => $dir));

      return;
    }

    $oldumask = umask(0);
    if(!mkdir($dir, $perm))
    {
      umask($oldumask);
      return new IOException('failed to create directory', array('dir' => $dir));
    }

    umask($oldumask);
  }

  function explodePath($path)
  {
    $path = Fs :: cleanPath($path);

    $separator = Fs :: separator();

    $dir_elements = explode($separator, $path);

    if(sizeof($dir_elements) > 1 &&  $dir_elements[sizeof($dir_elements)-1] === '')
      array_pop($dir_elements);

    if(Fs :: _hasWin32NetPrefix($path))
    {
      array_shift($dir_elements);
      array_shift($dir_elements);
      $dir_elements[0] = FS_WIN32_NET_PREFIX . $dir_elements[0];
    }

    return $dir_elements;
  }

  function chop($path)
  {
    $path = Fs :: cleanPath($path);
    if(substr($path, -1) == Fs :: separator())
      $path = substr($path, 0, -1);

    return $path;
  }

  function rm($dir)
  {
    Fs :: _doRm(Fs :: chop($dir), Fs :: separator());
    clearstatcache();
  }

  function _doRm($dir, $separator)
  {
    if (is_dir($dir) &&  ($handle = opendir($dir)))
    {
      while(($file = readdir($handle)) !== false)
      {
        if(( $file == '.' ) ||  ( $file == '..' ))
          continue;

        if(is_dir( $dir . $separator . $file))
          Fs :: _doRm($dir . $separator . $file, $separator);
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
    $src = Fs :: cleanPath($src);
    $dest = Fs :: cleanPath($dest);

    if (!is_dir($src))
      return new IOException('no such a directory', array('dir' => $src));

    Fs :: mkdir($dest);

    $separator = Fs :: separator();

    if ($as_child)
    {
      $separator_regex = preg_quote($separator);
      if (preg_match( "#^.+{$separator_regex}([^{$separator_regex}]+)$#", $src, $matches))
      {
        Fs :: _doMkdir($dest . $separator . $matches[1], 0777);
        $dest .= $separator . $matches[1];
      }
      else
        return false;
    }
    $items = Fs :: findSubitems($src, 'df', $exclude_regex, false, $include_hidden);

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
          Fs :: _doMkdir($dest . $separator . $item, 0777);

          $new_items = Fs :: findSubitems($full_path, 'df', $exclude_regex, $item, $include_hidden);

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
    $path = Fs :: cleanPath($path);
    if($handle = opendir($path))
    {
      while(($file = readdir($handle)) !== false)
      {
        if($file != '.' &&  $file != '..' )
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
  function separator($type = FS_SEPARATOR_LOCAL)
  {
    switch ($type)
    {
      case FS_SEPARATOR_LOCAL:
        return Sys :: fileSeparator();
      case FS_SEPARATOR_UNIX:
        return '/';
      case FS_SEPARATOR_DOS:
        return "\\";
    }
    return null;
  }

  /*
   Converts any directory separators found in $path, in both unix and dos style, into
   the separator type specified by $to_type and returns it.
  */
  function convertSeparators($path, $to_type = FS_SEPARATOR_UNIX)
  {
    $separator = Fs :: separator($to_type);
    return preg_replace("#[/\\\\]#", $separator, $path);
  }

  /*
   Removes all unneeded directory separators and resolves any "."s and ".."s found in $path.

   For instance: "var/../lib/db" becomes "lib/db", while "../site/var" will not be changed.
   Will also convert separators
  */
  function cleanPath($path, $to_type = FS_SEPARATOR_LOCAL)
  {
    $path = Fs :: convertSeparators($path, $to_type);
    $separator = Fs :: separator($to_type);

    $path = Fs :: _normalizeSeparators($path, $separator);

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

  function isPathRelative($file_path, $os_type = null)
  {
    return !Fs :: isPathAbsolute($file_path, $os_type);
  }

  function isPathAbsolute($file_path, $os_type = null)
  {
    if($os_type === null)
      $os_type = Sys :: osType();

    if($os_type == 'win32')
      return preg_match('/^[a-zA-Z]+?:/', $file_path);
    else
    {
      $piece = substr($file_path, 0, 1);
      return ($piece  == '/' ||  $piece  == '\\');
    }
  }

  function _normalizeSeparators($path, $separator)
  {
    $clean_path = preg_replace( "#$separator$separator+#", $separator, $path);

    if(Fs :: _hasWin32NetPrefix($path))
      $clean_path = '\\' . $clean_path;

    return $clean_path;
  }

  function _hasWin32NetPrefix($path)//ugly!!!
  {
    if(Sys :: osType() == 'win32' &&  strlen($path) > 2)
    {
      return (substr($path, 0, 2) == FS_WIN32_NET_PREFIX);
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
  function path($names, $include_end_separator=false, $type = FS_SEPARATOR_LOCAL)
  {
    $separator = Fs :: separator($type);
    $path = implode($separator, $names);
    $path = Fs :: cleanPath($path, $type);

    $has_end_separator = (strlen($path) > 0 &&  $path[strlen($path) - 1] == $separator);

    if ($include_end_separator &&  !$has_end_separator)
      $path .= $separator;
    elseif (!$include_end_separator &&  $has_end_separator)
      $path = substr($path, 0, strlen($path) - 1);

    return $path;
  }

  function recursiveFind($path, $regex)
  {
    $fs = new Fs();
    return Fs :: walkDir($path, array($fs, '_doRecursiveFind'), array('regex' => $regex));
  }

  function _doRecursiveFind($dir, $file, $params, &$return_params)
  {
    if(preg_match( '/' . $params['regex'] . '$/', $file))
    {
      $return_params[] = $dir . $params['separator'] . $file;
    }
  }

  function walkDir($dir, $function_def, $params=array())
  {
    $return_params = array();

    $separator = Fs :: separator();
    $dir = Fs :: cleanPath($dir);
    $dir = Fs :: chop($dir);

    $params['separator'] = $separator;

    Fs :: _doWalkDir($dir, $separator, $function_def, $return_params, $params);

    return $return_params;
  }

  function _doWalkDir($dir, $separator, $function_def, &$return_params, $params)
  {
    if(is_dir($dir))
    {
      $handle = opendir($dir);

      while(($file = readdir($handle)) !== false)
      {
        if (($file != '.') &&  ($file != '..'))
        {
          call_user_func_array($function_def, array('dir' => $dir, 'file' => $file, 'params' => $params, 'return_params' => &$return_params));

          if (is_dir($dir . $separator . $file))
            Fs :: _doWalkDir($dir . $separator . $file, $separator, $function_def, $return_params, $params);
        }
      }
      closedir($handle);
    }
  }

  /*
   Returns sub-items in the specific folder
  */
  function findSubitems($dir, $types = 'dfl', $exclude_regex = '', $add_path = true, $include_hidden = false)
  {
    $dir = Fs :: cleanPath($dir);
    $dir = Fs :: chop($dir);

    $items = array();

    $separator = Fs :: separator();

    if ($handle = opendir($dir))
    {
      while(($element = readdir($handle)) !== false)
      {
        if ($element == '.' ||  $element == '..')
          continue;
        if (!$include_hidden &&  $element[0] == '.')
          continue;
        if ($exclude_regex &&  preg_match($exclude_regex, $element))
          continue;
        if (is_dir($dir . $separator . $element) &&  strpos($types, 'd') === false)
          continue;
        if (is_link($dir . $separator . $element) &&  strpos($types, 'l') === false)
          continue;
        if (is_file( $dir . $separator . $element ) &&  strpos($types, 'f') === false)
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

  function findSubdirs($dir, $full_path = false, $include_hidden = false, $exclude_items = false)
  {
    return Fs :: findSubitems($dir, 'd', $full_path, $include_hidden, $exclude_items);
  }
}
?>