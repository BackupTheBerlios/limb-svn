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
require_once(LIMB_DIR . '/class/core/file_resolvers/FileResolverDecorator.class.php');
require_once(LIMB_DIR . '/class/lib/util/ini_support.inc.php');

class TemplateFileResolver extends FileResolverDecorator
{
  function resolve($file_path, $params = array())
  {
    $toolkit =& Limb :: toolkit();
    $ini =& $toolkit->getINI('common.ini');
    $tmpl_path = $ini->getOption('path', 'Templates');

    $locale = $this->_getLocalePrefix();

    if(file_exists($tmpl_path . $locale . $file_path))
      return $tmpl_path . $locale . $file_path;

    if(file_exists($tmpl_path . $file_path))
      return $tmpl_path . $file_path;

    try
    {
      $resolved_path = $this->_resolver->resolve('design/' . $locale . $file_path, $params);
    }
    catch(FileNotFoundException $e)
    {
      $resolved_path = $this->_resolver->resolve('design/'  . $file_path, $params);
    }

    return $resolved_path;
  }

  function _getLocalePrefix()
  {
    if (defined('CONTENT_LOCALE_ID'))
      $locale = '_' . CONTENT_LOCALE_ID . '/';
    elseif(defined('DEFAULT_CONTENT_LOCALE_ID'))
      $locale = '_' . DEFAULT_CONTENT_LOCALE_ID . '/';
    else
      $locale = '';

    return $locale;
  }
}

?>