<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'controllers/Common.php';

/** 
 * @property CI_DB_mysqli_driver $db 
 * @property CI_Loader $load
 * @property CI_Input $input
 * @property CI_Config $config
 * @property CI_Session $session 
 * @property SmilesModel $SmilesModel
 * @property OperatorModel $OperatorModel
 * @property HelperModel $HelperModel
 */

class Smiles extends Common
{
	function __construct()
	{
		parent::__construct();
		$op = $this->session->userdata();
		$is_ajax = $this->input->post("is_ajax");
		if ($op["Op_Id"] > 0) {
			//Busca el rol
			$rol = $this->HelperModel->getRol($op["Rol_Id"]);
			if ($rol->Status_Id == 0) {
				//Si rol deshabilitado				
				redirect(base_url() . "start/rol_disabled");
			} else { //Si els rol esta ok
				$this->op_id = $op["Op_Id"];
				//Si el rol est apto búsqueda
				if (!$rol->CanSearch) {
					if ($is_ajax) {
						header('Content-Type: application/json');
						echo json_encode(["nosession" => 2]);
						exit;
					} else {
						redirect(base_url() . "view");
					}
				}
				$this->rol_id = $rol->Rol_Id;
			}
		} else if ($is_ajax) {
			header('Content-Type: application/json');
			echo json_encode(["nosession" => 1]);
			exit;
		} else {
			redirect(base_url() . "start/not_validated");
		}
	}

	public function index()
	{
		$use_server = $this->session->userdata("UseServer");
		$mile_price = $this->session->userdata("mile_price");

		session_write_close();

		$rol =  $this->HelperModel->getRol($this->rol_id);

		$data = array();
		$data["user_request_count"] = $this->HelperModel->getUserRequests($this->op_id) ?? 0;
		$data["total_requests"] = $rol->Req_Day;
		$data["regions"] = $this->HelperModel->listRegions();
		$data["locals"] = $this->HelperModel->listAirportsByRegion("ARGENTINA");
		$data["all"] = $this->HelperModel->listAllAirports();
		$data["use_server"] = $use_server;
		$data["inc"] = $use_server ? 2 : 1;
		$data["rol_id"] = $this->rol_id;
		$data["auth"] = $this->HelperModel->getRandomAuth();
		$data["authTax"] = $this->HelperModel->getRandomAuth();

		$chkI = array();
		$chkV = array();

		$my_search = $this->input->get("s");

		//REVISAR ESTO (POPULA LOS COMBOS Y DEMAS)
		if ($my_search > 0) {
			$search = $this->SmilesModel->getSearch("SEARCH", $my_search, $mile_price, $this->rol_id, $this->op_id);
			$origenes = array();
			$destinos = array();

			if ($search != null) {
				$fdesde = $this->input->get("f");
				$fhasta = $this->input->get("h");
				$data["fdesde_sel"] = $fdesde != null ? date("Y-m-d", strtotime($fdesde)) : "";
				$data["fhasta_sel"] = $fhasta != null ? date("Y-m-d", strtotime($fhasta)) : "";
				foreach ($search->Idas as $ida) {
					$origenes[$ida->Orig] = true;
					$destinos[$ida->Dest] = true;
					$chkI[] = "chkI" . $ida->Orig . "-" . $ida->Dest;
				}
				foreach ($search->Vueltas as $vuelta) {
					$origenes[$vuelta->Dest] = true;
					$destinos[$vuelta->Orig] = true;
					$chkV[] = "chkV" . $vuelta->Orig . "-" . $vuelta->Dest;
				}

				$data["from_search"] = true;
				$data["origenes"] = array_keys($origenes);
				$data["destinos"] = array_keys($destinos);

				$data["chkI"] = implode(",", $chkI);
				$data["chkV"] = implode(",", $chkV);
			} else {

				redirect(base_url() . "smiles/my_search");
			}
		}
		$this->load->view('smiles', $data);
	}

	public function my_search()
	{
		$data = array();
		$data["rol_id"] = $this->rol_id;
		$rol =  $this->HelperModel->getRol($this->rol_id);
		$data["user_request_count"] = $this->HelperModel->getUserRequests($this->op_id) ?? 0;
		$data["total_requests"] = $rol->Req_Day;
		$this->load->view('my_search', $data);
	}

