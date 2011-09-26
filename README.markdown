# Error Notifier

The error notifier is (for now) a single method for generating and mailing the latest logs to a specified e-mail account. Each time it
runs it compiles the logs since the last run and composes an email of it. Any repeated lines will be concatenated and marked
as "[Repeated x times]" to increase readability.

## Setup
The config/error_notifier.php contains settings for the e-mail being sent and must be configured before use.

### sender_email, sender_name and recipient
Sets sender and recipient of the log emails

### send_empty
Determines if empty mails should be sent or not. Sending of empty mails are useful to see that
monitoring is still functioning, but that no log entries have appeared since last time.

### log_threshold
The equivalent of the common CI configuration entry, but here it specifies a filter to apply on the log before mailing.

### state_file
The error notifier keeps a state file with a timestamp in it to keep track of when the logs last were mailed. This file is by
default saved in the logfolder and is named ".notifier_state".

## colorize
Makes the warning level colored and the "Repeated x times" a lighter gray for readability. Any suggestions here are welcome,
readability is one of the most important aspects of these mails!

## shorten_paths
Trim the base path of CI from all paths for shorter messages. The full paths are always available in the real log file.

## Usage
Set up a controller something like this:

	class Cronjob extends CI_Controller {
		public function send_logs() {
			$this->load->spark('error_notifier/0.0.4');
			$this->error_notifier->send();
		}
	}

Then visit it with a browser to make sure it works, then setup a cron job on your server to run it continuously.


## Changes
### 0.0.4
* Added colorize option for coloring the mail
* Added shorten_paths option for trimming base path from all errors since it's always the same anyway

### 0.0.3
* Moved default notifier state file and made location configurable
* Removed an error message if nothing had been logged

### 0.0.2
* Added support for filtering
* Fixed bug in state file saving

### 0.0.1
* Initial release

## Planned
* Improved filtering
* More readable log mails