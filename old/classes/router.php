<?php

class Router {
	private $path;
	private $args = array();
	
	function delegate() {
		$this->getController($file, $controller, $action, $args);
		if (is_readable($file) == false) {
            die ('404 Not Found');
        }
		include ($file);
		$class = 'controller_' . $controller;
		$controller = new $class();
        if (is_callable(array($controller, $action)) == false) {
            die ('404 Not Found');
        }
		$controller->$action($args);
	}
	
	private function getController(&$file, &$controller, &$action, &$args) {
        $route = isset($_GET['route']) ? $_GET['route'] : '';
        if (empty($route)) { $route = 'index'; }
        $route = trim($route, '/\\');
        $parts = explode('/', $route);
        $cmd_path = $_SERVER['DOCUMENT_ROOT'] . 'controllers/';
        foreach ($parts as $part) {
                $fullpath = $cmd_path . $part;
                if (is_dir($fullpath)) {
                        $cmd_path .= $part . '/';
                        array_shift($parts);
                        continue;
                }
                if (is_file($fullpath . '.php')) {
                        $controller = $part;
                        array_shift($parts);
                        break;
                }
        }
        if (empty($controller)) { $controller = 'index'; };
        $action = array_shift($parts);
        if (empty($action)) { $action = 'index'; }
        $file = $cmd_path . $controller . '.php';
        $args = $parts;
	}

	
}