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
define('SHIPPING_FEDEX_GROUND_TYPE', 'ground');
define('SHIPPING_FEDEX_EXPRESS_TYPE', 'express');

define('SHIPPING_FEDEX_WEIGHT_UNIT_LB', 'lb');
define('SHIPPING_FEDEX_WEIGHT_UNIT_KG', 'kg');

define('SHIPPING_FEDEX_HOME_SERVER', 'http://www.fedex.com/ratefinder/home');
define('SHIPPING_FEDEX_SHIP_INFO_SERVER', 'http://www.fedex.com/ratefinder/shipInfo');
define('SHIPPING_FEDEX_SERVER_TIMEOUT', 60);
define('SHIPPING_FEDEX_SERVER_COOKIE_FILE', tempnam(VAR_DIR, 'cookie'));
define('SHIPPING_FEDEX_SERVER_USERAGENT', 'Mozilla/4.0 (compatible; MSIE 5.5; Windows 98)');

require_once(LIMB_DIR . '/core/model/shop/shipping/shipping_locator.class.php'); 

class fedex_shipping_locator extends shipping_locator
{
  function _do_get_shipping_options($shipping_configuration)
  {        
    $this->_clean_cookie();
    
    $this->_browse_to_home_page();
    
    $express_html = $this->_get_express_shipping_options_html($shipping_configuration);
      
    $ground_html = $this->_get_ground_shipping_options_html($shipping_configuration);
    
    if($express_html === false)
      $express_options = array();
    else
      $express_options = $this->_parse_html_options($express_html);
          
    if($ground_html === false)
      $ground_options = array();
    else
    {
      $ground_options = $this->_parse_html_options($ground_html);
      
      foreach($ground_options as $key => $value)
      {
        $ground_options[$key]['ground'] = true;
      }
    }
        
    $options = complex_array :: array_merge($express_options, $ground_options);
    
    if(empty($options))
      return false;
    
    return $options;
  }
  
  function _parse_html_options($html)
  {
    include_once(LIMB_DIR . '/core/lib/external/XML_HTMLSax/XML_HTMLSax.php');
    include_once(LIMB_DIR . '/core/model/shop/shipping/fedex_sax_handler.class.php');
    
    $options = array();
    
    $parser =& new XML_HTMLSax();
    $handler =& new fedex_sax_handler();
    
    $parser->set_object($handler);
    
		$parser->set_element_handler('open_handler','close_handler');
		$parser->set_data_handler('data_handler');
		$parser->set_escape_handler('escape_handler');

		$parser->parse($html);
		
		return $this->_process_raw_options($handler->get_options());
  }
  
  function _process_raw_options($raw_options)
  {
		$processed_options = array();
		foreach($raw_options as $data)
		{
		  if(!isset($data['price']) || empty($data['price']))
		    continue;
		  
		  $data['id'] = md5($data['name']);
		  $data['price'] = str_replace('&nbsp;', '', $data['price']);
		  $data['price'] *= 1; //???
		   
		  $processed_options[$data['id']] = $data; 
		}
		
		return $processed_options;
  }    
      
  function _clean_cookie()
  {
    if(is_file(SHIPPING_FEDEX_SERVER_COOKIE_FILE))
      unlink(SHIPPING_FEDEX_SERVER_COOKIE_FILE);    
  }
  
