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
/**
*  +----------------------------- IMPORTANT ------------------------------+
*  | Usage of this class compared to native php extensions such as        |
*  | mailparse or imap, is slow and may be feature deficient. If available|
*  | you are STRONGLY recommended to use the php extensions.              |
*  +----------------------------------------------------------------------+
*
* Mime Decoding class
*
* This class will parse a raw mime email and return
* the structure. Returned structure is similar to
* that returned by imap_fetchstructure().
*
*/

class mime_decode
{

	var $_input;
	var $_header;
	var $_body;

  var $_error;

  var $_include_bodies = false;
  var $_decode_bodies  = true;
  var $_decode_headers = true;

  var $_called_statically = true;

  function mime_decode($input = null, $params = null)
  {
		$this->set_message($input);
 		$this->set_params($params);

		$this->_called_statically = false;
  }

  function set_message($input = null)
  {
    $this->_input = $input;
    
    list($this->_header, $this->_body)   = $this->_split_body_header($this->_input);
  }

  function set_params($params = null)
  {
    $this->_include_bodies = isset($params['include_bodies'])  ? $params['include_bodies']  : $this->_include_bodies;
    $this->_decode_bodies  = isset($params['decode_bodies'])   ? $params['decode_bodies']   : $this->_decode_bodies;
    $this->_decode_headers = isset($params['decode_headers'])  ? $params['decode_headers']  : $this->_decode_headers;
  }

	function decode($input = null, $params = null)
	{
    if ($this->_called_statically)
    {
        $obj = new mime_decode($params['input']);
        $structure = $obj->decode($params);
    }
    
	  if (empty($this->_input))
	  {
      debug::write_warning('No input given');
      return false;
    }

    $this->set_params($params);
    $structure = $this->_decode($this->_header, $this->_body);
    if ($structure === false)
			debug::write_warning($this->_error);

	  return $structure;
	}

	function _decode($headers, $body, $default_ctype = 'text/plain')
	{
	    $result = new stdClass;
	    $headers = $this->_parse_headers($headers);

	    foreach ($headers as $value)
	    {
	      $value_name = strtolower($value['name']);
	      if (isset($result->headers[$value_name]))
	      {
		      if (!is_array( $result->headers[$value_name] ))
		        $result->headers[$value_name] = array($result->headers[$value_name]);

	      	$result->headers[$value_name][] = $value['value'];
	      }
				else
	      	$result->headers[$value_name] = $value['value'];
	    }
	
	    reset($headers);
	    while (list($key, $value) = each($headers))
	    {
	      $headers[$key]['name'] = strtolower($headers[$key]['name']);
	      switch ($headers[$key]['name'])
	      {
	        case 'content-type':
	          $content_type = $this->_parse_header_value($headers[$key]['value']);

	          if (preg_match('/([0-9a-z+.-]+)\/([0-9a-z+.-]+)/i', $content_type['value'], $regs)) 
	          {
              $result->ctype_primary   = $regs[1];
              $result->ctype_secondary = $regs[2];
	          }
	
	          if (isset($content_type['other']))
	            while (list($p_name, $p_value) = each($content_type['other']))
	            	$result->ctype_parameters[$p_name] = $p_value;

	          break;

	        case 'content-disposition':
            $content_disposition = $this->_parse_header_value($headers[$key]['value']);
            $result->disposition   = $content_disposition['value'];
            if (isset($content_disposition['other']))
                while (list($p_name, $p_value) = each($content_disposition['other']))
                    $result->d_parameters[$p_name] = $p_value;
            break;

	        case 'content-transfer-encoding':
            $content_transfer_encoding = $this->_parse_header_value($headers[$key]['value']);
            break;
	      }
	    }
	
	    if (isset($content_type)) 
	    {
        switch (strtolower($content_type['value']))
        {
          case 'multipart/parallel':
          case 'multipart/report': // RFC1892
          case 'multipart/signed': // PGP
          case 'multipart/digest':
          case 'multipart/alternative':
          case 'multipart/related':
          case 'multipart/mixed':
            if(!isset($content_type['other']['boundary']))
            {
              $this->_error = 'No boundary found for ' . $content_type['value'] . ' part';
              debug :: write_warning($this->_error);
              return false;
            }

            $default_ctype = (strtolower($content_type['value']) === 'multipart/digest') ? 'message/rfc822' : 'text/plain';

            $parts = $this->_split_by_boundary($body, $content_type['other']['boundary']);
            for ($i = 0; $i < count($parts); $i++)
            {
              list($part_header, $part_body) = $this->_split_body_header($parts[$i]);
              $part = $this->_decode($part_header, $part_body, $default_ctype);
              if($part === false)
                debug :: write_warning($this->_error);
              $result->parts[] = $part;
            }
            break;

          case 'message/rfc822':
            $obj = &new mime_decode($body);
            $result->parts[] = $obj->decode(array('include_bodies' => $this->_include_bodies));
            unset($obj);
            break;

          case 'text/plain':
          case 'text/html':
          default:
            $encoding = isset($content_transfer_encoding) ? $content_transfer_encoding['value'] : '7bit';
            $this->_include_bodies ? $result->body = ($this->_decode_bodies ? $this->_decode_body($body, $encoding) : $body) : null;
            break;
        }
	    }
	    else
	    {
	        $ctype = explode('/', $default_ctype);
	        $result->ctype_primary   = $ctype[0];
	        $result->ctype_secondary = $ctype[1];
	        $this->_include_bodies ? $result->body = ($this->_decode_bodies ? $this->_decode_body($body) : $body) : null;
	    }
	
	    return $result;
	}
	
