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
* RFC 822 Email address list validation Utility
*
* This class will take an address string, and parse it into it's consituent
* parts, be that either addresses, groups, or combinations. Nested groups
* are not supported. The structure it returns is pretty straight forward,
* and is similar to that provided by the imap_rfc822_parse_adrlist(). Use
* print_r() to view the structure.
*
* How do I use it?
*
* $address_string = 'My Group: "Richard" <richard@localhost> (A comment), ted@example.com (Ted Bloggs), Barney;';
* $structure = Mail_RFC822::parseAddressList($address_string, 'example.com', true)
*/
//////////////////////////////////////////////////////////////////////////
//3.3.  LEXICAL TOKENS
//          The following rules are used to define an underlying lexical
//     analyzer,  which  feeds  tokens to higher level parsers.  See the
//     ANSI references, in the Bibliography.
//                                                 ; (  Octal, Decimal.)
//     CHAR        =  <any ASCII character>        ; (  0-177,  0.-127.)
//     ALPHA       =  <any ASCII alphabetic character>
//                                                 ; (101-132, 65.- 90.)
//                                                 ; (141-172, 97.-122.)
//     DIGIT       =  <any ASCII decimal digit>    ; ( 60- 71, 48.- 57.)
//     CTL         =  <any ASCII control           ; (  0- 37,  0.- 31.)
//                     character and DEL>          ; (    177,     127.)
//     CR          =  <ASCII CR, carriage return>  ; (     15,      13.)
//     LF          =  <ASCII LF, linefeed>         ; (     12,      10.)
//     SPACE       =  <ASCII SP, space>            ; (     40,      32.)
//     HTAB        =  <ASCII HT, horizontal-tab>   ; (     11,       9.)
//     <">         =  <ASCII quote mark>           ; (     42,      34.)
//     CRLF        =  CR LF
//     LWSP-char   =  SPACE / HTAB                 ; semantics = SPACE
//     linear-white-space =  1*([CRLF] LWSP-char)  ; semantics = SPACE
//                                                 ; CRLF => folding
//     specials    =  "(" / ")" / "<" / ">" / "@"  ; Must be in quoted-
//                 /  "," / ";" / ":" / "\" / <">  ;  string, to use
//                 /  "." / "[" / "]"              ;  within a word.
//     delimiters  =  specials / linear-white-space / comment
//     text        =  <any CHAR, including bare    ; => atoms, specials,
//                     CR & bare LF, but NOT       ;  comments and
//                     including CRLF>             ;  quoted-strings are
//                                                 ;  NOT recognized.
//     atom        =  1*<any CHAR except specials, SPACE and CTLs>
//     quoted-string = <"> *(qtext/quoted-pair) <">; Regular qtext or
//                                                 ;   quoted chars.
//     qtext       =  <any CHAR excepting <">,     ; => may be folded
//                     "\" & CR, and including
//                     linear-white-space>
//     domain-literal =  "[" *(dtext / quoted-pair) "]"
//
//     dtext       =  <any CHAR excluding "[",     ; => may be folded
//                     "]", "\" & CR, & including
//                     linear-white-space>
//     comment     =  "(" *(ctext / quoted-pair / comment) ")"
//     ctext       =  <any CHAR excluding "(",     ; => may be folded
//                     ")", "\" & CR, & including
//                     linear-white-space>
//     quoted-pair =  "\" CHAR                     ; may quote any char
//     phrase      =  1*word                       ; Sequence of words
//     word        =  atom / quoted-string
//////////////////////////////////////////////////////////////////////////
//  6.  ADDRESS SPECIFICATION
//     6.1.  SYNTAX
//     address     =  mailbox                      ; one addressee
//                 /  group                        ; named list
//     group       =  phrase ":" [#mailbox] ";"
//     mailbox     =  addr-spec                    ; simple address
//                 /  phrase route-addr            ; name & addr-spec
//     route-addr  =  "<" [route] addr-spec ">"
//     route       =  1#("@" domain) ":"           ; path-relative
//     addr-spec   =  local-part "@" domain        ; global address
//     local-part  =  word *("." word)             ; uninterpreted
//                                                 ; case-preserved
//     domain      =  sub-domain *("." sub-domain)
//     sub-domain  =  domain-ref / domain-literal
//     domain-ref  =  atom                         ; symbolic reference
//		 atom 			 =  1*<any CHAR except specials, SPACE and CTLs>

require_once(LIMB_DIR . 'core/lib/debug/debug.class.php');	

