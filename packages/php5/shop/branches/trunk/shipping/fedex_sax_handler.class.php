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

class fedex_sax_handler 
{
  private $tags_list = array();
  private $options = array();
  private $total_options = 0;
  private $services_began = false;
  
  private $service_name_began = false;
  private $service_description_began = false;
  private $service_price_began = false;
  
  private $service_name_expected = false;
  private $service_description_expected = false;
  private $service_price_expected = false;
	 	  
  public function open_handler($parser, $name, $attrs) 
  {	
    if($this->service_name_expected || $this->service_description_expected || $this->service_price_expected)
  	  $this->tags_list[] = strtolower($name);

  	if($this->service_name_began)
  	{
  	  $this->options[$this->total_options]['name'] .= '<' . $name;
  	  $this->options[$this->total_options]['name'] .= $this->write_attributes($attrs);
  	  $this->options[$this->total_options]['name'] .= '>';
  	}

  	if($this->service_description_began)
  	{
  	  $this->options[$this->total_options]['description'] .= '<' . $name;
  	  $this->options[$this->total_options]['description'] .= $this->write_attributes($attrs);
  	  $this->options[$this->total_options]['description'] .= '>';
  	}

  	if($this->service_price_began)
  	{
  	  $this->options[$this->total_options]['price'] .= '<' . $name;
  	  $this->options[$this->total_options]['price'] .= $this->write_attributes($attrs);
  	  $this->options[$this->total_options]['price'] .= '>';
  	}
  	
  	if($this->services_began)  	
  	{  	
  	  if($this->service_name_expected && $this->tags_list == array('td', 'table', 'tr', 'td'))
  	    $this->service_name_began = true;
  	    	    
  	  if($this->service_description_expected && $this->tags_list == array('td', 'table', 'tr', 'td'))
  	    $this->service_description_began = true;
  	    
  	  if($this->service_price_expected && $this->tags_list == array('td', 'table', 'tr', 'td'))
  	  {
  	    $this->service_price_began = true;
  	  }
  	}  	
  }
  
  public function close_handler($parser, $name) 
  { 
    $tag = strtolower($name);
       
    if($this->service_name_began)
    {      
      if($tag == 'td')
      {
        $this->service_name_began = false;
        $this->service_name_expected = false;
        return;
      }
    
      $this->options[$this->total_options]['name'] .= '</' . $name . '>';      
    }

    if($this->service_description_began)
    {
      if($tag == 'td')
      {
        $this->service_description_began = false;
        $this->service_description_expected = false;
        return;
      }
      
      $this->options[$this->total_options]['description'] .= '</' . $name . '>';      
    }

    if($this->service_price_began)
    {
      if($tag == 'td')
      {
        $this->service_price_began = false;
        $this->service_price_expected = false;
        return;
      }
      
      $this->options[$this->total_options]['price'] .= '</' . $name . '>';      
    }    
  }

  public function data_handler($parser, $data) 
  {
    if($this->service_name_began)
    {
      $this->options[$this->total_options]['name'] .= trim($data);
    }
    
    if($this->service_description_began)
    {
      $this->options[$this->total_options]['description'] .= trim($data);
    }

    if($this->service_price_began)
    {
      $this->options[$this->total_options]['price'] .= trim($data);
    }
    
  }

  public function escape_handler($parser, $data) 
  {
    switch(trim($data))
    {
      case '-- TABLE:  Begin Services. --':      
        $this->services_began = true;
      break;
      
      case '-- column:  Service --':
        
        if(!$this->services_began)
          break;

     	  $this->total_options++;
     	  
     	  $this->tags_list = array();
     	  $this->service_name_expected = true;
     	  
     	  $this->options[$this->total_options]['name'] = '';        
     	  $this->options[$this->total_options]['description'] = '';
     	  $this->options[$this->total_options]['price'] = '';
     	        
      break;
      
      case '-- column:  Delivery Time --':
      
        if(!$this->services_began)
          break;      
      
        $this->tags_list = array();
        $this->service_description_expected = true;
        
      break;
      
      case '-- column:  Rate (US$) --':
      
        if(!$this->services_began)
          break;      
      
        $this->tags_list = array();
        $this->service_price_expected = true;
        
      break;      
    }
  }

  public function get_options() 
  { 
  	return $this->options;
  }
  
  public function write_attributes($attributes)
  {
    if(!is_array($attributes))
      return '';
    
    $attrs = '';
    
    foreach ($attributes as $name => $value) 
    {
      $attrs .= ' ' . $name . '="' . $value . '"';
    }
    
    return $attrs;
  }  
}
?>