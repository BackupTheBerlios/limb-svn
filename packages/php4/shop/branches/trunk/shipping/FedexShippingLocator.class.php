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
define('SHIPPING_FEDEX_GROUND_TYPE', 'ground');
define('SHIPPING_FEDEX_EXPRESS_TYPE', 'express');

define('SHIPPING_FEDEX_WEIGHT_UNIT_LB', 'lb');
define('SHIPPING_FEDEX_WEIGHT_UNIT_KG', 'kg');

define('SHIPPING_FEDEX_HOME_SERVER', 'http://www.fedex.com/ratefinder/home');
define('SHIPPING_FEDEX_SHIP_INFO_SERVER', 'http://www.fedex.com/ratefinder/shipInfo');
define('SHIPPING_FEDEX_SERVER_TIMEOUT', 60);
define('SHIPPING_FEDEX_SERVER_COOKIE_FILE', tempnam(VAR_DIR, 'cookie'));
define('SHIPPING_FEDEX_SERVER_USERAGENT', 'Mozilla/4.0 (compatible; MSIE 5.5; Windows 98)');

require_once(dirname(__FILE__) . '/ShippingLocator.class.php');

class FedexShippingLocator extends ShippingLocator
{
  function _doGetShippingOptions($shipping_configuration)
  {
    $this->_cleanCookie();

    $this->_browseToHomePage();

    $express_html = $this->_getExpressShippingOptionsHtml($shipping_configuration);

    $ground_html = $this->_getGroundShippingOptionsHtml($shipping_configuration);

    if($express_html === false)
      $express_options = array();
    else
      $express_options = $this->_parseHtmlOptions($express_html);

    if($ground_html === false)
      $ground_options = array();
    else
      $ground_options = $this->_parseHtmlOptions($ground_html);

    $options = ComplexArray :: array_merge($express_options, $ground_options);

    if(empty($options))
      return false;

    return $options;
  }

  function _parseHtmlOptions($html)
  {
    include_once(LIMB_COMMON_DIR . '/setup_HTMLSax.inc.php');
    include_once(dirname(__FILE__) . '/FedexSaxHandler.class.php');

    $options = array();

    $parser = new XMLHTMLSax3();
    $handler = new FedexSaxHandler();

    $parser->setObject($handler);

    $parser->setElementHandler('open_handler','close_handler');
    $parser->setDataHandler('data_handler');
    $parser->setEscapeHandler('escape_handler');

    $parser->parse($html);

    return $this->_processRawOptions($handler->getOptions());
  }

  function _processRawOptions($raw_options)
  {
    $processed_options = array();
    foreach($raw_options as $data)
    {
      if(!isset($data['price']) ||  empty($data['price']))
        continue;

      $data['id'] = md5($data['name']);
      $data['price'] = str_replace('&nbsp;', '', $data['price']);
      $data['price'] *= 1; //???

      $processed_options[$data['id']] = $data;
    }

    return $processed_options;
  }

  function _cleanCookie()
  {
    if(is_file(SHIPPING_FEDEX_SERVER_COOKIE_FILE))
      unlink(SHIPPING_FEDEX_SERVER_COOKIE_FILE);
  }

