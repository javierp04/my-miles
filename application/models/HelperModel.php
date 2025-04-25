<?php
defined('BASEPATH') or exit('No direct script access allowed');

use GuzzleHttp\Client;

class HelperModel extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}

	public function getRol($rol_id)
	{
		return $this->db->get_where("rol", array("Rol_Id" => $rol_id))->row();
	}

	// public function getUserRequests($op_id) {

	// 	$query = "SELECT SUM(CASE WHEN LogType IN (1, 2) THEN 1 ELSE 0 END) AS Req_Day,
	// 				SUM(CASE WHEN LogType = 3 THEN 1 ELSE 0 END) AS Tax_Day
	// 				FROM debug_log where Op_Id = $op_id and Date(Datelog) = CURDATE()";
	// 	return $this->db->query($query)->row();
	// }

	public function getUserRequests($op_id)
	{

		$ret = $this->db->query("select TotalRequest as ReqSent from am_op_request where Op_Id = $op_id and Fecha = CURDATE()")->row();
		$req_sent = $ret->ReqSent;
		return intval($req_sent);
	}


	// public function getRequestsLeft($op_id, $rol_id) {		
	// 	$ret = $this->db->query("select count(1) as ReqSent from debug_log where Op_Id = $op_id and Date(Datelog) = CURDATE() and LogType < 3")->row();
	// 	$req_sent = $ret->ReqSent;
	// 	$total_req = $this->getRol($rol_id)->Req_Day;
	// 	$req_left = $total_req - $req_sent;
	// 	return $req_left;
	// }

	public function getRequestsLeft($op_id, $rol_id)
	{

		$req_sent = $this->getUserRequests($op_id);
		$total_req = $this->getRol($rol_id)->Req_Day;
		$req_left = $total_req - $req_sent;
		return $req_left;
	}

	public function setUserDisabled($op_id)
	{
		$nextDay = date("Y-m-d", strtotime("+1 day"));
		$this->db->update("operator", array("DisabledUntil" => $nextDay), array("Op_Id" => $op_id));
		return $nextDay;
	}

	public function getRandomAuth()
	{
		$a[0] = "NkbP8I7HPun42b9Th9l8Jjh4t0vlIaUm25xhsN5Ft0c7V0ZhEapMxx";
		$a[1] = "eJoyCV53ippu1CGXhpus190FLsVMnaGAzAmW4hETamid567qXMfTFR";
		$a[2] = "TK66ScuzGyaZaUmO7JavtYVa2TNFaGYyXnyIcqiIc0oCA67rqQUxfq";
		$a[3] = "KtBdBQC5LcGnmDej2jNUjJPixqW8IOva5pHtK9w3vht4NMGJ6K0ijL";
		$a[4] = "TmrIRtJI3uhaMnV8EdeP52TF4igCoH94ywfK3Y8765TmaAbYZObS0M";
		$a[5] = "Tg4W0JckAhPcnXx62Zw6ASADQgX4so5S2oOLfWhEUOuKtusyAtKgGz";
		$a[6] = "lw9orb0kiur8Yhunkb4y7SNYz25C2iamK5awiF7eWgQI6jK17jqbT9";
		$a[7] = "scYQPcDRbOZQ6zK4gbl41UA064wg5RzUNSjunnkbzqRXluaZ1xOpng";
		return $a[rand(0, 7)];
	}

	public function listRegions()
	{
		$this->db->order_by("Region_Name");
		return $this->db->get("am_regions")->result();
	}

	public function listAirportsByRegion($region)
	{
		$this->db->from("am_codes");
		$this->db->join("am_region_air", "am_region_air.Air_Code = am_codes.Air_Code");
		$this->db->where("am_region_air.Region_Id = '{$region}'");
		return $this->db->get()->result();
	}

	public function listAllAirports()
	{
		$this->db->order_by("Country, Air_Code");
		return $this->db->get("am_codes")->result();
	}

	public function searchSmilesTax($op_id, $uid, $fuid)
	{
		session_write_close();
		//MULTI IP		
		$n = rand(0, 4);		
		if ($n == 0) {
			return $this->do_searchSmilesTax($uid, $fuid);
		} else {
			return $this->do_proxySmilesTax($n, $uid, $fuid);
		}
	}

	private function do_proxySmilesTax($n, $uid, $fuid)
	{
		//esto se usa para ejecutar localmente llamando a servidores distintos y poder tener request paralelos
		$url = "smiles{$n}.local/ProxyAPI/taxSearch/{$uid}/{$fuid}";
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$json = curl_exec($curl);
		curl_close($curl);

		$result = json_decode($json);
		if (isset($result->log)) {
			$log = $result->log;
			$this->logAPICall($log->results, $log->orig, $log->dest, $log->fecha, $log->type, $log->sender, $log->res);
		}
		if (isset($result->error)) {
			$er = $result->error;
			$this->logError($er->type, $er->code, $er->input, $er->errorType, $er->sender, $er->response);
		}
		return $result->tax;
	}

	private function do_searchSmilesTax($uid, $fuid)
	{			
		$sender = $_SERVER["HTTP_HOST"]; //LOG PURPOSES

		$url = "https://api-airlines-boarding-tax-prd.smiles.com.br/v1/airlines/flight/boardingtax?adults=1&children=0&infants=0&fareuid={$fuid}&uid={$uid}&type=SEGMENT_1&highlightText=SMILES_CLUB";
		$headers = [			
			'accept-language: es-ES,es;q=0.9',			
			'referer: https://www.smiles.com.ar/',
			'region: ARGENTINA',
			'sec-ch-ua: "Google Chrome";v="135", "Not-A.Brand";v="8", "Chromium";v="135"',			
			'sec-ch-ua-platform: "Windows"',			
			'sec-fetch-mode: cors',			
			'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36',
			'x-api-key: aJqPU7xNHl9qN3NVZnPaJ208aPo2Bh2p2ZV844tw'
		];

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$content = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		$response = json_decode($content);

		$tax = new stdClass();
		$tax->fareuid = $fuid;
		$tax->total = -1;
		if (isset($response->totals->total->money)) {
			$tax->total = $response->totals->total->money;
			$res = "Ok";
		} else {
			$res = "Error";
			$this->logError("TAX API", $tax->total, $uid, $status, $sender, $content);
			//$this->logError("TAX API", $tax->total, $uid, $status, $sender, print_r($response, true));
		}

		$this->logAPICall($tax, $uid, $fuid, "Uid-FareUid", 3, $sender, $res);
		return $tax;
	}

	public function logError($type, $code, $input, $ex, $sender, $response)
	{
		$e = new stdClass();
		$e->Op_Id = $this->session->userdata("Op_Id");

		session_write_close();

		$e->Type = $type;
		$e->Code = $code;

		$e->Input = $input;
		$e->Exception = $ex;
		$e->Sender = $sender;
		$e->Response = $response;
		$e->IP = $_SERVER['REMOTE_ADDR'];
		$this->db->insert("debug_error", $e);
	}

	public function saveOpError($ts, $er)
	{
		$e = new stdClass();
		$e->Op_Id = $this->session->userdata("Op_Id");

		session_write_close();

		$e->TextStatus = $ts;
		$e->ErrorThrown = $er;
		$e->IP = $_SERVER['REMOTE_ADDR'];
		$this->db->insert("operator_error", $e);
	}

	public function searchSmilesAPI($op_id, $orig, $dest, $fecha)
	{		
		//MULTI IP
		$n = $this->getNextInstance($op_id);		
		
		session_write_close();
		if ($n == 0) {
			return $this->do_searchSmilesAPI($orig, $dest, $fecha);
		} else {
			return $this->do_proxySmilesAPI($n, $orig, $dest, $fecha);
		}
	}

	private function do_proxySmilesAPI($n, $orig, $dest, $fecha)
	{
		//Esto esta en smiles-req/application/controllers
		//Se usa para ejecutar localmente llamando a servidores distintos y poder tener request paralelos		
		$url = "smiles{$n}.local/ProxyAPI/flightSearch/{$orig}/{$dest}/{$fecha}";
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$json = curl_exec($curl);
		curl_close($curl);

		$result = json_decode($json);
		if (isset($result->log)) {
			$log = $result->log;
			$this->logAPICall($log->results, $log->orig, $log->dest, $log->fecha, $log->type, $log->sender, $log->res);
		}
		if (isset($result->error)) {
			$er = $result->error;
			$this->logError($er->type, $er->code, $er->input, $er->errorType, $er->sender, $er->response);
		}
		return $result->flights;
	}

	public function do_searchSmilesAPI($orig, $dest, $fecha)
	{		
		$sender = $_SERVER["HTTP_HOST"]; //LOG PURPOSES
		$url = "https://api-air-flightsearch-green.smiles.com.br/v1/airlines/search?adults=1&cabinType=all&children=0&currencyCode=ARS&departureDate={$fecha}" . 
				"&destinationAirportCode={$dest}&infants=0&isFlexibleDateChecked=false&originAirportCode={$orig}&tripType=2&forceCongener=true&r=ar";
		
		$headers = [			
			'accept-language: es-ES,es;q=0.9',			
			'referer: https://www.smiles.com.ar/',
			'region: ARGENTINA',
			'sec-ch-ua: "Google Chrome";v="135", "Not-A.Brand";v="8", "Chromium";v="135"',			
			'sec-ch-ua-platform: "Windows"',			
			'sec-fetch-mode: cors',			
			'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36',
			'x-api-key: aJqPU7xNHl9qN3NVZnPaJ208aPo2Bh2p2ZV844tw'
		];

		$ch = curl_init();
		curl_setopt_array($ch, [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => $headers,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => 0
		]);

		$response = curl_exec($ch);
		$res = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$error = curl_error($ch);

		curl_close($ch);

		header('Content-Type: application/json');

		if ($res == 200) {
			$result = json_decode($response);
			$ret = $result->requestedFlightSegmentList[0]->flightList;			
		} else {			
			$ret = new stdClass();
			$ret->error = $res;
			$ret->response = $response . "\n" . $error;	
			
			$errorType = "HTTP_CODE: {$res}";
			$this->logError("SEARCH API", $ret->error, "{$fecha}: {$orig}-{$dest}", $errorType, $sender, $ret->response);
		}
		$this->logAPICall($ret, $orig, $dest, $fecha, 2, $sender, $res);
		return $ret;
	}

	public function logAPICall($results, $orig, $dest, $fecha, $type, $sender, $res)
	{
		$op_id = $this->session->userdata("Op_Id");

		session_write_close();

		$logObj = new stdClass();
		$api_call = "TAX API";
		switch ($type) {
			case 1:
				$api_call = "SEARCH API CLIENT";
				break;
			case 2:
				$api_call = "SEARCH API SERVER";
		}
		$logObj->Op_Id = $op_id;
		$logObj->LogType = $type;
		$logObj->ApiCall = $api_call;
		$logObj->Info = "{$fecha}: {$orig}-{$dest}";
		$logObj->Res = $res;
		$logObj->Sender = $sender;
		$logObj->IP = $_SERVER['REMOTE_ADDR'];
		//Log SMILES
		if ($type == 3) {
			$logObj->Results = isset($results->total) ? $results->total : "error";
		} else {
			//$logObj->Results = $results->error ? -1 : count($results);
			$logObj->Results = $results->error ? -1 : print_r($results, true);
		}
		$this->db->insert("debug_log", $logObj);
	}

	public function getNextInstance($op_id)
	{
		$query = "INSERT INTO am_op_instance (Op_Id, Curr_Inst) VALUES ({$op_id}, 0) ON DUPLICATE KEY UPDATE Curr_Inst = IF(Curr_Inst = 4, 0, Curr_Inst + 1)";
		$this->db->query($query);
		$ret = $this->db->get_where("am_op_instance", array("Op_Id" => $op_id))->row();
		return $ret->Curr_Inst;
	}
}
