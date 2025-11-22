<?php
	function load_top()
	{
		global $page_css;
		require('widget/top.php');

		if (!empty($page_css)) {
			echo '<link rel="stylesheet" href="assets/css/' . $page_css . '">';
		}
	}
	function load_header()
	{
		require('widget/header.php');
	}
	function load_menu()
	{
		require('widget/menu.php');
	}
	
	function load_footer()
	{
		require('widget/footer.php');
	}
?>