  function _getExpressShippingOptionsHtml($shipping_configuration)
  {
    $data = array();
    $data['shipDate'] = strftime("%m%d%Y");
    $data['packageCount'] = 1;
    $data['origCountry'] = $shipping_configuration->getCountryFrom();
    $data['destCountry'] = $shipping_configuration->getCountryTo();
    $data['origZip'] = $shipping_configuration->getZipFrom();
    $data['destZip'] = $shipping_configuration->getZipTo();
    $data['shipToResidence'] = $shipping_configuration->getResidence() ? 1 : 0;
    $data['companyType'] = 'Express';
    $data['cc'] = 'US';
    $data['language'] = 'en';
    $data['submitShipInfo'] = 'Continue';
    $data['submitAction'] = '';
    $data['locId'] = '';
    $data['autoDeviceType'] = '';

    $ch = curlInit();

    curlSetopt($ch, CURLOPT_URL, SHIPPING_FEDEX_SHIP_INFO_SERVER);
    curlSetopt($ch, CURLOPT_USERAGENT, SHIPPING_FEDEX_SERVER_USERAGENT);
    curlSetopt($ch, CURLOPT_REFERER, SHIPPING_FEDEX_HOME_SERVER);
    curlSetopt($ch, CURLOPT_FAILONERROR, 1);
    curlSetopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curlSetopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curlSetopt($ch, CURLOPT_TIMEOUT, SHIPPING_FEDEX_SERVER_TIMEOUT);
    curlSetopt($ch, CURLOPT_COOKIEJAR, SHIPPING_FEDEX_SERVER_COOKIE_FILE);
    curlSetopt($ch, CURLOPT_COOKIEFILE, SHIPPING_FEDEX_SERVER_COOKIE_FILE);
    curlSetopt($ch, CURLOPT_POST, 1); // set POST method
    curlSetopt($ch, CURLOPT_POSTFIELDS, $data);

    curlExec($ch);

    if(curlErrno($ch) != 0)
    {
      Debug :: writeError('curl error',
      __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
      array('error' => curlError($ch)));

      curlClose($ch);

      return false;
    }

    curlClose($ch);

    $data = array();
    $data['packageForm.packageList[0].weightUnit'] = $shipping_configuration->getWeightUnit();
    $data['packageForm.packageList[0].dimUnit'] = 'in';
    $data['packageForm.packageList[0].currencyCode'] = 'USD';
    $data['packageForm.packageList[0].weight'] = $shipping_configuration->getWeight();
    $data['packageForm.packageList[0].packageType'] = '1';
    $data['packageForm.packageList[0].dimLength'] = 'L';
    $data['packageForm.packageList[0].dimWidth'] = 'W';
    $data['packageForm.packageList[0].dimHeight'] = 'H';
    $data['packageForm.packageList[0].declaredValue'] = $shipping_configuration->getDeclaredValue();
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

    $ch = curlInit();

    curlSetopt($ch, CURLOPT_URL, SHIPPING_FEDEX_SHIP_INFO_SERVER);
    curlSetopt($ch, CURLOPT_USERAGENT, SHIPPING_FEDEX_SERVER_USERAGENT);
    curlSetopt($ch, CURLOPT_REFERER, SHIPPING_FEDEX_SHIP_INFO_SERVER);
    curlSetopt($ch, CURLOPT_FAILONERROR, 1);
    curlSetopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curlSetopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curlSetopt($ch, CURLOPT_TIMEOUT, SHIPPING_FEDEX_SERVER_TIMEOUT);
    curlSetopt($ch, CURLOPT_COOKIEJAR, SHIPPING_FEDEX_SERVER_COOKIE_FILE);
    curlSetopt($ch, CURLOPT_COOKIEFILE, SHIPPING_FEDEX_SERVER_COOKIE_FILE);
    curlSetopt($ch, CURLOPT_POST, 1); // set POST method
    curlSetopt($ch, CURLOPT_POSTFIELDS, $data);

    $html = curlExec($ch);

    if(curlErrno($ch) != 0)
    {
      Debug :: writeError('curl error',
      __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
      array('error' => curlError($ch)));

      curlClose($ch);

      return false;
    }

    curlClose($ch);

    return $html;
  }

