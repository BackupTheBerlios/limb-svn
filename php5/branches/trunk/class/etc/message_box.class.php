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
require_once(LIMB_DIR . 'class/lib/system/fs.class.php');
require_once(LIMB_DIR . 'class/core/session.class.php');

class message_box
{
  const NOTICE  = 1;
  const WARNING = 2;
  const ERROR   = 3;
  
  static protected $instance = null;
  
  protected $strings = array();
  
  function message_box()
  {
  	$this->strings = session :: get('strings');
  }

  public static function reset()
  {
    self :: instance()->strings = array();
  }
  
	static function instance()
	{
    if (!self :: $instance)
      self :: $instance = new message_box();

    return self :: $instance;	
	}
  
  static function write_notice($string, $label='')
  {
    self :: instance()->write($string, self :: NOTICE, $label);
  }

  static function write_warning($string, $label='')
  {
    self :: instance()->write($string, self :: WARNING, $label);
  }

  static function write_error($string, $label='')
  {
    self :: instance()->write($string, self :: ERROR, $label);
  }

  public function write($string, $verbosity_level = self :: NOTICE, $label='')
  {
  	$this->strings[] = array(
  																			'string' => str_replace("'", "\'", $string),
  																			'level' => $verbosity_level,
  																			'label' => str_replace("'", "\'", $label)
  	);
  }
  
  protected function _get_message_strings()
  {
  	return $this->strings;
	}

  /*
    fetches the message_box report
  */
  static function parse()
  {
    if(!($strings = self :: instance()->_get_message_strings()))
    	return '';
    
    $js_function = "
						function show_message_boxes( message_strings )
						{
							for(i=0; i<message_strings.length; i++)
							{
								arr = message_strings[i];
							  alert(arr['string']);
							}
						}";
						
		$js = '';
    $i = 0;
		foreach($strings as $id => $data)
		{	
			$js .= "\nmessage_strings[$i] = new Array();
			
							message_strings[$i]['label'] = '" . addslashes($data['label']) . "';
							message_strings[$i]['string'] = '" . addslashes($data['string']) . "';
							message_strings[$i]['level'] = '{$data['level']}';
						";
			$i++;
		}
		
		if($js)
	    $js = "<script language='JavaScript'>
						<!--
						$js_function
						
						var message_strings = new Array();
						$js
						show_message_boxes(message_strings);
						//-->
						</script>";
    
    message_box :: reset();
    
    return $js;
  }
}

?>