<?php
	/**
	* IPC Test Suite
	* @author Dirk Schaller <dschaller@probusiness.de>
	* @copyright Copyright (C) 2003-2004, Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package ipc_test_suite
	* @version $Id: index.php 14991 2004-05-26 14:36:27Z mkaemmerer $
	*/

$GLOBALS['phpgw_info']['flags'] = array(
	'currentapp' => 'ipc_test_suite',
	'nonavbar'   => True,
	'noheader'   => True
);
include('../header.inc.php');
$obj = createobject('ipc_test_suite.ipc_test_suite_ui');
$obj->init();
?>
