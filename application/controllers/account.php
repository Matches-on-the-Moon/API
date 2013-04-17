<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Example
 *
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array.
 *
 * @package		CodeIgniter
 * @subpackage	Rest Server
 * @category	Controller
 * @author		Phil Sturgeon
 * @link		http://philsturgeon.co.uk/code/
*/

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';

class Account extends REST_Controller
{
	/*
		
		boolean createAccount(String loginName, String password, String name, String email) throws FMSException;
		
		Account attemptLogin(String loginName, String password);
		
		Account getAccount(Integer accountID);
		
		Account.State getAccountStateByLoginName(String loginName);
		
		boolean lockAccount(Integer accountID);
		
		boolean unlockAccount(Integer accountID);
		
		boolean deleteAccount(Integer accountID);
		
		boolean editAccountPassword(Integer accountID, String password);
		
		boolean editAccountEmail(Integer accountID, String email);
		
		boolean isLoginNameUnique(String loginName);
		
		int getAccountIdByLoginName(String text);
		
		boolean isAdmin(Integer accountID);
		
		List<Account> getAllAccounts();
		
		boolean promoteAccount(Integer targetAccountID);
		
		== account ==
		Integer id;
	    String name;
	    String loginName;
	    String password;
	    State accountState;
	    String email;
	    int loginAttempts;
	
	*/
	
	const ACCOUNT_STATE_LOCKED = 'LOCKED';
	const ACCOUNT_STATE_UNLOCKED = 'UNLOCKED';
	
	// boolean createAccount(String loginName, String password, String name, String email) throws FMSException;
	function index_put()
	{
		$data = array(
    		'loginName' => $this->put('loginName'),
    		'password'	=> $this->put('password'),
    		'name'	  	=> $this->put('name'),
    		'email' 	=> $this->put('email')
    	);
    	
    	$result = $this->db->insert('accounts', $data);
    	
    	if($result){
    		// success
    		$this->response('success', 200);
    		
    	} else {
    		// failure
    		$this->response(array('error' => 'DB Error: '.$this->db->_error_message()), 404);
    	}
	}
	
	// Account attemptLogin(String loginName, String password);
	function attempLogin_get()
	{
		$loginName = $this->get('loginName');
		$password = $this->get('password');
	
		$this->db->where('loginName', $loginName);
		$this->db->get('accounts');
		
		if($result !== false){
        	// success
        	$row = $result->row_array();
        	if($result->num_rows() == 1){
        		// found a user, check password
        		if($row['password'] == $password){
        			// correct password
        			$this->response($row, 200);
        			
        		} else {
					// wrong password        	
	        		// increase login attempt
	        		$attempts = $row['loginAttempts'] + 1;
	        		$this->db->set('loginAttempts', $attempts);
	        		
	        		// if >= 3, then lock account
	        		if($attempts >= 3){
	        			$this->db->set('accountState', ACCOUNT_STATE_LOCKED);
	        		}
	        		
	        		$this->db->where('loginName', $this->get('loginName'));
					$this->db->where('password',  $this->get('password'));
	        		$this->db->update('accounts');
	        		
	        		// return empty row
	        		$this->response(array(), 200);
        		}
        	}
        	
        } else {
    		// failure
    		$this->response(array('error' => 'DB Error: '.$this->db->_error_message()), 404);
    	}
	}

	// Account getAccount(Integer accountID);
	function index_get()
	{
		$id = $this->get('id');
       	
    	$this->db->where('id', $id);
    	$result = $this->db->get('accounts');
    
        if($result !== false){
        	// success
        	$row = $result->row_array();
        	$this->response($row, 200); // 200 being the HTTP response code
        	
        } else {
    		// failure
    		$this->response(array('error' => 'DB Error: '.$this->db->_error_message()), 404);
    	}
	}

	// Account.State getAccountStateByLoginName(String loginName);
	function stateForLoginName_get()
	{
		$loginName = $this->get('loginName');
       	
    	$this->db->where('loginName', $loginName);
    	$result = $this->db->get('accounts');
    
        if($result !== false){
        	// success
        	$row = $result->row_array();
        	$state = ACCOUNT_STATE_UNLOCKED;// default
        	if(isset($row['accountState'])){
        		$state = $row['accountState'];
        	}
        	$this->response($state, 200); // 200 being the HTTP response code
        	
        } else {
    		// failure
    		$this->response(array('error' => 'DB Error: '.$this->db->_error_message()), 404);
    	}
	}

	// boolean lockAccount(Integer accountID);
	function lock_post()
	{
		$id = $this->post('id');
		
    	$this->db->set('accountState', ACCOUNT_STATE_LOCKED);
    	$this->db->where('id', $id);
    	$result = $this->db->update('accounts', $data);
    	
    	if($result){
    		// success
    		$this->response('success', 200);
    		
    	} else {
    		// failure
    		$this->response(array('error' => 'DB Error: '.$this->db->_error_message()), 404);
    	}
	}