	public function favorites()
	{
		if ($this->rol_id == 8) { //VIEW ONLY ROL
			exit;
		}
		$rol =  $this->HelperModel->getRol($this->rol_id);
		$data = array();
		$data["user_request_count"] = $this->HelperModel->getUserRequests($this->op_id) ?? 0;
		$data["total_requests"] = $rol->Req_Day;
		$this->load->view('favorites', $data);
	}

	public function ax_list_searches()
	{
		session_write_close();
		$orig = $this->input->post("hdnOrig");
		$dest = $this->input->post("hdnDest");
		$chkI = $this->input->post("hdnchkI");
		$chkV = $this->input->post("hdnchkV");
		$desde = $this->input->post("fDesde");
		$hasta = $this->input->post("fHasta");
		$force = $this->input->post("force");
		$fdesde = date("Y-m-d", strtotime($desde));
		$fhasta = date("Y-m-d", strtotime($hasta));
		$has_ret = $this->input->post("chkVuelta") !== null;
		$ways = $this->getWaysFromOrigsDests($orig, $dest, $has_ret);

		//WAYS IS PASSED BY REFERENCE AND CANT IS ADDED
		if ($this->rol_id != 1) {
			$days_valid = $this->SmilesModel->getSearchResponseDifference($this->op_id, $ways, $fdesde, $fhasta);
		}
		$days_upd = $this->SmilesModel->getDaysToUpdate($ways, $fdesde, $fhasta, $force);

		$json_data = new stdClass();
		$json_data->ways = $ways;
		$json_data->daysToUpdate = $days_upd;
		$data = array();
		$data["desde"] = $desde;
		$data["hasta"] = $hasta;
		$data["days"] = $days_upd;
		$data["ways"] = $ways;
		$data["has_ret"] = $has_ret;
		$data["chkI"] = $chkI;
		$data["chkV"] = $chkV;
		$data["rol_id"] = $this->rol_id;
		$data["departs"] = $this->OperatorModel->getUserDeparts($this->op_id);
		//$data["cant_search"] = count($days_upd) + count($days_valid) / 2;
		$data["cant_search"] = array_sum(array_column($ways, "cant"));
		$data["req_left"] = $this->HelperModel->getRequestsLeft($this->op_id, $this->rol_id);
		$html = $this->load->view('ax_sm_search_preview', $data, true);
		$jsonResponse = json_encode(["html" => $html, "json" => $json_data]);

		header('Content-Type: application/json');
		echo $jsonResponse;
	}

	public function ax_list_my_searches()
	{
		$mile_price = $this->session->userdata("mile_price");

		session_write_close();

		$searches = $this->SmilesModel->listOperatorSearches($mile_price, $this->rol_id, $this->op_id);
		$data = array();
		$data["rol_id"] = $this->rol_id;
		$data["searches"] = $searches;
		$html = $this->load->view('ax_sm_my_history', $data, true);
		$jsonResponse = json_encode(["html" => $html]);
		header('Content-Type: application/json');
		echo $jsonResponse;
		exit;
	}

	public function ax_list_favs()
	{
		$data = array();
		$data["mile_price"] = $this->session->userdata("mile_price");

		session_write_close();

		$data["favs"] = $this->SmilesModel->getFavorites($this->op_id);
		$data["int_hours"] = $this->SmilesModel->getConfig($this->config->item("id_interval"));
		$html = $this->load->view('ax_sm_list_favs', $data, true);
		$jsonResponse = json_encode(["html" => $html]);
		header('Content-Type: application/x-json; charset=utf-8');
		echo $jsonResponse;
	}

	public function ax_fav_flight($id)
	{
		$ret = $this->SmilesModel->toogleFavFlight($this->op_id, $id);
		$response["fav"] = $ret;
		header('Content-Type: application/x-json; charset=utf-8');
		echo json_encode($response);
		exit;
	}

	public function ax_del_search_response()
	{
		session_write_close();

		header('Content-Type: application/x-json; charset=utf-8');

		if ($this->rol_id == 1) {
			$idas = $this->input->post("idas") ?? array();
			$vueltas = $this->input->post("vueltas") ?? array();
			$fdesde = date("Y-m-d", strtotime($this->input->post("fdesde")));
			$fhasta = date("Y-m-d", strtotime($this->input->post("fhasta")));
			$ways = array_merge($idas, $vueltas);
			$this->SmilesModel->deleteSearchResponse($ways, $fdesde, $fhasta);
			echo '{"delete" : "ok"}';
		} else {
			echo '{"delete" : "error"}';
		}
	}

