<?php

/* 

	Sections:

	|- MOR's Home Page (VIEW1)
	|- Stream's Page (VIEW1)
	|- Director's Profile (VIEW1)
	|- Dashboard:
	---------
		| (Authorized) (VIEW2)
		|- Main View
		* Stream View
		* Edit Stream Info
		* Edit Profile Info

		(Unauthorized)
		* Login View
		* Register View

*/

class Router {
	function __construct($route) {
		require($_SERVER['DOCUMENT_ROOT'] . "/core/view/view_dashboard_main.php");
	}
}

