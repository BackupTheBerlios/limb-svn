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
require_once(LIMB_DIR . '/core/lib/i18n/utf8.inc.php');
include_once(LIMB_DIR . '/core/lib/xml/sax_parser.class.php');

class menu_parser extends sax_parser
{
  function menu_parser()
  {
    parent :: sax_parser(null, 'func');
  }

  function parse()
  {
    if($phpbb_sid = session :: get('phpbb_sid'))
      $this->xml_string = utf8_str_replace('%phpbb_sid%', $phpbb_sid, $this->xml_string);

    return parent :: parse();
  }
}
?>