  function _get_express_shipping_options_html($shipping_configuration)
  {
    $data = array();
    $data['shipDate'] = strftime("%m%d%Y");
    $data['packageCount'] = 1;
    $data['origCountry'] = $shipping_configuration->get_country_from();
    $data['destCountry'] = $shipping_configuration->get_country_to();
    $data['origZip'] = $shipping_configuration->get_zip_from();
    $data['destZip'] = $shipping_configuration->get_zip_to();
    $data['shipToResidence'] = $shipping_configuration->get_residence() ? 1 : 0;
    $data['companyType'] = 'Express';
    $data['cc'] = 'US';
    $data['language'] = 'en';
    $data['submitShipInfo'] = 'Continue';
    $data['submitAction'] = '';
    $data['locId'] = '';
    $data['autoDeviceType'] = '';
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, SHIPPING_FEDEX_SHIP_INFO_SERVER);
    curl_setopt($ch, CURLOPT_USERAGENT, SHIPPING_FEDEX_SERVER_USERAGENT);
    curl_setopt($ch, CURLOPT_REFERER, SHIPPING_FEDEX_HOME_SERVER);
    curl_setopt($ch, CURLOPT_FAILONERROR, 1); 
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, SHIPPING_FEDEX_SERVER_TIMEOUT);
    curl_setopt($ch, CURLOPT_COOKIEJAR, SHIPPING_FEDEX_SERVER_COOKIE_FILE);
    curl_setopt($ch, CURLOPT_COOKIEFILE, SHIPPING_FEDEX_SERVER_COOKIE_FILE); 
    curl_setopt($ch, CURLOPT_POST, 1); // set POST method 
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    
    curl_exec($ch);
    
    if(curl_errno($ch) != 0)
    {
      debug :: write_error('curl error',
		  __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
		  array('error' => curl_error($ch)));
		  
		  curl_close($ch);
		  
		  return false;
		}
    
    curl_close($ch);
    
    $data = array();
    $data['packageForm.packageList[0].weightUnit'] = $shipping_configuration->get_weight_unit();
    $data['packageForm.packageList[0].dimUnit'] = 'in';
    $data['packageForm.packageList[0].currencyCode'] = 'USD';
    $data['packageForm.packageList[0].weight'] = $shipping_configuration->get_weight();
    $data['packageForm.packageList[0].packageType'] = '1';
    $data['packageForm.packageList[0].dimLength'] = 'L';
    $data['packageForm.packageList[0].dimWidth'] = 'W';
    $data['packageForm.packageList[0].dimHeight'] = 'H';
    $data['packageForm.packageList[0].declaredValue'] = $shipping_configuration->get_declared_value();
    $data['optionsList[0].optionCode'] = '999';
    $data['optionsList[1].optionCode'] = '4';
    $data['optionsList[2].optionCode'] = '6';
    $data['optionsList[3].optionCode'] = '14';
    $data['optionsList[4].optionCode'] = '9';    
    $data['cc'] = 'US';
    $data['language'] = 'en';
    $data['submitGetRates'] = 'Continue';
    $data['submitAction'] = '';
    $data['groundCOD'] = 'false';
    $data['isExpress'] = 'true';
    $data['isGround'] = 'false';
    $data['locId'] = '';
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, SHIPPING_FEDEX_SHIP_INFO_SERVER);
    curl_setopt($ch, CURLOPT_USERAGENT, SHIPPING_FEDEX_SERVER_USERAGENT);
    curl_setopt($ch, CURLOPT_REFERER, SHIPPING_FEDEX_SHIP_INFO_SERVER);
    curl_setopt($ch, CURLOPT_FAILONERROR, 1); 
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, SHIPPING_FEDEX_SERVER_TIMEOUT);
    curl_setopt($ch, CURLOPT_COOKIEJAR, SHIPPING_FEDEX_SERVER_COOKIE_FILE);
    curl_setopt($ch, CURLOPT_COOKIEFILE, SHIPPING_FEDEX_SERVER_COOKIE_FILE); 
    curl_setopt($ch, CURLOPT_POST, 1); // set POST method 
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    
    $html = curl_exec($ch);
    
    if(curl_errno($ch) != 0)
    {
      debug :: write_error('curl error',
		  __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
		  array('error' => curl_error($ch)));
		  
		  curl_close($ch);
		  
		  return false;
		}
    
    curl_close($ch);
    
    return $html;    
  }

  function _get_ground_shipping_options_html($shipping_configuration)
  {
    $data = array();
    $data['shipDate'] = strftime("%m%d%Y");
    $data['packageCount'] = 1;
    $data['origCountry'] = $shipping_configuration->get_country_from();
    $data['destCountry'] = $shipping_configuration->get_country_to();
    $data['origZip'] = $shipping_configuration->get_zip_from();
    $data['destZip'] = $shipping_configuration->get_zip_to();
    $data['shipToResidence'] = $shipping_configuration->get_residence() ? 1 : 0;
    $data['companyType'] = 'Ground';
    $data['cc'] = 'US';
    $data['language'] = 'en';
    $data['submitShipInfo'] = 'Continue';
    $data['submitAction'] = '';
    $data['locId'] = '';
    $data['autoDeviceType'] = '';
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, SHIPPING_FEDEX_SHIP_INFO_SERVER);
    curl_setopt($ch, CURLOPT_USERAGENT, SHIPPING_FEDEX_SERVER_USERAGENT);
    curl_setopt($ch, CURLOPT_REFERER, SHIPPING_FEDEX_HOME_SERVER);
    curl_setopt($ch, CURLOPT_FAILONERROR, 1); 
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, SHIPPING_FEDEX_SERVER_TIMEOUT);
    curl_setopt($ch, CURLOPT_COOKIEJAR, SHIPPING_FEDEX_SERVER_COOKIE_FILE);
    curl_setopt($ch, CURLOPT_COOKIEFILE, SHIPPING_FEDEX_SERVER_COOKIE_FILE); 
    curl_setopt($ch, CURLOPT_POST, 1); // set POST method 
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    
    curl_exec($ch);
    
    if(curl_errno($ch) != 0)
    {
      debug :: write_error('curl error',
		  __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
		  array('error' => curl_error($ch)));
		  
		  curl_close($ch);
		  
		  return false;
		}
    
    curl_close($ch);
    
    $data = array();
    $data['packageForm.packageList[0].weightUnit'] = $shipping_configuration->get_weight_unit();
    $data['packageForm.packageList[0].dimUnit'] = 'in';
    $data['packageForm.packageList[0].currencyCode'] = 'USD';
    $data['packageForm.packageList[0].weight'] = $shipping_configuration->get_weight();
    $data['packageForm.packageList[0].packageType'] = '1';
    $data['packageForm.packageList[0].dimLength'] = 'L';
    $data['packageForm.packageList[0].dimWidth'] = 'W';
    $data['packageForm.packageList[0].dimHeight'] = 'H';
    $data['packageForm.packageList[0].declaredValue'] = $shipping_configuration->get_declared_value();
    $data['optionsList[0].optionCode'] = 'USAOD';
    $data['optionsList[1].optionCode'] = 'USAPOD';
    $data['optionsList[2].optionCode'] = 'USCT';
    $data['optionsList[3].optionCode'] = 'USECOD';
    $data['optionsList[4].optionCode'] = 'USECT';
    $data['optionsList[5].optionCode'] = 'USHAZMAT';
    $data['cc'] = 'US';
    $data['language'] = 'en';
    $data['submitGetRates'] = 'Continue';
    $data['submitAction'] = '';
    $data['groundCOD'] = 'false';
    $data['isExpress'] = 'false';
    $data['isGround'] = 'true';
    $data['locId'] = '';
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, SHIPPING_FEDEX_SHIP_INFO_SERVER);
    curl_setopt($ch, CURLOPT_USERAGENT, SHIPPING_FEDEX_SERVER_USERAGENT);
    curl_setopt($ch, CURLOPT_REFERER, SHIPPING_FEDEX_SHIP_INFO_SERVER);
    curl_setopt($ch, CURLOPT_FAILONERROR, 1); 
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, SHIPPING_FEDEX_SERVER_TIMEOUT);
    curl_setopt($ch, CURLOPT_COOKIEJAR, SHIPPING_FEDEX_SERVER_COOKIE_FILE);
    curl_setopt($ch, CURLOPT_COOKIEFILE, SHIPPING_FEDEX_SERVER_COOKIE_FILE); 
    curl_setopt($ch, CURLOPT_POST, 1); // set POST method 
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    
    $html = curl_exec($ch);
    
    if(curl_errno($ch) != 0)
    {
      debug :: write_error('curl error',
		  __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
		  array('error' => curl_error($ch)));
		  
		  curl_close($ch);
		  
		  return false;
		}
    
    curl_close($ch);
    
    return $html;    
  }
  
  function _browse_to_home_page()
  {
    $ch = curl_init();    
    
    curl_setopt($ch, CURLOPT_URL, SHIPPING_FEDEX_HOME_SERVER); 
    curl_setopt($ch, CURLOPT_USERAGENT, SHIPPING_FEDEX_SERVER_USERAGENT);
    curl_setopt($ch, CURLOPT_FAILONERROR, 1); 
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, SHIPPING_FEDEX_SERVER_TIMEOUT);
    curl_setopt($ch, CURLOPT_COOKIEJAR, SHIPPING_FEDEX_SERVER_COOKIE_FILE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    curl_exec($ch);
    
    if(curl_errno($ch) != 0)
    {
      debug :: write_error('curl error',
		  __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
		  array('error' => curl_error($ch)));
		  
		  curl_close($ch);
		  
		  return false;
		}
    
    curl_close($ch);  
    return true;
  }
}

?>