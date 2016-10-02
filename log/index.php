<?php

$log_file = "/tmp/myownradio.dev.log";

$wait_time = 10;
$pre_load = 20000;

header("text/html; charset=utf-8");

if($_SERVER['REQUEST_METHOD'] == 'POST') {
	$fs = filesize($log_file);
	$from = isset($_POST['f']) ? (int) $_POST['f'] : -1;
	if($from == -1)
		$from = (($fs <= $pre_load) ? 0 : ($fs - $pre_load));
	$start = time();
	while($start+$wait_time>time()) {
		if(file_exists($log_file)) {
			$fs = filesize($log_file);
			if($fs > $from) {
				$fh = fopen($log_file, 'r');
				fseek($fh, $from);
				$bytes = fread($fh, 4096);
				$end = ftell($fh);
				fclose($fh);
				$result = array(
					'status' => 'OK',
					'size' => $end,
					'data' => base64_encode($bytes)
				);
				die(json_encode($result));
			}
		} else {
			die(json_encode(array('status' => 'File not found!')));
		}
		clearstatcache();
		usleep(250000);
	}
	die(json_encode(array('status' => 'null')));
}

?>
<!DOCTYPE HTML>
<html>
<head>
	<title>MyOwnRadio Debug</title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link href="style.css" rel="stylesheet" />
	<script type="text/javascript" src="jquery-1.7.1.min.js"></script>
	<script type="text/javascript" src="errors.js"></script>
</head>
<body>
	<div class="data-log"></div>
</body>
</html>
