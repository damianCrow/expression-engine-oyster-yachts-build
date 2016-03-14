<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Number_format {

	public $return_data = "";

    public function __construct()
    {
    	$this->return_data = number_format(ee()->TMPL->tagdata);
    }
}