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
class mail_rfc822
{
	var $address = '';

	var $default_domain = 'localhost';

	var $nest_groups = true;

	var $validate = true;

	var $addresses = array();

	var $structure = array();

	var $error = null;

	var $index = null;

	var $num_groups = 0;

	var $mail_rfc822 = true;

	var $limit = null;

	function mail_rfc822($address = null, $default_domain = null, $nest_groups = null, $validate = null, $limit = null)
	{
		if (isset($address)) $this->address = $address;
		if (isset($default_domain)) $this->default_domain = $default_domain;
		if (isset($nest_groups)) $this->nest_groups = $nest_groups;
		if (isset($validate)) $this->validate = $validate;
		if (isset($limit)) $this->limit = $limit;
	} 

	function parse_address_list($address = null, $default_domain = null, $nest_groups = null, $validate = null, $limit = null)
	{
		if (!isset($this->mail_rfc822))
		{
			$obj = new mail_rfc822($address, $default_domain, $nest_groups, $validate, $limit);
			return $obj->parse_address_list();
		} 

		if (isset($address)) $this->address = $address;
		if (isset($default_domain)) $this->default_domain = $default_domain;
		if (isset($nest_groups)) $this->nest_groups = $nest_groups;
		if (isset($validate)) $this->validate = $validate;
		if (isset($limit)) $this->limit = $limit;

		$this->structure = array();
		$this->addresses = array();
		$this->error = null;
		$this->index = null;

		while ($this->address = $this->_split_addresses($this->address))
		{
			continue;
		} 

		if ($this->address === false || isset($this->error))
		{
			return false;
		} 
		set_time_limit(30);
		for ($i = 0; $i < count($this->addresses); $i++)
		{
			if (($return = $this->_validate_address($this->addresses[$i])) === false || isset($this->error))
			{
				return false;
			} 

			if (!$this->nest_groups)
			{
				$this->structure = array_merge($this->structure, $return);
			} 
			else
			{
				$this->structure[] = $return;
			} 
		} 

		return $this->structure;
	} 

	function _split_addresses($address)
	{
		if (!empty($this->limit) AND count($this->addresses) == $this->limit)
		{
			return '';
		} 

		if ($this->_is_group($address) && !isset($this->error))
		{
			$split_char = ';';
			$is_group = true;
		} elseif (!isset($this->error))
		{
			$split_char = ',';
			$is_group = false;
		} elseif (isset($this->error))
		{
			return false;
		} 
		$parts = explode($split_char, $address);
		$string = $this->_split_check($parts, $split_char);
		if ($is_group)
		{
			if (strpos($string, ':') === false)
			{
				$this->error = 'Invalid address: ' . $string;
				return false;
			} 
			if (!$this->_split_check(explode(':', $string), ':'))
				return false;
			$this->num_groups++;
		} 
		$this->addresses[] = array('address' => trim($string),
			'group' => $is_group
			);
		$address = trim(substr($address, strlen($string) + 1));
		if ($is_group && substr($address, 0, 1) == ',')
		{
			$address = trim(substr($address, 1));
			return $address;
		} elseif (strlen($address) > 0)
		{
			return $address;
		} 
		else
		{
			return '';
		} 
		return false;
	} 

	function _is_group($address)
	{
		$parts = explode(',', $address);
		$string = $this->_split_check($parts, ',');
		if (count($parts = explode(':', $string)) > 1)
		{
			$string2 = $this->_split_check($parts, ':');
			return ($string2 !== $string);
		} 
		else
		{
			return false;
		} 
	} 

	function _split_check($parts, $char)
	{
		$string = $parts[0];

		for ($i = 0; $i < count($parts); $i++)
		{
			if ($this->_has_unclosed_quotes($string) || $this->_has_unclosed_brackets($string, '<>') || $this->_has_unclosed_brackets($string, '[]') || $this->_has_unclosed_brackets($string, '()') || substr($string, -1) == '\\')
			{
				if (isset($parts[$i + 1]))
				{
					$string = $string . $char . $parts[$i + 1];
				} 
				else
				{
					$this->error = 'Invalid address spec. Unclosed bracket or quotes';
					return false;
				} 
			} 
			else
			{
				$this->index = $i;
				break;
			} 
		} 

		return $string;
	} 

	function _has_unclosed_quotes($string)
	{
		$string = explode('"', $string);
		$string_cnt = count($string);

		for ($i = 0; $i < (count($string) - 1); $i++)
		if (substr($string[$i], -1) == '\\')
			$string_cnt--;

		return ($string_cnt % 2 === 0);
	} 

