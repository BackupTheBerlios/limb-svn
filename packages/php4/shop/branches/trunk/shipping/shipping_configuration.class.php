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
require_once(LIMB_DIR . 'class/core/object.class.php'); 

class shipping_configuration extends object
{
  function get_hash()
  {
    return md5(serialize($this->_attributes->export()));
  }
  
	function get_zip_from()
	{
		return $this->get('zip_from');
	}
  
	function set_zip_from($zip)
	{
		return $this->set('zip_from', $zip);
	}

	function get_zip_to()
	{
		return $this->get('zip_to');
	}
  
	function set_zip_to($zip)
	{
		return $this->set('zip_to', $zip);
	}

	function get_country_from()
	{
		return $this->get('country_from');
	}
  
	function set_country_from($country)
	{
		return $this->set('country_from', $country);
	}

	function get_country_to()
	{
		return $this->get('country_to');
	}
  
	function set_country_to($country)
	{
		return $this->set('country_to', $country);
	}
	
	function get_declared_value()
	{
		return 1*$this->get('declared_value');
	}
  
	function set_declared_value($declared_value)
	{
		return $this->set('declared_value', 1*$declared_value);
	}

	function get_weight()
	{
		return 1*$this->get('weight');
	}
  
	function set_weight($weight)
	{
		return $this->set('weight', 1*$weight);
	}
	
	function get_weight_unit()
	{
		return $this->get('weight_unit');
	}
  
	function set_weight_unit($unit)
	{
		return $this->set('weight_unit', $unit);
	}
	
	function get_residence()
	{
		return (bool)$this->get('residence');
	}
  
	function set_residence($status = true)
	{
		return $this->set('residence', (bool)$status);
	}
}

?>