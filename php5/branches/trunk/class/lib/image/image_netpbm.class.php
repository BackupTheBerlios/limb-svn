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

if(!defined('NETPBM_LIB_DIR'))
{
  if(sys :: os_type() == 'win32')
  	define('NETPBM_LIB_DIR', 'c:/netpbm/');
  else
  	define('NETPBM_LIB_DIR', '/usr/local/netpbm/bin/');
 }

require_once(LIMB_DIR . 'class/lib/image/image_library.class.php');

class image_netpbm extends image_library
{
  // Read utilities
  const JPEGTOPNM = 'jpegtopnm';
  const GIFTOPNM = 'giftopnm';
  const PNGTOPNM = 'pngtopnm';
  const BMPTOPNM = 'bmptopnm';
  const TIFFTOPNM = 'tifftopnm';
  
  // Write utilities
  const PNMTOJPEG = 'pnmtojpeg';
  const PNMTOGIF = 'ppmtogif';
  const PNMTOPNG = 'pnmtopng';
  const PNMTOBMP = 'ppmtobmp';
  const PNMTOTIFF = 'pnmtotiff';
  
  // Editor utilities
  const PNMSCALE = 'pnmscale';
  const PNMCUT = 'pamcut';
  const PNMFLIP = 'pnmflip';
  const PNMROTATE = 'pnmrotate';
  const PNMQUANT = 'ppmquant';
  const PNMMAKE = 'ppmmake';
  const PNMCOMP = 'pnmpaste';

	protected $lib_dir = '';
	protected $os = '';
	protected $ext = '';
	protected $cmd_array = array();
	protected $to_pnm = '';
	protected $from_pnm = '';
  protected $tmp_dir = '';
	protected $current_input_file = '';
	protected $current_input_file_type = '';
	protected $current_output_file = '';
	protected $current_output_file_type = '';

	function __construct($lib_dir = self :: NETPBM_LIB_DIR)
	{
	  $this->lib_dir = $lib_dir;

    $this->_determine_options();
	}
	
	protected function _determine_options()
	{
    if (sys :: os_type() == "win32")
      $this->ext = '.exe';
    
    $this->_determine_read_types();
    $this->_determine_write_types();
    
    if (sizeof($this->read_types) == 0)
      $this->library_installed = false;
    else
      $this->library_installed = true;
  }
  
  protected function _determine_read_types()
  {
	  if (file_exists($this->lib_dir . self :: JPEGTOPNM . $this->ext))
	    $this->read_types[] = 'JPEG';
	  
	  if (file_exists($this->lib_dir . self :: GIFTOPNM . $this->ext))
	    $this->read_types[] = 'GIF';
	  
	  if (file_exists($this->lib_dir . self :: PNGTOPNM . $this->ext))
	    $this->read_types[] = 'PNG';
	  
	  if (file_exists($this->lib_dir . self :: BMPTOPNM . $this->ext))
	    $this->read_types[] = 'BMP';
	  
	  if (file_exists($this->lib_dir . self :: TIFFTOPNM . $this->ext))
	    $this->read_types[] = 'TIFF';
  }
    
  protected function _determine_write_types()
  {
	  if (file_exists($this->lib_dir . self :: PNMTOJPEG . $this->ext))
	    $this->create_types[] = 'JPEG';
	  
	  if (file_exists($this->lib_dir . self :: PNMTOGIF . $this->ext))
	    $this->create_types[] = 'GIF';
	  
	  if (file_exists($this->lib_dir . self :: PNMTOPNG . $this->ext))
	    $this->create_types[] = 'PNG';
	  
	  if (file_exists($this->lib_dir . self :: PNMTOBMP . $this->ext))
	    $this->create_types[] = 'BMP';
	  
	  if (file_exists($this->lib_dir . self :: PNMTOTIFF . $this->ext))
	    $this->create_types[] = 'TIFF';
	}
	
	public function set_input_file($file_name, $type)
	{
	  parent :: set_input_file($file_name, $type);
    
    $this->to_pnm = constant(strtoupper($type . 'TOPNM')) . " $file_name";
	}
	
	public function set_output_file($file_name, &$type)
	{
	  parent :: set_output_file($file_name, $type);
    
    $this->from_pnm = '';

	if (strtoupper($type) == 'GIF')
      $this->from_pnm = self :: PNMQUANT . ' 256 | ';
    
	$this->from_pnm .= constant(strtoupper('PNMTO' . $type)) . " > $file_name";
	}
	
	public function reset()
	{
	  $this->cmd_array = array();
	}
	
