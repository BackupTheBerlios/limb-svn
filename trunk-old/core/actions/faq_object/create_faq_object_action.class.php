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
require_once(LIMB_DIR . 'core/actions/form_create_site_object_action.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/email_rule.class.php');

class create_faq_object_action extends form_create_site_object_action
{
	function create_faq_object_action()
	{
		$definition = array(
			'site_object' => 'faq_object',
			'datamap' => array(
				'question' => 'question',
				'question_author' => 'question_author',
				'question_author_email' => 'question_author_email',
				'answer' => 'answer',
				'answer_author' => 'answer_author',
				'answer_author_email' => 'answer_author_email',
			)
		);
		
		parent :: form_create_site_object_action('create_faq_object', $definition);
	}
	
	function _init_validator()
	{
		parent :: _init_validator();

		$this->validator->add_rule(new required_rule('question'));
		$this->validator->add_rule(new required_rule('answer'));
		$this->validator->add_rule(new email_rule('question_author_email'));
		$this->validator->add_rule(new email_rule('answer_author_email'));
	}
}

?>