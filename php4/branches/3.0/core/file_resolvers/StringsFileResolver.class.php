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
require_once(LIMB_DIR . '/core/file_resolvers/FileResolverDecorator.class.php');

class StringsFileResolver extends FileResolverDecorator
{
  function resolve($file_name, $params = array())
  {
    if(!isset($params[0]))
      $locale_id = DEFAULT_CONTENT_LOCALE_ID;
    else
      $locale_id = $params[0];

    return $this->_resolver->resolve('i18n/' . $file_name . '_' . $locale_id . '.ini');
  }
}

?>