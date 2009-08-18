CodeIgniter-Twitter
===================

CodeIgniter-Twitter is a CodeIgniter library which assists in the 
development of applications using the Twitter API.

Also: This library isn't CodeIgniter specific. It has been
tested in Kohana and as a normal PHP class.

Usage
-----

	$this->load->library('twitter');
	$this->twitter->auth('someuser','somepass');
	$this->twitter->update('My awesome tweet!');

You *must* call the auth() method before doing anything else in
the class.

Future
------

Anything else?

Extra
-----

If you'd like to request changes, report bug fixes, or contact
the developer of this library, email <simon@simonmaddox.com>

Thanks
------

Noah Stokes
Phil Sturgeon - http://philsturgeon.co.uk
Carlos López - http://blewblew.com