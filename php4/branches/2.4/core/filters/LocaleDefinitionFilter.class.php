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

class LocaleDefinitionFilter// implements InterceptingFilter
{
  function run(&$filter_chain, &$request, &$response)
  {
    $toolkit =& Limb :: toolkit();
    $locale =& $toolkit->getLocale();

    Debug :: addTimingPoint('locale filter started');

    $locale->setlocale();

    $dao =& $toolkit->createDAO('RequestedObjectDAO');

    if(!$node = $dao->mapRequestToNode($request))
    {
      $toolkit->define('CONTENT_LOCALE_ID', DEFAULT_CONTENT_LOCALE_ID);
      $toolkit->define('MANAGEMENT_LOCALE_ID', DEFAULT_CONTENT_LOCALE_ID);

      $filter_chain->next();
      return;
    }

    if($object_locale_id = $this->_findSiteObjectLocaleId($node['object_id']))
      $toolkit->define('CONTENT_LOCALE_ID', $object_locale_id);
    else
      $toolkit->define('CONTENT_LOCALE_ID', DEFAULT_CONTENT_LOCALE_ID);

    $user =& $toolkit->getUser();
    if($user_locale_id = $user->get('locale_id'))
      $toolkit->define('MANAGEMENT_LOCALE_ID', $user_locale_id);
    else
      $toolkit->define('MANAGEMENT_LOCALE_ID', $toolkit->constant('CONTENT_LOCALE_ID'));

    Debug :: addTimingPoint('locale filter finished');

    $filter_chain->next();
  }

  //for mocking
  function _findSiteObjectLocaleId($object_id)
  {
    include_once(LIMB_DIR . '/core/site_objects/SiteObject.class.php');
    return SiteObject :: findObjectLocaleId($object_id);
  }
}
?>