<?php

$plugin_info = array(
	'pi_name'			=> 'Member Validation',
	'pi_version'		=> '1.0',
	'pi_author'			=> 'Nine Four',
	'pi_author_url'		=> 'http://ninefour.co.uk/labs',
	'pi_description'	=> 'Allows you to make asynchronous JavaScript validation calls to ExpressionEngine',
	'pi_usage'			=> ''
);

class member_validation {

	function member_validation() {
		global $DB;
		
		$DB->enable_cache(FALSE);	
	}

	function get_captcha() {
		global $DB;
		
		$ip = $_SERVER['REMOTE_ADDR'];
		
		$sql = "SELECT word FROM exp_captcha
				WHERE ip_address = '".$ip."'
				ORDER BY date DESC
				LIMIT 1";
		$query = $DB->query($sql);
		if ($query->num_rows > 0) {
			$word = $query->result[0]['word'];
		}		
		else {
			$word = '';
		}
				
		//delete last session entry
		$this->_delete_session_entry();
		
		$this->return_data = $word;
		return $this->return_data;
	}
	
	public function username_check() {
		global $DB, $SESS;
		
		//if user is logged in, their username may remain the same
		$current_username = $SESS->userdata('username');
				
		if (isset($_GET['username'])) {
			$username = $_GET['username'];
			
			$sql = "SELECT username FROM exp_members
					WHERE username = '".$DB->escape_str($username)."'";
			$query = $DB->query($sql);
			
			if ($query->num_rows > 0 && $query->result[0]['username'] != $current_username) {
				$this->return_data = "false";
			} else {
				$this->return_data = "true";
			}
		} else {
			$this->return_data = "No username passed";
		}
						
		//delete last session entry
		$this->_delete_session_entry();
		
		return $this->return_data;
	}
	
	public function email_check() {
		global $DB, $SESS;
		
		//if user is logged in, their email may remain the same
		$current_email = $SESS->userdata('email');
		
		if (isset($_GET['email'])) {
			$email = $_GET['email'];
			
			$sql = "SELECT email FROM exp_members
					WHERE email = '".$DB->escape_str($email)."'";
			$query = $DB->query($sql);
			
			if ($query->num_rows > 0 && $query->result[0]['email'] != $current_email) {
				$this->return_data = "false";
			} else {
				$this->return_data = "true";
			}
		} else {
			$this->return_data = "No email address passed";
		}
		
		//delete last session entry
		$this->_delete_session_entry();
		
		return $this->return_data;
	}
	
	public function screen_name_check() {
		global $DB, $SESS;
		
		//if user is logged in, their screen name may remain the same
		$current_screen_name = $SESS->userdata('screen_name');
		
		if (isset($_GET['screen_name'])) {
			$screen_name = $_GET['screen_name'];
			
			$sql = "SELECT screen_name FROM exp_members
					WHERE screen_name = '".$DB->escape_str($screen_name)."'";
			$query = $DB->query($sql);
			
			if ($query->num_rows > 0 && $query->result[0]['screen_name'] != $current_screen_name) {
				$this->return_data = "false";
			} else {
				$this->return_data = "true";
			}
		} else {
			$this->return_data = "No screen name passed";
		}
		
		//delete last session entry
		$this->_delete_session_entry();
		
		return $this->return_data;
	}
	
	private function _delete_session_entry() {
		global $DB, $FNS;
		
		//get and decode
		$history = $_COOKIE['exp_tracker'];
		$history = str_replace('\\"', '"', $history);
		$history = unserialize(urldecode($history));
		
		//unset some entries
		foreach ($history as $k=>$h) {
			if (strpos($h, 'ajax') > 0 || strpos($h, 'assets') > 0 || strpos($h, 'member/login') > 0) {
				unset($history[$k]);
			}
		}
		ksort($history);
				
		//encode
		$history = urlencode(serialize($history));
		
		//reset cookie
		$FNS->set_cookie('tracker', $history, '0');
	}
	
}
?>