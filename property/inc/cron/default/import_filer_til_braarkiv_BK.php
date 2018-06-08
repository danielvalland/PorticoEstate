<?php
	/**
	 * phpGroupWare - property: a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @subpackage cron
	 * @version $Id$
	 */
	/**
	 * Description
	 * example cron : /usr/local/bin/php -q /var/www/html/phpgroupware/property/inc/cron/cron.php default import_filer_til_braarkiv_BK
	 * @package property
	 */
	include_class('property', 'cron_parent', 'inc/cron/');
	/**
	 * Load autoload
	 */
	require_once PHPGW_API_INC . '/soap_client/bra5/Bra5Autoload.php';

	use PhpOffice\PhpSpreadsheet\Spreadsheet;

	class import_filer_til_braarkiv_BK extends property_cron_parent
	{

		protected $config,
			$secKey,
			$fields;

		public function __construct()
		{
			parent::__construct();

			$this->function_name = get_class($this);
			$this->sub_location = lang('property');
			$this->function_msg = 'Importer filer til braarkiv';
			$this->db = & $GLOBALS['phpgw']->db;
			$this->join = & $this->db->join;

			$location_id = $GLOBALS['phpgw']->locations->get_id('admin', 'vfs_filedata');

			$c = CreateObject('admin.soconfig', $location_id);

			$section = 'braArkiv';
			$location_url = $c->config_data[$section]['location_url'];//'http://braarkiv.adm.bgo/service/services.asmx';
			$braarkiv_user = $c->config_data[$section]['braarkiv_user'];
			$braarkiv_pass = $c->config_data[$section]['braarkiv_pass'];
			$this->config = $c;

			if (!isset($c->config_data) || !$c->config_data)
			{
				$this->init_config();
			}

			if (!$location_url || !$braarkiv_pass || !$braarkiv_pass)
			{
				throw new Exception('braArkiv is not configured');
			}

			$wdsl = "{$location_url}?WSDL";
			$options = array();

			$options[Bra5WsdlClass::WSDL_URL] = $wdsl;
			$options[Bra5WsdlClass::WSDL_ENCODING] = 'UTF-8';
			$options[Bra5WsdlClass::WSDL_TRACE] = false;
			$options[Bra5WsdlClass::WSDL_SOAP_VERSION] = SOAP_1_2;

			try
			{
				$wsdlObject = new Bra5WsdlClass($options);
			}
			catch (Exception $e)
			{
				if ($e)
				{
					phpgwapi_cache::message_set($e->getMessage(), 'error');
					return false;
				}
			}

//
//			$bra5ServiceLogin = new Bra5ServiceLogin();
//			if ($bra5ServiceLogin->Login(new Bra5StructLogin($braarkiv_user, $braarkiv_pass)))
//			{
//				$this->secKey = $bra5ServiceLogin->getResult()->getLoginResult()->LoginResult;
//			}
//			else
//			{
//				throw new Exception('vfs_fileoperation_braArkiv::Login failed');
//			}
		}

		private function init_config()
		{
			$receipt_section = $this->config->add_section(array
				(
				'name' => 'braArkiv',
				'descr' => 'braArkiv'
				)
			);

			$receipt = $this->config->add_attrib(array
				(
				'section_id' => $receipt_section['section_id'],
				'input_type' => 'text',
				'name' => 'location_url',
				'descr' => 'Location url'
				)
			);
			$receipt = $this->config->add_attrib(array
				(
				'section_id' => $receipt_section['section_id'],
				'input_type' => 'text',
				'name' => 'braarkiv_user',
				'descr' => 'braArkiv user'
				)
			);

			$receipt = $this->config->add_attrib(array
				(
				'section_id' => $receipt_section['section_id'],
				'input_type' => 'password',
				'name' => 'braarkiv_pass',
				'descr' => 'braArkiv password'
				)
			);

			$receipt = $this->config->add_attrib(array
				(
				'section_id' => $receipt_section['section_id'],
				'input_type' => 'text',
				'name' => 'pickup_catalog',
				'descr' => 'Pickup catalog for files'
				)
			);

			$GLOBALS['phpgw']->redirect_link('/index.php', array(
				'menuaction' => 'admin.uiconfig2.list_attrib',
				'section_id' => $receipt_section['section_id'],
				'location_id' => $GLOBALS['phpgw']->locations->get_id('admin', 'vfs_filedata')
				)
			);
		}

		function execute()
		{
			$start = time();


			$files = $this->get_files();

			foreach ($files as $file_info)
			{
				$this->process_file($file_info);
			}

			$msg = 'Tidsbruk: ' . (time() - $start) . ' sekunder';
			$this->cron_log($msg, $cron);
			echo "$msg\n";
			$this->receipt['message'][] = array('msg' => $msg);
		}

		function cron_log( $receipt = '' )
		{

			$insert_values = array(
				$this->cron,
				date($this->db->datetime_format()),
				$this->function_name,
				$receipt
			);

			$insert_values = $this->db->validate_insert($insert_values);

			$sql = "INSERT INTO fm_cron_log (cron,cron_date,process,message) "
				. "VALUES ($insert_values)";
			$this->db->query($sql, __LINE__, __FILE__);
		}

		function get_files( )
		{
			$path = rtrim($this->config->config_data['braArkiv']['pickup_catalog'], '/');

			$data = $this->get_data($path);
			$files = array();

			foreach ($data as $entry)
			{
				$matrikkel_info = explode('/', $entry[0]);

				$files[] = array(
					'gnr'					=> $matrikkel_info[0],
					'bnr'					=> $matrikkel_info[1],
					'Lokasjonskode'			=> $entry[2],
					'byggNummer'			=> $entry[1],
					'file'					=> "{$path}/{$entry[7]}",//filnavn inkl fil-sti
					'fileDokumentTittel'	=> basename($entry[7]),
					'fileKategorier'		=> $entry[6],
					'fileBygningsdeler'		=> $entry[5] ? "{$entry[4]};$entry[5]" : $entry[4],
					'fileFag'				=> $entry[3]
				);
			}

			if($this->debug)
			{
				_debug_array($files);
				return array();
			}
			return $files;
		}
		/*
		 * Leses fra regneark
		 */

		function get_data($path )
		{
			phpgw::import_class('phpgwapi.phpspreadsheet');

			$accepted_file_formats = array('xls', 'xlsx', 'ods', 'csv');

			$dir_handle = opendir($path);
			while ($file = readdir($dir_handle))
			{
				if ((substr($file, 0, 1) != '.') && is_file("{$path}/{$file}"))
				{

					$extension = pathinfo($file, PATHINFO_EXTENSION);
					$lock =  basename($file,$extension) . 'lck' ;

					if(is_file("{$path}/{$lock}"))
					{
						$this->receipt['error'][] = array('msg' => "'{$path}/{$file}' er allerede behandlet");
						return array();
					}

					if(in_array($extension, $accepted_file_formats) && !is_file("{$path}/{$lock}"))
					{
						touch("{$path}/{$lock}");
						$input_file = "{$path}/{$file}";
						break;
					}
				}
			}
			closedir($dir_handle);

			if(!$input_file)
			{
				return array();
			}

			/** Load $inputFileName to a Spreadsheet Object  **/
			$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($input_file);


			$spreadsheet->setActiveSheetIndex(0);

			$result = array();

			$highestColumm = $spreadsheet->getActiveSheet()->getHighestDataColumn();

			$highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumm);

			$rows = $spreadsheet->getActiveSheet()->getHighestDataRow();

			$first_cell_value = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($highestColumnIndex, 1)->getCalculatedValue();

			$start =  $first_cell_value ? 1 : 2; // Read the first line to get the headers out of the way

			for ($j = 1; $j < $highestColumnIndex +1; $j++)
			{
				$this->fields[] = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($j, $start)->getCalculatedValue();
			}

			$start++; // first data line

			$rows = $rows ? $rows + 1 : 0;
			for ($row = $start; $row < $rows; $row++)
			{
				$_result = array();

				for ($j = 1; $j <= $highestColumnIndex; $j++)
				{
					$value = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($j, $row)->getCalculatedValue();
					$_result[] = $value;
				}

				if($value)
				{
					$result[] = $_result;
				}
			}

			$this->receipt['message'][] = array('msg' => "'{$path}' contained " . count($result) . " lines");

			return $result;
		}

		/**
		 *
		 * @param type $file
		 */
		function process_file( $file_info )
		{
			$gnr = $file_info['gnr'];
			$bnr = $file_info['bnr'];
			$byggNummer = $file_info['byggNummer'];
			$lokasjonskode = $file_info['Lokasjonskode'];
			$file = $file_info['file'];
			$fileDokumentTittel = $file_info['fileDokumentTittel'];
			$fileKategorier = $file_info['fileKategorier'];
			$fileBygningsdeler = $file_info['fileBygningsdeler'];
			$fileFag = $file_info['fileFag'];

			try
			{
				$result = $this->uploadFile($gnr, $bnr, $byggNummer, $file, $fileDokumentTittel, $fileKategorier, $fileBygningsdeler, $fileFag, $lokasjonskode);
			}
			catch (Exception $e)
			{
				echo $e->getTraceAsString();
				$this->receipt['error'][] = array('msg' => $e->getMessage());
			}
		}

		function uploadFile( $gnr, $bnr, $byggNummer, $file, $DokumentTittel, $kategorier, $bygningsdeler, $fag, $lokasjonskode )
		{

			$accepted_file_formats = array('dwg', 'dxf', 'jpg', 'doc', 'docx', 'xls', 'xlsx',
				'pdf', 'tif', 'gif');
			$extension = pathinfo($file, PATHINFO_EXTENSION);

			if ($extension == null || $extension == "" || !in_array($extension, $accepted_file_formats))
			{
				throw new Exception("Fileformat not accepted: {$extension}");
			}

			$document = $this->setupDocument($gnr, $bnr, $byggNummer, $DokumentTittel, $kategorier, $bygningsdeler, $fag, $lokasjonskode);

			$bra5ServiceCreate = new Bra5ServiceCreate();
			$bra5ServiceCreateDocument = new Bra5StructCreateDocument($_assignDocKey = false, $this->secKey, $document);
//			_debug_array($bra5ServiceCreateDocument);
//			die();
			if ($bra5ServiceCreate->createDocument($bra5ServiceCreateDocument))
			{
//				_debug_array($bra5ServiceCreate->getResult());
			}
			else
			{
		//		_debug_array($bra5ServiceCreate->getLastError());

				throw new Exception($bra5ServiceCreate->getLastError());
			}

			$document_id = $bra5ServiceCreate->getResult()->getCreateDocumentResult()->getcreateDocumentResult()->ID;

			if (!$document_id)
			{
				return false;
			}

			return $this->write($file, $document_id);
		}

		/**
		 * 	Initierer en ny overføring.
		 * @param type $file
		 * @param type $document_id
		 * @return boolean true on success
		 */
		public function write( $file, $document_id = 0 )
		{
			$filename = basename($file);
			$content = file_get_contents($file);

			$bra5ServiceFile = new Bra5ServiceFile();
			if ($bra5ServiceFile->fileTransferSendChunkedInit(new Bra5StructFileTransferSendChunkedInit($this->secKey, $document_id, $filename)))
			{
				$transaction_id = $bra5ServiceFile->getResult()->getfileTransferSendChunkedInitResult()->fileTransferSendChunkedInitResult;
			}
			else
			{
				_debug_array($bra5ServiceFile->getLastError());
				die();
			}

			$new_string = chunk_split(base64_encode($content), 1048576);// Definerer en bufferstørrelse/pakkestørrelse på ca 1mb.

			$content_arr = explode('\r\n', $new_string);

			foreach ($content_arr as $content_part)
			{
				$bra5ServiceFile->fileTransferSendChunk(new Bra5StructFileTransferSendChunk($this->secKey, $transaction_id, $content_part));
			}

			$ok = !!$bra5ServiceFile->fileTransferSendChunkedEnd(new Bra5StructFileTransferSendChunkedEnd($this->secKey, $transaction_id));
			/*
			  _debug_array($bra5ServiceFile->getResult());
			 */
//			die();

			if (!$ok)
			{
				_debug_array($bra5ServiceFile->getLastError());
			}

//	_debug_array($document_id);
			return $ok;
		}

		private function setupDocument( $gnr, $bnr, $byggNummer, $dokumentTittel, $kategorier, $bygningsdeler, $fag, $lokasjonskode )
		{

			/*
				* @param boolean $_bFDoubleSided
				* @param boolean $_bFSeparateKeySheet
				* @param boolean $_classified
				* @param int $_priority
				* @param int $_productionLineID
				* @param int $_docSplitTypeID
			*/

			$doc = new Bra5StructDocument(false, false, false, 0, 0, 1, 0);
			$attribs = new Bra5StructArrayOfAttribute();

			$asta = new AttributeFactory(Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVSTRING);
			$asta->setName("ASTA");
			$asta->setStringValue("bkbygg/[saknr]");
			$attribs->add($asta->build());

			$lokasjonskode = new AttributeFactory(Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVSTRING);
			$lokasjonskode->setName("Lokasjonskode");
			$lokasjonskode->setStringValue($byggNummer);
			$attribs->add($lokasjonskode->build());

			$byggnavn = new AttributeFactory(Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVSTRING);
			$byggnavn->setName("Byggnavn");
			$byggnavn->setStringValue("Bergen rådhus");
			$attribs->add($byggnavn->build());

			$matrikkel = new AttributeFactory(Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVMATRIKKEL);
			$matrikkel->setName("GNR/BNR");
			$matrikkel->setMatrikkelValue($gnr, $bnr, 0, 0);
			$attribs->add($matrikkel->build());

			$bygningsnummer = new AttributeFactory(Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVSTRING);
			$bygningsnummer->setName("Bygningsnummer");
			$bygningsnummer->setStringValue($byggNummer);
			$attribs->add($bygningsnummer->build());

			$innhold = new AttributeFactory(Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVSTRING);
			$innhold->setName("Innhold");
			$innhold->setStringValue($dokumentTittel);
			$attribs->add($innhold->build());

			$now = date('Y-m-d');
			$dato = new AttributeFactory(Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVDATE);
			$dato->setName("Dato");
			$dato->setDateValue($now);
			$attribs->add($dato->build());

			$dokumentkategorier = new AttributeFactory(Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVSTRING);
			$dokumentkategorier->setName("Dokumentkategorier");
			$dokumentkategorier->setStringArrayValue(explode(";", $kategorier));
			$dokumentkategorier->build();
			$attribs->add($dokumentkategorier->build());

			$fagAttrib = new AttributeFactory(Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVSTRING);
			$fagAttrib->setName("Fag/Tema");
			$fagAttrib->setStringArrayValue(explode(";", $fag));
			$attribs->add($fagAttrib->build());

			$bygningsdelAttrib = new AttributeFactory(Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVSTRING);
			$bygningsdelAttrib->setName("Bygningsdel");
			$bygningsdelAttrib->setStringArrayValue(explode(";", $bygningsdeler));
			$attribs->add($bygningsdelAttrib->build());

			$merknad = new AttributeFactory(Bra5EnumBraArkivAttributeType::VALUE_BRAARKIVSTRING);
			$merknad->setName("Merknad");
			$merknad->setStringValue("[BkBygg bestillings tittel]");
			$attribs->add($merknad->build());

			$doc->setAttributes($attribs);

			$baseclassname = "Etat for bygg og eiendom";
			$classname = "FDV";
			// TODO FIXME  - Make baseclass and classname config options
			$doc->setBaseClassName($baseclassname);
			$doc->setClassName($classname);
			$doc->setClassified(false);

			return $doc;
		}
	}

	class AttributeFactory
	{

		private $attribute;

		function __construct($_attribType)
		{
			$this->attribute = new Bra5StructAttribute($_usesLookupValues = false,$_attribType);
		}

		public function setType( $type )
		{
			$this->attribute->setAttribType($type);
			return $this;
		}

		public function setName( $attributeName )
		{
			$this->attribute->setName($attributeName);
			return $this;
		}

		public function setStringValue( $value )
		{
			$values = array($value);
			$this->setStringArrayValue($values);
			return $this;
		}

		public function setStringArrayValue( $values )
		{
			$attributeValue = new Bra5StructArrayOfAnyType();

			foreach ($values as $value)
			{
				$attributeValue->add($value);
			}

			$this->attribute->setValue($attributeValue);
			return $this;
		}

		public function setMatrikkelValue( $gnr, $bnr, $fnr, $snr )
		{
			$gnrBnrValue = new Bra5StructArrayOfAnyType();
			$matrikkel = new Bra5StructMatrikkel();

			$matrikkel->setGNr((int)$gnr);
			$matrikkel->setBNr((int)$bnr);
			$matrikkel->setFNr((int)$fnr);
			$matrikkel->setSNr((int)$snr);

			$gnrBnrValue->add($matrikkel);
			$this->attribute->setValue($gnrBnrValue);
			return $this;
		}

		public function setDateValue( $date )
		{
			$datoValue = new Bra5StructArrayOfAnyType();
			$datoValue->add($date);
			$this->attribute->setValue($datoValue);
			return $this;
		}

		public function build()
		{
			return $this->attribute;
		}
	}