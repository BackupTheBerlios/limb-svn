<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: create_news_action.class.php 455 2004-02-16 18:43:12Z mike $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/actions/form_create_site_object_action.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/locale_date_rule.class.php');

class create_news_action extends form_create_site_object_action
{
	function create_news_action()
	{
		$definition = array(
			'site_object' => 'news_object',
			'datamap' => array(
				'title' => 'title',
				'annotation' => 'annotation',
				'news_content' => 'content',
				'news_date' => 'news_date',
			)
		);
		
		parent :: form_create_site_object_action('news_form', $definition);
	}
	
	function _init_validator()
	{
		parent :: _init_validator();

		$this->validator->add_rule(new required_rule('title'));
		$this->validator->add_rule(new required_rule('annotation'));
		$this->validator->add_rule(new required_rule('news_date'));
		$this->validator->add_rule(new locale_date_rule('news_date'));
	}
}

?>