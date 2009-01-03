<?php

/**
* A CodeIgniter library to allow use of the Twitter API
*
* Example Usage:
*
*	$this->load->library('twitter');
*	$this->twitter->auth('someuser','somepass');
*	$this->twitter->update('My awesome tweet!');
*
* Methods return a mixture of boolean and SimpleXML objects
*
* @author Simon Maddox <simon@simonmaddox.com>
* @license Creative Commons Attribution-Share Alike 3.0 Unported
* http://creativecommons.org/licenses/by-sa/3.0/
**/

class Twitter {
	var $type = 'xml';
	
	var $username;
	var $password;
	var $auth;
	var $user;
	
	var $friends_timeline;
	var $replies;
	var $friends;
	var $followers;
	var $direct_messages;
	var $sent_direct_messages;
	var $favorites;
	
	function auth($username,$password){
		$this->username = $username;
		$this->password = $password;
		
		$user = $this->_fetch('http://twitter.com/account/verify_credentials.xml');
		
		if ($user == false){
			$this->auth = false;
			return false;
		} else {
			$this->user = $user;
			$this->auth = true;
			return true;
		}
	}
	
	function get_user(){
		if (!$this->auth){ return false; }
		return $this->user;
	}
	
	/*
		GET Methods
	*/
	
	function friends_timeline($count = '', $since = '', $since_id = '', $page = ''){
		if (!$this->auth){ return false; }
		
		$params = $this->_build_params(array('count' => $count, 'since' => $since, 'since_id' => $since_id, 'page' => $page));
		
		if (empty($this->friends_timeline)){
			$this->friends_timeline = $this->_fetch('http://twitter.com/statuses/friends_timeline.' . $this->type . $params);
		}
		
		return $this->friends_timeline;
	}
	
	function user_timeline($id = '', $count = '', $since = '', $since_id = '', $page = ''){
		if (!$this->auth){ return false; }
		
		$params = $this->_build_params(array('id' => $id, 'count' => $count, 'since' => $since, 'since_id' => $since_id, 'page' => $page));
		
		if (empty($this->user_timeline)){
			$this->user_timeline = $this->_fetch('http://twitter.com/statuses/user_timeline.' . $this->type . $params);
		}
		
		return $this->user_timeline;
	}
	
	function show($id = 55){
		if (!$this->auth){ return false; }
		return $this->_fetch('http://twitter.com/statuses/show/'.$id.'.xml');
	}
	
	function replies($since = '', $since_id = '', $page = ''){
		if (!$this->auth){ return false; }
		
		$params = $this->_build_params(array('since' => $since, 'since_id' => $since_id, 'page' => $page));
		
		if (empty($this->replies)){
			$this->replies = $this->_fetch('http://twitter.com/statuses/replies.' . $this->type . $params);
		}
		
		return $this->replies;
	}
	
	function friends($id = '', $page = ''){
		if (!$this->auth){ return false; }
		
		$params = $this->_build_params(array('id' => $id, 'page' => $page));
		
		if (empty($this->friends)){
			$this->friends = $this->_fetch('http://twitter.com/statuses/friends.' . $this->type . $params);
		}
		
		return $this->friends;
	}
	
	function followers($id = '', $page = ''){
		if (!$this->auth){ return false; }
		
		$params = $this->_build_params(array('id' => $id, 'page' => $page));
		
		if (empty($this->followers)){
			$this->followers = $this->_fetch('http://twitter.com/statuses/friends.' . $this->type . $params);
		}	
			
		return $this->followers;
	}
	
	function user_show($id = ''){
		if (!$this->auth){ return false; }
		return $this->_fetch('http://twitter.com/users/show/id.'.$this->type.'?id=' . $id);
	}
	
	function direct_messages($since = '', $since_id = '', $page = ''){
		if (!$this->auth){ return false; }
		
		$params = $this->_build_params(array('since' => $since, 'since_id' => $since_id, 'page' => $page));
		
		if (empty($this->direct_messages)){
			$this->direct_messages = $this->_fetch('http://twitter.com/direct_messages.' . $this->type . $params);
		}
		
		return $this->direct_messages;
	}
	
	function sent_direct_messages($since = '', $since_id = '', $page = ''){
		if (!$this->auth){ return false; }
		
		$params = $this->_build_params(array('since' => $since, 'since_id' => $since_id, 'page' => $page));
		
		if (empty($this->sent_direct_messages)){
			$this->sent_direct_messages = $this->_fetch('http://twitter.com/direct_messages/sent.' . $this->type . $params);
		}
		
		return $this->sent_direct_messages;
	}
	
