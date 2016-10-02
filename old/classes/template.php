<?php


class Template {

        private $vars = array(), $template;

		function __construct($template) {
			$this->template = $template;
		}
		
		function set($varname, $value) {
			$this->vars[$varname] = $value;
			return $this;
		}
		
		function remove($varname) {
			unset($this->vars[$varname]);
			return $this;
		}

		function show() {
			$template_file = $_SERVER['DOCUMENT_ROOT'] . 'templates/' . $this->template . '.tmpl';
			if(is_readable($template_file) == false)
				die('Error 404 Template not found!');
			$template_body = file_get_contents($template_file);
			foreach($this->vars as $key=>$val) {
				$template_body = str_replace("/({$key})", $val, $template_body);
			}
			return $template_body;
		}

}


?>