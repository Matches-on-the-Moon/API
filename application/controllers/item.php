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
    		'reward' 	  => $this->put('reward'),
    		'type'  	  => $this->put('type'),
    		'category'    => $this->put('category'),
    		'description' => $this->put('description')
    	);
    	
    	$result = $this->db->insert('items', $data);
    	
    	if($result){
    		// success
    		$this->response('success', 200);
    		
    	} else {
    		// failure
    		$this->response(array('error' => 'DB Error: '.$this->db->_error_message()), 404);
    	}
    }
    
    // void deleteItem(Integer itemID)
    // delete an item given it's id
    function index_delete()
    {
    	$id = $this->delete('id');
    	
    	$this->db->where('id', $id);
    	$this->db->delete('items');
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
        	$this->response($row, 200); // 200 being the HTTP response code
        	
        } else {
    		// failure
    		$this->response(array('error' => 'DB Error: '.$this->db->_error_message()), 404);
    	}
    }
    
    // boolean editItem(Integer itemId, String itemName, String itemLocation, String itemReward, String itemCategory, String itemDescription);
    // update an item
    function index_post()
    {
    	$id = $this->post('id');
    
    	$data = array(
    		'ownerID' 	  => $this->post('ownerID'),
    		'name'		  => $this->post('name'),
    		'location'	  => $this->post('location'),
    		'reward' 	  => $this->post('reward'),
    		'type'  	  => $this->post('type'),
    		'category'    => $this->post('category'),
    		'description' => $this->post('description')
    	);
    	
    	$this->db->where('id', $id);
    	$result = $this->db->update('items', $data);
    	
    	if($result){
    		// success
    		$this->response('success', 200);
    		
    	} else {
    		// failure
    		$this->response(array('error' => 'DB Error: '.$this->db->_error_message()), 404);
    	}
    }
    
    // ArrayList<Item> getAllItems()
    // get all the items
    function all_get()
    {
        $result = $this->db->get('items');
    
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
    
    // ArrayList<Item> getMatches(Item item)
    // get all itme matching this item
    function matches_get()
    {
    	$name = $this->get('name');
    	$location = $this->get('location');
    
    	$this->db->like('LOWER(name)', strtolower($name));
    	$this->db->like('LOWER(location)', strtolower($location));
    	$this->db->limit(100);// just in case
    	$result = $this->db->get('items');
    
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
}