	public function commit()
	{
		if (!$this->library_installed)
			return false;
		
	  array_unshift($this->cmd_array, $this->_to_pnm());
	  array_push($this->cmd_array, $this->from_pnm);

	  echo $cmd = implode(' | ', $this->cmd_array);
    $this->_run_cmd($cmd);
	  
	  $this->reset();
	  
	  if (is_file($this->current_output_file))
  		unlink($this->current_output_file);
	  
	  $this->current_output_file = '';
	  $this->current_output_file_type = '';
	  $this->current_input_file = '';
	  $this->current_input_file_type = '';
	  
	  return true;
	}
		
	public function resize($params)
	{
		if (!$this->library_installed)
			return false;
		
		$info = getimagesize($this->input_file);
		$src_width = $info[0];
		$src_height = $info[1];

		list($dst_width, $dst_height) = $this->get_dst_dimensions($src_width, $src_height, $params);
		
		$this->cmd_array[] = self :: PNMSCALE . " -width={$dst_width} -height={$dst_height}";
  }

	public function rotate($angle, $bg_color = '')
	{
		if (!$this->library_installed)
			return false;
		
		$this->cmd_array[] = self :: PNMROTATE . " {$angle}";
  }

	public function flip($params)
	{
		if (!$this->library_installed)
			return false;
		
	  if ($params == self :: FLIP_HORIZONTAL)
  		$args = '-leftright';

	  if ($params == self :: FLIP_VERTICAL)
  		$args = '-topbottom';
		
		$this->cmd_array[] = self :: PNMFLIP . " {$args}";
  }
  
  protected function _to_pnm()
  {
    if (!empty($this->current_input_file))
    {
      $file_name = $this->current_input_file;
      $type = $this->current_input_file_type;
    }
    else
    {
      $file_name = $this->input_file;
      $type = $this->input_file_type;
    }
    
    return constant(strtoupper($type . 'TOPNM')) . " $file_name";
  }
  
  protected function _from_pnm()
  {
    if (!empty($this->current_output_file))
    {
      $file_name = $this->current_output_file;
      $type = $this->current_output_file_type;
    }
    else
    {
      $file_name = $this->output_file;
      $type = $this->output_file_type;
    }
    
    return constant(strtoupper('PNMTO' . $type)) . " > $file_name";
  }
  
  protected function _run_cmd($cmd)
  {
	  $cwd = getcwd();
	  chdir($this->lib_dir);
	  shell_exec($cmd);
	  chdir($cwd);
  }

	public function cut($x, $y, $w, $h, $bg_color = '')
	{
		if (!$this->library_installed)
			return false;
	  
		$tmp_file = tempnam(VAR_DIR, 'netpbm');
		$this->current_output_file = $tmp_file;
		$this->current_output_file_type = 'BMP';
		
	  if (sizeof($this->cmd_array) > 0)
	  {
  	  array_unshift($this->cmd_array, $this->_to_pnm());
  	  array_push($this->cmd_array, $this->_from_pnm());

  	  $cmd = implode(' | ', $this->cmd_array);
  	  
	    $this->_run_cmd($cmd);
	    
  		$file_name = $this->current_input_file = $tmp_file;
  		$type = $this->current_input_file_type = $this->current_output_file_type;

  		$this->reset();
	  }
	  else
	  {
  		$file_name = $this->input_file;
  		$type = $this->input_file_type;
	  }

		$info = getimagesize($file_name);
		
		if ($x < 0)
		{
		  $cx = 0;
		  $cw = $w + $x;
		}
		else
		{
		  $cx = $x;
		  $cw = $w;
		}
		
		if ($y < 0)
		{
		  $cy = 0;
		  $ch = $h + $y;
		}
		else
		{
		  $cy = $y;
		  $ch = $h;
		}
		
		if ($cx + $cw > $info[0])
		  $cw = $info[0] - $cx;
		
		if ($cy + $ch > $info[1])
		  $ch = $info[1] - $cy;

		$tmp_file = tempnam(VAR_DIR, 'netpbm');

		$cmd = $this->_to_pnm() . ' | ' . self :: PNMCUT . " $cx $cy $cw $ch > {$tmp_file}";
		
    $this->_run_cmd($cmd);

		$cx = ($x < 0) ? -$x : 0;
		$cy = ($y < 0) ? -$y : 0;
		$cmd = self :: PNMMAKE . " " . $this->_hex_color_to_X11($bg_color) . " {$w} {$h} | " . self :: PNMCOMP . " {$tmp_file} {$cx} {$cy}" . ' | ' . $this->_from_pnm();
		
    $this->_run_cmd($cmd);

		unlink($tmp_file);

		$this->current_input_file = $this->current_output_file;
		$this->current_input_file_type = $this->current_output_file_type;
  }
}
?>