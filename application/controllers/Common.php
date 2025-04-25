<?php
defined('BASEPATH') or exit('No direct script access allowed');
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

class Common extends CI_Controller
{
	var $op_id;
	var $is_mobile;
	var $rol_id;

    function __construct()
	{
		parent::__construct();
		$this->load->model("OperatorModel");
		$this->load->model("SmilesModel");
		$this->load->model("HelperModel");
		$userAgent = $_SERVER['HTTP_USER_AGENT'];
		$this->is_mobile = preg_match("/(android|iphone|ipod|blackberry|iemobile|opera mini)/i", $userAgent);
	}

    public function ax_trip_result()
	{
		$mile_price = $this->input->post("mile_price") ?? $this->session->userdata('mile_price');
		$op_id = $this->session->userdata('Op_Id');
		$rol_id = $this->session->userdata('Rol_Id');
		session_write_close();

		$desde = $this->input->post("fdesde"); //formato 30-03-2024
		$hasta = $this->input->post("fhasta"); //formato 30-03-2024
		$duracion = $this->input->post("duracion");
		$cabina = $this->input->post("cabina");
		$escalas = $this->input->post("escalas");
		$emin = $this->input->post("emin");
		$emax = $this->input->post("emax");
		$ttot = $this->input->post("ttot");
		$asientos = $this->input->post("asientos");
		$idas = $this->input->post("idas");
		$vueltas = $this->input->post("vueltas");
		$airline = $this->input->post("airline");
		$sm = $this->input->post("sm");
		$ret_idas = $this->SmilesModel->listFlightResults($op_id, $rol_id, $idas, $desde, $hasta, $mile_price, $sm, $duracion, $cabina, $escalas, $asientos, $airline, null) ?? array();
		$ret_vueltas = $this->SmilesModel->listFlightResults($op_id, $rol_id, $vueltas, $desde, $hasta, $mile_price, $sm, $duracion, $cabina, $escalas, $asientos, $airline, null) ?? array();
		if (count($ret_idas) * count($ret_vueltas) < 200000) {
			$html = $this->getFilteredTable($ret_idas, $ret_vueltas, $emin, $emax, $ttot, $mile_price, $sm);			
		} else {
			$html = "<h5 class='alert alert-danger'>La búsqueda es demasiado amplia. Debe acotar las fechas de búsqueda o la diferencia entre estadía mínima y máxima</h5>";
		}
		$jsonResponse = json_encode(["html" => $html]);
		header('Content-Type: application/json');
		echo $jsonResponse;
		exit;
	}

	public function do_ax_list_results($listType, $op_id, $rol_id, $tax_res = null)
	{
		$mile_price = $this->input->post("mile_price") ?? $this->session->userdata('mile_price');		

		session_write_close();
		
		$duracion = $this->input->post("duracion");
		$cabina = $this->input->post("cabina");
		$escalas = $this->input->post("escalas");
		$asientos = $this->input->post("asientos");
		$airline = $this->input->post("airline");
		$total_v = $this->input->post("total");
		$idas = $this->input->post("idas");
		$vueltas = $this->input->post("vueltas");
		$sm = $this->input->post("sm");
		$tomorrow = date('Y-m-d', strtotime('+1 day'));
		$fDesde = date("Y-m-d", strtotime($this->input->post("fdesde")));
		$fHasta = date("Y-m-d", strtotime($this->input->post("fhasta")));
		$fDesde = max($fDesde, $tomorrow);
		$data["fDesde"] = $fDesde;
		$data["fHasta"] = $fHasta;
		$data["fHastaCal"] = $fHasta;

		//STRING IDAS
		$stIdas = array();
		foreach ($idas as $ida) {
			$stIdas[$ida["orig"]][] = $ida["dest"];
		}

		$search_sm = $listType == 3 ? $sm : 1;
		$air_lines = array();
		$data["cab"] = $cabina;
		$data["idas"] = $stIdas;
		$ret_idas = $this->SmilesModel->listFlightResults($op_id, $rol_id, $idas, $fDesde, $fHasta, $mile_price, $search_sm, $duracion, $cabina, $escalas, $asientos, $airline, $total_v) ?? array();
		$data["f_idas"] = $ret_idas;
		$data["tot_idas"] = sizeof($ret_idas);
		foreach ($ret_idas as $air_ida) {
			$air_lines[$air_ida->AirCode] = $air_ida->Aerolinea;
		}

		if ($vueltas != null) {
			//STRING VUELTAS
			$stVueltas = array();
			foreach ($vueltas as $vuelta) {
				$stVueltas[$vuelta["dest"]][] = $vuelta["orig"];
			}

			$ret_vueltas = $this->SmilesModel->listFlightResults($op_id, $rol_id, $vueltas, $fDesde, $fHasta, $mile_price, $search_sm, $duracion, $cabina, $escalas, $asientos, $airline, $total_v) ?? array();
			$data["f_vueltas"] = $ret_vueltas;
			$data["tot_vueltas"] = sizeof($ret_vueltas);			
			$data["vueltas"] = $stVueltas;
			foreach ($ret_vueltas as $air_vta) {
				$air_lines[$air_vta->AirCode] = $air_vta->Aerolinea;
			}
		}
		$data["mile_price"] = $mile_price;
		asort($air_lines);
		$req_sent = $this->HelperModel->getUserRequests($this->op_id);
		switch ($listType) {
			case 1:
				$html = $this->load->view("ax_sm_flight_result", $data, TRUE);
				$jsonResponse = json_encode(["html" => $html, "res" => $tax_res, "airlines" => $air_lines, "req_sent" => $req_sent]);
				break;
			case 2:
				$html = $this->load->view("ax_sm_set_trip", $data, TRUE);
				$jsonResponse = json_encode(["html" => $html, "airlines" => $air_lines]);
				break;
			case 3:
				$html = $this->getFilteredCalendar($data, $idas, $vueltas, $fDesde, $fHasta, $search_sm);
				$jsonResponse = json_encode(["html" => $html, "res" => $tax_res, "airlines" => $air_lines, "req_sent" => $req_sent]);
		}
		header('Content-Type: application/json');
		echo $jsonResponse;
		exit;
	}

