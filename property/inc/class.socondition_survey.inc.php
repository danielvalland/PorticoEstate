<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2012 Free Software Foundation, Inc. http://www.fsf.org/
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
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage project
 	* @version $Id$
	*/

	phpgw::import_class('phpgwapi.datetime');
	phpgw::import_class('property.socommon_core');

	/**
	 * Description
	 * @package property
	 */

	class property_socondition_survey extends property_socommon_core
	{

		public function __construct()
		{
			parent::__construct();
		}

		function read($data = array())
		{
			$start		= isset($data['start'])  ? (int) $data['start'] : 0;
			$filter		= isset($data['filter']) ? $data['filter'] : 'none';
			$query		= isset($data['query']) ? $data['query'] : '';
			$sort		= isset($data['sort']) ? $data['sort'] : 'DESC';
			$dir		= isset($data['dir']) ? $data['dir'] : '' ;
			$cat_id		= isset($data['cat_id']) ? (int)$data['cat_id'] : 0;
			$allrows	= isset($data['allrows']) ? $data['allrows'] : '';

			$table = 'fm_condition_survey';
			if ($sort)
			{
				switch($sort)
				{
					case 'year':
						$sort = 'entry_date';
						break;
					default:
					//
				}
				
				$metadata = $this->_db->metadata($table);
				if(isset($metadata[$sort]))
				{
					$ordermethod = " ORDER BY {$table}.$sort $dir";
				}
			}
			else
			{
				$ordermethod = " ORDER BY {$table}.id DESC";
			}

			$where = 'WHERE';
			if ($cat_id)
			{
				$filtermethod .= " {$where} {$table}.category = {$cat_id}";
				$where = 'AND';
			}

			if($query)
			{
				$query			= $this->_db->db_addslashes($query);
				$querymethod	= " {$where} {$table}.title {$this->_like} '%{$query}%'";
			}

			$groupmethod = "GROUP BY $table.id,$table.title,$table.descr,$table.address,$table.entry_date,$table.user_id";
			$sql = "SELECT DISTINCT $table.id,$table.title,$table.descr,$table.address,$table.entry_date,$table.user_id , count(condition_survey_id) AS cnt"
			. " FROM {$table} {$this->_left_join} fm_request ON {$table}.id =fm_request.condition_survey_id {$filtermethod} {$querymethod} {$groupmethod}";

	
			$this->_db->query($sql,__LINE__,__FILE__);
			$this->_total_records = $this->_db->num_rows();

			if(!$allrows)
			{
				$this->_db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->_db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			$values = array();
			while ($this->_db->next_record())
			{
				$values[] = array
				(
					'id'			=> $this->_db->f('id'),
					'title'			=> $this->_db->f('title',true),
					'descr'			=> $this->_db->f('descr',true),
					'address'		=> $this->_db->f('address',true),
					'entry_date'	=> $this->_db->f('entry_date'),
					'user'			=> $this->_db->f('user_id'),
					'cnt'			=> $this->_db->f('cnt'),
				);
			}

			return $values;
		}

		function read_single($data = array())
		{
			$table = 'fm_condition_survey';

			$id		= (int)$data['id'];
			$this->_db->query("SELECT * FROM {$table} WHERE id={$id}",__LINE__,__FILE__);

			$values = array();
			if ($this->_db->next_record())
			{
				$values = array
				(
					'id'				=> $id,
					'title'				=> $this->_db->f('title',true),
					'descr'				=> $this->_db->f('descr', true),
					'location_code'		=> $this->_db->f('location_code', true),
					'status_id'			=> (int)$this->_db->f('status_id'),
					'cat_id'			=> (int)$this->_db->f('category'),
					'vendor_id'			=> (int)$this->_db->f('vendor_id'),
					'coordinator_id'	=> (int)$this->_db->f('coordinator_id'),
					'report_date'		=> (int)$this->_db->f('report_date'),
					'user_id'			=> (int)$this->_db->f('user_id'),
					'entry_date'		=> (int)$this->_db->f('entry_date'),
					'modified_date'		=> (int)$this->_db->f('modified_date'),
				);

				if ( isset($data['attributes']) && is_array($data['attributes']) )
				{
					$values['attributes'] = $data['attributes'];
					foreach ( $values['attributes'] as &$attr )
					{
						$attr['value'] 	= $this->db->f($attr['column_name']);
					}
				}
			}

			return $values;
		}


		function add($data)
		{
			$table = 'fm_condition_survey';

			$this->_db->transaction_begin();

			$value_set						= $this->_get_value_set( $data );

			$id = $this->_db->next_id($table);

			$value_set['id']				= $id;
			$value_set['entry_date']		= time();
			$value_set['title']				= $this->_db->db_addslashes($data['title']);
			$value_set['descr']				= $this->_db->db_addslashes($data['descr']);
			$value_set['status_id']			= (int)$data['status_id'];
			$value_set['category']			= (int)$data['cat_id'];
			$value_set['vendor_id']			= (int)$data['vendor_id'];
			$value_set['coordinator_id']	= (int)$data['coordinator_id'];
			$value_set['report_date']		= phpgwapi_datetime::date_to_timestamp($data['report_date']);
			$value_set['user_id']			= $this->account;
			$value_set['modified_date']		= time();


			$cols = implode(',', array_keys($value_set));
			$values	= $this->_db->validate_insert(array_values($value_set));
			$sql = "INSERT INTO {$table} ({$cols}) VALUES ({$values})";

			try
			{
				$this->_db->Exception_On_Error = true;
				$this->_db->query($sql,__LINE__,__FILE__);
				$this->_db->Exception_On_Error = false;
			}

			catch(Exception $e)
			{
				if ( $e )
				{
					throw $e;
				}
				return 0;
			}

			if($this->_db->transaction_commit())
			{
				return $id;
			}
			else
			{
				return 0;
			}
		}

		public function edit($data)
		{
			$table = 'fm_condition_survey';
			$id = (int)$data['id'];

			$value_set	= $this->_get_value_set( $data );

			$value_set['title']				= $this->_db->db_addslashes($data['title']);
			$value_set['descr']				= $this->_db->db_addslashes($data['descr']);
			$value_set['status_id']			= (int)$data['status_id'];
			$value_set['category']			= (int)$data['cat_id'];
			$value_set['vendor_id']			= (int)$data['vendor_id'];
			$value_set['coordinator_id']	= (int)$data['coordinator_id'];
			$value_set['report_date']		= phpgwapi_datetime::date_to_timestamp($data['report_date']);
			$value_set['user_id']			= $this->account;
			$value_set['modified_date']		= time();

			try
			{
				$this->_edit($id, $value_set, 'fm_condition_survey');
			}

			catch(Exception $e)
			{
				if ( $e )
				{
					throw $e;
				}
			}

			return $id;
		}

		public function edit_title($data)
		{
			$id = (int)$data['id'];

			$value_set	= array
			(
				'title' => $data['title']
			);

			try
			{
				$this->_edit($id, $value_set, 'fm_condition_survey');
			}

			catch(Exception $e)
			{
				if ( $e )
				{
					throw $e;
				}
			}

			return $id;
		}

		public function import($survey, $import_data = array())
		{
			if(!isset($survey['id']) || !$survey['id'])
			{
				throw new Exception('property_socondition_survey::import - missing id');
			}

			$location_data = execMethod('property.solocation.read_single', $survey['location_code']);
			
			$_locations = explode('-', $survey['location_code']);
			$i=1;
			foreach ($_locations as $_location)
			{
				$location["loc{$i}"] = $_location;
				$i++;
			}

			$sorequest	= CreateObject('property.sorequest');

			$this->_db->transaction_begin();

			$config	= CreateObject('phpgwapi.config','property');
			$config->read();

			if(!$survey['location_code'])
			{
				throw new Exception('property_socondition_survey::import - condition survey location_code not configured');
			}

			//FIXME
			if(!isset($config->config_data['condition_survey_import_cat']) || !is_array($config->config_data['condition_survey_import_cat']))
			{
				throw new Exception('property_socondition_survey::import - condition survey import categories not configured');
			}

			if(!isset($config->config_data['condition_survey_initial_status']) || !$config->config_data['condition_survey_initial_status'])
			{
				throw new Exception('property_socondition_survey::import - condition survey initial status not configured');
			}

			if(!isset($config->config_data['condition_survey_hidden_status']) || !$config->config_data['condition_survey_hidden_status'])
			{
				throw new Exception('property_socondition_survey::import - condition survey hidden status not configured');
			}

			/**
			* Park old request on the location and below as obsolete
			*/
			if(isset($config->config_data['condition_survey_obsolete_status'])  && $config->config_data['condition_survey_obsolete_status'])
			{
				$this->_db->query("UPDATE fm_request SET status = '{$config->config_data['condition_survey_obsolete_status']}'"
				 . " WHERE location_code {$this->_db->like} '{$survey['location_code']}%'",__LINE__,__FILE__);
			}
			else
			{
				throw new Exception('property_socondition_survey::import - condition survey obsolete status not configured');
			}

			$cats	= CreateObject('phpgwapi.categories', -1, 'property', '.project');
			$cats->supress_info = true;
			$categories = $cats->return_sorted_array(0, false, '', '', '', $globals = true, '', $use_acl = false);


/*
		$import_types = array
		(
			1 => 'Hidden',
			2 => 'Normal import',
			3 => 'Users/Customers responsibility',
		);
*/


		$import_type_responsibility = array();
		
		$import_type_responsibility[1] = 1; //hidden => responsible_unit 1
		$import_type_responsibility[2] = 1; //'Normal import' => responsible_unit 1
		$import_type_responsibility[3] = 2; //'Customers' => responsible_unit 2
		$import_type_responsibility[4] = 3; //'Customers' => responsible_unit 3
		$import_type_responsibility[5] = 4; //'Customers' => responsible_unit 3
		$import_type_responsibility[6] = 5; //'Customers' => responsible_unit 3
		$import_type_responsibility[7] = 6; //'Customers' => responsible_unit 3
		$import_type_responsibility[8] = 7; //'Customers' => responsible_unit 3

/*
		$cats_candidates = array
		(
			1 => 'Investment',
			2 => 'Operation',
			3 => 'Combined::Investment/Operation',
		);

*/
			$_update_buildingpart = array();
			$filter_buildingpart = isset($config->config_data['filter_buildingpart']) ? $config->config_data['filter_buildingpart'] : array();
			
			if($filter_key = array_search('.project.request', $filter_buildingpart))
			{
				$_update_buildingpart = array("filter_{$filter_key}" => 1);
			}

			foreach ($import_data as &$entry)
			{
				$entry['amount_investment'] = (int) str_replace(array(' ', ','),array('','.'),$entry['amount_investment']);
				$entry['amount_operation'] = (int) str_replace(array(' ', ','),array('','.'),$entry['amount_operation']);
				$entry['amount_potential_grants'] = (int) str_replace(array(' ', ','),array('','.'),$entry['amount_potential_grants']);
			}

			unset($entry);

			$origin_id = $GLOBALS['phpgw']->locations->get_id('property', '.project.condition_survey');
			foreach ($import_data as $entry)
			{

				if( ctype_digit($entry['condition_degree']) &&  $entry['condition_degree'] > 0 && $entry['building_part'] && (int)$entry['import_type'] > 0)
				{
					$request = array();

					if( $entry['amount_investment'] && !$entry['amount_operation'] )
					{
						if(isset($config->config_data['condition_survey_import_cat'][1]))
						{
							$request['cat_id'] = (int)$config->config_data['condition_survey_import_cat'][1];
						}
					}
					if( !$entry['amount_investment'] && $entry['amount_operation'] )
					{
						if(isset($config->config_data['condition_survey_import_cat'][2]))
						{
							$request['cat_id'] = (int)$config->config_data['condition_survey_import_cat'][2];
						}
					}
					else
					{
						if(isset($config->config_data['condition_survey_import_cat'][3]))
						{
							$request['cat_id'] = (int)$config->config_data['condition_survey_import_cat'][3];
						}
					}

					if(!isset($request['cat_id']) || !$request['cat_id'])
					{
						$request['cat_id'] = (int)$categories[0]['id'];
					}

					$this->_check_building_part($entry['building_part'],$_update_buildingpart);


					$request['condition_survey_id'] 	= $survey['id'];
					$request['street_name']				= $location_data['street_name'];
					$request['street_number']			= $location_data['street_number'];
					$request['location']				= $location;
					$request['location_code']			= $survey['location_code'];
					$request['origin_id']				= $origin_id;
					$request['origin_item_id']			= (int)$survey['id'];
					$request['title']					= substr($entry['title'], 0, 255);
					$request['descr']					= $entry['descr'];
					$request['building_part']			= $entry['building_part'];
					$request['coordinator']				= $survey['coordinator_id'];

					if($entry['import_type'] == 1)
					{
						$request['status']				= $config->config_data['condition_survey_hidden_status'];
					}
					else
					{
						$request['status']				= $config->config_data['condition_survey_initial_status'];					
					}

					$request['amount_investment']		= $entry['amount_investment'];
					$request['amount_operation']		= $entry['amount_operation'];
					$request['amount_potential_grants']	= $entry['amount_potential_grants'];

					$request['planning_value']			= $entry['amount'];
					$request['planning_date']			= mktime( 13,0,0,7,1, $entry['due_year'] ? (int) $entry['due_year'] : date('Y') );
					$request['recommended_year']		= $entry['due_year'] ? (int)$entry['due_year'] : date('Y');

					$request['responsible_unit']		= (int)$import_type_responsibility[$entry['import_type']];

					$request['condition']			= array
					(
						array
						(
							'degree' => $entry['condition_degree'],
							'condition_type' => $entry['condition_type'],
							'consequence' => $entry['consequence'],
							'probability' => $entry['probability']
						)
					);

//_debug_array($request);
					$sorequest->add($request, $values_attribute = array());
				}
			}

			$this->_db->transaction_commit();
		}

		private function _check_building_part($id, $_update_buildingpart)
		{
			$sql = "SELECT id FROM fm_building_part WHERE id = '{$id}'";
			$this->_db->query($sql,__LINE__,__FILE__);
			if(!$this->_db->next_record())
			{
				$sql = "INSERT INTO fm_building_part (id, descr) VALUES ('{$id}', '{$id}::__')";
				$this->_db->query($sql,__LINE__,__FILE__);
			}

			if($_update_buildingpart)
			{
				$value_set	= $this->_db->validate_update($_update_buildingpart);
				$this->_db->query("UPDATE fm_building_part SET {$value_set} WHERE id = '{$id}'",__LINE__,__FILE__);
			}
		}

		public function get_summation($id)
		{
			$table = 'fm_request';

			$condition_survey_id		= (int)$id;
			$sql = "SELECT category as cat_id, left(building_part,1) as building_part_,"
			. " sum(amount_investment) as investment ,sum(amount_operation) as operation,"
			. " recommended_year as year"
			." FROM {$table}"
			." WHERE condition_survey_id={$condition_survey_id}"
			." GROUP BY building_part_ ,category, year ORDER BY building_part_";

			$this->_db->query($sql,__LINE__,__FILE__);

			$values = array();
			while ($this->_db->next_record())
			{
				$amount = $this->_db->f('investment') + $this->_db->f('operation');
				
				$values[] = array
				(
					'building_part'		=> $this->_db->f('building_part_'),
					'amount'			=> $amount,
					'year'				=> $this->_db->f('year'),
					'cat_id'			=> $this->_db->f('cat_id'),
				);
			}

			return $values;
		}


		public function delete($id)
		{
			$id = (int) $id;
			$this->_db->transaction_begin();

			try
			{
				$this->_db->Exception_On_Error = true;
				$this->_db->query("DELETE FROM fm_condition_survey WHERE id={$id}",__LINE__,__FILE__);
				$this->_db->query("DELETE FROM fm_request WHERE condition_survey_id={$id}",__LINE__,__FILE__);
				$this->_db->Exception_On_Error = false;
			}

			catch(Exception $e)
			{
				if ( $e )
				{
					throw $e;
				}
			}

			$this->_db->transaction_commit();
		}
	}
