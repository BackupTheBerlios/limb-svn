<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/core/filters/intercepting_filter.interface.php');

class site_object_controller_filter implements intercepting_filter
{
  public function run($filter_chain, $request, $response)
  {    
    debug :: add_timing_point('site object controller filter started');
    
    $datasource = Limb :: toolkit()->getDatasource('requested_object_datasource');
    $datasource->set_request($request);    

    $site_object = wrap_with_site_object($datasource->fetch());

    $site_object->get_controller()->process($request);    

    debug :: add_timing_point('site object controller filter finished');

    $filter_chain->next();
  }
  
  protected function _get_controller($behaviour)
  {
    return new site_object_controller($behaviour);
  }
}
?>