	/**
	 * Given the output of the above function, this will return an
	 * array of references to the parts, indexed by mime number.
	 *
	 * @param  object $structure   The structure to go through
	 * @param  string $mime_number Internal use only.
	 * @return array               Mime numbers
	 */
	function &get_mime_parts_array(&$structure, $no_refs = false, $mime_number = '', $prepend = '')
	{
    $result = array();
    if (!empty($structure->parts))
    {
      if ($mime_number != '')
      {
          $structure->mime_id = $prepend . $mime_number;
          $result[$prepend . $mime_number] = &$structure;
      }

      for ($i = 0; $i < count($structure->parts); $i++)
      {
        if (!empty($structure->headers['content-type']) AND 
        		substr(strtolower($structure->headers['content-type']), 0, 8) == 'message/')
        {
            $prepend      = $prepend . $mime_number . '.';
            $_mime_number = '';
        }
        else
          $_mime_number = ($mime_number == '' ? $i + 1 : sprintf('%s.%s', $mime_number, $i + 1));

        $arr = &mime_decode::get_mime_parts_array($structure->parts[$i], $no_refs, $_mime_number, $prepend);
        foreach ($arr as $key => $val)
					$no_refs ? $result[$key] = '' : $result[$key] = &$arr[$key];

      }
    }
    else
    {
      if ($mime_number == '')
				$mime_number = '1';
      $structure->mime_id = $prepend . $mime_number;
      $no_refs ? $result[$prepend . $mime_number] = '' : $result[$prepend . $mime_number] = &$structure;
    }
    
    return $result;
	}
	
	function _split_body_header($input)
	{
	  if (preg_match("/^(.*?)\r?\n\r?\n(.*)/s", $input, $match))
			return array($match[1], $match[2]);
	
	  $this->_error = 'Could not split header and body';
	  return false;
	}
	
	/**
	 * Parse headers given in $input and return as assoc array.
	 */
	function _parse_headers($input)
	{
	  if ($input !== '')
	  {
	    // Unfold the input
	    $input   = preg_replace("/\r?\n/", "\r\n", $input);
	    $input   = preg_replace("/\r\n(\t| )+/", ' ', $input);
	    $headers = explode("\r\n", trim($input));
	
	    foreach ($headers as $value)
	    {
        $hdr_name = substr($value, 0, $pos = strpos($value, ':'));
        $hdr_value = substr($value, $pos + 1);
        if($hdr_value[0] == ' ')
        	$hdr_value = substr($hdr_value, 1);

				$result[] = array(
					'name'  => $hdr_name,
					'value' => $this->_decode_headers ? $this->_decode_header($hdr_value) : $hdr_value
				);
	    }
	  }
	  else
			$result = array();
	
	  return $result;
	}
	
	/**
	 * Function to parse a header value, extract first part, and any secondary
	 * parts (after ;) This function is not as robust as it could be. Eg. header comments
	 * in the wrong place will probably break it.
	 */
	function _parse_header_value($input)
	{
	
		if (($pos = strpos($input, ';')) !== false)
		{
			$result['value'] = trim(substr($input, 0, $pos));
			$input = trim(substr($input, $pos+1));
			
			if (strlen($input) > 0)
			{
		    // This splits on a semi-colon, if there's no preceeding backslash
		    // Can't handle if it's in double quotes however.
		    $parameters = preg_split('/\s*(?<!\\\\);\s*/i', $input);
		
		    for ($i = 0; $i < count($parameters); $i++)
		    {
	        $param_name  = substr($parameters[$i], 0, $pos = strpos($parameters[$i], '='));
	        $param_value = substr($parameters[$i], $pos + 1);
	        if ($param_value[0] == '"')
	        	$param_value = substr($param_value, 1, -1);
	        $result['other'][$param_name] = $param_value;
	        $result['other'][strtolower($param_name)] = $param_value;
		    }
			}
		}
		else
			$result['value'] = trim($input);

		return $result;
	}

