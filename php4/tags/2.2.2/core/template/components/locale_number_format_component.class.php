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
require_once(LIMB_DIR . 'core/lib/i18n/locale.class.php');

class locale_number_format_component extends component
{
	function format($value)
	{
	  $locale =& locale :: instance();
	  
	  if(!isset($this->attributes['fract_digits']) || !$this->attributes['fract_digits'])
	    $fract_digits = $locale->fract_digits;
	  else
	    $fract_digits = (int)$this->attributes['fract_digits'];
	  
	  if(!isset($this->attributes['decimal_symbol']) || !$this->attributes['decimal_symbol'])
	    $decimal_symbol = $locale->decimal_symbol;
	  else
	    $decimal_symbol = $this->attributes['dec_point'];

	  if(!isset($this->attributes['thousand_separator']) || !$this->attributes['thousand_separator'])
	    $thousand_separator = $locale->thousand_separator;
	  else
	    $thousand_separator = $this->attributes['thousand_separator'];

		return number_format($value, $fract_digits, $decimal_symbol, $thousand_separator);
	}
	
} 

?>