  function _getGroundShippingOptionsHtml($shipping_configuration)
  {
    $data = array();
    $data['shipDate'] = strftime("%m%d%Y");
    $data['packageCount'] = 1;
    $data['origCountry'] = $shipping_configuration->getCountryFrom();
    $data['destCountry'] = $shipping_configuration->getCountryTo();
    $data['origZip'] = $shipping_configuration->getZipFrom();
    $data['destZip'] = $shipping_configuration->getZipTo();
    $data['shipToResidence'] = $shipping_configuration->getResidence() ? 1 : 0;
    $data['companyType'] = 'Ground';
    $data['cc'] = 'US';
    $data['language'] = 'en';
    $data['submitShipInfo'] = 'Continue';
    $data['submitAction'] = '';
    $data['locId'] = '';
    $data['autoDeviceType'] = '';

    $ch = curlInit();

    curlSetopt($ch, CURLOPT_URL, SHIPPING_FEDEX_SHIP_INFO_SERVER);
    curlSetopt($ch, CURLOPT_USERAGENT, SHIPPING_FEDEX_SERVER_USERAGENT);
    curlSetopt($ch, CURLOPT_REFERER, SHIPPING_FEDEX_HOME_SERVER);
    curlSetopt($ch, CURLOPT_FAILONERROR, 1);
    curlSetopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curlSetopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curlSetopt($ch, CURLOPT_TIMEOUT, SHIPPING_FEDEX_SERVER_TIMEOUT);
    curlSetopt($ch, CURLOPT_COOKIEJAR, SHIPPING_FEDEX_SERVER_COOKIE_FILE);
    curlSetopt($ch, CURLOPT_COOKIEFILE, SHIPPING_FEDEX_SERVER_COOKIE_FILE);
    curlSetopt($ch, CURLOPT_POST, 1); // set POST method
    curlSetopt($ch, CURLOPT_POSTFIELDS, $data);

    curlExec($ch);

    if(curlErrno($ch) != 0)
    {
      Debug :: writeError('curl error',
      __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
      array('error' => curlError($ch)));

      curlClose($ch);

      return false;
    }

    curlClose($ch);

    $data = array();
    $data['packageForm.packageList[0].weightUnit'] = $shipping_configuration->getWeightUnit();
    $data['packageForm.packageList[0].dimUnit'] = 'in';
    $data['packageForm.packageList[0].currencyCode'] = 'USD';
    $data['packageForm.packageList[0].weight'] = $shipping_configuration->getWeight();
    $data['packageForm.packageList[0].packageType'] = '1';
    $data['packageForm.packageList[0].dimLength'] = 'L';
    $data['packageForm.packageList[0].dimWidth'] = 'W';
    $data['packageForm.packageList[0].dimHeight'] = 'H';
    $data['packageForm.packageList[0].declaredValue'] = $shipping_configuration->getDeclaredValue();
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

    $ch = curlInit();

    curlSetopt($ch, CURLOPT_URL, SHIPPING_FEDEX_SHIP_INFO_SERVER);
    curlSetopt($ch, CURLOPT_USERAGENT, SHIPPING_FEDEX_SERVER_USERAGENT);
    curlSetopt($ch, CURLOPT_REFERER, SHIPPING_FEDEX_SHIP_INFO_SERVER);
    curlSetopt($ch, CURLOPT_FAILONERROR, 1);
    curlSetopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curlSetopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curlSetopt($ch, CURLOPT_TIMEOUT, SHIPPING_FEDEX_SERVER_TIMEOUT);
    curlSetopt($ch, CURLOPT_COOKIEJAR, SHIPPING_FEDEX_SERVER_COOKIE_FILE);
    curlSetopt($ch, CURLOPT_COOKIEFILE, SHIPPING_FEDEX_SERVER_COOKIE_FILE);
    curlSetopt($ch, CURLOPT_POST, 1); // set POST method
    curlSetopt($ch, CURLOPT_POSTFIELDS, $data);

    $html = curlExec($ch);

    if(curlErrno($ch) != 0)
    {
      Debug :: writeError('curl error',
      __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
      array('error' => curlError($ch)));

      curlClose($ch);

      return false;
    }

    curlClose($ch);

    return $html;
  }

  function _browseToHomePage()
  {
    $ch = curlInit();

    curlSetopt($ch, CURLOPT_URL, SHIPPING_FEDEX_HOME_SERVER);
    curlSetopt($ch, CURLOPT_USERAGENT, SHIPPING_FEDEX_SERVER_USERAGENT);
    curlSetopt($ch, CURLOPT_FAILONERROR, 1);
    curlSetopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curlSetopt($ch, CURLOPT_TIMEOUT, SHIPPING_FEDEX_SERVER_TIMEOUT);
    curlSetopt($ch, CURLOPT_COOKIEJAR, SHIPPING_FEDEX_SERVER_COOKIE_FILE);
    curlSetopt($ch, CURLOPT_RETURNTRANSFER, 1);

    curlExec($ch);

    if(curlErrno($ch) != 0)
    {
      Debug :: writeError('curl error',
      __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
      array('error' => curlError($ch)));

      curlClose($ch);

      return false;
    }

    curlClose($ch);
    return true;
  }
}

?>