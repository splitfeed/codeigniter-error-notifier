<?php
class MY_Exceptions extends CI_Exceptions {
	function log_exception($severity, $message, $filepath, $line)
	{
		if (ENVIRONMENT != 'development') {
			$CI =& get_instance();
			$severity = ( ! isset($this->levels[$severity])) ? $severity : $this->levels[$severity];

			log_message('error', 'Severity: '.$severity.'  --> '.$message. ' '.$filepath.' '.$line, TRUE);

			if(true/* || $CI->config->item('base_url') == 'http://www.production-domain.com/'*/) {
				$CI->load->library('email');

				$uri = $CI->uri->uri_string();

				$CI->email->from('user@domain.com', 'CodeIgniter');
				$CI->email->to('user@domain.com');

				$CI->email->subject('Error [severity: '.$severity.']');
				$CI->email->message('Severity: '.$severity.'  --> '.$message. ' '.$filepath.' '.$line."\n"."From URL: <a href=\"".site_url($uri)."\">".site_url($uri)."</a>");

				$CI->email->send();
			}

		} else {
			return parent::log_exception($severity, $message, $filepath, $line);
		}
	}

	function show_error($heading, $message, $template = 'error_general', $status_code = 500) {
		if (ENVIRONMENT != 'development') {
			$mail = implode("\n- ", ( ! is_array($message)) ? array($message) : $message)."\n";
			$CI =& get_instance();
			$CI->load->library('email');

			$uri = $CI->uri->uri_string();

			$CI->email->from('user@domain.com', 'CodeIgniter');
			$CI->email->to('user@domain.com');

			$CI->email->subject('Error [severity: DB]');


			$message = '<p>'.implode('</p><p>', ( ! is_array($message)) ? array($message) : $message).'</p>';

			if (ob_get_level() > $this->ob_level + 1)
			{
				ob_end_flush();
			}
			ob_start();
			include(APPPATH.'errors/'.$template.EXT);
			$buffer = ob_get_contents();
			ob_end_clean();

			$CI->email->message($buffer."<br/>\n"."From URL: <a href=\"".site_url($uri)."\">".site_url($uri)."</a>");

			$CI->email->send();
		}

		return parent::show_error($heading, $message, $template, $status_code);
	}
}