	public function ax_client_search()
	{
		$idas = $this->input->post("idas") ?? array();
		$vueltas = $this->input->post("vueltas") ?? array();
		$fdesde = date("Y-m-d", strtotime($this->input->post("fdesde")));
		$dialog =  $this->input->post("dialog");
		if ($dialog) {
			$fhasta = $this->SmilesModel->getFechaHastaForDialog($idas, $fdesde);
		} else {
			$fhasta = date("Y-m-d", strtotime($this->input->post("fhasta")));
		}
		$ways = array_merge($idas, $vueltas);
		$days_upd = $this->SmilesModel->getDaysToUpdate($ways, $fdesde, $fhasta);
		header('Content-Type: application/x-json; charset=utf-8');
		if (count($days_upd) > 0) {
			$ts = new stdClass();
			$ts->search = $days_upd[0];
			$ts->progress = count($days_upd);
			$src = $ts->search;
			$this->SmilesModel->insertResponse($this->op_id, $src->Orig, $src->Dest, $src->Fecha, 0);
			echo json_encode($ts);
		} else {
			echo '{"status" : "finished"}';
		}
		session_write_close();
	}

	public function ax_days_to_update()
	{
		session_write_close(); //VER
		//pasar fdesde y fhasta		
		$idas = $this->input->post("idas") ?? array();
		$vueltas = $this->input->post("vueltas") ?? array();
		$fdesde = date("Y-m-d", strtotime($this->input->post("fdesde")));

		$dialog =  $this->input->post("dialog");
		if ($dialog) {
			$fhasta = $this->SmilesModel->getFechaHastaForDialog($idas, $fdesde);
		} else {
			$fhasta = date("Y-m-d", strtotime($this->input->post("fhasta")));
		}
		$search_id = 0;
		$ways = array_merge($idas, $vueltas);
		if (!$dialog) {
			$search_id = $this->SmilesModel->saveOpSearch($this->op_id, $idas, $vueltas);
		}

		$days_valid = array();
		$valid_req = 0;
		if (sizeof($ways) > 0) {
			$this->SmilesModel->resetNotLoadedTaxes($ways);
			if ($this->rol_id != 1) {
				$days_valid = $this->SmilesModel->getSearchResponseDifference($this->op_id, $ways, $fdesde, $fhasta);
				$valid_req = array_sum(array_column($ways, 'cant'));
			}

			$days_upd = $this->SmilesModel->getDaysToUpdate($ways, $fdesde, $fhasta);
			if ($dialog) {
				$days_upd = array_slice($days_upd, 0, 14);
			}
		} else {
			$days_upd = array();
		}
		$reqLeft = $this->HelperModel->getRequestsLeft($this->op_id, $this->rol_id);

		$reqNeeded =  array_sum(array_column($ways, 'cant'));


		//HACER UNA DEVOLUCION ACA PARA MOSTRAR EL CUADRO DE DIALOGO DE CREDITOS.
		//MODIFICAR EL .JS PARA QUE EL CUADRO DE DIALOGO APAREZCA POR ACA Y NO POR DO_SEARCHFLIGHTS (TOTALSEARCH > 0)
		$result["req_left"] = $reqLeft;
		$result["req_need"] = $reqNeeded;
		$result["search_id"] = $search_id;
		if ($reqNeeded > $reqLeft) {
			$result["status"] = "req_exceed";
		} else if (count($days_upd) > 6000) {
			$result["status"] = "oversize";
		} else {
			$result["status"] = "ok";
			$result["daysToUpdate"] = $days_upd;
			//Inserta los MAIN search_reponse VALIDOS
			$this->SmilesModel->insertOpSearchResponse($this->op_id, $ways, $fdesde, $fhasta);
			$this->SmilesModel->updateOpRequest($this->op_id, $valid_req);
		}

		header('Content-Type: application/x-json; charset=utf-8');
		echo json_encode($result);
		exit;
	}

	public function ax_delete_my_search($id)
	{
		session_write_close();
		echo $this->SmilesModel->deleteMySearch($id);
	}

	public function ax_invalidate_search($id)
	{
		session_write_close();
		$desde = $this->input->post("desde");
		$hasta = $this->input->post("hasta");
		echo $this->SmilesModel->invalidateSearch($id, $desde, $hasta);
	}

	public function ax_toogle_public_view($id)
	{
		session_write_close();
		$info = $this->input->post("info");
		echo $this->SmilesModel->tooglePublicView($id, $info);
	}

