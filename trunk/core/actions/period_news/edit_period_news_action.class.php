<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: edit_period_news_action.class.php 21 2004-02-29 18:59:25Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/actions/form_edit_site_object_action.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/locale_date_rule.class.php');

class edit_period_news_action extends form_edit_site_object_action
{
	function edit_period_news_action()
	{
		$definition = array(
			'site_object' => 'period_news_object',
			'datamap' => array(
				'title' => 'title',
				'annotation' => 'annotation',
				'news_content' => 'content',
				'news_date' => 'news_date',
				'start_date' => 'start_date',
				'finish_date' => 'finish_date',
			)
		);

		parent :: form_edit_site_object_action('news_form', $definition);
	}
	
	function _init_validator()
	{
		parent :: _init_validator();
		
		$this->validator->add_rule(new required_rule('title'));
		$this->validator->add_rule(new required_rule('annotation'));
		$this->validator->add_rule(new required_rule('news_date'));
		$this->validator->add_rule(new locale_date_rule('news_date'));
		$this->validator->add_rule(new required_rule('start_date'));
		$this->validator->add_rule(new required_rule('finish_date'));
		$this->validator->add_rule(new locale_date_rule('start_date'));
		$this->validator->add_rule(new locale_date_rule('finish_date'));
	}
}

?>