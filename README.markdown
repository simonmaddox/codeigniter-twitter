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

At the moment, parameters (such as "since") passed to most GET 
methods don't work. This will be fixed in v1.1

Every method will either return a boolean or a SimpleXML object. 
I'd like to change this so that something more meaningful is 
returned.

Extra
-----

If you'd like to request changes, report bug fixes, or contact
the developer of this library, email <simon@simonmaddox.com>