	function friendship_exists($user_a = '', $user_b = ''){
		if (!$this->auth){ return false; }
		$friends = (string) $this->_fetch('http://twitter.com/friendships/exists.'.$this->type.'?user_a='.$user_a.'&user_b=' . $user_b);
		return ($friends == 'true') ? true : false;
	}
	
	function rate_limit_status(){
		if (!$this->auth){ return false; }
		return $this->_fetch('http://twitter.com/account/rate_limit_status.' . $this->type);
	}
	
	function favorites($id = '', $page = ''){
		if (!$this->auth){ return false; }
		
		$params = $this->_build_params(array('id' => $id, 'page' => $page));
		
		if (empty($this->favorites)){
			$this->favorites = $this->_fetch('http://twitter.com/favorites.' . $this->type);
		}
		
		return $this->favorites;
	}
	
	function downtime_schedule(){
		return $this->_fetch('http://twitter.com/help/downtime_schedule.' . $this->type);
	}
	
	/*
		POST Methods
	*/
	
	function update($status = '', $in_reply_to_status_id = ''){
		$params = array();
		$params['status'] = $status;
		
		if (!empty($in_reply_to_status_id)){
			$params['in_reply_to_status_id'] = $in_reply_to_status_id;
		}
		
		return $this->_post('http://twitter.com/statuses/update.' . $this->type, $params);
	}
	
	function destroy($id = ''){
		$params = array();
		
		if (!empty($id)){
			$params['id'] = $id;
		}
		
		return $this->_post('http://twitter.com/statuses/destroy/id.' . $this->type, $params);
	}
	
	function new_direct_message($user = '', $text = ''){
		$params = array();
		
		if (!empty($user)){
			$params['user'] = $user;
		}
		
		if (!empty($text)){
			$params['text'] = $text;
		}
		
		return $this->_post('http://twitter.com/direct_messages/new.' . $this->type, $params);
	}
	
	function destroy_direct_message($id = ''){
		$params = array();
		
		if (!empty($id)){
			$params['id'] = $id;
		}
		
		return $this->_post('http://twitter.com/direct_messages/destroy/id.' . $this->type, $params);
	}
	
	function create_friendship($id = '', $follow = ''){
		$params = array();
		
		if (!empty($id)){
			$params['id'] = $id;
		}
		
		$params = array();
		
		if (!empty($follow)){
			$params['follow'] = $follow;
		}
				
		return $this->_post('http://twitter.com/friendships/create/id.' . $this->type, $params);
	}
	
	function destroy_friendship($id = ''){
		$params = array();
		
		if (!empty($id)){
			$params['id'] = $id;
		}
		
		return $this->_post('http://twitter.com/friendships/destroy/id.' . $this->type, $params);
	}
	
	function update_profile($name = '', $email = '', $url = '', $location = '', $description = ''){
		$params = array();
		
		if (!empty($name)){
			$params['name'] = $name;
		}
		
		if (!empty($email)){
			$params['email'] = $email;
		}
		
		if (!empty($url)){
			$params['url'] = $url;
		}
		
		if (!empty($location)){
			$params['location'] = $location;
		}
		
		if (!empty($description)){
			$params['description'] = (strlen($description) > 160) ? substr($description,0,160) : $description;
		}
		
		return $this->_post('http://twitter.com/account/update_profile.' . $this->type, $params);
	}
	
	/*
		System Methods
	*/
	
	function _fetch($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERPWD, $this->username . ':' . $this->password);
		$returned = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close ($ch);
		
		if ($status == '200'){
			return $this->_parse_returned($returned);
		} else {
			return false;
		}
	}
	
	function _post($url,$array){
		$params = $this->_build_params($array,FALSE);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERPWD, $this->username . ':' . $this->password);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		$returned = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close ($ch);
		
		if ($status == '200'){
			return $this->_parse_returned($returned);
		} else {
			return false;
		}
	}
	
	function _parse_returned($xml){
		return new SimpleXMLElement($xml); // if you don't like SimpleXML, change it!
	}
	
	function _build_params($array, $query_string = TRUE){
		$params = '';
		
		foreach ($array as $key => $value){
			if (!empty($value)){
				$params .= $key . '=' . $value . '&';
			}
		}
		
		$character = ($query_string) ? '?' : '';
		
		return (!empty($params)) ? $character . $params : '';
	}
	
}