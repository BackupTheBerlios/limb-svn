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
//inspired by EZpublish(http://ez.no) ini class

require_once(LIMB_DIR . '/core/system/Fs.class.php');
require_once(LIMB_DIR . '/core/error/Debug.class.php');

class Ini
{
  // Variable to store the ini file values.
  var $group_values;
  // Stores the file path
  var $file_path;
  // Stores the path and file_path of the cache file
  var $cache_file;

  var $cache_dir = '';

  var $charset = 'utf8';

  function Ini($file_path, $use_cache = null)
  {
    if ($use_cache === null)
      $use_cache = $this->isCacheEnabled();

    $this->file_path = $file_path;
    $this->use_cache = $use_cache;
    $this->cache_dir = VAR_DIR . '/ini/';

    $this->load();
  }

  function getOverrideFile()
  {
    if(file_exists($this->file_path . '.override'))
      return $this->file_path . '.override';
    else
      return false;
  }

  function getCacheFile()
  {
    return $this->cache_file;
  }

  function getCharset()
  {
    return $this->charset;
  }

  // returns the file_path
  function getOriginalFile()
  {
    return $this->file_path;
  }

  // returns true if INI cache is enabled globally, the default value is true.
  function isCacheEnabled()
  {
    return (!defined('INI_CACHING_ENABLED') ||  (defined('INI_CACHING_ENABLED') &&  constant('INI_CACHING_ENABLED')));
  }

  /*
   Tries to load the ini file specified in the constructor or instance() function.
   If cache files should be used and a cache file is found it loads that instead.
  */
  function load()
  {
    if(!file_exists($this->file_path))
      return throw_error(new FileNotFoundException('ini file not found', $this->file_path));

    if ($this->use_cache)
      $this->_loadCache();
    else
      $this->_parse($this->file_path);
  }

  /*
    Will load a cached version of the ini file if it exists,
    if not it will _parse the original file and create the cache file.
  */
  function _loadCache()
  {
    $this->reset();

    $cache_dir = $this->cache_dir;

    Fs :: mkdir($cache_dir);

    if(catch_error('IOException', $e))
    {
      Debug :: writeWarning('could not create cache directory for ini',
      __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
      array('cache_dir' => $cache_dir));

      $this->_parse();
      return;
    }

    if($override_file = $this->getOverrideFile())
      $this->cache_file = $this->cache_dir . md5($override_file) . '.php';
    else
      $this->cache_file = $this->cache_dir . md5($this->file_path) . '.php';

    if ($this->_isCacheValid())
    {
      $charset = null;
      $group_values = array();

      include($this->cache_file);

      $this->charset = $charset;
      $this->group_values = $group_values;
      unset($group_values);
    }
    else
    {
      $this->_parse();
      $this->_saveCache();
    }
  }

  function _isCacheValid()
  {
    if(!file_exists($this->cache_file))
      return false;

    $override_file = $this->getOverrideFile();

    if (filemtime($this->cache_file) > filemtime($this->file_path))
    {
      if($override_file &&  filemtime($this->cache_file) < filemtime($override_file))
        return false;
      else
        return true;
    }

    return false;
  }

  /*
   Stores the content of the INI object to the cache file
  */
  function _saveCache()
  {
    if (is_array($this->group_values))
    {
      $fp = @fopen($this->cache_file, 'w+');
      if ($fp === false)
      {
        Debug::writeError("Couldn't create cache file '{$this->cache_file}', perhaps wrong permissions",
        __FILE__ . ' : ' . __LINE__ . ' : ' . __FUNCTION__);
        return;
      }

      fwrite($fp, "<?php\n");
      fwrite($fp, '$charset = "' . $this->charset . '";' . "\n");

      fwrite($fp, '$group_values = ' . var_export($this->group_values, true) . ";\n");

      fwrite($fp, "\n?>");
      fclose($fp);
    }
  }

  /*
    Parses either the override ini file or the standard file and then the append
    override file if it exists.
   */
  function _parse()
  {
    $this->reset();

    $this->_parseFileContents($this->file_path);

    if($override_file = $this->getOverrideFile())
      $this->_parseFileContents($override_file);
  }

  function _parseFileContents($file_path)
  {
    $fp = @fopen($file_path, 'r');
    if (!$fp)
      return false;

    $size = filesize($file_path);

    if($size == 0)
        return;

    $contents = fread($fp, $size);
    fclose($fp);

    $this->_parseString($contents);
  }

