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
require_once(LIMB_DIR . '/core/system/Fs.class.php');
require_once(LIMB_DIR . '/core/session/Session.class.php');

define('MESSAGE_BOX_NOTICE', 1);
define('MESSAGE_BOX_WARNING', 2);
define('MESSAGE_BOX_ERROR', 3);

class MessageBox
{
  var $strings = array();

  function MessageBox()
  {
    $toolkit =& Limb :: toolkit();
    $session =& $toolkit->getSession();
    $this->strings = $session->get('strings');
  }

  function reset()
  {
    $inst =& MessageBox :: instance();
    $inst->strings = array();
  }

  function & instance()
  {
    if (!isset($GLOBALS['MessageBoxGlobalInstance']) || !is_a($GLOBALS['MessageBoxGlobalInstance'], 'MessageBox'))
      $GLOBALS['MessageBoxGlobalInstance'] =& new MessageBox();

    return $GLOBALS['MessageBoxGlobalInstance'];
  }

  function writeNotice($string, $label='')
  {
    $inst =& MessageBox :: instance();
    $inst->write($string, MESSAGE_BOX_NOTICE, $label);
  }

  function writeWarning($string, $label='')
  {
    $inst =& MessageBox :: instance();
    $inst->write($string, MESSAGE_BOX_WARNING, $label);
  }

  function writeError($string, $label='')
  {
    $inst =& MessageBox :: instance();
    $inst->write($string, MESSAGE_BOX_ERROR, $label);
  }

  function write($string, $verbosity_level = MESSAGE_BOX_NOTICE, $label='')
  {
    $this->strings[] = array(
                                        'string' => str_replace("'", "\'", $string),
                                        'level' => $verbosity_level,
                                        'label' => str_replace("'", "\'", $label)
    );
  }

  function _getMessageStrings()
  {
    return $this->strings;
  }

  /*
    fetches the message_box report
  */
  function parse()
  {
    $inst =& MessageBox :: instance();
    if(!($strings = $inst->_getMessageStrings()))
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