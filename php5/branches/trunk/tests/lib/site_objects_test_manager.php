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
class SiteObjectsTestManager
{
	function getTestCasesHandlesWithLoader($loader, $tester_postfix='')
	{
	  $manager = new SiteObjectsTestManager();
	  
	  $handles = array();
	  foreach($loader->get_classes_list() as $site_object_class)
	  {
	  	$handles[] = $manager->_getSiteObjectTestCaseHandle($site_object_class, $tester_postfix);
	  }
	  
	  return $handles;
	}
	
	function &_getSiteObjectTestCaseHandle($site_object_class, $tester_postfix = '')
	{
	  if($tester_postfix)
	  {
	    $tester_postfix = '_' . $tester_postfix;
	  } 

		if(file_exists(LIMB_APP_DIR . '/tests/cases/site_objects_testers/' . $site_object_class . $tester_postfix . '_tester.class.php'))
		{		  
		  return array(LIMB_APP_DIR . '/tests/cases/site_objects_testers/' . $site_object_class . $tester_postfix . '_tester', $site_object_class);
		}
		elseif(file_exists(LIMB_DIR . '/tests/cases/site_objects_testers/' . $site_object_class . $tester_postfix . '_tester.class.php'))
		{
		  return array(LIMB_DIR . '/tests/cases/site_objects_testers/' . $site_object_class . $tester_postfix . '_tester', $site_object_class);
		}
		else
		{	
			$site_object = site_object_factory :: create($site_object_class);

	  	if (is_subclass_of($site_object, 'content_object'))
	  	{   	  	
	  	  $tester_name = 'content_object' . $tester_postfix . '_tester';
	  	  return array(LIMB_DIR . '/tests/cases/site_objects_testers/' . $tester_name , $site_object_class);
	  	}
	  	else
	  	{
	  	  $tester_name = 'site_object' . $tester_postfix . '_tester';
	  	  return array(LIMB_DIR . '/tests/cases/site_objects_testers/' . $tester_name , $site_object_class);
	  	}
		}
	}

}

?>