<?php
 /**********************************************************************\
 * phpGroupWare - eTemplate						*
 * http://www.phpgroupware.org						*
 * This program is part of the GNU project, see http://www.gnu.org/	*
 *									*
 * Copyright 2002, 2003 Free Software Foundation, Inc.			*
 *									*
 * Originally Written by Ralf Becker - <RalfBecker@outdoor-training.de>	*
 * --------------------------------------------				*
 * This program is Free Software; you can redistribute it and/or modify *
 * it under the terms of the GNU General Public License as published by *
 * the Free Software Foundation; either version 2 of the License, or 	*
 * at your option) any later version.					*
 \**********************************************************************/
 /* $Id: process_exec.php 17936 2007-02-10 16:03:46Z sigurdne $ */

	list($app) = explode('.',$_GET['menuaction']);

	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp'	=> $app,
		'noheader'		=> True,
		'nonavbar'		=> True
	);
	include('../header.inc.php');

	ExecMethod('etemplate.etemplate.process_exec');
