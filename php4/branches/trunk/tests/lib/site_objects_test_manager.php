<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
class SiteObjectsTestManager
{
  function addTestCasesWithLoader(&$group, $loader, $tester_postfix='')
  {
    $manager =& new SiteObjectsTestManager();

    foreach($loader->get_classes_list() as $site_object_class)
    {
      $group->addTestCase($manager->_getSiteObjectTestCase($site_object_class, $tester_postfix));
    }
  }

  function &_getSiteObjectTestCase($site_object_class, $tester_postfix = '')
  {
    if($tester_postfix)
    {
      $tester_postfix = '_' . $tester_postfix;
    }

    if(file_exists(PROJECT_DIR . '/tests/cases/site_objects_testers/' . $site_object_class . $tester_postfix . '_tester.class.php'))
    {
      include_once(PROJECT_DIR . '/tests/cases/site_objects_testers/' . $site_object_class . $tester_postfix . '_tester.class.php');
      $tester_name = $site_object_class . $tester_postfix . '_tester';
      $test_case =& new $tester_name($site_object_class);
    }
    elseif(file_exists(LIMB_DIR . '/tests/cases/site_objects_testers/' . $site_object_class . $tester_postfix . '_tester.class.php'))
    {
      include_once(LIMB_DIR . '/tests/cases/site_objects_testers/' . $site_object_class . $tester_postfix . '_tester.class.php');
      $tester_name = $site_object_class . $tester_postfix . '_tester';
      $test_case =& new $tester_name($site_object_class);
    }
    else
    {
      $site_object = site_object_factory :: create($site_object_class);

      if (is_subclass_of($site_object, 'content_object'))
      {
        $tester_name = 'content_object' . $tester_postfix . '_tester';
        include_once(LIMB_DIR . '/tests/cases/site_objects_testers/' . $tester_name . '.class.php');
        $test_case =& new $tester_name($site_object_class);
      }
      else
      {
        $tester_name = 'site_object' . $tester_postfix . '_tester';
        include_once(LIMB_DIR . '/tests/cases/site_objects_testers/' . $tester_name . '.class.php');
        $test_case =& new $tester_name($site_object_class);
      }
    }

    return $test_case;
  }

}

?>