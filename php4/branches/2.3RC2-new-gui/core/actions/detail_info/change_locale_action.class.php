<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: create_article_action.class.php 916 2004-11-23 09:14:28Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/actions/detail_info/display_detail_info_action.class.php');

class change_locale_action extends display_detail_info_action
{
  function _valid_perform(&$request, &$response)
  {
    $locale_id = $this->dataspace->get('locale_id');
    $object_data = $this->_load_object_data();
    
    $site_object =& wrap_with_site_object($object_data);
    $site_object->set_locale_id($locale_id);
    $site_object->update(false);

    $request->set_status(REQUEST_STATUS_FORM_SUBMITTED);
    if($request->has_attribute('popup'))
      $response->write(close_popup_response($request)); 
    
    if(!$this->dataspace->get('locale_recursive'))
      return;
    $params = array('depth' => -1,
                    'restrict_by_class' => false
                    );
    $objects = fetch_sub_branch($object_data['path'], 'site_object', $counter, $params);
    foreach ($objects as $object_id => $object_data)
    {
      $site_object =& wrap_with_site_object($object_data);
      $site_object->set_locale_id($locale_id);
      $site_object->update(false);
    }
  }
}
?>