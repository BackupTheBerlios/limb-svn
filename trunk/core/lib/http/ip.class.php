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

class ip
{
	//Accepts string representation of ip addresess with wildcards and ranges.
	//IPs come delimetered with ,
	//May accept IP: 
	//	--	nnn.nnn.nnn.nnn
	//	--	*|nnn.*|nnn.*|nnn.*|nnn
	//	--	nnn.nnn.nnn.nnn - nnn.nnn.nnn.nnn
	//Returns an array of hexed IPs
	
	function process_ip_range($ip_range)
	{
		$ip_list = array();
		$ip_list_temp = explode(',', $ip_range);

		for($i = 0; $i < count($ip_list_temp); $i++)
		{
			if ( preg_match('/^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})[ ]*\-[ ]*([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$/', trim($ip_list_temp[$i]), $ip_range_explode) )
			{
				$ip_1_counter = $ip_range_explode[1];
				$ip_1_end = $ip_range_explode[5];

				while ( $ip_1_counter <= $ip_1_end )
				{
					$ip_2_counter = ( $ip_1_counter == $ip_range_explode[1] ) ? $ip_range_explode[2] : 0;
					$ip_2_end = ( $ip_1_counter < $ip_1_end ) ? 254 : $ip_range_explode[6];

					if ( $ip_2_counter == 0 && $ip_2_end == 254 )
					{
						$ip_2_counter = 255;
						$ip_2_fragment = 255;

						$ip_list[] = ip :: encode_ip("{$ip_1_counter}.255.255.255");
					}

					while ( $ip_2_counter <= $ip_2_end )
					{
						$ip_3_counter = ( $ip_2_counter == $ip_range_explode[2] && $ip_1_counter == $ip_range_explode[1] ) ? $ip_range_explode[3] : 0;
						$ip_3_end = ( $ip_2_counter < $ip_2_end || $ip_1_counter < $ip_1_end ) ? 254 : $ip_range_explode[7];

						if ( $ip_3_counter == 0 && $ip_3_end == 254 )
						{
							$ip_3_counter = 255;
							$ip_3_fragment = 255;

							$ip_list[] = ip :: encode_ip("{$ip_1_counter}.{$ip_2_counter}.255.255");
						}

						while ( $ip_3_counter <= $ip_3_end )
						{
							$ip_4_counter = ( $ip_3_counter == $ip_range_explode[3] && $ip_2_counter == $ip_range_explode[2] && $ip_1_counter == $ip_range_explode[1] ) ? $ip_range_explode[4] : 0;
							$ip_4_end = ( $ip_3_counter < $ip_3_end || $ip_2_counter < $ip_2_end ) ? 254 : $ip_range_explode[8];

							if ( $ip_4_counter == 0 && $ip_4_end == 254 )
							{
								$ip_4_counter = 255;
								$ip_4_fragment = 255;

								$ip_list[] = ip :: encode_ip("{$ip_1_counter}.{$ip_2_counter}.{$ip_3_counter}.255");
							}

							while ( $ip_4_counter <= $ip_4_end )
							{
								$ip_list[] = ip :: encode_ip("{$ip_1_counter}.{$ip_2_counter}.{$ip_3_counter}.{$ip_4_counter}");
								$ip_4_counter++;
							}
							$ip_3_counter++;
						}
						$ip_2_counter++;
					}
					$ip_1_counter++;
				}
			}
			elseif ( preg_match('/^([0-9\*]{1,3})\.([0-9\*]{1,3})\.([0-9\*]{1,3})\.([0-9\*]{1,3})$/', trim($ip_list_temp[$i])) )
			{
				$ip_list[] = ip :: encode_ip(str_replace('*', '255', trim($ip_list_temp[$i])));
			}
		}
		
		return $ip_list;
	}
	
	function encode_ip($ip=null)
	{		
		$ip_sep = explode('.', $ip);
			
		return sprintf('%02x%02x%02x%02x', $ip_sep[0], $ip_sep[1], $ip_sep[2], $ip_sep[3]);
	}
	
	function decode_ip($hex_ip)
	{
		$hexipbang = explode('.', chunk_split($hex_ip, 2, '.'));
		return hexdec($hexipbang[0]). '.' . hexdec($hexipbang[1]) . '.' . hexdec($hexipbang[2]) . '.' . hexdec($hexipbang[3]);
	}

}

?>