class rfc822
{

  var $_address = '';
  var $_default_domain = 'localhost';

  /**
   * Should we return a nested array showing groups, or flatten everything?
   */
  var $_nest_groups = true;

  /**
   * Whether or not to validate atoms for non-ascii characters.
   */
  var $_validate_atoms = true;

  /**
   * The array of raw addresses built up as we parse.
   */
  var $_addresses = array();

  /**
   * The final array of parsed address information that we build up.
   */
  var $_structure = array();

  var $_error = null;
  var $_index = null;
  var $_num_groups = 0;
  var $_limit = null;

  var $_called_via_object = true;



  function rfc822($address = null, $default_domain = null, $nest_groups = null, $validate_atoms = null, $limit = null)
  {
		$this->set_params($address, $default_domain, $nest_groups, $validate_atoms, $limit);
  }


  function set_params($address = null, $default_domain = null, $nest_groups = null, $validate_atoms = null, $limit = null)
  {
    if (isset($address)) 				$this->_address        = $address;
    if (isset($default_domain)) $this->_default_domain = $default_domain;
    if (isset($nest_groups))    $this->_nest_groups    = $nest_groups;
    if (isset($validate))       $this->_validate_atoms = $validate_atoms;
    if (isset($limit))          $this->_limit          = $limit;

    $this->_structure  = array();
    $this->_addresses  = array();
    $this->_error      = null;
    $this->_index      = null;

  }

