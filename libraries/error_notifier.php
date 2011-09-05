<?php
class Error_Notifier {
	//Same as in CI_Log, but those are protected and cannot be reused
	protected $log_levels	= array('ERROR' => 1, 'DEBUG' => 2,  'INFO' => 3, 'ALL' => 4);

	/**
	 * Set up CI and load config
	 */
	public function __construct() {
		$this->CI = &get_instance();
		$this->CI->config->load('error_notifier', true);
	}

	/**
	 * Get the time when the notifier was last run, or 24h ago if no
	 * date was set
	 *
	 * @return int Time of last run
	 */
	protected function get_last_summary() {
		if (file_exists(APPPATH."cache/error_notifier_state")) {
			return file_get_contents(APPPATH."cache/error_notifier_state");
		} else {
			return strtotime("-1 day");
		}
	}

	/**
	 * Set time of last run
	 *
	 * @param int $date Timestamp when last run
	 * @return bool Return value of file_get_contents
	 */
	protected function set_last_summary($date) {
		return file_put_contents(APPPATH."cache/error_notifier_state", $date);
	}

	/**
	 * Determine if the passed line is to be shown or not
	 *
	 * @param array $line The log line
	 * @return bool Show the line or not?
	 */
	protected function filter_line($line) {
		$threshold = $this->CI->config->item('log_threshold', 'error_notifier');

		//4 means ALL
		if ($threshold >= 4) return true;

		$level = $line[0];
		if (!isset($this->log_levels[$level]) || $this->log_levels[$level] > $threshold) {
			return false;
		}

		return true;
	}

	/**
	 * Collect, filter and sort lines into an array
	 *
	 * @return array Log lines
	 */
	public function collect_logs() {
		$now			= time();
		$last_summary	= $this->get_last_summary();
		$current_date	= $last_summary;

		$this->set_last_summary($now);

		echo 'Showing logs between '.date('Y-m-d H:i:s', $last_summary).' and '.date('Y-m-d H:i:s', $now)."\n";

		//Read all logs since last time
		$log_lines		= array();
		while ($current_date <= $now) {
			echo date('Y-m-d H:i:s', $current_date)."\n";

			$current_log	= APPPATH.'logs/log-'.date('Y-m-d', $current_date).'.php';

			$log	= file_get_contents($current_log);
			$lines	= preg_split('/^([A-z0-9]+)\s-\s([0-9]{4}-[0-9]{2}-[0-9]{2}\s[0-9]{2}:[0-9]{2}:[0-9]{2})/ism', $log, -1, PREG_SPLIT_DELIM_CAPTURE);

			//Remove the PHP-tag
			unset($lines[0]);

			$repeat		= 0;
			$last_line	= '';
			$last_level	= '';
			foreach (array_chunk($lines,3) as $line_parts) {
				if (count($line_parts) != 3) {
					trigger_error("Mismatch in chunk");

				} elseif (strtotime($line_parts[1]) <= $now) {
					$line_parts[2] = trim($line_parts[2]);

					//Put all lines through filter method
					if ($this->filter_line($line_parts)) {
						if ($last_level == $line_parts[0] && $last_line == $line_parts[2]) {
							$repeat++;

						} else {
							if ($repeat > 0) $log_lines[count($log_lines)-1][2] .= " [Repeated $repeat times]";

							$log_lines[] = $line_parts;
							$repeat = 0;
						}

						$last_level	= $line_parts[0];
						$last_line	= $line_parts[2];
					}
				}
			}

			$current_date	= strtotime("+1 day", $current_date);
		}

		return $log_lines;
	}

	/**
	 * Collect and mail logs, if any was found
	 */
	public function send() {
		$log_lines = $this->collect_logs();

		if (!empty($log_lines)) {
			$message = '';

			foreach ($log_lines as $line) {
				if (strlen($line[2]) > 1000) $line[2] = substr($line[2],0,1000);

				$message .= '<div style="display:block;padding:4px;margin:0;border-bottom:1px solid #444;white-space: pre-wrap;;">'.$line[0].' - '.$line[1].' -'.$line[2].'</div>';
			}


		} elseif ($this->CI->config->item('send_empty', 'error_notifier')) {
			$message = 'No new matched log entries collected';
		}

		if ($message) {
			$this->CI->load->library('email');

			$sender_email	= $this->CI->config->item('sender_email', 'error_notifier');
			$sender_name	= $this->CI->config->item('sender_name', 'error_notifier');
			$recipient		= $this->CI->config->item('recipient', 'error_notifier');

			$this->CI->email->from($sender_email, $sender_name);
			$this->CI->email->to($recipient);

			$this->CI->email->subject("Log harvest of ".base_url());
			$this->CI->email->message($message);

			$this->CI->email->send();
		}
	}
}