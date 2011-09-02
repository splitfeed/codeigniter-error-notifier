# Error Notifier

The error notifier is (for now) a single method for generating and mailing the latest logs to a specified e-mail account. Each time it
runs it compiles the logs since the last run and composes an email of it. Any repeated lines will be concatenated and marked
as "[Repeated x times]" to increase readability.

## Setup
The config/error_notifier.php contains settings for the e-mail being sent and must be configured before use.

## Usage
Set up a controller something like this:
	class Cronjob extends CI_Controller {
		public function send_logs() {
			$this->load->spark('error_notifier/0.0.1');
			$this->error_notifier->send();
		}
	}

Then visit it with a browser to make sure it works, then setup a cron job on your server to run it continuously.

## Planned features
- Log level filtering