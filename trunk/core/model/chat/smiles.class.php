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

class smiles
{
	var $smiles_path = '/shared/images/smiles/';
	
	function smiles()
	{
		$this->smiles_array = array(
				array(
						'img' => 'icon_biggrin.gif',
						'title' => 'Very Happy',
						'abbr' => ':D',
				),
				array(
						'img' => 'icon_smile.gif',
						'title' => 'Smile',
						'abbr' => ':)',
				),
				array(
						'img' => 'icon_sad.gif',
						'title' => 'Sad',
						'abbr' => ':(',
				),
				array(
						'img' => 'icon_surprised.gif',
						'title' => 'Surprized',
						'abbr' => ':o',
				),
				array(
						'img' => 'icon_eek.gif',
						'title' => 'Shocked',
						'abbr' => ':shock:',
				),
				array(
						'img' => 'icon_confused.gif',
						'title' => 'Cofused',
						'abbr' => ':?',
				),
				array(
						'img' => 'icon_cool.gif',
						'title' => 'Cool',
						'abbr' => '8)',
				),
				array(
						'img' => 'icon_exclaim.gif',
						'title' => 'Exclamation',
						'abbr' => ':!:',
				),
				array(
						'img' => 'icon_question.gif',
						'title' => 'Question',
						'abbr' => ':?:',
				),
				array(
						'img' => 'icon_idea.gif',
						'title' => 'Idea',
						'abbr' => ':idea:',
				),
				array(
						'img' => 'icon_mad.gif',
						'title' => 'Mad',
						'abbr' => ':x',
				),
				array(
						'img' => 'icon_razz.gif',
						'title' => 'Razz',
						'abbr' => ':P',
				),
				array(
						'img' => 'icon_redface.gif',
						'title' => 'Embarassed',
						'abbr' => ':oops:',
				),
				array(
						'img' => 'icon_cry.gif',
						'title' => 'Crying or Very sad',
						'abbr' => ':cry:',
				),
				array(
						'img' => 'icon_evil.gif',
						'title' => 'Evil',
						'abbr' => ':evil:',
				),
				array(
						'img' => 'icon_twisted.gif',
						'title' => 'Twisted Evil',
						'abbr' => ':twisted:',
				),
				array(
						'img' => 'icon_rolleyes.gif',
						'title' => 'Rolling Eyes',
						'abbr' => ':roll:',
				),
				array(
						'img' => 'icon_arrow.gif',
						'title' => 'Arrow',
						'abbr' => ':arrow:',
				),
				array(
						'img' => 'icon_wink.gif',
						'title' => 'Wink',
						'abbr' => ':wink:',
				),
				array(
						'img' => 'icon_lol.gif',
						'title' => 'Laughing',
						'abbr' => ':lol:',
				),
			);
	}
	
	function set_smiles_array($array)
	{
		if (!is_array($array))
			return true;
		
		$this->smiles_array = $array;
	}
	
	function get_smiles_array()
	{
		return $this->smiles_array;
	}

	function decode_smiles($str)
	{
		$replace = array();
		
		foreach($this->smiles_array as $id => $data)
			$replace[$data['abbr']] = "<img src='{$this->smiles_path}{$data['img']}' border='0' alt='{$data['title']}' title='{$data['title']}'>";
			
		return strtr($str, $replace);
	}
}


?>