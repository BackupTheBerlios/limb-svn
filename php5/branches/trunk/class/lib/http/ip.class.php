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

class ip
{	
	//Returns an array of hexed IPs
	static public function encode_ip_range($ip_begin, $ip_end)
	{
		$ip_regex = '/^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$/';
		
		if( ! preg_match($ip_regex, $ip_begin, $ip_begin_range_explode) ||
				! preg_match($ip_regex, $ip_end, $ip_end_range_explode))
				return array();
		
		$ip_list = array();
		
		$ip_1_counter = $ip_begin_range_explode[1];
		$ip_1_end = $ip_end_range_explode[1];

		while ( $ip_1_counter <= $ip_1_end )
		{
			$ip_2_counter = ( $ip_1_counter == $ip_begin_range_explode[1] ) ? $ip_begin_range_explode[2] : 0;
			$ip_2_end = ( $ip_1_counter < $ip_1_end ) ? 254 : $ip_end_range_explode[2];

			if ( $ip_2_counter == 0 && $ip_2_end == 254 )
			{
				$ip_2_counter = 255;
				$ip_2_fragment = 255;

				$ip_list[] = self :: encode_ip("{$ip_1_counter}.255.255.255");
			}

			while ( $ip_2_counter <= $ip_2_end )
			{
				$ip_3_counter = ( $ip_2_counter == $ip_begin_range_explode[2] && $ip_1_counter == $ip_begin_range_explode[1] ) ? $ip_begin_range_explode[3] : 0;
				$ip_3_end = ( $ip_2_counter < $ip_2_end || $ip_1_counter < $ip_1_end ) ? 254 : $ip_end_range_explode[3];

				if ( $ip_3_counter == 0 && $ip_3_end == 254 )
				{
					$ip_3_counter = 255;
					$ip_3_fragment = 255;

					$ip_list[] = self :: encode_ip("{$ip_1_counter}.{$ip_2_counter}.255.255");
				}

				while ( $ip_3_counter <= $ip_3_end )
				{
					$ip_4_counter = ( $ip_3_counter == $ip_begin_range_explode[3] && $ip_2_counter == $ip_begin_range_explode[2] && $ip_1_counter == $ip_begin_range_explode[1] ) ? $ip_begin_range_explode[4] : 0;
					$ip_4_end = ( $ip_3_counter < $ip_3_end || $ip_2_counter < $ip_2_end ) ? 254 : $ip_end_range_explode[4];

					if ( $ip_4_counter == 0 && $ip_4_end == 254 )
					{
						$ip_4_counter = 255;
						$ip_4_fragment = 255;

						$ip_list[] = self :: encode_ip("{$ip_1_counter}.{$ip_2_counter}.{$ip_3_counter}.255");
					}

					while ( $ip_4_counter <= $ip_4_end )
					{
						$ip_list[] = self :: encode_ip("{$ip_1_counter}.{$ip_2_counter}.{$ip_3_counter}.{$ip_4_counter}");
						$ip_4_counter++;
					}
					$ip_3_counter++;
				}
				$ip_2_counter++;
			}
			$ip_1_counter++;
		}
		
		return $ip_list;
	}
	
	static public function encode_ip($ip)
	{		
		$ip_sep = explode('.', $ip);
			
		return sprintf('%02x%02x%02x%02x', $ip_sep[0], $ip_sep[1], $ip_sep[2], $ip_sep[3]);
	}
	
	static public function decode_ip($hex_ip)
	{
		$hexipbang = explode('.', chunk_split($hex_ip, 2, '.'));
		
		return hexdec($hexipbang[0]). '.' . hexdec($hexipbang[1]) . '.' . hexdec($hexipbang[2]) . '.' . hexdec($hexipbang[3]);
	}
	
	static public function is_valid($ip)
	{
		return preg_match('/^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$/', $ip);
	}

}

?>