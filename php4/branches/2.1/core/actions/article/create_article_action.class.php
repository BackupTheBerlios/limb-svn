<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: create_document_action.class.php 59 2004-03-22 13:54:41Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . 'core/actions/form_create_site_object_action.class.php');

class create_article_action extends form_create_site_object_action
{
	function create_article_action()
	{
		$definition = array(
			'site_object' => 'article',
			'datamap' => array(
				'article_content' => 'content',
				'annotation' => 'annotation',
				'author' => 'author',
				'source' => 'source',
				'uri' => 'uri',
			)
		);
		
		parent :: form_create_site_object_action('article_form', $definition);
	}
	
	function _init_validator()
	{
		parent :: _init_validator();

		$this->validator->add_rule(new required_rule('title'));
		$this->validator->add_rule(new required_rule('author'));
		$this->validator->add_rule(new required_rule('article_content'));
	}
}

?>