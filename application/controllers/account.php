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
    		'loginName'     => $this->put('loginName'),
    		'password'	    => $this->put('password'),
    		'name'	  	    => $this->put('name'),
    		'email' 	    => $this->put('email'),
    		'accountState'  => self::ACCOUNT_STATE_UNLOCKED,
    		'loginAttempts' => 0
    	);
    	
    	$result = $this->db->insert('accounts', $data);
    	
    	if($result){
    		// success
    		$this->response_success('account created');
    		
    	} else {
    		// failure
    		$this->response_error('DB Error: '.$this->db->_error_message());
    	}
	}
	
	// Account attemptLogin(String loginName, String password);
	function attemptLogin_get()
	{
		$loginName = $this->get('loginName');
		$password = $this->get('password');
	
		$this->db->where('loginName', $loginName);
		$result = $this->db->get('accounts');
		
		if($result !== false){
        	// valid loginName
        	$row = $result->row_array();
        	// allow mutiple rows, in a real application you would NEVER do this
        	// but for this app, we don't check this rare error, but we don't create accounts if the loginName already exists 
        	if($result->num_rows() >= 1){
        		// found a user, check password
        		if($row['password'] == $password){
        			// correct password
        			$this->response_success($row);
        			
        		} else {
					// wrong password        	
	        		// increase login attempt
	        		$attempts = $row['loginAttempts'] + 1;
	        		$this->db->set('loginAttempts', $attempts);
	        		
	        		// if >= 3, then lock account
	        		if($attempts >= 3){
	        			$this->db->set('accountState', self::ACCOUNT_STATE_LOCKED);
	        		}
	        		
	        		$this->db->where('loginName', $this->get('loginName'));
	        		$this->db->update('accounts');
	        		
	        		// return empty row
	        		$this->response_error('wrong password');
        		}
        	} else {
	        	$this->response_error('multiple rows found!');
        	}
        	
        } else {
    		// failure
    		$this->response_error('DB Error: '.$this->db->_error_message());
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
        	$this->response_success($row); // 200 being the HTTP response code
        	
        } else {
    		// failure
    		$this->response_error('DB Error: '.$this->db->_error_message());
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
        	$state = self::ACCOUNT_STATE_UNLOCKED;// default
        	if(isset($row['accountState'])){
        		$state = $row['accountState'];
        	}
        	$this->response_success(array('state' => $state)); // 200 being the HTTP response code
        	
        } else {
    		// failure
    		$this->response_error('DB Error: '.$this->db->_error_message());
    	}
	}

	// boolean lockAccount(Integer accountID);
	function lock_post()
	{
		$id = $this->post('id');
		
    	$this->db->set('accountState', self::ACCOUNT_STATE_LOCKED);
    	$this->db->where('id', $id);
    	$result = $this->db->update('accounts');
    	
    	if($result){
    		// success
    		$this->response_success('account locked');
    		
    	} else {
    		// failure
    		$this->response_error('DB Error: '.$this->db->_error_message());
    	}
	}

	// boolean unlockAccount(Integer accountID);
	function unlock_post()
	{
		$id = $this->post('id');
		
    	$this->db->set('accountState', self::ACCOUNT_STATE_UNLOCKED);
    	$this->db->where('id', $id);
    	$result = $this->db->update('accounts');
    	
    	if($result){
    		// success
    		$this->response_success('account unlocked');
    		
    	} else {
    		// failure
    		$this->response_error('DB Error: '.$this->db->_error_message());
    	}
	}

	// boolean deleteAccount(Integer accountID);
	function delete_post()
	{
		$id = $this->post('id');
    	
    	$this->db->where('id', $id);
    	$result = $this->db->delete('accounts');
    	
    	if($result !== false){
        	// success
        	$this->response_success('account id='.$id.' deleted'); // 200 being the HTTP response code
        	
        } else {
    		// failure
    		$this->response_error('DB Error: '.$this->db->_error_message());
    	}
	}

	// boolean editAccountPassword(Integer accountID, String password);
	function editPassword_post()
	{
		$id = $this->post('id');
		$password = $this->post('password');
		
    	$this->db->set('password', $password);
    	$this->db->where('id', $id);
    	$result = $this->db->update('accounts');
    	
    	if($result){
    		// success
    		$this->response_success('password changed');
    		
    	} else {
    		// failure
    		$this->response_error('DB Error: '.$this->db->_error_message());
    	}
	}

	// boolean editAccountEmail(Integer accountID, String email);
	function editEmail_post()
	{
		$id = $this->post('id');
		$email = $this->post('email');
		
    	$this->db->set('email', $email);
    	$this->db->where('id', $id);
    	$result = $this->db->update('accounts');
    	
    	if($result){
    		// success
    		$this->response_success('emailed changed');
    		
    	} else {
    		// failure
    		$this->response_error('DB Error: '.$this->db->_error_message());
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
			$this->response_success(array('unique' => $unique)); // 200 being the HTTP response code
        	
        } else {
    		// failure
    		$this->response_error('DB Error: '.$this->db->_error_message());
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
        	$this->response_success(array('id' => $id)); // 200 being the HTTP response code
        	
        } else {
    		// failure
    		$this->response_error('DB Error: '.$this->db->_error_message());
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
        	$this->response_success(array('isAdmin' => $isAdmin)); // 200 being the HTTP response code
        	
        } else {
    		// failure
    		$this->response_error('DB Error: '.$this->db->_error_message());
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
			   $all[] = $row;
			}
			$this->response_success($all); // 200 being the HTTP response code
        	
        } else {
    		// failure
    		$this->response_error('DB Error: '.$this->db->_error_message());
    	}
	}

	// boolean promoteAccount(Integer targetAccountID);
	function promote_post()
	{
		$id = $this->post('id');
		
    	$this->db->set('isAdmin', true);
    	$this->db->where('id', $id);
    	$result = $this->db->update('accounts');
    	
    	if($result){
    		// success
    		$this->response_success('account promoted');
    		
    	} else {
    		// failure
    		$this->response_error('DB Error: '.$this->db->_error_message());
    	}
	}

	function response_success($message)
	{
		$data = array(
			'success' => true,
			'error'   => false,
			'result' => $message
		);
		$this->response($data, 200);
	}
	
	function response_error($message)
	{
		$data = array(
			'success' => false,
			'error'   => true,
			'result' => $message
		);
		$this->response($data, 404);
	}
}