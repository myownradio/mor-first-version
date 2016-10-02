<?php

class controller_index {

	function index($args) {
		$data = md5(time());
		$tmpl = new Template('radiomanager/index');
		$tmpl->set('time', $data);
		echo $tmpl->show();
	}
	
	
	
}