	function _split_by_boundary($input, $boundary)
	{
	  $tmp = explode('--'.$boundary, $input);
	
	  for ($i=1; $i<count($tmp)-1; $i++)
	      $parts[] = $tmp[$i];
	
	  return $parts;
	}

	//  decode header according RFC2047
	function _decode_header($input)
	{
    $input = preg_replace('/(=\?[^?]+\?(q|b)\?[^?]*\?=)(\s)+=\?/i', '\1=?', $input);
    while (preg_match('/(=\?([^?]+)\?(q|b)\?([^?]*)\?=)/i', $input, $matches))
    {
      $encoded  = $matches[1];
      $charset  = $matches[2];
      $encoding = $matches[3];
      $text     = $matches[4];

      switch (strtolower($encoding))
      {
        case 'b':
            $text = base64_decode($text);
            break;
        case 'q':
            $text = str_replace('_', ' ', $text);
            preg_match_all('/=([a-f0-9]{2})/i', $text, $matches);
            foreach($matches[1] as $value)
                $text = str_replace('='.$value, chr(hexdec($value)), $text);
            break;
      }

      $input = str_replace($encoded, $text, $input);
    }
    return $input;
	}
	

	function _decode_body($input, $encoding = '7bit')
	{
    switch ($encoding)
    {
      case 'quoted-printable':
          return $this->_decode_quoted_printable_string($input);
          break;
      case 'base64':
          return base64_decode($input);
          break;
      case '7bit':
      default:
				return $input;
    }
	}
	
	function _decode_quoted_printable_string($input)
	{
    $input = preg_replace("/=\r?\n/", '', $input);
		$input = preg_replace('/=([a-f0-9]{2})/ie', "chr(hexdec('\\1'))", $input);
	
	  return $input;
	}
	
	/**
	 * Checks the input for uuencoded files and returns
	 * an array of them. Can be called statically, eg:
	 * $files =& mime_decode::uudecode($some_text);
	 *
	 * It will check for the begin 666 ... end syntax however and won't just blindly 
	 * decode whatever you pass it.
	 */
	function &uudecode($input)
	{
    // Find all uuencoded sections
    preg_match_all("/begin ([0-7]{3}) (.+)\r?\n(.+)\r?\nend/Us", $input, $matches);

    for ($j = 0; $j < count($matches[3]); $j++)
    {
      $str      = $matches[3][$j];
      $filename = $matches[2][$j];
      $fileperm = $matches[1][$j];

      $file = '';
      $str = preg_split("/\r?\n/", trim($str));
      $strlen = count($str);

      for ($i = 0; $i < $strlen; $i++)
      {
        $pos = 1;
        $d = 0;
        $len=(int)(((ord(substr($str[$i],0,1)) -32) - ' ') & 077);

        while (($d + 3 <= $len) AND ($pos + 4 <= strlen($str[$i])))
        {
          $c0 = (ord(substr($str[$i],$pos,1)) ^ 0x20);
          $c1 = (ord(substr($str[$i],$pos+1,1)) ^ 0x20);
          $c2 = (ord(substr($str[$i],$pos+2,1)) ^ 0x20);
          $c3 = (ord(substr($str[$i],$pos+3,1)) ^ 0x20);

          $file .= chr(((($c0 - ' ') & 077) << 2) | ((($c1 - ' ') & 077) >> 4));
          $file .= chr(((($c1 - ' ') & 077) << 4) | ((($c2 - ' ') & 077) >> 2));
          $file .= chr(((($c2 - ' ') & 077) << 6) |  (($c3 - ' ') & 077));

          $pos += 4;
          $d += 3;
        }

        if (($d + 2 <= $len) && ($pos + 3 <= strlen($str[$i])))
        {
					$c0 = (ord(substr($str[$i],$pos,1)) ^ 0x20);
	        $c1 = (ord(substr($str[$i],$pos+1,1)) ^ 0x20);
	        $c2 = (ord(substr($str[$i],$pos+2,1)) ^ 0x20);
	        $file .= chr(((($c0 - ' ') & 077) << 2) | ((($c1 - ' ') & 077) >> 4));
	
	        $file .= chr(((($c1 - ' ') & 077) << 4) | ((($c2 - ' ') & 077) >> 2));
	
	        $pos += 3;
	        $d += 2;
        }

        if (($d + 1 <= $len) && ($pos + 2 <= strlen($str[$i])))
        {
            $c0 = (ord(substr($str[$i],$pos,1)) ^ 0x20);
            $c1 = (ord(substr($str[$i],$pos+1,1)) ^ 0x20);
            $file .= chr(((($c0 - ' ') & 077) << 2) | ((($c1 - ' ') & 077) >> 4));

        }
      }
      $files[] = array('filename' => $filename, 'fileperm' => $fileperm, 'filedata' => $file);
    }
    return $files;
	}
}
?>
