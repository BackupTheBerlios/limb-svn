<?php

require_once(LIMB_DIR . '/core/template/tags/form/control_tag.class.php');

class button_tag_info
{
	var $tag = 'button';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'button_tag';
} 

register_tag(new button_tag_info());

/**
* Compile time component for button tags
*/
class button_tag extends control_tag
{
	var $runtime_component_path = '/core/template/components/form/button_component';
} 

?>