	public function ax_extend_search($id)
	{
		session_write_close();
		$hours = $this->input->post("hours");
		echo $this->SmilesModel->extendSearch($id, $hours);
	}

	public function ax_delete_my_fav($id)
	{
		session_write_close();
		echo $this->SmilesModel->deleteMyFav($this->op_id, $id);
	}

	public function ax_get_flights($orig, $dest, $desde, $hasta)
	{
		session_write_close();
		$final_flights = $this->SmilesModel->listFlightsByOrigDest($orig, $dest, $desde, $hasta);
		$data["flights"] = $final_flights;
		$html = $this->load->view('ax_sm_flight_result', $data, TRUE);
		echo $html;
	}

	public function ax_process_search()
	{
		$use_server = $this->session->userdata("UseServer");
		$mile_price = $this->input->post("mile_price") ?? $this->session->userdata("mile_price");

		session_write_close();

		$orig = $this->input->post("orig");
		$dest = $this->input->post("dest");
		$fecha = $this->input->post("fecha");
		$listType = $this->input->post("listType");
		$flights = json_decode($this->input->post("flightList"));
		$onlyAirlines = $this->input->post("onlyAirlines");
		$txtOnly =  $this->input->post("txtOnly");
		$prefilter = $this->input->post("prefilter");
		//si no hay selOnly o  txtOnly toma los calores de exclude.
		if ($onlyAirlines == null && $txtOnly == null) {
			$excludeAirlines = $this->input->post("excludeAirlines");
			$txtExclude = $this->input->post("txtExclude");
		}

		$tax_res = $this->SmilesModel->processFlightsFromAPI($this->op_id, $flights, $orig, $dest, $fecha, $mile_price, 1, $onlyAirlines, $excludeAirlines, $txtOnly, $txtExclude, $prefilter, $use_server); //Devuelve 1 = OK, -1 = ERROR
		if ($use_server) { // && 1 == 2) {
			$this->ax_list_results($listType, $tax_res);
		} else {
			$this->ax_get_to_tax($orig, $dest, $fecha, $mile_price, $listType);
		}
	}

	public function ax_list_results($listType, $tax_res = null)
	{
		$this->do_ax_list_results($listType, $this->op_id, $this->rol_id, $tax_res);
	}

	private function ax_get_to_tax($orig, $dest, $fecha, $mile_price, $listType)
	{
		session_write_close();
		//NO SE LLAMA DE AFUERA.
		$flights_to_tax = $this->SmilesModel->getFlightsToTax($orig, $dest, $fecha);
		if (count($flights_to_tax) > 0) {
			$ret = array_slice($flights_to_tax, 0, 1);
			$jsonResponse = json_encode(["res" => "get_tax", "fl_to_tax" => $ret]);
		} else {
			$jsonResponse = json_encode(["res" => "ok"]);
			$this->SmilesModel->endProcessFlights($this->op_id, $orig, $dest, $fecha, $mile_price);
			//no tiene sentido
			$this->ax_list_results($listType);
		}

		header('Content-Type: application/json');
		echo $jsonResponse;
		exit;
	}

	public function ax_update_tax($listType)
	{
		//ONLY CLIENT SIDE CALL
		session_write_close();
		$tf = $this->input->post("tf");
		$tax_value = $this->input->post("tax_value");
		$mile_price = $this->input->post("mile_price");

		//Lo ejecuta luego de llamar a la funcion de API smiles
		//Va a escribir en am_fare_tax y va a ejecutar el udpate y va a devolver el siguiente vuelo a taxear
		$arr_where = ["AirCode" => $tf->AirCode, "Escalas" => $tf->Escalas, "AirlineTax" => $tf->AirlineTax];
		if ($this->config->item('use_origdest_tax')) {
			$arr_where += ["Orig" => $tf->Orig, "Dest" => $tf->Dest];
		}

		$fare_tax = new stdClass();
		$fare_tax->Orig = $tf["Orig"];
		$fare_tax->Dest = $tf["Dest"];
		$fare_tax->AirCode = $tf["AirCode"];
		$fare_tax->Escalas = $tf["Escalas"];
		$fare_tax->AirlineTax = $tf["AirlineTax"];
		$fare_tax->RealTax = $tax_value;
		$fare_tax->Status = 1;
		$this->SmilesModel->processClientTaxFlight($fare_tax, $tf["Uid"]);
		$this->ax_get_to_tax($tf["Orig"], $tf["Dest"], $tf["Fecha"], $mile_price, $listType);
	}

