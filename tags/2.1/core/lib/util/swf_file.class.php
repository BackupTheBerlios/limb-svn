<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: swf_file.class.php 410 2004-02-06 10:46:51Z server $
*
***********************************************************************************/ 
class swf_file
{
  var $header;
  var $contents;
  var $loaded = false;
  
  function load($name)
  {
    if (!file_exists($name))
      return false;
    
    if ($fp = fopen($name, 'rb'))
    {
      $buf = fread($fp, 8);

      $hdr = substr($buf, 0, 3);
      $ver = ord($buf{3});

      if ($hdr != 'FWS' && $hdr != 'CWS')
        return false;
      
      $contents = fread($fp, filesize($name) - 8);
      if ($hdr == 'CWS' && $ver >= 6)
        $contents = gzuncompress($contents);
      
      $this->contents = substr($contents, 12);

      $this->_parse_header(substr($buf . $contents, 0, 20));
      
      fclose($fp);
      
      $this->loaded = true;
      
      return true;
    }
    else
      return false;
  }
  
  function save($name, $compress = false)
  {
    if (!$this->loaded)
      return false;

    if ($compress)
      $this->header['header'] = 'CWS';
    else
      $this->header['header'] = 'FWS';
    
    $this->header['file_length'] = strlen($this->contents) + 20;
    
    $header = $this->_assemble_header();
    
    $meta_header = substr($header, 0, 8);

    $contents = substr($header, 8) . $this->contents;
    
    if ($compress)
      $contents = gzcompress(substr($header, 8) . $this->contents);
    
    if ($fp = fopen($name, 'wb'))
    {
      fwrite($fp, $meta_header . $contents);
      fclose($fp);
      return true;
    }
    
    return false;
    
  }
  
  function get_header()
  {
    if ($this->loaded)
      return $this->header;
  }
  
  function get_contents()
  {
    if ($this->loaded)
      return $this->contents;
  }
  
  function _parse_header($header_str)
  {
    $this->header['header'] = substr($header_str, 0, 3);
    $this->header['version'] = ord($header_str{3});
    
    $this->header['file_length'] = 0;
    for($i=0; $i<4; $i++)
      $this->header['file_length'] += ord($header_str{$i+4}) * pow(256, $i);
     
    for($i=0; $i<8; $i++)
      $this->header['frame_size'][] = ord($header_str{$i+8});

    $this->header['frame_rate'] = ord($header_str{16}) + ord($header_str{17}) * 256;
    $this->header['frame_count'] = ord($header_str{18}) + ord($header_str{19}) * 256;
  }
  
  function _assemble_header()
  {
    $header = $this->header['header'];
    $header .= chr($this->header['version']);
    
    $flen = $this->header['file_length'];
    for($i=0; $i<4; $i++)
    {
      $res = $flen % 256;
      $header .= chr($res);
      $flen = round($flen / 256);
    }

    for($i=0; $i<8; $i++)
      $header .= chr($this->header['frame_size'][$i]);
    
    $header .= chr($this->header['frame_rate'] % 256) . chr(round($this->header['frame_rate'] / 256));
    $header .= chr($this->header['frame_count'] % 256) . chr(round($this->header['frame_count'] / 256));
    
    return $header;
  }
}
?>
