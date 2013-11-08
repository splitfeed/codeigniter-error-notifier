<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:
*
* Author: Niklas Nilsson
*         splitfeed@gmail.com
*         @splitfeed
*
* Location: http://github.com/splitfeed/codeigniter-error-notifier
*
* Created:  2011-08-31
*
* Description:
*
*/

/**
 * Email
 **/
$config['sender_email']	= 'user@domain.com';
$config['sender_name']  = 'Error Notifier';

$config['recipient']	= 'admin@domain.com';

$config['send_empty']	= true;

$config['colorize']	= true;
$config['shorten_paths']	= true;

/**
 * If you want to use postmark as email gateway set true
 * You should have postmark spark working on your app.
 */
$config['postmark']    = true;

/**
 * Filter log messages by log level (same values as CI log_threshold config)
 * 1 = Error Messages (including PHP errors)
 * 2 = Debug Messages
 * 3 = Informational Messages
 * 4 = All Messages
 */
$config['log_threshold'] = 4;

/**
 * Send messages in plain format 
 */
$config['plain'] = true;

/* End of file error_notifier.php */
/* Location: ./application/config/error_notifier.php */