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
define( 'MESSAGE_LEVEL_NOTICE', 1 );
define( 'MESSAGE_LEVEL_WARNING', 2 );
define( 'MESSAGE_LEVEL_ERROR', 3 );
define( 'MESSAGE_LEVEL', 4 );

require_once(LIMB_DIR . '/core/lib/system/fs.class.php');
require_once(LIMB_DIR . '/core/lib/session/session.class.php');

class message_box
{
  // String array containing the message_box information
  var $strings = array();

  function message_box()
  {
    $this->strings =& session :: get('strings');
  }

  function reset()
  {
    $this->strings = array();
  }

  function &instance()
  {
    $impl =& $GLOBALS['global_message_box_instance'];

    $class =& get_class( $impl );
    if ($class != 'message_box')
      $impl = new message_box();

    return $impl;
  }

  function write_notice($string, $label='')
  {
    $message_box =& message_box::instance();
    $message_box->write($string, MESSAGE_LEVEL_NOTICE, $label);
  }

  function write_warning($string, $label='')
  {
    $message_box =& message_box::instance();
    $message_box->write($string, MESSAGE_LEVEL_WARNING, $label);
  }

  function write_error($string, $label='')
  {
    $message_box =& message_box::instance();
    $message_box->write($string, MESSAGE_LEVEL_ERROR, $label);
  }

  function write($string, $verbosity_level = MESSAGE_LEVEL_NOTICE, $label='')
  {
    $this->strings[] = array('string' => str_replace("'", "\'", $string),
                             'level' => $verbosity_level,
                             'label' => str_replace("'", "\'", $label)
                             );
  }

  function get_message_strings()
  {
    return $this->strings;
  }

  function parse()
  {
    $message_box =& message_box::instance();

    if(!($strings = $message_box->get_message_strings()))
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

    $message_box->reset();

    return $js;
  }
}

?>