	function _has_unclosed_brackets($string, $chars)
	{
		$num_angle_start = substr_count($string, $chars[0]);
		$num_angle_end = substr_count($string, $chars[1]);

		$this->_has_unclosed_brackets_sub($string, $num_angle_start, $chars[0]);
		$this->_has_unclosed_brackets_sub($string, $num_angle_end, $chars[1]);

		if ($num_angle_start < $num_angle_end)
		{
			$this->error = 'Invalid address spec. Unmatched quote or bracket (' . $chars . ')';
			return false;
		} 
		else
		{
			return ($num_angle_start > $num_angle_end);
		} 
	} 

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
			$parts = explode(':', $address['address']);
			$groupname = $this->_split_check($parts, ':');
			$structure = array();
			if (!$this->_validate_phrase($groupname))
			{
				$this->error = 'Group name did not validate.';
				return false;
			} 
			else
			{
				if ($this->nest_groups)
				{
					$structure = new stdclass();
					$structure->groupname = $groupname;
				} 
			} 

			$address['address'] = ltrim(substr($address['address'], strlen($groupname . ':')));
		} 
		if ($is_group)
		{
			while (strlen($address['address']) > 0)
			{
				$parts = explode(',', $address['address']);
				$addresses[] = $this->_split_check($parts, ',');
				$address['address'] = trim(substr($address['address'], strlen(end($addresses) . ',')));
			} 
		} 
		else
		{
			$addresses[] = $address['address'];
		} 
		if (!isset($addresses))
		{
			$this->error = 'Empty group.';
			return false;
		} 

		for ($i = 0; $i < count($addresses); $i++)
		{
			$addresses[$i] = trim($addresses[$i]);
		} 
		array_walk($addresses, array($this, 'validate_mailbox'));
		if ($this->nest_groups)
		{
			if ($is_group)
			{
				$structure->addresses = $addresses;
			} 
			else
			{
				$structure = $addresses[0];
			} 
		} 
		else
		{
			if ($is_group)
			{
				$structure = array_merge($structure, $addresses);
			} 
			else
			{
				$structure = $addresses;
			} 
		} 

		return $structure;
	} 

	function _validate_phrase($phrase)
	{
		$parts = preg_split('/[ \\x09]+/', $phrase, -1, PREG_SPLIT_NO_EMPTY);

		$phrase_parts = array();
		while (count($parts) > 0)
		{
			$phrase_parts[] = $this->_split_check($parts, ' ');
			for ($i = 0; $i < $this->index + 1; $i++)
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
			if (!$this->_validate_atom($phrase_parts[$i])) return false;
		} 

		return true;
	} 

	function _validate_atom($atom)
	{
		if (!$this->validate)
		{
			return true;
		} 
		if (!preg_match('/^[\\x00-\\x7E]+$/i', $atom, $matches))
		{
			return false;
		} 
		if (preg_match('/[][()<>@,;\\:". ]/', $atom))
		{
			return false;
		} 
		if (preg_match('/[\\x00-\\x1F]+/', $atom))
		{
			return false;
		} 

		return true;
	} 

	function _validate_quoted_string($qstring)
	{
		$qstring = substr($qstring, 1, -1);
		return !(preg_match('/(.)[\x0D\\\\"]/', $qstring, $matches) && $matches[1] != '\\');
	} 

	function validate_mailbox(&$mailbox)
	{
		$phrase = '';
		$comment = '';
		$_mailbox = $mailbox;
		while (strlen(trim($_mailbox)) > 0)
		{
			$parts = explode('(', $_mailbox);
			$before_comment = $this->_split_check($parts, '(');
			if ($before_comment != $_mailbox)
			{
				$comment = substr(str_replace($before_comment, '', $_mailbox), 1);
				$parts = explode(')', $comment);
				$comment = $this->_split_check($parts, ')');
				$comments[] = $comment;
				$_mailbox = substr($_mailbox, strpos($_mailbox, $comment) + strlen($comment) + 1);
			} 
			else
			{
				break;
			} 
		} 

		for($i = 0; $i < count(@$comments); $i++)
		{
			$mailbox = str_replace('(' . $comments[$i] . ')', '', $mailbox);
		} 
		$mailbox = trim($mailbox);
		if (substr($mailbox, -1) == '>' && substr($mailbox, 0, 1) != '<')
		{
			$parts = explode('<', $mailbox);
			$name = $this->_split_check($parts, '<');

			$phrase = trim($name);
			$route_addr = trim(substr($mailbox, strlen($name . '<'), -1));

			if ($this->_validate_phrase($phrase) === false || ($route_addr = $this->_validate_route_addr($route_addr)) === false)
				return false;
		} 
		else
		{
			if (substr($mailbox, 0, 1) == '<' && substr($mailbox, -1) == '>')
				$addr_spec = substr($mailbox, 1, -1);
			else
				$addr_spec = $mailbox;

			if (($addr_spec = $this->_validate_addr_spec($addr_spec)) === false)
				return false;
		} 
		$mbox = new stdclass();
		$mbox->personal = $phrase;
		$mbox->comment = isset($comments) ? $comments : array();

		if (isset($route_addr))
		{
			$mbox->mailbox = $route_addr['local_part'];
			$mbox->host = $route_addr['domain'];
			$route_addr['adl'] !== '' ? $mbox->adl = $route_addr['adl'] : '';
		} 
		else
		{
			$mbox->mailbox = $addr_spec['local_part'];
			$mbox->host = $addr_spec['domain'];
		} 

		$mailbox = $mbox;
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
		{
			$route = $route_addr;
		} 
		if ($route === $route_addr)
		{
			unset($route);
			$addr_spec = $route_addr;
			if (($addr_spec = $this->_validate_addr_spec($addr_spec)) === false)
			{
				return false;
			} 
		} 
		else
		{
			if (($route = $this->_validate_route($route)) === false)
			{
				return false;
			} 

			$addr_spec = substr($route_addr, strlen($route . ':'));
			if (($addr_spec = $this->_validate_addr_spec($addr_spec)) === false)
			{
				return false;
			} 
		} 

		if (isset($route))
		{
			$return['adl'] = $route;
		} 
		else
		{
			$return['adl'] = '';
		} 

		$return = array_merge($return, $addr_spec);
		return $return;
	} 

	function _validate_route($route)
	{
		$domains = explode(',', trim($route));

		for ($i = 0; $i < count($domains); $i++)
		{
			$domains[$i] = str_replace('@', '', trim($domains[$i]));
			if (!$this->_validate_domain($domains[$i])) return false;
		} 

		return $route;
	} 

	function _validate_domain($domain)
	{
		$subdomains = explode('.', $domain);

		while (count($subdomains) > 0)
		{
			$sub_domains[] = $this->_split_check($subdomains, '.');
			for ($i = 0; $i < $this->index + 1; $i++)
			array_shift($subdomains);
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
			if (!$this->_validate_dliteral($arr[1])) return false;
		} 
		else
		{
			if (!$this->_validate_atom($subdomain)) return false;
		} 
		return true;
	} 

	function _validate_dliteral($dliteral)
	{
		return !preg_match('/(.)[][\x0D\\\\]/', $dliteral, $matches) && $matches[1] != '\\';
	} 

	function _validate_addr_spec($addr_spec)
	{
		$addr_spec = trim($addr_spec);
		if (strpos($addr_spec, '@') !== false)
		{
			$parts = explode('@', $addr_spec);
			$local_part = $this->_split_check($parts, '@');
			$domain = substr($addr_spec, strlen($local_part . '@'));
		} 
		else
		{
			$local_part = $addr_spec;
			$domain = $this->default_domain;
		} 

		if (($local_part = $this->_validate_local_part($local_part)) === false) return false;
		if (($domain = $this->_validate_domain($domain)) === false) return false;
		return array('local_part' => $local_part, 'domain' => $domain);
	} 

	function _validate_local_part($local_part)
	{
		$parts = explode('.', $local_part);
		while (count($parts) > 0)
		{
			$words[] = $this->_split_check($parts, '.');
			for ($i = 0; $i < $this->index + 1; $i++)
			{
				array_shift($parts);
			} 
		} 
		for ($i = 0; $i < count($words); $i++)
		{
			if ($this->_validate_phrase(trim($words[$i])) === false) return false;
		} 
		return $local_part;
	} 

	function approximate_count($data)
	{
		return count(preg_split('/(?<!\\\\),/', $data));
	} 

	function is_valid_inet_address($data, $strict = false)
	{
		$regex = $strict ? '/^([.0-9a-z_-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i' : '/^([*+!.&#$|\'\\%\/0-9a-z^_`{}=?~:-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i';
		if (preg_match($regex, trim($data), $matches))
		{
			return array($matches[1], $matches[2]);
		} 
		else
		{
			return false;
		} 
	} 
} 

?>