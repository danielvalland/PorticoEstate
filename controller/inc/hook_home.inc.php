<?php
	/**
	* phpGroupWare - controller: a part of a Facilities Management System.
	*
	* @author Erik Holm-Larsen <erik.holm-larsen@bouvet.no>
	* @author Torstein Vadla <torstein.vadla@bouvet.no>
	* @copyright Copyright (C) 2011,2012 Free Software Foundation, Inc. http://www.fsf.org/
	* This file is part of phpGroupWare.
	*
	* phpGroupWare is free software; you can redistribute it and/or modify
	* it under the terms of the GNU General Public License as published by
	* the Free Software Foundation; either version 2 of the License, or
	* (at your option) any later version.
	*
	* phpGroupWare is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	* GNU General Public License for more details.
	*
	* You should have received a copy of the GNU General Public License
	* along with phpGroupWare; if not, write to the Free Software
	* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/
	* @package property
	* @subpackage controller
 	* @version $Id$
	*/	

	include_class('controller', 'check_list', 'inc/model/');
	include_class('controller', 'check_item', 'inc/model/');
	include_class('controller', 'check_list_status_info', 'inc/helper/');
	include_class('controller', 'date_generator', 'inc/component/');
	include_class('controller', 'location_finder', 'inc/helper/');
	
	$so = CreateObject('controller.socheck_list');
	$so_control = CreateObject('controller.socontrol');

	//echo '<H1> Hook for controller </H1>';	
	$location_code = '1101';
	$year = phpgw::get_var('year');
	
	if(empty($year)){
		$year = date("Y");	
	}
	
	$year = intval($year);
				
	//$from_date_ts = strtotime("01/01/$year");
	$from_date_ts = strtotime("now");
	$to_year = $year + 1;
	$to_date_ts = strtotime("01/01/$to_year");	
				
	$criteria = array
	(
		'user_id' => $GLOBALS['phpgw_info']['user']['account_id'],
		'type_id' => 1,
		'role_id' => 0, // For å begrense til en bestemt rolle - ellers listes alle roller for brukeren
		'allrows' => false
	);

	$location_finder = new location_finder();
	$my_locations = $location_finder->get_responsibilities( $criteria );
	//print_r($my_locations);
	
	if(empty($location_code)){
		$location_code = $my_locations[0]["location_code"];	
	}
	
	$repeat_type = null;
	
	$controls_for_location_array = $so_control->get_controls_by_location($location_code, $from_date_ts, $to_date_ts, $repeat_type );
	//var_dump($controls_for_location_array);
	$controls_array = array();
	$control_dates = array();
	foreach($controls_for_location_array as $control){
		$date_generator = new date_generator($control->get_start_date(), $control->get_end_date(), $from_date_ts, $to_date_ts, $control->get_repeat_type(), $control->get_repeat_interval());
		$controls_array[] = array($control, $date_generator->get_dates());
	}

	$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
	
	$portalbox1 = CreateObject('phpgwapi.listbox', array
	(
		'title'		=> "Mine planlagte kontroller",
		'primary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
		'secondary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
		'tertiary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
		'width'	=> '100%',
		'outerborderwidth'	=> '0',
		'header_background_image'	=> $GLOBALS['phpgw']->common->image('phpgwapi','bg_filler', '.png', False)
	));

	$app_id = $GLOBALS['phpgw']->applications->name2id('controller');
	if( !isset($GLOBALS['portal_order']) ||!in_array($app_id, $GLOBALS['portal_order']) )
	{
		$GLOBALS['portal_order'][] = $app_id;
	}
	$var = array
	(
		'up'	=> array('url'	=> '/set_box.php', 'app'	=> $app_id),
		'down'	=> array('url'	=> '/set_box.php', 'app'	=> $app_id),
		'close'	=> array('url'	=> '/set_box.php', 'app'	=> $app_id),
		'question'	=> array('url'	=> '/set_box.php', 'app'	=> $app_id),
		'edit'	=> array('url'	=> '/set_box.php', 'app'	=> $app_id)
	);

	foreach ( $var as $key => $value )
	{
		//			$portalbox->set_controls($key,$value);
	}

	$category_name = array(); // caching
	
	$cats	= CreateObject('phpgwapi.categories', -1, 'controller', '.control');
	$cats->supress_info	= true;
	$control_areas = $cats->formatted_xslt_list(array('format'=>'filter','selected' => '','globals' => true,'use_acl' => $this->_category_acl));

	$portalbox1->data = array();
	foreach ($controls_array as $control_instance)
	{
		$current_control = $control_instance[0];
		$check_lists = $so->get_planned_check_lists_for_control($current_control->get_id());
		$control_location = $so_control->getLocationCodeFromControl($current_control->get_id());
		$location_array = execMethod('property.bolocation.read_single', array('location_code' => $control_location));
		$location_name = $location_array["loc1_name"];
		foreach($control_areas['cat_list'] as $area)
		{
			if($area['cat_id'] == $current_control->get_control_area_id())
			{
				$control_area_name = $area['name'];
			}
		}
		foreach($check_lists as $check_list)
		{
			$next_date = "Planlagt: " . date('d/m/Y', $check_list->get_planned_date());
			$portalbox1->data[] = array
			(
				'text' => "{$location_name} - {$control_area_name} - {$current_control->get_title()} :: {$next_date}",
				'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list_for_location.add_check_list', 'date' => $current_date, 'control_id' => $current_control->get_id(), 'location_code' => '1101'))
			);
		}
