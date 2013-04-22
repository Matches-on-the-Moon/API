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

class Item extends REST_Controller
{
	/*
		public Integer createItem(Integer ownerID, String name, String location, String reward, Item.Type type, String category, String description)
	            throws Exception;
	    
	    public void deleteItem(Integer itemID);
	
	    x public void deleteUsersItems(Integer userID);

		public ArrayList<Item> getAllItems();
	
		public Item getItem(Integer itemId);
	
		public ArrayList<Item> getMatches(Item item);
		
		== Item ==
		Integer id;
		Integer ownerID; 
		Calendar calendar;
		String name;
		String location;
		Status status;
		String reward;
		Type type;
		String category;
		String description;
	*/
	
	// Integer createItem(Integer ownerID, String name, String location, String reward, Item.Type type, String category, String description)
	// create an item and return it's item id
	function index_put()
    {
    	$data = array(
    		'ownerID' 	  => $this->put('ownerID'),
    		'name'		  => $this->put('name'),
    		'location'	  => $this->put('location'),
    		'status'	  => 'OPEN',
    		'reward' 	  => $this->put('reward'),
    		'type'  	  => $this->put('type'),
    		'category'    => $this->put('category'),
    		'description' => $this->put('description')
    	);
    	
    	$result = $this->db->insert('items', $data);
    	
    	if($result){
    		// success
    		// respond with id
    		$id = $this->db->insert_id();
    		$this->response_success($id);
    		
    	} else {
    		// failure
    		$this->response_error('DB Error: '.$this->db->_error_message());
    	}
    }
    
    // void deleteItem(Integer itemID)
    // delete an item given it's id
    function index_delete()
    {
    	$id = $this->delete('id');
    	
    	$this->db->where('id', $id);
    	$result = $this->db->delete('items');
    	
    	if($result !== false){
        	// success
        	$this->response_success('item deleted'); // 200 being the HTTP response code
        	
        } else {
    		// failure
    		$this->response_error('DB Error: '.$this->db->_error_message());
    	}
    }
	
	// Item getItem(Integer itemId)
	// get a single item given it's id
	function index_get()
    {
    	$id = $this->get('id');
       	
    	$this->db->where('id', $id);
    	$result = $this->db->get('items');
    
        if($result !== false){
        	// success
        	$row = $result->row_array();
        	$this->response_success($row); // 200 being the HTTP response code
        	
        } else {
    		// failure
    		$this->response_error('DB Error: '.$this->db->_error_message());
    	}
    }
    
    // boolean editItem(Integer itemId, String itemName, String itemLocation, String itemReward, String itemCategory, String itemDescription);
    // update an item
    function index_post()
    {
    	$id = $this->post('id');
    
    	$data = array(
    		'name'		  => $this->post('name'),
    		'location'	  => $this->post('location'),
    		'reward' 	  => $this->post('reward'),
    		'category'    => $this->post('category'),
    		'description' => $this->post('description')
    	);
    	
    	$this->db->where('id', $id);
    	$result = $this->db->update('items', $data);
    	
    	if($result){
    		// success
    		$this->response_success('item edited');
    		
    	} else {
    		// failure
    		$this->response_error('DB Error: '.$this->db->_error_message());
    	}
    }
    
    // ArrayList<Item> getAllItems()
    // get all the items
    function all_get()
    {
    	$this->db->order_by('id');
        $result = $this->db->get('items');
    
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
    
    // ArrayList<Item> getMatches(Item item)
    // get all itme matching this item
    function matches_get()
    {
    	$id = $this->get('id');
    	$name = $this->get('name');
    	$location = $this->get('location');
    
    	$this->db->like('LOWER(name)', strtolower($name));
    	$this->db->like('LOWER(location)', strtolower($location));
    	$this->db->where_not_in('id', array($id));
    	$this->db->limit(20);// just in case
    	$result = $this->db->get('items');
    
    	if($result !== false){
        	// success
        	$all = array();
	    	foreach ($result->result_array() as $row){
			   $all[] = $result->row_array();
			}
			$this->response_success($all); // 200 being the HTTP response code
        	
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