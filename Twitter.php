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
		
		if (empty($this->friends_timeline)){
			$this->friends_timeline = $this->_fetch('http://twitter.com/statuses/friends_timeline.xml');
		}
		
		return $this->friends_timeline;
	}
	
	function user_timeline($id = '', $count = '', $since = '', $since_id = '', $page = ''){
		if (!$this->auth){ return false; }
		
		if (empty($this->user_timeline)){
			$this->user_timeline = $this->_fetch('http://twitter.com/statuses/user_timeline.xml');
		}
		
		return $this->user_timeline;
	}
	
	function show($id = 55){
		if (!$this->auth){ return false; }
		return $this->_fetch('http://twitter.com/statuses/show/'.$id.'.xml');
	}
	
	function replies($since = '', $since_id = '', $page = ''){
		if (!$this->auth){ return false; }
		
		if (empty($this->replies)){
			$this->replies = $this->_fetch('http://twitter.com/statuses/replies.xml');
		}
		
		return $this->replies;
	}
	
	function friends($id = '', $page = ''){
		if (!$this->auth){ return false; }
		
		if (empty($this->friends)){
			$this->friends = $this->_fetch('http://twitter.com/statuses/friends.xml');
		}
		
		return $this->friends;
	}
	
	function followers($id = '', $page = ''){
		if (!$this->auth){ return false; }
		
		if (empty($this->followers)){
			$this->followers = $this->_fetch('http://twitter.com/statuses/friends.xml');
		}	
			
		return $this->followers;
	}
	
	function user_show($id = ''){
		if (!$this->auth){ return false; }
		return $this->_fetch('http://twitter.com/users/show/id.xml?id=' . $id);
	}
	
	function direct_messages($since = '', $since_id = '', $page = ''){
		if (!$this->auth){ return false; }
		
		if (empty($this->direct_messages)){
			$this->direct_messages = $this->_fetch('http://twitter.com/direct_messages.xml');
		}
		
		return $this->direct_messages;
	}
	
	function sent_direct_messages($since = '', $since_id = '', $page = ''){
		if (!$this->auth){ return false; }
		
		if (empty($this->sent_direct_messages)){
			$this->sent_direct_messages = $this->_fetch('http://twitter.com/direct_messages/sent.xml');
		}
		
		return $this->sent_direct_messages;
	}
	
	function friendship_exists($user_a = '', $user_b = ''){
		if (!$this->auth){ return false; }
		return $this->_fetch('http://twitter.com/friendships/exists.xml?user_a='.$user_a.'&user_b=' . $user_b);
	}
	
	function rate_limit_status(){
		if (!$this->auth){ return false; }
		return $this->_fetch('http://twitter.com/account/rate_limit_status.xml');
	}
	
	function favorites($id = '', $page = ''){
		if (!$this->auth){ return false; }
		
		if (empty($this->favorites)){
			$this->favorites = $this->_fetch('http://twitter.com/favorites.xml');
		}
		
		return $this->favorites;
	}
	
	function downtime_schedule(){
		return $this->_fetch('http://twitter.com/help/downtime_schedule.xml');
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
		
		return $this->_post('http://twitter.com/statuses/update.xml', $params);
	}
	
	function destroy($id = ''){
		$params = array();
		
		if (!empty($id)){
			$params['id'] = $id;
		}
		
		return $this->_post('http://twitter.com/statuses/destroy/id.xml', $params);
	}
	
	function new_direct_message($user = '', $text = ''){
		$params = array();
		
		if (!empty($user)){
			$params['user'] = $user;
		}
		
		if (!empty($text)){
			$params['text'] = $text;
		}
		
		return $this->_post('http://twitter.com/direct_messages/new.xml', $params);
	}
	
	function destroy_direct_message($id = ''){
		$params = array();
		
		if (!empty($id)){
			$params['id'] = $id;
		}
		
		return $this->_post('http://twitter.com/direct_messages/destroy/id.xml', $params);
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
				
		return $this->_post('http://twitter.com/friendships/create/id.xml', $params);
	}
	
	function destroy_friendship($id = ''){
		$params = array();
		
		if (!empty($id)){
			$params['id'] = $id;
		}
		
		return $this->_post('http://twitter.com/friendships/destroy/id.xml', $params);
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
		
		return $this->_post('http://twitter.com/account/update_profile.xml', $params);
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
			return $this->_parse_xml($returned);
		} else {
			return false;
		}
	}
	
	function _post($url,$array){
		$params = '';
		foreach ($array as $key => $value){
			$params .= $key . '=' . $value . '&';
		}
		
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
			return $this->_parse_xml($returned);
		} else {
			return false;
		}
	}
	
	function _parse_xml($xml){
		return new SimpleXMLElement($xml); // if you don't like SimpleXML, change it!
	}
	
}