	private function getFilteredTable($idas, $vueltas, $emin, $emax, $tot, $mile_price, $sm)
	{
		//ESTA FUNCION ES PARA LOS RESULTADOS CON ESTADIAS
		$flight_ok = array();
		$totalFlights = 0;
		foreach ($idas as $i) {
			$f_llegada = new DateTime(substr($i->Llegada, 0, 10));
			foreach ($vueltas as $v) {
				$f_retorno = new DateTime(substr($v->Salida, 0, 10));
				$intervalo = $f_retorno->diff($f_llegada);
				$estadia = $intervalo->days;
				if ($f_retorno > $f_llegada && $estadia >= $emin && $estadia <= $emax) {
					$res = new stdClass();
					$res->precio_ida = $i->Total;
					$res->precio_vta = $v->Total;
					//Calculo del precio conveniente		
					if ($sm == 0 || $i->Millas * $mile_price <= $i->SM * $mile_price + $i->Money) {
						$i->Type = "MILES";
						$res->precio_ida = $i->Total;
						$res->millas_ida = $i->Millas;
						$res->dinero_ida = $i->Tasas;
					} else {
						$i->Type = "S&M";
						$res->millas_ida = $i->SM;
						$res->dinero_ida = $i->Tasas + $i->Money;
					}
					if ($sm ==0 || $v->Millas * $mile_price <= $v->SM * $mile_price + $v->Money) {
						$v->Type = "MILES";
						$res->millas_vta = $v->Millas;
						$res->dinero_vta = $v->Tasas;
					} else {
						$v->Type = "S&M";
						$res->millas_vta = $v->SM;
						$res->dinero_vta = $v->Tasas + $v->Money;
					}

					if ($v->Total + $i->Total < $tot || !$tot) {
						$res->estadia = $estadia;
						$res->precio_tot = $v->Total + $i->Total;
						$res->ida = $i;
						$res->vuelta = $v;
						$flight_ok[] = $res;
					}
				}
			}
		}
		usort($flight_ok, array($this, 'compararPorPrecio'));
		$flight_ok = array_slice($flight_ok, 0, 400);
		$data = array();
		$data["trips"] = $flight_ok;
		$html = $this->load->view('ax_sm_trip_result', $data, TRUE);
		return $html;
	}

    protected function getFilteredCalendar($data, $w_idas, $w_vueltas, $fDesde, $fHasta, $sm)
	{
		$rol_id = $this->session->userdata("Rol_Id");
		$op_id = $this->session->userdata("Op_Id");
		session_write_close();
		$cal_data = $this->SmilesModel->getFilteredCalendar($data, $op_id, $rol_id, $w_idas, $w_vueltas, $fDesde, $fHasta, $sm);
		$html = $this->load->view('ax_sm_flight_calendar', $cal_data, TRUE);
		return $html;		
	}

    private function compararPorEstadia($a, $b)
	{

		$comparacionEstadia = $a->estadia - $b->estadia;
		if ($comparacionEstadia !== 0) {
			return $comparacionEstadia;
		}
		return $a->precio_tot - $b->precio_tot;
	}

	private function compararPorPrecio($a, $b)
	{
		$comparacionPrecio = $a->precio_tot - $b->precio_tot;
		if ($comparacionPrecio !== 0) {
			return $comparacionPrecio;
		}
		return $a->estadia - $b->estadia;
	}
}
