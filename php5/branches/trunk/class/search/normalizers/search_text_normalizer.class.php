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

class search_text_normalizer
{	
	public function process($content)
	{
		$content = strtolower($content);
    $content = str_replace("\n", ' ', $content );
    $content = str_replace("\t", ' ', $content );
    $content = str_replace("\r", ' ', $content );
    
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
    
    $content = preg_replace( "#(\s|^)(\"|'|`|”|“)(\w)#", '\\1\\3', $content);
    $content = preg_replace( "#(\w)(\"|'|`|”|“)(\s|$)#", '\\1\\3', $content);
		
		$content = str_replace("&nbsp;", ' ', $content );
    $content = str_replace(":", ' ', $content );
    $content = str_replace(",", ' ', $content );
    $content = str_replace(";", ' ', $content );
    $content = str_replace("(", ' ', $content );
    $content = str_replace(")", ' ', $content );
    $content = str_replace("-", ' ', $content );
    $content = str_replace("+", ' ', $content );
    $content = str_replace("/", ' ', $content );
    $content = str_replace("!", ' ', $content );
    $content = str_replace("?", ' ', $content );
    $content = str_replace("[", ' ', $content );
    $content = str_replace("]", ' ', $content );
    $content = str_replace("$", ' ', $content );
    $content = str_replace("\\", ' ', $content );
    $content = str_replace("<", ' ', $content );
    $content = str_replace(">", ' ', $content );
    $content = str_replace("*", ' ', $content );

    $content = trim(preg_replace("(\s+)", ' ', $content));

    return $content;
	}
}


?>