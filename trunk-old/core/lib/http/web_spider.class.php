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

define('WEB_SPIDER_ITERATION_LIMIT', 100000);
//define('WEB_SPIDER_ITERATION_LIMIT', 10);//for test purposes
define('WEB_SPIDER_TIME_LIMIT', 30000);

require_once(LIMB_DIR . 'core/lib/http/uri.class.php');

class web_spider
{
	var $_host_bind = '';
	
	var $_uri_contents = array();
	var $_iteration_counter = 0;
	
	var $_uri = null;
	
	function web_spider()
	{			
		$this->_uri = new uri();
	}
	
	function bind_to_host($host)
	{
		$this->_uri->parse($host);
		
		$this->_host_bind = $this->_uri->host;
	}
	
	function & crawl($init_uri)
	{
		set_time_limit(WEB_SPIDER_TIME_LIMIT);

		$this->_reset();
		
		progress :: process_start('crawl');
						
		$this->_crawl_recursive($this->_normalize_uri($init_uri));
		
		progress :: process_end('crawl');
		
		return $this->_uri_contents;
	}
	
	function _reset()
	{
		$this->_iteration_counter = 0;
		$this->_uri_contents = array();
	}
	
	function _crawl_recursive($uri)
	{														
		if(++$this->_iteration_counter > WEB_SPIDER_ITERATION_LIMIT)
			return;
		
		progress :: write_notice("$uri");
		
		$content = '';
		
		if($fd = fopen($uri, 'r'))
		{
			while($line = fgets($fd, 2000))
				$content .= $line;
			
			fclose ($fd);
		}
		
		if($content)
		{
			$this->_uri_contents[$uri] =& $content;
			$uris = $this->_get_content_hrefs(& $content);
			
			foreach($uris as $uri)
			{
				if(!($uri = $this->_normalize_uri($uri)))
					continue;
				
				if(isset($this->_uri_contents[$uri]))
					continue;
					
				$this->_crawl_recursive($uri);
			}
		}
	}
	
	function _normalize_uri($uri)
	{
		$this->_uri->parse($uri);
		
		$host = $this->_uri->host;
		
		if(	$this->_host_bind && 
				$host != $this->_host_bind)
			return;

		$this->_uri->remove_query_item('PHPSESSID');
		$this->_uri->anchor = '';
						
		return $this->_uri->get_url();
	}
			
	function _get_content_hrefs(& $content)
	{
		preg_match_all('/(<a.*?href=(?:"|\'|)([^"\'>\s]+)(?:"|\'|).*?>)(.*?)<\/a>/', $content, $matches, PREG_SET_ORDER);
		
		$hrefs = array();
	
		for ($i=0; $i < sizeof($matches); $i++) 
			$hrefs[] = $matches[$i][2];
		
		return $hrefs;
	}	
}
?>