/*		
		$current_dates = $control_instance[1];
		foreach($current_dates as $current_date)
		{
			$next_date = "Planlagt: " . date('d/m/Y', $current_date);
			$portalbox1->data[] = array
			(
				'text' => "{$location_name} - {$control_area_name} - {$current_control->get_title()} :: {$next_date}",
				'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list_for_location.add_check_list', 'date' => $current_date, 'control_id' => $current_control->get_id(), 'location_code' => '1101'))
			);
		}
*/
	}
	echo "\n".'<!-- BEGIN checklist info -->'."\n".$portalbox1->draw()."\n".'<!-- END checklist info -->'."\n";
	
	$portalbox2 = CreateObject('phpgwapi.listbox', array
	(
		'title'		=> "Mine tildelte kontroller",
		'primary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
		'secondary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
		'tertiary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
		'width'	=> '100%',
		'outerborderwidth'	=> '0',
		'header_background_image'	=> $GLOBALS['phpgw']->common->image('phpgwapi','bg_filler', '.png', False)
	));

	$app_id = $GLOBALS['phpgw']->applications->name2id('controller');
	if( !isset($GLOBALS['portal_order']) ||!in_array($app_id, $GLOBALS['portal_order']) )
	{
		$GLOBALS['portal_order'][] = $app_id;
	}
	$var = array
	(
		'up'	=> array('url'	=> '/set_box.php', 'app'	=> $app_id),
		'down'	=> array('url'	=> '/set_box.php', 'app'	=> $app_id),
		'close'	=> array('url'	=> '/set_box.php', 'app'	=> $app_id),
		'question'	=> array('url'	=> '/set_box.php', 'app'	=> $app_id),
		'edit'	=> array('url'	=> '/set_box.php', 'app'	=> $app_id)
	);

	foreach ( $var as $key => $value )
	{
		//			$portalbox->set_controls($key,$value);
	}

	$category_name = array(); // caching

	$portalbox2->data = array();
	foreach ($controls_array as $control_instance)
	{
		$current_control = $control_instance[0];
		$control_location = $so_control->getLocationCodeFromControl($current_control->get_id());
		$location_array = execMethod('property.bolocation.read_single', array('location_code' => $control_location));
		$location_name = $location_array["loc1_name"];
		foreach($control_areas['cat_list'] as $area)
		{
			if($area['cat_id'] == $current_control->get_control_area_id())
			{
				$control_area_name = $area['name'];
			}
		}
		$current_dates = $control_instance[1];
		foreach($current_dates as $current_date)
		{
			$next_date = "Fristdato: " . date('d/m/Y', $current_date);
			$portalbox2->data[] = array
			(
				'text' => "{$location_name} - {$control_area_name} - {$current_control->get_title()} :: {$next_date}",
				'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list_for_location.add_check_list', 'date' => $current_date, 'control_id' => $current_control->get_id(), 'location_code' => '1101'))
			);
		}
	}
	echo "\n".'<!-- BEGIN assigned checklist info -->'."\n".$portalbox2->draw()."\n".'<!-- END assigned checklist info -->'."\n";
