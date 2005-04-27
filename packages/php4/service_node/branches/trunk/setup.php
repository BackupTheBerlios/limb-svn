<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: setup.php 913 2004-11-22 12:32:48Z pachanga $
*
***********************************************************************************/

$PACKAGE_NAME = 'LIMB_SERVICE_NODE';

require_once(dirname(__FILE__) . '/request_resolvers/ServiceNodeRequestResolver.class.php');

$toolkit =& Limb :: toolkit();
$toolkit->setRequestResolver('service_node', new ServiceNodeRequestResolver());


require_once(dirname(__FILE__) . '/ServiceNodePackageToolkit.class.php');
$service_node_toolkit = new ServiceNodePackageToolkit();

Limb :: registerToolkit($service_node_toolkit, 'service_node_toolkit');

?>