<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/lib/system/Fs.class.php');
require_once(LIMB_DIR . '/class/core/session/Session.class.php');

class MessageBox
{
  const NOTICE  = 1;
  const WARNING = 2;
  const ERROR   = 3;

  static protected $instance = null;

  protected $strings = array();

  function messageBox()
  {
    $this->strings = Limb :: toolkit()->getSession()->get('strings');
  }

  public static function reset()
  {
    self :: instance()->strings = array();
  }

  static function instance()
  {
    if (!self :: $instance)
      self :: $instance = new MessageBox();

    return self :: $instance;
  }

  static function writeNotice($string, $label='')
  {
    self :: instance()->write($string, self :: NOTICE, $label);
  }

  static function writeWarning($string, $label='')
  {
    self :: instance()->write($string, self :: WARNING, $label);
  }

  static function writeError($string, $label='')
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

  protected function _getMessageStrings()
  {
    return $this->strings;
  }

  /*
    fetches the message_box report
  */
  static function parse()
  {
    if(!($strings = self :: instance()->_getMessageStrings()))
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
            showMessageBoxes(message_strings);
            //-->
            </script>";

    MessageBox :: reset();

    return $js;
  }
}

?>