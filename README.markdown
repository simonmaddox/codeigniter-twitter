CodeIgniter-Twitter
===================

CodeIgniter-Twitter is a CodeIgniter library which assists in the 
development of applications using the Twitter API.

Usage
-----

	$this->load->library('twitter');
	$this->twitter->auth('someuser','somepass');
	$this->twitter->update('My awesome tweet!');

You *must* call the auth() method before doing anything else in
the class.

Future
------

Add API methods not currently implemented

JSON support

Twitter Trends (requires JSON)

Extra
-----

If you'd like to request changes, report bug fixes, or contact
the developer of this library, email <simon@simonmaddox.com>

Also: This library isn't CodeIgniter specific. It has been
tested in Kohana and as a normal PHP class.