	public function ax_save_error_log()
	{
		session_write_close();
		$ts = $this->input->post("ts");
		$er = $this->input->post("er");
		$this->HelperModel->saveOpError($ts, $er);
		$this->SmilesModel->clearResponseZeroOp();
		echo "ok";
	}

	public function ax_do_search()
	{
		$use_server = $this->session->userdata("UseServer");
		$mile_price = $this->input->post("mile_price") ?? $this->session->userdata("mile_price");

		$onlyAirlines = $this->input->post("onlyAirlines");
		$txtOnly = $this->input->post("txtOnly");
		if ($onlyAirlines == null && $txtOnly == null) {
			$excludeAirlines = $this->input->post("excludeAirlines");
			$txtExclude = $this->input->post("txtExclude");
		}
		$prefilter = $this->input->post("prefilter");
		$fdesde = date("Y-m-d", strtotime($this->input->post("fdesde")));
		$dialog =  $this->input->post("dialog");
		$idas = $this->input->post("idas") ?? array();
		$vueltas = $this->input->post("vueltas") ?? array();
		$ways = array_merge($idas, $vueltas);
		if ($dialog) {
			$fhasta = $this->SmilesModel->getFechaHastaForDialog($idas, $fdesde);
		} else {
			$fhasta = date("Y-m-d", strtotime($this->input->post("fhasta")));
		}

		if (!$use_server) {
			//No esta habilitado para usar do_search por permisos
			$jsonRes = json_encode(["nosession" => 1]);
		} else {
			//Tenemos que obtener la proxima fecha a buscar.
			$daysToUpdate = $this->SmilesModel->getDaysToUpdate($ways, $fdesde, $fhasta);
			$jsonRes = '{"status" : "finished"}';
			if (count($daysToUpdate) > 0) {
				$ts = new stdClass();
				$ts->search = $daysToUpdate[0];
				$ts->progress = count($daysToUpdate);
				$jsonRes = json_encode($ts);
				$src = $ts->search;
				$orig = $src->Orig;
				$dest = $src->Dest;
				$fecha = $src->Fecha;
				$ts = new stdClass();

				//Inserta en search_response para marcar al cliente
				$this->SmilesModel->insertResponse($this->op_id, $src->Orig, $src->Dest, $src->Fecha, 0);
				//cambiar el numero de server que ejecuta.

				session_write_close();

				$flights = $this->HelperModel->searchSmilesAPI($this->op_id, $orig, $dest, $fecha);
				//A este punto ya escribio debug_log y debug_error
				if (isset($flights->error)) {
					$jsonRes = json_encode($flights); //si es error me hace un encode del array
				} else {
					//SERVER BASED TAMBIEN - BUSCA TAX POR SERVER Y PUEDE DAR ERROR
					$res = $this->SmilesModel->processFlightsFromAPI($this->op_id, $flights, $orig, $dest, $fecha, $mile_price, 2, $onlyAirlines, $excludeAirlines, $txtOnly, $txtExclude, $prefilter, 1);
					if ($res == -1) { //TAX ERROR - 403 CONFIRMADO
						$jsonRes = '{"error" : "403"}';
					}
				}
			}
		}
		header('Content-Type: application/json');
		echo $jsonRes;
		exit;
	}

	private function getWaysFromOrigsDests($origs, $dests, $has_ret = null)
	{
		$arrayOrigen = explode(",", $origs);
		$arrayDestino = explode(",", $dests);

		// Inicializar el array de resultados
		$resultado = [];

		// Crear todas las combinaciones posibles
		foreach ($arrayOrigen as $origen) {
			foreach ($arrayDestino as $destino) {
				$combinacion = ["orig" => $origen, "dest" => $destino];
				$resultado[] = $combinacion;
				if ($has_ret) {
					$c = ["dest" => $origen, "orig" => $destino];
					$resultado[] = $c;
				}
			}
		}
		return $resultado;
	}

	public function config()
	{
		$rol =  $this->HelperModel->getRol($this->rol_id);
		$data = array();
		$data["user_request_count"] = $this->HelperModel->getUserRequests($this->op_id) ?? 0;
		$data["total_requests"] = $rol->Req_Day;
		$this->load->view('sm_config', $data);
	}
}
