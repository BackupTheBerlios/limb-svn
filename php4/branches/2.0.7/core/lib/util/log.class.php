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

define( 'MAX_LOGROTATE_FILES', 5 );
define( 'MAX_LOGFILE_SIZE', 500*1024 );

require_once(LIMB_DIR . '/core/lib/system/dir.class.php');
require_once(LIMB_DIR . '/core/lib/security/user.class.php');
require_once(LIMB_DIR . '/core/lib/system/sys.class.php');
require_once(LIMB_DIR . '/core/lib/debug/debug.class.php');

if(!defined('REQUEST_URI'))
	define('REQUEST_URI', $_SERVER['REQUEST_URI']);

class log
{
  function log( )
  {
  }

  /*!
   Writes file name $name and storage directory $dir to storage log
  */
  function write( $log_file_data, $string)
  {
    $log_dir = $log_file_data[0];
    $log_name = $log_file_data[1];
    $file_name = $log_dir . $log_name;
    
    if (!is_dir($log_dir))
    	dir :: mkdir($log_dir, 0775, true);
    
    $oldumask = @umask( 0 );
    $file_existed = @file_exists( $file_name );
    $log_file = @fopen( $file_name, 'a' );
    
    if ( $log_file )
    {
      $time = strftime( "%b %d %Y %H:%M:%S", strtotime( 'now' ) );
  		
  		$notice = '[ ' . $time . " ]\n";
  		
			if(($user_id = user :: get_id()) != VISITOR_USER_ID)
				$notice .= '[ ' . $user_id . ' ] [ '  . user :: get_login() . ' ] [ ' . user :: get_email() . ' ] ';

      $notice .= '[' . sys::client_ip() . '] [' . (isset($_SERVER['REQUEST_URI']) ?  $_SERVER['REQUEST_URI'] : '') . "]\n" . $string . "\n\n";
      
      @fwrite( $log_file, $notice );
      @fclose( $log_file );
      if ( !$file_existed )
      	@chmod( $file_name, 0664 );

      @umask( $oldumask );
      $result = true;
    }
    else
    {
      @umask( $oldumask );
     	$result = false;
      debug :: write_error( "Cannot open log file '$file_name' for writing\n" .
                         "The web server must be allowed to modify the file.\n" .
                         "File logging for '$file_name' is disabled." , __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, false);
    
    }

    return $result;
  }
  	
  /*!
   Rotates logfiles so the current logfile is backed up,
   old rotate logfiles are rotated once more and those that
   exceed max_logrotate_files() will be removed.
   Rotated files will get the extension .1, .2 etc.
  */
  function rotate_log( $file_name )
  {
    $max_logrotate_files = MAX_LOGROTATE_FILES;
    for ( $i = $max_logrotate_files; $i > 0; --$i )
    {
      $log_rotate_name = $file_name . '.' . $i;
      if ( @file_exists( $log_rotate_name ) )
      {
        if ( $i == $max_logrotate_files )
        {
        	@unlink( $log_rotate_name );
        }
        else
        {
	        $new_log_rotate_name = $file_name . '.' . ($i + 1);
	        @rename( $log_rotate_name, $new_log_rotate_name );
        }
      }
    }
    if ( @file_exists( $file_name ) )
    {
	    $new_log_rotate_name = $file_name . '.' . 1;
	    @rename( $file_name, $new_log_rotate_name );
	    return true;
    }
    return false;
  }
}
?>