<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
require_once(LIMB_DIR . '/tests/cases/site_objects_testers/content_object_tester.class.php');
require_once(LIMB_DIR . 'core/model/site_objects/subscribe_mail.class.php');

Mock::generatePartial
(
  'subscribe_mail',
  'subscribe_mail_test_version',
  array('_send_mail')
); 


class subscribe_mail_tester extends content_object_tester 
{ 
  function subscribe_mail_tester($class_name) 
  {
  	parent :: content_object_tester($class_name);
  }

  function &_create_site_object()
  {
  	$object = new subscribe_mail_test_version($this);
  	$object->subscribe_mail();
  	
  	$object->setReturnValue('_send_mail', true);
  	
  	return $object;
  }
}

?>