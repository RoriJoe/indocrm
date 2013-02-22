<?php

/*
 * Copyright 2011 by ORCA, Jl. Taman Sulfat 7 No 4, Malang, ID
 * All rights reserved
 * 
 * Written By: herdian ferdianto
 * ferdhie@orca.web.id
 * http://ferdianto.com/
 */

/**
 * Description of Docs
 *
 * @author ferdhie
 */
class Docs extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index(){
	}
	
	public function faqs(){
		$this->load->view('faqs');
	}
}