	// boolean unlockAccount(Integer accountID);
	function unlock_post()
	{
		$id = $this->post('id');
		
    	$this->db->set('accountState', ACCOUNT_STATE_UNLOCKED);
    	$this->db->where('id', $id);
    	$result = $this->db->update('accounts', $data);
    	
    	if($result){
    		// success
    		$this->response('success', 200);
    		
    	} else {
    		// failure
    		$this->response(array('error' => 'DB Error: '.$this->db->_error_message()), 404);
    	}
	}

	// boolean deleteAccount(Integer accountID);
	function index_delete()
	{
		$id = $this->delete('id');
    	
    	$this->db->where('id', $id);
    	$this->db->delete('accounts');
	}

	// boolean editAccountPassword(Integer accountID, String password);
	function editPassword_post()
	{
		$id = $this->post('id');
		$password = $this->post('password');
		
    	$this->db->set('password', $password);
    	$this->db->where('id', $id);
    	$result = $this->db->update('accounts', $data);
    	
    	if($result){
    		// success
    		$this->response('success', 200);
    		
    	} else {
    		// failure
    		$this->response(array('error' => 'DB Error: '.$this->db->_error_message()), 404);
    	}
	}

	// boolean editAccountEmail(Integer accountID, String email);
	function editEmail_post()
	{
		$id = $this->post('id');
		$email = $this->post('email');
		
    	$this->db->set('email', $email);
    	$this->db->where('id', $id);
    	$result = $this->db->update('accounts', $data);
    	
    	if($result){
    		// success
    		$this->response('success', 200);
    		
    	} else {
    		// failure
    		$this->response(array('error' => 'DB Error: '.$this->db->_error_message()), 404);
    	}
	}

	// boolean isLoginNameUnique(String loginName);
	function isLoginNameUnique_get()
	{
		$loginName = $this->get('loginName');
		
		$this->db->where('loginName', $loginName);
		$result = $this->db->get('accounts');
    
    	if($result !== false){
        	// success
        	$unique = 'no';
        	if($result->num_rows() == 0){
        		$unique = 'yes';
        	}
			$this->response(array('unique' => $unique), 200); // 200 being the HTTP response code
        	
        } else {
    		// failure
    		$this->response(array('error' => 'DB Error: '.$this->db->_error_message()), 404);
    	}
	}

	// int getAccountIdByLoginName(String text);
	function idByLoginName_get()
	{
		$loginName = $this->get('loginName');
       	
    	$this->db->where('loginName', $loginName);
    	$result = $this->db->get('accounts');
    
        if($result !== false){
        	// success
        	$row = $result->row_array();
        	$id = 0;// default
        	if(isset($row['id'])){
        		$id = $row['id'];
        	}
        	$this->response($id, 200); // 200 being the HTTP response code
        	
        } else {
    		// failure
    		$this->response(array('error' => 'DB Error: '.$this->db->_error_message()), 404);
    	}
	}

	// boolean isAdmin(Integer accountID);
	function isAdmin_get()
	{
		$id = $this->get('id');
       	
    	$this->db->where('id', $id);
    	$result = $this->db->get('accounts');
    
        if($result !== false){
        	// success
        	$row = $result->row_array();
        	$isAdmin = 'no';
        	if(isset($row['isAdmin']) && $row['isAdmin'] == true){
        		$isAdmin = 'yes';
        	}
        	$this->response(array('isAdmin' => $isAdmin), 200); // 200 being the HTTP response code
        	
        } else {
    		// failure
    		$this->response(array('error' => 'DB Error: '.$this->db->_error_message()), 404);
    	}
	}

	// List<Account> getAllAccounts();	
	function all_get()
	{
		$result = $this->db->get('accounts');
    
    	if($result !== false){
        	// success
        	$all = array();
	    	foreach ($result->result_array() as $row){
			   $all[] = $result->row_array();
			}
			$this->response($all, 200); // 200 being the HTTP response code
        	
        } else {
    		// failure
    		$this->response(array('error' => 'DB Error: '.$this->db->_error_message()), 404);
    	}
	}

	// boolean promoteAccount(Integer targetAccountID);
	function promote_post()
	{
		$id = $this->post('id');
		
    	$this->db->set('isAdmin', true);
    	$this->db->where('id', $id);
    	$result = $this->db->update('accounts', $data);
    	
    	if($result){
    		// success
    		$this->response('success', 200);
    		
    	} else {
    		// failure
    		$this->response(array('error' => 'DB Error: '.$this->db->_error_message()), 404);
    	}
	}

}