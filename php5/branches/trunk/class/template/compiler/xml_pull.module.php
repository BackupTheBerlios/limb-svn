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

if (!defined('XML_HTMLSAX'))
	define('XML_HTMLSAX', 'XML/');

if (!@include(XML_HTMLSAX . 'XML_HTMLSax.php'))
	error('PEARMODULEREQUIRED', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('module' => 'XML_HTMLSax'));

/**
* Define XML events
*/
define('XML_OPEN', 1);
define('XML_CLOSE', 2);
define('XML_TEXT', 3);
define('XML_PI', 4);
define('XML_ESCAPE', 5); // HTMLSax only
define('XML_JASP', 6); // HTMLSax only

/**
* Wraps SAX parser to provide an XML pull API
*/
class xml_pull
{
	/**
	* Text to be parsed
	* 
	* @var string 
	* @access private 
	*/
	var $rawtext;
	/**
	* Instance of a SAX parser
	* 
	* @var string 
	* @access private 
	*/
	var $saxparser;
	/**
	* FIFO queue for XML events
	* 
	* @var array 
	* @access private 
	*/
	var $stack = array();
	/**
	* Switch for whether SAX parsing has begun
	* 
	* @var boolean (default = false)
	* @access private 
	*/
	var $started = false;
	/**
	* Constructs the xml_pull parser
	* 
	* @param string $ raw document to parse
	* @access protected 
	*/
	function xml_pull ($rawtext)
	{
		$this->rawtext = $rawtext;
		$this->saxparser = &new XML_HTMLSax();
		$this->saxparser->set_object($this);
		$this->saxparser->set_element_handler('open', 'close');
		$this->saxparser->set_data_handler('data');
		$this->saxparser->set_pi_handler('pi');
		$this->saxparser->set_escape_handler('escape');
		$this->saxparser->set_jasp_handler('jasp');
	} 
	/**
	* Sax Open Handler
	* 
	* @param XML $ _HTMLSax instance of the parser
	* @param string $ tag name
	* @param array $ attributes
	* @return void 
	* @access private 
	*/
	function open($parser, $tag, $attrs)
	{
		$this->stack[] = new xml_open($tag, $attrs, $parser->get_current_position());
	} 
	/**
	* Sax Close Handler
	* 
	* @param XML $ _HTMLSax instance of the parser
	* @param string $ tag name
	* @return void 
	* @access private 
	*/
	function close($parser, $tag)
	{
		$this->stack[] = new xml_close($tag, $parser->get_current_position());
	} 
	/**
	* Sax Data Handler
	* 
	* @param XML $ _HTMLSax instance of the parser
	* @param string $ text content in tag
	* @return void 
	* @access private 
	*/
	function data($parser, $data)
	{
		$this->stack[] = new xml_data($data, $parser->get_current_position());
	} 
	/**
	* Sax Processing Instruction Handler
	* 
	* @param XML $ _HTMLSax instance of the parser
	* @param string $ target processor (e.g. php)
	* @param string $ text content in PI
	* @return void 
	* @access private 
	*/
	function pi($parser, $target, $data)
	{
		$this->stack[] = new xml_pi($target, $data, $parser->get_current_position());
	} 
	/**
	* Sax XML Escape Handler
	* 
	* @param XML $ _HTMLSax instance of the parser
	* @param string $ text content in escape
	* @return void 
	* @access private 
	*/
	function escape($parser, $data)
	{
		$this->stack[] = new xml_escape($data, $parser->get_current_position());
	} 
	/**
	* Sax XML Jasp Handler
	* 
	* @param XML $ _HTMLSax instance of the parser
	* @param string $ text content in JASP block
	* @return void 
	* @access private 
	*/
	function jasp($parser, $data)
	{
		$this->stack[] = new xml_jasp($data, $parser->get_current_position());
	} 
	/**
	* Returns an XML event from the stack
	* 
	* @return mixed NULL when finished or subclass of xml_event
	* @access private 
	*/
	function parse()
	{
		if ($this->started)
		{
			return array_shift($this->stack);
		} 
		else
		{
			$this->saxparser->parse($this->rawtext);
			$this->started = true;
			return $this->parse();
		} 
	} 
} 
// --------------------------------------------------------------------------------
/**
* Base class for representing SAX XML events
* 
* @abstract 
* @package WACT_TEMPLATE
*/
class xml_event
{
	/**
	* Type of event
	* 
	* @var string 
	* @access private 
	*/
	var $type;
	/**
	* Strpos in XML document
	* 
	* @var string 
	* @access private 
	*/
	var $position;
	/**
	* Constructs xml_event
	* 
	* @param string $ type of event
	* @access protected 
	*/
	function xml_event ($type, $position)
	{
		$this->type = $type;
		$this->position = $position;
	} 
	/**
	* Returns the type of event
	* 
	* @return string type of event
	* @access public 
	*/
	function type()
	{
		return $this->type;
	} 
	function position()
	{
		return $this->position;
	} 
} 
// --------------------------------------------------------------------------------
/**
* Represents an XML start element
* 
* @access public 
* @package WACT_TEMPLATE
*/
class xml_open extends xml_event
{
	/**
	* tag name
	* 
	* @var string 
	* @access private 
	*/
	var $tag;
	/**
	* tag attributes
	* 
	* @var array 
	* @access private 
	*/
	var $attribs;
	/**
	* Constructs StartElement
	* 
	* @param string $ tag name
	* @param array $ tag attributes
	* @access public 
	*/
	function xml_open ($tag, $attribs = array(), $position)
	{
		xml_event::xml_event(XML_OPEN, $position);
		$this->tag = $tag;
		$this->attribs = $attribs;
	} 
	/**
	* Returns the tag name
	* 
	* @return string 
	* @access public 
	*/
	function tag()
	{
		return $this->tag;
	} 
	/**
	* Returns the attributes
	* Converts PEAR::XML_HTMLSax null attributes
	* 
	* @return array 
	* @access public 
	*/
	function attributes()
	{
		$attribs = array();
		foreach ($this->attribs as $key => $value)
		{
			if ($value === true)
			{
				$attribs[$key] = null;
			} 
			else
			{
				$attribs[$key] = $value;
			} 
		} 
		return $attribs;
	} 
} 
// --------------------------------------------------------------------------------
/**
* Represents an XML end element
* 
* @access public 
* @package WACT_TEMPLATE
*/
class xml_close extends xml_event
{
	/**
	* tag name
	* 
	* @var string 
	* @access private 
	*/
	var $tag;
	/**
	* Constructs EndElement
	* 
	* @param string $ tag name
	* @access public 
	*/
	function xml_close ($tag, $position)
	{
		xml_event::xml_event(XML_CLOSE, $position);
		$this->tag = $tag;
	} 
	/**
	* Returns the tag name
	* 
	* @return string 
	* @access public 
	*/
	function tag()
	{
		return $this->tag;
	} 
} 
// --------------------------------------------------------------------------------
/**
* Represents data inside an element
* 
* @access public 
* @package WACT_TEMPLATE
*/
class xml_data extends xml_event
{
	/**
	* The data inside the element
	* 
	* @var string 
	* @access private 
	*/
	var $data;
	/**
	* Constructs CharacterData
	* 
	* @param string $ character data inside an element
	* @access public 
	*/
	function xml_data ($data, $position)
	{
		xml_event::xml_event(XML_TEXT, $position);
		$this->data = $data;
	} 
	/**
	* Returns the character data
	* 
	* @return string 
	* @access public 
	*/
	function text()
	{
		return $this->data;
	} 
} 
// --------------------------------------------------------------------------------
/**
* For XML processing instructions
* 
* @access public 
* @package WACT_TEMPLATE
*/
class xml_pi extends xml_event
{
	/**
	* Target processor
	* 
	* @var string 
	* @access private 
	*/
	var $target;
	/**
	* Contents of PI
	* 
	* @var string 
	* @access private 
	*/
	var $instruction;
	/**
	* Constructs PI
	* 
	* @param string $ target processor
	* @param string $ processing instruction contents
	* @access public 
	*/
	function xml_pi ($target, $instruction, $position)
	{
		xml_event::xml_event(XML_PI, $position);
		$this->target = $target;
		$this->instruction = $instruction;
	} 
	/**
	* Returns the name of the target processor
	* 
	* @return string 
	* @access public 
	*/
	function target()
	{
		return $this->target;
	} 
	/**
	* Returns the processing instruction
	* 
	* @return string 
	* @access public 
	*/
	function instruction()
	{
		return $this->instruction;
	} 
} 
// --------------------------------------------------------------------------------
/**
* For XML escapes<br />
* Note: HTMLSax only
* 
* @access public 
* @package WACT_TEMPLATE
*/
class xml_escape extends xml_event
{
	/**
	* Contents of escape
	* 
	* @var string 
	* @access private 
	*/
	var $data;
	/**
	* Constructs Escape
	* 
	* @param string $ character data inside an element
	* @access public 
	*/
	function xml_escape ($data, $position)
	{
		xml_event::xml_event(XML_ESCAPE, $position);
		$this->data = $data;
	} 
	/**
	* Returns the contents of the escaped block
	* 
	* @return string 
	* @access public 
	*/
	function text()
	{
		return $this->data;
	} 
} 
// --------------------------------------------------------------------------------
/**
* For JSP / ASP markup<br />
* Note: HTMLSax only
* 
* @access public 
* @package WACT_TEMPLATE
*/
class xml_jasp extends xml_event
{
	/**
	* Contents of escape
	* 
	* @var string 
	* @access private 
	*/
	var $data;
	/**
	* Constructs Jasp
	* 
	* @param string $ character data inside an element
	* @access public 
	*/
	function xml_jasp ($data, $position)
	{
		xml_event::xml_event(XML_JASP, $position);
		$this->data = $data;
	} 
	/**
	* Returns the contents of the JASP block
	* 
	* @return string 
	* @access public 
	*/
	function text()
	{
		return $this->data;
	} 
} 

?>