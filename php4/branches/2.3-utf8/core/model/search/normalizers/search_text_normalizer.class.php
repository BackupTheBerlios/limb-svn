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

class search_text_normalizer
{
  function process($content)
  {
    $content = utf8_strtolower($content);
    $content = utf8_str_replace("\n", ' ', $content );
    $content = utf8_str_replace("\t", ' ', $content );
    $content = utf8_str_replace("\r", ' ', $content );

    $search = array (
                "'<script[^>]*?>.*?</script>'si",  	// Strip out javascript
                "'<[\/\!]*?[^<>]*?>'si",           	// Strip out html tags
                "'([\r\n])[\s]+'"                 	// Strip out white space
              );

    $replace = array ('',
                     ' ',
                     ' ');

    $content = preg_replace ($search, $replace, $content);

    $content = preg_replace("#(\.){2,}#", ' ', $content );
    $content = preg_replace("#^\.#", ' ', $content);
    $content = preg_replace("#\s\.#", ' ', $content );
    $content = preg_replace("#\.\s#", ' ', $content);
    $content = preg_replace("#\.$#", ' ', $content);

    //non utf8 chars(�,�)
    $content = preg_replace( "#(\s|^)(\"|'|`|�|�)(\w)#", '\\1\\3', $content);
    $content = preg_replace( "#(\w)(\"|'|`|�|�)(\s|$)#u", '\\1\\3', $content);

    $content = utf8_str_replace("&nbsp;", ' ', $content );
    $content = utf8_str_replace(":", ' ', $content );
    $content = utf8_str_replace(",", ' ', $content );
    $content = utf8_str_replace(";", ' ', $content );
    $content = utf8_str_replace("(", ' ', $content );
    $content = utf8_str_replace(")", ' ', $content );
    $content = utf8_str_replace("-", ' ', $content );
    $content = utf8_str_replace("+", ' ', $content );
    $content = utf8_str_replace("/", ' ', $content );
    $content = utf8_str_replace("!", ' ', $content );
    $content = utf8_str_replace("?", ' ', $content );
    $content = utf8_str_replace("[", ' ', $content );
    $content = utf8_str_replace("]", ' ', $content );
    $content = utf8_str_replace("$", ' ', $content );
    $content = utf8_str_replace("\\", ' ', $content );
    $content = utf8_str_replace("<", ' ', $content );
    $content = utf8_str_replace(">", ' ', $content );
    $content = utf8_str_replace("*", ' ', $content );

    $content = utf8_trim(preg_replace("~\s+~u", ' ', $content));

    return $content;
  }
}


?>