<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
	'pi_name'			=> 'Member Validation',
	'pi_version'		=> '2.0',
	'pi_author'			=> 'Nine Four',
	'pi_author_url'		=> 'http://ninefour.co.uk/labs',
	'pi_description'	=> 'Allows you to make asynchronous JavaScript validation calls to ExpressionEngines',
	'pi_usage'			=> nf_validation::usage()
);

class member_validation {

	function member_validation() {
		
	}

	function captcha() {
		
		$this->EE =& get_instance();
		
		$match = "false";
		$reset = false;
		
		// Disable DB caching if it's currently set
		if ($this->EE->config->item('enable_db_caching') == 'y')
		{
			$this->EE->db->cache_off();
			$reset = true;
		}
		
		$ip = $_SERVER['REMOTE_ADDR'];
		
		$sql = "SELECT word FROM exp_captcha
				WHERE ip_address = '".$ip."'
				ORDER BY date DESC
				LIMIT 1";
		$query = $this->EE->db->query($sql);
		$result = $query->result_array();
		if ($query->num_rows = 1) {
			$word = $result[0]['word'];
			
			if ($word == $this->EE->input->get('captcha')) {			
				$match = "true";
			}
			
		}
				
		//delete last session entry
		$this->_delete_session_entry();
		
		// Re-enable DB caching
		if ($reset == TRUE)
		{
			ee()->db->cache_on();			
		}
		
		$this->return_data = $match;
		return $this->return_data;
	}
	
	public function username_check() {
		
		$this->EE =& get_instance();
		
		//if user is logged in, their username may remain the same
		$current_username = $this->EE->session->userdata('username');
				
		if (isset($_POST['username'])) {
			$username = $_POST['username'];
			
			$sql = "SELECT username FROM exp_members
					WHERE username = '".$this->EE->db->escape_str($username)."'";
			$query = $this->EE->db->query($sql);
			
			if ($query->num_rows > 0 && $query->result[0]['username'] != $current_username) {
				$this->return_data = '0';
			}
			else {
				$this->return_data = '1';
			}
		}
						
		//delete last session entry
		$this->_delete_session_entry();
		
		return $this->return_data;
	}
	
	public function email_check() {
		
		$this->EE =& get_instance();
		
		//if user is logged in, their email may remain the same
		$current_email = $this->EE->session->userdata('email');
		
		if (isset($_POST['email'])) {
			$email = $_POST['email'];
			
			$sql = "SELECT email FROM exp_members
					WHERE email = '".$this->EE->db->escape_str($email)."'";
			$query = $this->EE->db->query($sql);
			
			if ($query->num_rows > 0 && $query->result[0]['email'] != $current_email) {
				$this->return_data = '0';
			}
			else {
				$this->return_data = '1';
			}
		}
		
		//delete last session entry
		$this->_delete_session_entry();
		
		return $this->return_data;
	}
	
	public function email_valid() {
		
		$this->EE =& get_instance();
		
		if (isset($_POST['email'])) {
			$email = $_POST['email'];
			
			$sql = "SELECT email FROM exp_members
					WHERE email = '".$this->EE->db->escape_str($email)."'";
			$query = $this->EE->db->query($sql);
			
			if ($query->num_rows > 0) {
				$this->return_data = '0';
			}
			else {
				$this->return_data = '1';
			}
		}
		
		//delete last session entry
		$this->_delete_session_entry();
		
		return $this->return_data;
	}
	
	private function _delete_session_entry() {
		
		$this->EE =& get_instance();
					
		//get and decode
		$history = $this->EE->input->cookie('tracker');
		
		//exit;
		//$history = str_replace('\\"', '"', $history);
		$history = unserialize($history);
		
		//unset some entries
		foreach ($history as $k=>$h) {
			$pos = strpos($h, 'validation');
			if ($pos !== false) { 
				unset($history[$k]);
			}
		}
		$history = array_values($history);
			
		//encode
		$history = serialize($history);
		
		//update cookie
		$this->EE->functions->set_cookie('tracker', $history, '0');
	}
	
	// ----------------------------------------
	//  Plugin Usage
	// ----------------------------------------

	function usage() {
	ob_start(); 
	?>
	Meh!

  <?php
  $buffer = ob_get_contents();
	
  ob_end_clean(); 

  return $buffer;
  }
  // END

}

?>