  function _parseString(&$contents)
  {
    $lines =& preg_split("#\r\n|\r|\n#", $contents);
    unset($contents);

    if ($lines === false)
    {
      Debug::writeError("'{$this->file_path}' is empty", __FILE__ . ' : ' . __LINE__ . ' : ' . __FUNCTION__);
      return false;
    }

    $current_group = 'default';

    if (count($lines) == 0)
      return false;

    // check for charset
    if (preg_match("/#charset[^=]*=(.+)/", $lines[0], $match))
    {
      $this->charset = trim($match[1]);
    }

    foreach ($lines as $line)
    {
      if (($line = trim($line)) == '')
        continue;
      // removing comments after #, not after # inside ""

      $line = preg_replace('/([^"#]+|"(.*?)")|(#[^#]*)/', "\\1", $line);
      // check for new group
      if (preg_match("#^\[(.+)\]\s*$#", $line, $new_group_name_array))
      {
        $new_group_name = trim($new_group_name_array[1]);
        $current_group = $this->_parseConstants($new_group_name);

        if(!isset($this->group_values[$current_group]))
          $this->group_values[$current_group] = array();
        continue;
      }
      // check for variable
      if (preg_match("#^([a-zA-Z0-9_-]+)(\[([a-zA-Z0-9_-]*)\]){0,1}(\s*)=(.*)$#", $line, $value_array))
      {
        $var_name = trim($value_array[1]);

        $var_value = trim($value_array[5]);

        if (preg_match('/^"(.*)"$/', $var_value, $m))
          $var_value = $m[1];

        $var_value = $this->_parseConstants($var_value);

        if ($value_array[2])//check for array []
        {
          if ($value_array[3]) //check for hashed array ['test']
          {
            $key_name = $value_array[3];

            if (isset($this->group_values[$current_group][$var_name]) &&
                is_array($this->group_values[$current_group][$var_name]))
              $this->group_values[$current_group][$var_name][$key_name] = $var_value;
            else
              $this->group_values[$current_group][$var_name] = array($key_name => $var_value);
          }
          else
          {
            if (isset($this->group_values[$current_group][$var_name]) &&
                is_array($this->group_values[$current_group][$var_name]))
              $this->group_values[$current_group][$var_name][] = $var_value;
            else
              $this->group_values[$current_group][$var_name] = array($var_value);
          }
        }
        else
        {
          $this->group_values[$current_group][$var_name] = $var_value;
        }
      }
    }
  }

  function _parseConstants($value)
  {
    return preg_replace('~\{([^\}]+)\}~e', "constant('\\1')", $value);
  }

  // removes the cache file if it exists.
  function resetCache()
  {
    if (file_exists($this->cache_file))
      unlink($this->cache_file);
  }

  /*
   Removes all read data from .ini files.
  */
  function reset()
  {
    $this->group_values = array();
  }

  /*
    Reads a variable from the ini file.
    false is returned if the variable was not found.
  */
  function getOption($var_name, $group_name = 'default')
  {
    if (!isset($this->group_values[$group_name]))
    {
      Debug::writeNotice('undefined group',
        __FILE__ . ' : ' . __LINE__ . ' : ' . __FUNCTION__,
        array('ini' => $this->file_path,
          'group' => $group_name,
          'option' => $var_name)
        );
    }
    elseif (isset($this->group_values[$group_name][$var_name]))
    {
      return $this->group_values[$group_name][$var_name];
    }
    else
    {
      Debug::writeNotice('undefined option',
        __FILE__ . ' : ' . __LINE__ . ' : ' . __FUNCTION__,
        array('ini' => $this->file_path,
          'group' => $group_name,
          'option' => $var_name)
        );
    }

    return '';
  }

  /*
    Reads a variable from the ini file and puts it in the parameter $variable.
    $variable is not modified if the variable does not exist
  */
  function assignOption(&$variable, $var_name, $group_name = 'default')
  {
    if (!$this->hasOption($var_name, $group_name))
      return false;

    $variable = $this->getOption($var_name, $group_name);
    return true;
  }

  /*
    Checks if a variable is set. Returns true if the variable exists, false if not.
  */
  function hasOption($var_name, $group_name = 'default')
  {
    return isset($this->group_values[$group_name][$var_name]);
  }
  // Checks if group $group_name is set. Returns true if the group exists, false if not.
  function hasGroup($group_name)
  {
    return isset($this->group_values[$group_name]);
  }
  // Fetches a variable group and returns it as an associative array.
  function getGroup($group_name)
  {
    if (isset($this->group_values[$group_name]))
      return $this->group_values[$group_name];

    Debug::writeNotice('undefined group',
      __FILE__ . ' : ' . __LINE__ . ' : ' . __FUNCTION__,
      array('ini' => $this->file_path,
        'group' => $group_name
        )
      );
    return null;
  }

  // Returns group_values, which is a nicely named Array
  function getAll()
  {
    return $this->group_values;
  }
}

?>
