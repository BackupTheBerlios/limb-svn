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
require_once(LIMB_DIR . '/class/lib/util/Ini.class.php');

final class Strings
{
  var $_ini_objects = array();
  var $_path_cache = array();
  var $_cache = array();

  function & instance()
  {
    if (!isset($GLOBALS['StringsGlobalInstance']) || !is_a($GLOBALS['StringsGlobalInstance'], 'Strings'))
      $GLOBALS['StringsGlobalInstance'] =& new Strings();

    return $GLOBALS['StringsGlobalInstance'];
  }

  function get($key, $filename='common', $locale_id=null)
  {
    if(!$locale_id)
    {
      if(defined('MANAGEMENT_LOCALE_ID'))
        $locale_id = MANAGEMENT_LOCALE_ID;
      else
        $locale_id = DEFAULT_MANAGEMENT_LOCALE_ID;
    }

    $inst =& Strings :: instance();
    return $inst->_doGet($key, $filename, $locale_id);
  }

  function _doGet($key, $filename, $locale_id)
  {
    $path = $this->_getPath($filename, $locale_id);

    if(isset($this->_cache[$path][$key]))
      return $this->_cache[$path][$key];

    if(isset($this->_ini_objects[$path]))
      $ini = $this->_ini_objects[$path];
    else
    {
      $ini = new Ini($path);
      $this->_ini_objects[$path] = $ini;
    }

    if($value = $ini->getOption($key, 'constants'))
      $this->_cache[$path][$key] = $value;

    return $value;
  }

  function _getPath($file_name, $locale_id)
  {
    if(isset($this->_path_cache[$file_name][$locale_id]))
      return $this->_path_cache[$file_name][$locale_id];

    resolveHandle($resolver =& getFileResolver('strings'));
    $path = $resolver->resolve($file_name, array($locale_id));

    $this->_path_cache[$file_name][$locale_id] = $path;

    return $path;
  }
}

?>