  function parse_address_list($address = null, $default_domain = null, $nest_groups = null, $validate_atoms = null, $limit = null)
  {
    if (!isset($this->_called_via_object))
    {
        $obj = new rfc822($address, $default_domain, $nest_groups, $validate, $limit);
        return $obj->parse_address_list();
    }

		$this->set_params($address, $default_domain, $nest_groups, $validate_atoms, $limit);

    while ($this->_address = $this->_split_addresses($this->_address))
        continue;
    
    if ($this->_address === false || isset($this->_error))
        return debug :: write_warning($this->_error, __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);

    for ($i = 0; $i < count($this->_addresses); $i++)
    {
      if (($return = $this->_validate_address($this->_addresses[$i])) === false || isset($this->_error))
          return debug :: write_warning($this->_error, __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
      
      if (!$this->_nest_groups)
				$this->_structure = array_merge($this->_structure, $return);
			else
				$this->_structure[] = $return;
    }

    return $this->_structure;
  }

  /**
   * Splits an address into seperate addresses.
   * 
   * @access private
   * @param string $address The addresses to split.
   * @return boolean Success or failure.
   */
  function _split_addresses($address)
  {

    if (!empty($this->_limit) AND count($this->_addresses) == $this->_limit)
			return '';

    if(isset($this->_error))
    	return false;
    	
    if($is_group = $this->_is_group($address))
      $split_char = ';';
    else
      $split_char = ',';

    $parts  = explode($split_char, $address);
    $string = $this->_split_check($parts, $split_char);

    if ($is_group)
    {
      if (strpos($string, ':') === false)
      {
          $this->_error = 'Invalid address: ' . $string;
          return false;
      }

      if (!$this->_split_check(explode(':', $string), ':'))
				return false;

      $this->_num_groups++;
    }

    $this->_addresses[] = array(
			'address' => trim($string),
			'group'   => $is_group
		);

    // Remove the now stored address from the initial line, the +1
    // is to account for the explode character.
    $address = trim(substr($address, strlen($string) + 1));

    // If the next char is a comma and this was a group, then
    // there are more addresses, otherwise, if there are any more
    // chars, then there is another address.
    if ($is_group && substr($address, 0, 1) == ',')
    {
      $address = trim(substr($address, 1));
      return $address;
    }
    elseif (strlen($address) > 0)
			return $address;
		else
    	return '';

    return false;
  }


  function _is_group($address)
  {
    $parts  = explode(',', $address);
    $string = $this->_split_check($parts, ',');

    if (count($parts = explode(':', $string)) > 1)
    {
      $string2 = $this->_split_check($parts, ':');
      return ($string2 !== $string);
    }
    else
	    return false;

  }

  /**
   * A common function that will check an exploded string.
   * 
   * @access private
   * @param array $parts The exloded string.
   * @param string $char  The char that was exploded on.
   * @return mixed False if the string contains unclosed quotes/brackets, or the string on success.
   */
  function _split_check($parts, $char)
  {
    $string = $parts[0];

    for ($i = 0; $i < count($parts); $i++)
    {
        if ($this->_has_unclosed_quotes($string)
            || $this->_has_unclosed_brackets($string, '<>')
            || $this->_has_unclosed_brackets($string, '[]')
            || $this->_has_unclosed_brackets($string, '()')
            || substr($string, -1) == '\\')
        {
          if (isset($parts[$i + 1]))
              $string = $string . $char . $parts[$i + 1];
					else
					{
              $this->_error = 'Invalid address spec. Unclosed bracket or quotes';
              return false;
          }
        }
        else
        {
            $this->_index = $i;
            break;
        }
    }

    return $string;
  }

  /**
   * Checks if a string has an unclosed quotes or not.
   * 
   * @access private
   * @param string $string The string to check.
   * @return boolean True if there are unclosed quotes inside the string, false otherwise.
   */
  function _has_unclosed_quotes($string)
  {
      $string     = explode('"', $string);
      $string_cnt = count($string);

      for ($i = 0; $i < (count($string) - 1); $i++)
          if (substr($string[$i], -1) == '\\')
              $string_cnt--;

      return ($string_cnt % 2 === 0);
  }

  /**
   * Checks if a string has an unclosed brackets or not. IMPORTANT:
   * This function handles both angle brackets and square brackets;
   * 
   * @access private
   * @param string $string The string to check.
   * @param string $chars  The characters to check for.
   * @return boolean True if there are unclosed brackets inside the string, false otherwise.
   */
  function _has_unclosed_brackets($string, $chars)
  {
    $num_angle_start = substr_count($string, $chars[0]);
    $num_angle_end   = substr_count($string, $chars[1]);

    $this->_has_unclosed_brackets_sub($string, $num_angle_start, $chars[0]);
    $this->_has_unclosed_brackets_sub($string, $num_angle_end, $chars[1]);

    if ($num_angle_start < $num_angle_end)
    {
	    $this->_error = 'Invalid address spec. Unmatched quote or bracket (' . $chars . ')';
	    return false;
    }
    else
	    return ($num_angle_start > $num_angle_end);
  }

  /**
   * Sub function that is used only by _has_unclosed_brackets().
   * 
   * @access private
   * @param string $string The string to check.
   * @param integer &$num    The number of occurences.
   * @param string $char   The character to count.
   * @return integer The number of occurences of $char in $string, adjusted for backslashes.
   */
  function _has_unclosed_brackets_sub($string, &$num, $char)
  {
    $parts = explode($char, $string);
    for ($i = 0; $i < count($parts); $i++)
    {
      if (substr($parts[$i], -1) == '\\' || $this->_has_unclosed_quotes($parts[$i]))
				$num--;
      if (isset($parts[$i + 1]))
				$parts[$i + 1] = $parts[$i] . $char . $parts[$i + 1];
    }
    
    return $num;
  }


  function _validate_address($address)
  {
    $is_group = false;

    if ($address['group'])
    {
      $is_group = true;

      $parts     = explode(':', $address['address']);
      $groupname = $this->_split_check($parts, ':');
      $structure = array();

      // And validate the group part of the name.
      if (!$this->_validate_phrase($groupname))
      {
        $this->_error = 'Group name did not validate.';
        return false;
      }
      else
      {
        // Don't include groups if we are not nesting
        // them. This avoids returning invalid addresses.
        if ($this->_nest_groups)
        {
            $structure = new stdClass;
            $structure->groupname = $groupname;
        }
      }

      $address['address'] = ltrim(substr($address['address'], strlen($groupname . ':')));
    }

    // If a group then split on comma and put into an array.
    // Otherwise, Just put the whole address in an array.
    if ($is_group)
    {
      while (strlen($address['address']) > 0)
      {
        $parts       = explode(',', $address['address']);
        $addresses[] = $this->_split_check($parts, ',');
        $address['address'] = trim(substr($address['address'], strlen(end($addresses) . ',')));
      }
    }
    else
			$addresses[] = $address['address'];

    // Check that $addresses is set, if address like this:
    // Groupname:;
    // Then errors were appearing.
    if (!isset($addresses))
    {
        $this->_error = 'Empty group.';
        return false;
    }

    for ($i = 0; $i < count($addresses); $i++)
        $addresses[$i] = trim($addresses[$i]);

    // Validate each mailbox.
    // Format could be one of: name <geezer@domain.com>
    //                         geezer@domain.com
    //                         geezer
    // ... or any other format valid by RFC 822.
    array_walk($addresses, array($this, 'validate_mailbox'));

    // Nested format
    if ($this->_nest_groups)
    {
      if ($is_group)
				$structure->addresses = $addresses;
      else
				$structure = $addresses[0];
    }
    // Flat format
    else
    {
      if ($is_group)
				$structure = array_merge($structure, $addresses);
      else
				$structure = $addresses;
    }

    return $structure;
  }


  function _validate_phrase($phrase)
  {
    // Splits on one or more Tab or space.
    $parts = preg_split('/[ \\x09]+/', $phrase, -1, PREG_SPLIT_NO_EMPTY);

    $phrase_parts = array();
    while (count($parts) > 0)
    {
      $phrase_parts[] = $this->_split_check($parts, ' ');
      for ($i = 0; $i < $this->_index + 1; $i++)
        array_shift($parts);
    }

    for ($i = 0; $i < count($phrase_parts); $i++)
    {
      if (substr($phrase_parts[$i], 0, 1) == '"')
      {
        if (!$this->_validate_quoted_string($phrase_parts[$i]))
            return false;
        continue;
      }

      if (!$this->_validate_atom($phrase_parts[$i]))
      	return false;
    }

    return true;
  }

  /**
   * Function to validate an atom which from rfc822 is:
   * atom = 1*<any CHAR except specials, SPACE and CTLs>
   * 
   * If validation ($this->_validate_atoms) has been turned off, then
   * validateAtom() doesn't actually check anything. This is so that you
   * can split a list of addresses up before encoding personal names
   * (umlauts, etc.), for example.
   * 
   * @access private
   * @param string $atom The string to check.
   * @return boolean Success or failure.
   */
  function _validate_atom($atom)
  {
    // Validation has been turned off; assume the atom is okay.
    if (!$this->_validate_atoms)
			return true;

    // Check for any char from ASCII 0 - ASCII 127
    if (!preg_match('/^[\\x00-\\x7E]+$/i', $atom, $matches))
			return false;

    // Check for specials:
    if (preg_match('/[][()<>@,;\\:". ]/', $atom))
			return false;

    // Check for control characters (ASCII 0-31):
    if (preg_match('/[\\x00-\\x1F]+/', $atom))
			return false;

    return true;
  }


  function _validate_quoted_string($qstring)
  {
    // Leading and trailing "
    $qstring = substr($qstring, 1, -1);

    return !(preg_match('/(.)[\x0D\\\\"]/', $qstring, $matches) && $matches[1] != '\\');
  }

  function _exclude_comments($mailbox)
  {
    $comment = '';

    while (strlen(trim($mailbox)) > 0)
    {
      $parts = explode('(', $mailbox);
      $before_comment = $this->_split_check($parts, '(');
      if ($before_comment != $mailbox)
      {
        // First char should be a (
        $comment    = substr(str_replace($before_comment, '', $mailbox), 1);
        $parts      = explode(')', $comment);
        $comment    = $this->_split_check($parts, ')');
        $comments[] = $comment;

        $mailbox   = substr($mailbox, strpos($mailbox, $comment)+strlen($comment)+1);
      }
      else
      	break;
    }

    for($i=0; $i < count(@$comments); $i++)
			$mailbox = str_replace('('.$comments[$i].')', '', $mailbox);

    $mailbox = trim($mailbox);
    return array(
    	'mailbox' => $mailbox,
    	'comments' => $comments
    );
  }

  function validate_mailbox(&$mailbox)
  {
    $phrase  = '';

		$clear_mailbox = $this->_exclude_comments($mailbox);
		$mailbox = $clear_mailbox['mailbox'];
		$comments = $clear_mailbox['comments'];
		
    // Check for name + route-addr
    if (substr($mailbox, -1) == '>' && substr($mailbox, 0, 1) != '<')
    {
      $parts  = explode('<', $mailbox);
      $name   = $this->_split_check($parts, '<');

      $phrase     = trim($name);
      $route_addr = trim(substr($mailbox, strlen($name.'<'), -1));

      if ($this->_validate_phrase($phrase) === false || ($route_addr = $this->_validate_route_addr($route_addr)) === false)
          return false;
		}
    else
    {
      // First snip angle brackets if present.
      if (substr($mailbox,0,1) == '<' && substr($mailbox,-1) == '>')
        $addr_spec = substr($mailbox,1,-1);
      else
        $addr_spec = $mailbox;

      if (($addr_spec = $this->_validate_addr_spec($addr_spec)) === false)
	      return false;
    }

    $return = new stdClass();

    $return->personal = $phrase;
    $return->comment  = isset($comments) ? $comments : array();

    if (isset($route_addr))
    {
      $return->mailbox = $route_addr['local_part'];
      $return->host    = $route_addr['domain'];
      $route_addr['adl'] !== '' ? $return->adl = $route_addr['adl'] : '';
    }
    else
    {
      $return->mailbox = $addr_spec['local_part'];
      $return->host    = $addr_spec['domain'];
    }

    $mailbox = $return;
    return true;
  }


  function _validate_route_addr($route_addr)
  {
    if (strpos($route_addr, ':') !== false)
    {
      $parts = explode(':', $route_addr);
      $route = $this->_split_check($parts, ':');
    }
    else
			$route = $route_addr;

    if ($route === $route_addr)
    {
      unset($route);
      $addr_spec = $route_addr;
      if (($addr_spec = $this->_validate_addr_spec($addr_spec)) === false)
        return false;
    }
    else
    {
      if (($route = $this->_validate_route($route)) === false)
        return false;

      $addr_spec = substr($route_addr, strlen($route . ':'));

      if (($addr_spec = $this->_validate_addr_spec($addr_spec)) === false)
        return false;
    }

    if (isset($route))
      $return['adl'] = $route;
    else
      $return['adl'] = '';

    $return = array_merge($return, $addr_spec);
    return $return;
  }


  function _validate_route($route)
  {
    $domains = explode(',', trim($route));

    for ($i = 0; $i < count($domains); $i++) 
    {
      $domains[$i] = str_replace('@', '', trim($domains[$i]));
      if (!$this->_validate_domain($domains[$i]))
      	return false;
    }

    return $route;
  }


  function _validate_domain($domain)
  {
    $subdomains_splitted = explode('.', $domain);

    while (count($subdomains_splitted) > 0)
    {
      $sub_domains[] = $this->_split_check($subdomains_splitted, '.');
      for ($i = 0; $i < $this->_index + 1; $i++)
          array_shift($subdomains_splitted);
    }

    for ($i = 0; $i < count($sub_domains); $i++)
    {
      if (!$this->_validate_subdomain(trim($sub_domains[$i])))
          return false;
    }

    return $domain;
  }


  function _validate_subdomain($subdomain)
  {
    if (preg_match('|^\[(.*)]$|', $subdomain, $arr))
    {
        if (!$this->_validate_domain_literal($arr[1]))
        	return false;
    }
    else
      if (!$this->_validate_atom($subdomain)) 
      	return false;

    return true;
  }


  function _validate_domain_literal($dliteral)
  {
		return !preg_match('/(.)[][\x0D\\\\]/', $dliteral, $matches) && $matches[1] != '\\';
  }


  function _validate_addr_spec($addr_spec)
  {
    $addr_spec = trim($addr_spec);

    if (strpos($addr_spec, '@') !== false)
    {
      $parts      = explode('@', $addr_spec);
      $local_part = $this->_split_check($parts, '@');
      $domain     = substr($addr_spec, strlen($local_part . '@'));
    }
    else
    {
      $local_part = $addr_spec;
      $domain     = $this->_default_domain;
    }

    if (($local_part = $this->_validate_address_local_part($local_part)) === false)
    	return false;

    if (($domain     = $this->_validate_domain($domain)) === false)
    	return false;
    
    return array('local_part' => $local_part, 'domain' => $domain);
  }


  function _validate_address_local_part($local_part)
  {
    $parts = explode('.', $local_part);

    while (count($parts) > 0)
    {
      $words[] = $this->_split_check($parts, '.');
      for ($i = 0; $i < $this->_index + 1; $i++)
      	array_shift($parts);
    }

    for ($i = 0; $i < count($words); $i++)
			if ($this->_validate_phrase(trim($words[$i])) === false)
				return false;

    return $local_part;
  }


  function approximate_count($data)
  {
      return count(preg_split('/(?<!\\\\),/', $data));
  }
  

  function is_valid_inet_address($data, $strict = false)
  {
		if($strict)
			$regex = '/^([.0-9a-z_-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i';
		else
			$regex = '/^([*+!.&#$|\'\\%\/0-9a-z^_`{}=?~:-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i';

    if (preg_match($regex, trim($data), $matches_address))
      return array($matches_address[1], $matches_address[2]);
    else
			return false;
  }
}
?>					