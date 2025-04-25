<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SmilesModel extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	private function getWhereClause($ways, $inc_ts = true, $pf = "")
	{
		$int_hours = $this->SmilesModel->getConfig($this->config->item("id_interval"));
		foreach ($ways as $w) {
			$where_conditions[] = "({$pf}Orig = '" . $w["orig"] . "' AND {$pf}Dest = '" . $w["dest"] . "')";
		}

		$where_clause = " (" . implode(' OR ', $where_conditions) . ")";
		if ($inc_ts)
			$where_clause .= " AND TIMESTAMPDIFF(HOUR, {$pf}Datelog, NOW()) <= {$int_hours}";
		return $where_clause;
	}

	public function deleteSearchResponse($ways, $fdesde, $fhasta)
	{
		$where_clause = " where" . $this->getWhereClause($ways, false, "");
		$fecha_where = " and Fecha >= '" . $fdesde . "' AND Fecha <= '" . $fhasta . "'";
		$this->db->query("delete from am_search_response {$where_clause} {$fecha_where}");
		return 1;
	}

	public function getDaysToUpdate(&$ways, $fdesde, $fhasta, $not_check = 0)
	{
		$int_hours = $this->SmilesModel->getConfig($this->config->item("id_interval"));
		$toAdd = array();
		//NON EXISTING RESULTS
		foreach ($ways as $w) {
			$orig = $w["orig"];
			$dest = $w["dest"];
			if ($not_check) {
				$query ="SELECT Fecha from am_all_dates where Fecha >= '{$fdesde}' AND Fecha <= '{$fhasta}'";
			} else {
				$query = "SELECT ad.Fecha FROM am_all_dates ad LEFT JOIN am_search_response sr ON ad.Fecha = sr.Fecha AND sr.Orig = '{$orig}' AND sr.Dest = '{$dest}'
						WHERE (sr.Fecha IS NULL OR TIMESTAMPDIFF(HOUR, sr.Datelog, NOW()) >= {$int_hours}) AND ad.Fecha >= '{$fdesde}' AND ad.Fecha <= '{$fhasta}'";
			}
			$non_exist = $this->db->query($query)->result();
			foreach ($non_exist as $n) {
				$non_exist = $this->db->query($query)->result();
				$dta = new stdClass();
				$dta->Fecha = $n->Fecha;
				$dta->Orig = $orig;
				$dta->Dest = $dest;
				$toAdd[] = $dta;
			}
		}
		usort($toAdd, function ($a, $b) {
			return strtotime($a->Fecha) - strtotime($b->Fecha);
		});

		foreach ($ways as &$w) {
			if (!isset($w["cant"])) {
				$w["cant"] = 0;
			}
			foreach ($toAdd as $d) {
				if ($d->Orig == $w["orig"] && $d->Dest == $w["dest"]) {
					$w["cant"]++;
				}
			}
		}
		return $toAdd;
	}

	public function listOperatorSearches($mile_price, $rol_id, $op_id)
	{
		//$rol_where = $rol_id != 1 ? " where vs.Status_Id = 1" : "";		
		$rol_where = " where vs.Status_Id = 1";
		$has_view_query = "Select os.*, 1 as HasView from am_op_search os inner join am_view_search vs on vs.Search_Id = os.Search_Id {$rol_where}";
		$view_res = $this->db->query($has_view_query)->result();

		$op_query = "Select os.*, 0 as HasView from am_op_search os where os.Op_Id = {$op_id} and Search_Id not in (Select Search_Id from am_view_search vs {$rol_where})";
		$op_res = $this->db->query($op_query)->result();

		$ms = array_merge($view_res, $op_res);

		return $this->commonOpSearch($ms, $mile_price, $rol_id, $op_id);
	}

	private function commonOpSearch(&$ms, $mile_price, $rol_id, $op_id)
	{
		if ($ms != null) {
			foreach ($ms as $search) {
				$this->populateSearch($search, $mile_price, $rol_id, $op_id);
			}
			if ($rol_id != 1) {
				$ms = array_filter($ms, function ($m) {
					return $m->Valid == true;
				});
			}
			usort($ms, array($this, 'compareMySearches'));
			return $ms;
		}
	}

	public function getSearch($mode, $id, $mile_price, $rol_id, $op_id = 0)
	{

		$rol_where = $rol_id != 1 ? " and vs.Status_Id = 1" : "";

		$search = $this->db->get_where("am_op_search", array("Op_Id" => $op_id, "Search_Id" => $id))->row();

		if ($search == null && $mode == "VIEW") {
			$has_view_query = "Select *, 1 as HasView from am_op_search os inner join am_view_search vs on vs.Search_Id = os.Search_Id where vs.Search_Id = {$id} {$rol_where}";
			$search = $this->db->query($has_view_query)->row();
		}
		if ($search != null) {
			$this->populateSearch($search, $mile_price, $rol_id, $op_id);
		}
		return $search;
	}

	public function compareMySearches($a, $b)
	{
		if ($a->Valid !== $b->Valid) {
			return $b->Valid - $a->Valid; // Orden descendente por Valid
		}

		// Si Valid es igual, comparar por HasView
		if ($a->HasView !== $b->HasView) {
			return $b->HasView - $a->HasView; // Orden descendente por HasView
		}

		// Si Valid y HasView son iguales, comparar por Datelog
		return strtotime($b->Datelog) - strtotime($a->Datelog);
	}


	public function getConfig($id)
	{
		return $this->config->item("cfg_{$id}");
		//return $this->db->get_where("am_configs", array("Config_Id" => $id))->row()->Val;
	}

	public function deleteMySearch($id)
	{
		$this->db->delete("am_op_search", array("Search_Id" => $id));
		$this->db->delete("am_op_search_child", array("Search_Id" => $id));
		return 1;
	}

	public function invalidateSearch($id, $start, $end)
	{
		$searches = $this->db->get_where("am_op_search_child", array("Search_Id" => $id))->result();
		$tbl[0] = "am_search_response";
		$tbl[1] = "am_flight_result";
		$tbl[2] = "am_op_search_response";

		for ($t = 0; $t <= 2; $t++) {
			foreach ($searches as $s) {
				if ($start != "") {
					$this->db->where("Fecha >= '{$start}'");
				}
				if ($end != "") {
					$this->db->where("Fecha <= '{$end}'");
				}
				$this->db->where("Orig = '{$s->Orig}' and Dest = '{$s->Dest}'");
				$del_q = $this->db->get_compiled_delete($tbl[$t]);
				$this->db->query($del_q);
			}
		}
		return 1;
	}
	public function extendSearch($id, $hours)
	{
		$childs = $this->db->get_where("am_op_search_child", array("Search_Id" => $id))->result();
		foreach ($childs as $c) {
			$this->db->query("UPDATE am_search_response set Datelog = DATE_ADD(Datelog, INTERVAL {$hours} HOUR) where Orig = '{$c->Orig}' and Dest = '{$c->Dest}'");
			$this->db->query("UPDATE am_flight_result set Datelog = DATE_ADD(Datelog, INTERVAL {$hours} HOUR) where Orig = '{$c->Orig}' and Dest = '{$c->Dest}'");
		}
		return 1;
	}

	public function tooglePublicView($id, $info)
	{
		$vs = $this->db->get_where("am_view_search", array("Search_Id" => $id))->row();
		if ($vs == null) {
			//Agrega al viewsearch
			$this->db->query("INSERT into am_view_search values ($id, '$info', 1);");
		} else {
			$nstatus = $vs->Status_Id == 1 ? 0 : 1;
			$this->db->query("UPDATE am_view_search set Status_Id = $nstatus, Des = '$info' where Search_Id = $id");
		}
		//SI OK
		return 1;
	}

	public function deleteMyFav($op_id, $id)
	{
		$this->db->delete("am_op_favorite", array("Flight_Id" => $id, "Op_Id" => $op_id));
		return 1;
	}

	private function getValidAirports()
	{
		return true;
	}

	public function isNaNorOld($orig, $dest, $fecha)
	{
		$int_hours = $this->SmilesModel->getConfig($this->config->item("id_interval"));
		date_default_timezone_set('America/Argentina/Buenos_Aires');
		$resp = $this->db->get_where("am_search_response", array("Orig" => $orig, "Dest" => $dest, "Fecha" => $fecha))->row();
		if ($resp != null) {
			$last_date = DateTime::createFromFormat('Y-m-d H:i:s', $resp->Datelog);
			$now = new DateTime();
			$hour_diff = $last_date->diff($now)->h + $last_date->diff($now)->days * 24;
			if ($hour_diff < $int_hours) {
				return false;
			}
		}
		return true;
	}

	public function writeSearchResponse($orig, $dest, $fdesde, $fhasta)
	{
		$query = "select Fecha from am_all_dates ad where Fecha not in (select Fecha from am_search_response where Orig = '{$orig}' and  Dest = '{$dest}') " .
			"and ad.Fecha >= '{$fdesde}' and ad.Fecha <= '{$fhasta}'";
	}

	private function orderByOrigDest(&$array)
	{
		usort($array, function ($a, $b) {
			// Compara los valores 'orig'
			$result = strcmp($a['orig'], $b['orig']);
			// Si los valores 'orig' son iguales, compara los valores 'dest'
			if ($result === 0) {
				$result = strcmp($a['dest'], $b['dest']);
			}
			return $result;
		});
		return $array;
	}

	public function saveOpSearch($op_id, $idas, $vueltas)
	{
		$this->orderByOrigDest($idas);
		$this->orderByOrigDest($vueltas);

		$search_child = array();
		foreach ($idas as $i) {
			$child = new stdClass();
			$child->Tipo = 1;
			$child->Orig = $i["orig"];
			$child->Dest = $i["dest"];
			$search_child[] = $child;
		}
		foreach ($vueltas as $v) {
			$child = new stdClass();
			$child->Tipo = 2;
			$child->Orig = $v["orig"];
			$child->Dest = $v["dest"];
			$search_child[] = $child;
		}
		$shash = hash('sha256', json_encode($search_child));
		$op_search = new stdClass();
		$op_search->Op_Id = $op_id;
		$op_search->SHash = $shash;

		$search_exists = $this->db->get_where("am_op_search", array("Op_Id" => $op_id, "SHash" => $shash))->row();

		if ($search_exists) {
			$s_id = $search_exists->Search_Id;
			$this->db->update("am_op_search", array("Datelog" => date("Y-m-d H:i:s")), array("Op_Id" => $op_id, "SHash" => $shash));
		} else {
			$this->db->insert("am_op_search", $op_search);
			$s_id = $this->db->insert_id();
			foreach ($search_child as $s) {
				$s->Search_Id = $s_id;
			}
			$this->db->insert_batch("am_op_search_child", $search_child);
		}
		return $s_id;
	}

	public function insertOpSearchResponse($op_id, $ways, $fdesde, $fhasta)
	{
		//INSERTA LOS SEARCH_RESPONSE QUE YA EXISTEN		
		$where_clause = $this->getWhereClause($ways, true);
		$query = "INSERT INTO am_op_search_response (Op_Id, Orig, Dest, Fecha, Datelog) Select {$op_id}, Orig, Dest, Fecha, Datelog from am_search_response
			WHERE {$where_clause} and Fecha >= '{$fdesde}' and Fecha <= '{$fhasta}' on duplicate key update Datelog = am_search_response.Datelog"; //CURRENT_TIMESTAMP";
		$this->db->query($query);
	}

	public function getSearchResponseDifference($op_id, &$ways, $fdesde, $fhasta)
	{
		//TRAE LOS MAIN SEARCH_RESPONSE VALIDOS QUE NO ESTAN EN EL OP_SEARCH_RESPONSE
		$div_age = $this->getConfig(2);

		$where_clause = $this->getWhereClause($ways, true, "sr.");
		$query = "SELECT sr.Orig, sr.Dest, sr.Fecha, TIMESTAMPDIFF(HOUR, sr.Datelog, NOW()) as Age from am_search_response sr LEFT JOIN am_op_search_response os on os.Orig = sr.Orig and os.Dest = sr.Dest and os.Fecha = sr.Fecha and os.Op_Id = {$op_id}
				 WHERE {$where_clause} and os.Fecha IS NULL and sr.Fecha >= '{$fdesde}' and sr.Fecha <= '{$fhasta}'";
		$days_valid = $this->db->query($query)->result();
		foreach ($ways as &$w) {
			if (!isset($w["cant"])) {
				$w["cant"] = 0;
			}

			foreach ($days_valid as $d) {
				if ($d->Orig == $w["orig"] && $d->Dest == $w["dest"]) {
					$w["cant"] += round(1 / (floor($d->Age / $div_age) + 1), 2);
				}
			}
		}
		return $days_valid;
	}

	public function clearResponseZeroOp()
	{
		$this->db->delete("am_search_response", array("Status_Id" => 0));
	}

	public function getFechaHastaForDialog($ways, $fdesde)
	{
		$where_clause = "AND " . $this->getWhereClause($ways);
		$query = "SELECT DATE_SUB(Fecha, INTERVAL 1 DAY) as NextFecha, Count(1) as Cant from am_search_response where Fecha >= '$fdesde' {$where_clause} 
					group by Fecha Having Cant = " . count($ways) . " order by Fecha Limit 1";
		$ret = $this->db->query($query)->row();
		return $ret->NextFecha ?? $fdesde;
	}


	public function getDateWithNoFlights($arr_fechas, $fDesde, $fHasta, $op_id, $rol_id)
	{
		$fechaString = "'" . implode("', '", $arr_fechas) . "'";
		$query = "SELECT Fecha from am_all_dates where Fecha >= '$fDesde' and Fecha < '$fHasta' and Fecha not in ($fechaString)";
		$ret =  $this->db->query($query)->result();
		$fechasResultantes = array_map(function ($row) {
			return $row->Fecha;
		}, $ret);
		return $fechasResultantes;
	}

	public function getDatesNotCompleted($ways, $fDesde, $fHasta, $op_id, $rol_id)
	{
		$where_clause =  $this->getWhereClause($ways);
		$tbl = $rol_id == 1 ? "am_search_response" : "am_op_search_response";
		$op_where = $rol_id != 1 ? "and sr.Op_Id = {$op_id}" : "";
		$query = "SELECT ad.Fecha, (select count(1) from {$tbl} sr where sr.Fecha = ad.Fecha {$op_where} and {$where_clause}) as Cant from am_all_dates ad 
					where ad.Fecha >= '$fDesde' and ad.Fecha <= '$fHasta' group by ad.Fecha"; // having cant < " . count($ways);
		$results = $this->db->query($query)->result();
		$hash = array();
		foreach ($results as $row) {
			$hash[$row->Fecha] = intval($row->Cant);
		}
		return $hash;
	}

	public function toogleFavFlight($op_id, $id)
	{
		$isFav = $this->db->get_where("am_op_favorite", array("Op_Id" => $op_id, "Flight_Id" => $id))->row();
		if ($isFav) {
			$this->db->delete("am_op_favorite", array("Op_Id" => $op_id, "Flight_Id" => $id));
			return "star_border";
		} else {
			$nf = new stdClass();
			$nf->Op_Id = $op_id;
			$nf->Flight_Id = $id;
			$this->db->insert("am_op_favorite", $nf);
			return "star";
		}
	}

	public function getFilteredCalendar($data, $op_id, $rol_id, $w_idas, $w_vueltas, $fDesde, $fHasta, $sm)
	{
		$hash_idas = array();
		$hash_vueltas = array();
		$map_idas = array();
		$map_vueltas = array();
		$idas = $data["f_idas"];
		$vueltas = $data["f_vueltas"];

		foreach ($idas as $i) {
			$fecha = $i->Fecha;
			$tipoCabina = $i->Cabina;
			if ($tipoCabina == "FIRST_CLASS")
				continue;
			$map_idas[$fecha][] = $i;

			if (!isset($hash_idas[$fecha][$tipoCabina]) || $i->Total < $hash_idas[$fecha][$tipoCabina]->Total) {
				$hash_idas[$fecha][$tipoCabina] = $i;
			}
		}
		// Aplanar el array para idas
		foreach ($hash_idas as $fecha => &$vuelosPorFecha) {
			$vuelosPorFecha = array_values($vuelosPorFecha);
		}

		$data["hash_idas"] = $hash_idas;
		$data["map_idas"] = $map_idas;
		$data["sm"] = $sm;
		$fechas_idas = array_keys($hash_idas);
		$data["ida_no_flight"] = $this->SmilesModel->getDateWithNoFlights($fechas_idas, $fDesde, $fHasta, $op_id, $rol_id);
		$data["ida_not_completed"] = $this->SmilesModel->getDatesNotCompleted($w_idas, $fDesde, $fHasta, $op_id, $rol_id);

		if ($vueltas != null) {
			foreach ($vueltas as $v) {
				$fecha = $v->Fecha;
				$tipoCabina = $v->Cabina;
				$map_vueltas[$fecha][] = $v;

				if (!isset($hash_vueltas[$fecha][$tipoCabina]) || $v->Total < $hash_vueltas[$fecha][$tipoCabina]->Total) {
					$hash_vueltas[$fecha][$tipoCabina] = $v;
				}
			}

			// Aplanar el array para vueltas
			foreach ($hash_vueltas as $fecha => &$vuelosPorFecha) {
				$vuelosPorFecha = array_values($vuelosPorFecha);
			}

			$data["hash_vueltas"] = $hash_vueltas;
			$data["map_vueltas"] = $map_vueltas;
			$fechas_vueltas = array_keys($hash_vueltas);
			$data["vta_no_flight"] = $this->SmilesModel->getDateWithNoFlights($fechas_vueltas, $fDesde, $fHasta, $op_id, $rol_id);
			$data["vta_not_completed"] = $this->SmilesModel->getDatesNotCompleted($w_vueltas, $fDesde, $fHasta, $op_id, $rol_id);
		}
		return $data;
	}

	private function startProcessFlights($flights, $orig, $dest, $fecha, $onlyAirlines, $excludeAirlines, $txtOnly, $txtExclude, $mile_price, $prefilter)
	{
		$ff_results = [];
		if ($flights !== null) {
			$cot_usd = $this->config->item('cot_usd');
			foreach ($flights as $f) {
				if (
					strpos($f->tripType, "Award") !== FALSE && $f->cabin != "FIRST_CLASS" &&
					($excludeAirlines == "" && $txtExclude == "" || $f->airline->code != $excludeAirlines || strpos($txtExclude, $f->airline->code) === FALSE) && 
					($onlyAirlines == "" && $txtOnly == "" || $f->airline->code == $onlyAirlines || strpos($txtOnly, $f->airline->code) !== FALSE)

				) {
					$ff = new stdClass();
					$ff->MP = $mile_price;
					$ff->Uid = $f->uid;
					$ff->Orig = $orig;
					$ff->Dest = $dest;
					$ff->AirOrig = $f->departure->airport->code;
					$ff->AirDest = $f->arrival->airport->code;
					$ff->AirCode = $f->airline->code;
					$ff->Fecha = $fecha;
					$ff->Duracion = $f->duration->hours * 60 + $f->duration->minutes;
					$ff->Salida = $f->departure->date;
					$ff->Llegada = $f->arrival->date;
					$ff->Aerolinea = $f->airline->name;
					$ff->Cabina = $f->cabin;
					if ($ff->Cabina == "FIRST_CLASS") {
						$ff->Cabina == "BUSINESS";
					}
					$vuelos = "[";
					foreach ($f->legList as $l)
						$vuelos .= $l->flightNumber . "-";
					$vuelos = substr($vuelos, 0, -1) . "]";
					$ff->Escalas = $f->stops;
					$ff->Vuelos = $vuelos;
					$ff->Asientos = $f->availableSeats;

					//Tarifa
					foreach ($f->fareList as $fare) {
						if ($fare->type == "SMILES_CLUB") {
							$ff->Millas = $fare->miles;
							$ff->FareUid = $fare->uid;
							$ff->AirlineTax = $fare->airlineTax;
							$ff->Tasas = 0; //round($fare->airlineTax * $cot_usd);
							break;
						}
					}

					foreach ($f->fareList as $fare) {
						if ($fare->type == "SMILES_MONEY_CLUB") {
							$ff->SM = $fare->miles;
							$ff->Money = $fare->money;
							break;
						}
					}
					$ff->CotUSD = $cot_usd;
					$ff_results[] = $ff;
					//$this->db->insert("am_tmp_result", $ff);
				}
			}
		}

		if ($prefilter) {
			// Ordenar el array
			usort($ff_results, array($this, 'tmp_flight_sort'));

			$c_cabin = "";
			$minDuracion = $ff_results[0]->Duracion;
			foreach ($ff_results as $key => $tmp) {
				if ($tmp->Cabina != $c_cabin || $tmp->Duracion < $minDuracion) {
					$c_cabin = $tmp->Cabina;
					$minDuracion = $tmp->Duracion;
				} else if ($tmp->Escalas > 0 && $tmp->Duracion >= $minDuracion) {
					unset($ff_results[$key]);
					continue;
				}
			}
		}
		if (count($ff_results) > 0) {
			foreach ($ff_results as $r) {
				unset($r->MP);
			}
			$this->db->insert_batch("am_tmp_result", $ff_results);
		}

		return $ff_results;
	}

	function tmp_flight_sort($a, $b)
	{
		// Comparar por Cabina
		if ($a->Cabina != $b->Cabina) {
			return strcmp($a->Cabina, $b->Cabina);
		}

		// Comparar por Millas o SM + Money
		$aValue = $a->Millas * $a->MP < $a->SM * $a->MP + $a->Money ? $a->Millas * $a->MP : $a->SM * $a->MP + $a->Money;
		$bValue = $b->Millas * $b->MP < $b->SM * $b->MP + $b->Money ? $b->Millas * $b->MP : $b->SM * $b->Mp + $b->Money;

		if ($aValue != $bValue) {
			return $aValue - $bValue;
		}

		// Comparar por Duracion
		return $a->Duracion - $b->Duracion;
	}

	public function getFlightsToTax($orig, $dest, $fecha)
	{
		$arr_where = ["Orig" => $orig, "Dest" => $dest, "Fecha" => $fecha];
		$this->updateTmpTaxesFromFareTax();
		$no_tax_flights = $this->db->get_where("am_tmp_result", $arr_where + ["TaxLoaded" => 0])->result();
		return $no_tax_flights;
	}

	private function updateTmpTaxesFromFareTax()
	{
		$query_upd_all_taxes = "UPDATE am_tmp_result fls, am_fare_tax tax SET fls.Tasas = tax.RealTax, fls.TaxLoaded = 1 
								WHERE fls.TaxLoaded = 0 AND fls.AirCode = tax.AirCode AND fls.Escalas = tax.Escalas and fls.AirlineTax = tax.AirlineTax 
								AND tax.Status = 1 AND fls.Orig = tax.Orig and fls.Dest = tax.Dest";
		$this->db->query($query_upd_all_taxes);
	}

	public function processFlightsFromAPI($op_id, $flights, $orig, $dest, $fecha, $mile_price, $type, $onlyAirlines, $excludeAirlines, $txtOnly, $txtExclude, $prefilter, $use_server)
	{
		$taxError = false;
		$arr_where = ["Orig" => $orig, "Dest" => $dest, "Fecha" => $fecha];
		if ($type == 1) { //CLIENT LOG			
			$this->HelperModel->logAPICall($flights, $orig, $dest, $fecha, $type, $_SERVER["REMOTE_ADDR"], 200);
		}

		//VAMOS A ELIMINAR LOS QUE SI ESTEN EN FLIGHT RESULTS
		$this->db->where($arr_where);
		$del_query = $this->db->get_compiled_delete("am_tmp_result");
		$del_query .= " And Uid not in (select Uid From am_flight_result);";
		$this->db->query($del_query);

		//ESTO LO ESCRIBE EN AM_TMP_RESULT
		$ff_results = $this->startProcessFlights($flights, $orig, $dest, $fecha, $onlyAirlines, $excludeAirlines, $txtOnly, $txtExclude, $mile_price, $prefilter);

		//GET TAX FROM SERVER
		if ($use_server) {

			foreach ($ff_results as $ff) {
				$this->updateAirlineTax($ff);
				if ($ff->Tasas == -3) {
					$taxError = true;
					break;
				}
			}

			if (!$taxError) {
				//WAIT FOR UPDATE CURRENT TMP RESULT TAXES				
				$retry = 0;
				$no_tax_qty = null;
				do {
					$this->updateTmpTaxesFromFareTax();
					$no_tax_qty = $this->db->get_where("am_tmp_result", $arr_where + ["TaxLoaded" => 0])->result();
					if ($no_tax_qty === null || empty($no_tax_qty)) { //SI OBTUVO LOS IMPUESTOS NO RECHEQUEA
						break;
					}
					usleep(500000);
					$retry++;
				} while ($retry < 3);

				if ($retry == 3) {
					$this->HelperModel->logError("RETRY TAX UPDATE FAILED", 0, "{$orig}-{$dest}-{$fecha}", "", $_SERVER["HTTP_HOST"], print_r($no_tax_qty, true));
				}
				//FILTRA E INSERTA EN TABLA FLIGHT_RESULT
				$this->endProcessFlights($op_id, $orig, $dest, $fecha, $mile_price);
			} else {
				return -1;
			}
		}
		return 1;
	}

	public function endProcessFlights($op_id, $orig, $dest, $fecha, $mile_price)
	{
		//FILTRADO DE VUELOS TODO - And Tasas > -2 => No se inserta los que dio error 452.
		//TASA > 0
		//SELECCIONA DE am_tmp_result
		$arr_where = ["Orig" => $orig, "Dest" => $dest, "Fecha" => $fecha];
		$query_get_tmp_res = "SELECT * From am_tmp_result where Orig = '{$orig}' and Dest = '{$dest}' and Fecha = '{$fecha}' and Tasas > 0
		ORDER BY Cabina, 
		CASE
			WHEN Millas * {$mile_price}  + Tasas < SM * {$mile_price} + Money + Tasas THEN Millas * {$mile_price}  + Tasas
			ELSE SM * {$mile_price} + Money + Tasas
		END,
		Duracion;";

		$tmp_result = $this->db->query($query_get_tmp_res)->result();
		$c_cabin = "";
		$minDuracion = $tmp_result[0]->Duracion;
		foreach ($tmp_result as $key => $tmp) {
			if ($tmp->Cabina != $c_cabin || $tmp->Duracion < $minDuracion) {
				$c_cabin = $tmp->Cabina;
				$minDuracion = $tmp->Duracion;
			} else if ($tmp->Escalas > 0 && $tmp->Duracion >= $minDuracion) {
				unset($tmp_result[$key]);
				continue;
			}
		}
		//INSERTA VUELOS FILTRADOS
		$this->db->delete("am_flight_result", $arr_where);
		if (sizeof($tmp_result) > 0) {
			$this->db->insert_batch("am_flight_result", $tmp_result);
		}
		//NO HACE FALTA BORRAR ACA => AUNQUE PUEDE HACERSE
		$this->db->query("INSERT into am_taxed_error select * from am_tmp_result where Orig = '{$orig}' and Dest = '{$dest}' and Fecha = '{$fecha}' and Tasas <= 0");
		$this->db->delete("am_tmp_result", $arr_where);
		$this->insertResponse($op_id, $orig, $dest, $fecha, 1);
	}

	public function processClientTaxFlight($fare_tax, $uid)
	{
		if ($fare_tax->RealTax > 0) {
			$this->db->replace("am_fare_tax", $fare_tax);
		} else {
			$this->db->update("am_tmp_result", array("TaxLoaded" => 1, "Tasas" => $fare_tax->RealTax), array("Uid" => $uid));
			//borra el am_fare_tax??
			//updatea el vuelo por uid a taxloaded?
		}
	}

	private function updateAirlineTax(&$tf)
	{
		$arr_where = ["AirCode" => $tf->AirCode, "Escalas" => $tf->Escalas, "AirlineTax" => $tf->AirlineTax];
		if ($this->config->item('use_origdest_tax')) {
			$arr_where += ["Orig" => $tf->Orig, "Dest" => $tf->Dest];
		}
		$fare_tax = $this->db->get_where("am_fare_tax", $arr_where)->row();

		if ($fare_tax == null) {
			//SI NO EXISTE FARE TAX
			$fare_tax = new stdClass();
			$fare_tax->Orig = $tf->Orig;
			$fare_tax->Dest = $tf->Dest;
			$fare_tax->AirCode = $tf->AirCode;
			$fare_tax->Escalas = $tf->Escalas;
			$fare_tax->AirlineTax = $tf->AirlineTax;

			//INSERTA TEMPORAL PARA NO REALIZAR NUEVA BUSQUEDA
			$this->db->insert("am_fare_tax", $fare_tax);

			//BUSCA FARE TAX SYNC Y ACTUALIZA
			$tax = $this->HelperModel->searchSmilesTax($this->op_id, $tf->Uid, $tf->FareUid);
			$tf->Tasas = $tax->total;
			$tf->TaxLoaded = 1;
			$datelog = date("Y-m-d H:i:s");
			if ($tax->total > -2) {
				$this->db->query("UPDATE am_fare_tax set RealTax = {$tax->total}, Status = 1, Datelog = '{$datelog}' 
							WHERE AirCode = '{$tf->AirCode}' and Escalas = {$tf->Escalas}  and AirlineTax = '{$tf->AirlineTax}' and Orig = '{$tf->Orig}' and Dest = '{$tf->Dest}'");
			} else {
				//lo borra porque lo inserto antes para updatear y no pude.. no es que elimina algo ya cargado.
				$this->db->delete("am_fare_tax", $arr_where);
			}
		} else if ($fare_tax->Status == 1) {
			//SI EXISTE CON STATUS == 1 ACTUALIZA FLIGHT => esto esta al pedo porque lo hace en el update all
			$tf->Tasas = $fare_tax->RealTax;
			$tf->TaxLoaded = 1;
		} else {
			//SINO EXISTE INSERTA EN PENDING TAX
			$this->db->query("insert ignore into am_tmp_pending_tax values ('{$tf->Uid}')");
		}
	}

	public function listFlightResults($op_id, $rol_id, $ways, $fDesde, $fHasta, $mile_price, $sm, $dur = null, $cab = null, $esc = null, $asi = null, $airline = null, $tot = null)
	{
		//hacer que si viene $sm = 0 no haga el case
		$where_clause = "WHERE Tasas > -3";
		$where_clause .= " AND " . $this->getWhereClause($ways, true, "fr.");
		if ($dur != null) {
			$where_clause .= " and Duracion <= $dur * 60";
		}
		if ($esc != null) {
			$where_clause .= " and Escalas <= $esc";
		}
		if ($asi != null) {
			$where_clause .= " and Asientos >= $asi";
		}
		if ($cab != null) {
			$where_clause .= " and Cabina = '$cab'";
		}

		if ($airline != null) {
			$where_clause .= " and AirCode = '$airline'";
		}

		$where_clause .= " AND fr.Fecha >= '" . $fDesde . "' AND fr.Fecha <= '" . $fHasta . "'";
		if ($sm == 1) {
			$case_string = "CASE
								WHEN Millas * {$mile_price}  + Tasas < SM * {$mile_price} + Money + Tasas THEN Millas * {$mile_price}  + Tasas
								ELSE SM * {$mile_price} + Money + Tasas
							END";
		} else {
			$case_string = "Millas * {$mile_price}  + Tasas";
		}
		if ($tot != null) {
			$where_clause .= " and ({$case_string}) <= {$tot}";
		}
		if ($rol_id == 1) {
			$query = "SELECT fr.*, of.Op_Id, {$case_string} as Total FROM am_flight_result fr left join am_op_favorite of on of.Flight_Id = fr.Id {$where_clause}
				AND (of.Op_Id = {$op_id} or of.Op_Id is null) order by Total, Duracion, Salida";
		} else {
			$query = "SELECT fr.*, of.Op_Id, {$case_string} as Total FROM am_flight_result fr 
			inner join am_op_search_response os on os.Op_Id = {$op_id}  and os.Orig = fr.Orig and os.Dest = fr.Dest and os.Fecha = fr.Fecha
			left join am_op_favorite of on of.Flight_Id = fr.Id {$where_clause}
			AND (of.Op_Id = {$op_id} or of.Op_Id is null) order by Total, Duracion, Salida";
		}
		$ret = $this->db->query($query)->result();
		$this->setPercentilValues($ret);
		return $ret;
	}

	private function setPercentilValues(&$flights)
	{
		// Agrupar vuelos por cabina y recolectar los precios de vuelo por cabina
		$preciosPorCabina = [];
		foreach ($flights as $flight) {
			$cabina = $flight->Cabina;
			$preciosPorCabina[$cabina][] = $flight->Total;
		}

		// Calcular el precio típico (referencia) para cada cabina
		$preciosTipicosPorCabina = [];
		foreach ($preciosPorCabina as $cabina => $precios) {
			// Calcular el promedio ponderado por la cantidad de vuelos en cada grupo de precios
			$totalPrecios = array_sum($precios);
			$cantidadVuelos = count($precios);
			$precioTipico = $totalPrecios / $cantidadVuelos;

			// Almacenar el precio típico para la cabina actual
			$preciosTipicosPorCabina[$cabina] = $precioTipico;
		}

		// Asignar percentiles a cada vuelo de la cabina basado en la diferencia entre su precio y el precio típico
		foreach ($flights as &$flight) {
			$cabina = $flight->Cabina;
			$total = $flight->Total;

			// Obtener el precio típico para la cabina del vuelo actual
			$precioTipico = $preciosTipicosPorCabina[$cabina];

			// Calcular la diferencia entre el precio del vuelo y el precio típico
			$diferencia = $total - $precioTipico;
			if ($precioTipico == 0) {
				$flight->Percentil = 1;
				continue;
			}

			// Ajustar la diferencia al rango del 1 al 4 para los percentiles
			$rango = max(1, min(4, ceil($diferencia / ($precioTipico / 4) + 2)));

			// Asignar el percentil al vuelo
			$flight->Percentil = $rango;
		}

		return $flights;
	}

	public function getFavorites($op_id)
	{
		$query = "SELECT fr.*, TIMESTAMPDIFF(HOUR, fr.Datelog, NOW()) as Age FROM am_flight_result fr inner join am_op_favorite of on of.Flight_Id = fr.Id where Op_Id = {$op_id} order by Orig, Dest";
		$favs = $this->db->query($query)->result();
		$hash = [];
		foreach ($favs as $f) {
			$hash[$f->Orig . "-" . $f->Dest][] = $f;
		}
		return $hash;
	}
	public function resetNotLoadedTaxes($ways)
	{
		foreach ($ways as $w) {
			$this->db->delete("am_fare_tax", array("Orig" => $w["orig"], "Dest" => $w["dest"], "Status" => 0));
		}
	}


	public function listFlightsByOrigDest($orig, $dest, $fdesde = null, $fhasta = null)
	{
		$this->db->order_by("Total, Duracion, Salida");
		return $this->db->get_where("am_flight_result", array("Orig" => $orig, "Dest" => $dest, "Fecha >=" => $fdesde, "Fecha <=" => $fhasta))->result();
	}

	public function insertResponse($op_id, $orig, $dest, $fecha, $status)
	{
		$r = new stdClass();
		$r->Orig = $orig;
		$r->Dest = $dest;
		$r->Fecha = $fecha;
		$r->Status_Id = $status;
		$sql = $this->db->insert_string("am_search_response", $r);
		$sql .= " on duplicate key update Datelog = CURRENT_TIMESTAMP, Status_Id = {$status}";
		$this->db->query($sql);
		if ($status == 1) {
			unset($r->Status_Id);
			$r->Op_Id = $op_id;
			$sql = $this->db->insert_string("am_op_search_response", $r);
			$sql .= " on duplicate key update Datelog = CURRENT_TIMESTAMP";
			$this->db->query($sql);
			$this->updateOpRequest($op_id, 1);
		} else {
			$this->db->delete("am_flight_result", array("Orig" => $orig, "Dest" => $dest, "Fecha" => $fecha));
		}
	}

	public function updateOpRequest($op_id, $req)
	{
		$this->db->query("Insert into am_op_request VALUES ($op_id, CURDATE(), {$req}) on duplicate key update TotalRequest = TotalRequest + {$req}");
	}

	//VIEW EXCLUSIVE METHODS
	public function getPeriods($op_id, $get_all, $orig, $dest, $min_days = null)
	{
		$min_days_having = $min_days > 0 ? "HAVING duracion >= {$min_days}" : "";
		$tbl = $get_all ? "am_search_response" : "am_op_search_response";
		$where_op = $get_all == 1 ? "1 = 1" : "Op_Id = {$op_id}";

		$int_hours = $this->SmilesModel->getConfig($this->config->item("id_interval"));
		$query = "SELECT MIN(Fecha) AS fecha_inicio, MAX(Fecha) AS fecha_fin, COUNT(*) AS duracion
					FROM (
    					SELECT Fecha, Fecha - INTERVAL @rn := @rn + 1 DAY AS grp, IF(@prev + INTERVAL 1 DAY = Fecha, @group_id, @group_id := @group_id + 1) AS group_id, @prev := Fecha
    					FROM (SELECT @group_id := 0, @prev := NULL, @rn := 0) AS vars,
        					(SELECT Fecha FROM {$tbl} am  where {$where_op} and Orig = '{$orig}' and Dest = '{$dest}' and Fecha > CURDATE() and Timestampdiff(HOUR, Datelog, Now()) < {$int_hours} ORDER BY Fecha) AS t
						) AS subquery						
						GROUP BY group_id	
						{$min_days_having}					
						ORDER BY fecha_inicio";
		return $this->db->query($query)->result();
	}

	public function getAdminLongestPeriod($orig, $dest)
	{
		$int_hours = $this->SmilesModel->getConfig($this->config->item("id_interval"));
		$query = "SELECT MIN(Fecha) AS fecha_inicio, MAX(Fecha) AS fecha_fin, COUNT(*) AS duracion
					FROM (
    					SELECT Fecha, Fecha - INTERVAL @rn := @rn + 1 DAY AS grp, IF(@prev + INTERVAL 1 DAY = Fecha, @group_id, @group_id := @group_id + 1) AS group_id, @prev := Fecha
    					FROM (SELECT @group_id := 0, @prev := NULL, @rn := 0) AS vars,
        					(SELECT Fecha FROM am_search_response am  where Orig = '{$orig}' and Dest = '{$dest}' and Timestampdiff(HOUR, Datelog, Now()) < {$int_hours} ORDER BY Fecha) AS t
						) AS subquery
						GROUP BY group_id
						ORDER BY duracion DESC
					LIMIT 1;";
		return $this->db->query($query)->row();
	}

	public function listViewSearches($mile_price)
	{
		$ms = $this->db->query("select os.*, v.Des, 1 as HasView from am_op_search os inner join am_view_search v on v.Search_Id = os.Search_Id and v.Status_Id = 1 order By os.Datelog DESC")->result();
		return $this->commonOpSearch($ms, $mile_price, 8, null);
	}

	private function populateSearch(&$s, $mile_price, $rol_id, $op_id = null)
	{
		$first_flight = "2030-12-31";
		$last_flight = "2024-01-01";
		$last_update = "2030-12-31";
		$cheapest[1] = 10000000;
		$cheapest[2] = 10000000;

		$not_valid_update = "2030-12-31";
		$not_valid_cheapest[1] = 10000000;
		$not_valid_cheapest[2] = 10000000;
		$not_valid_first = "2030-12-31";
		$not_valid_last = "2024-01-01";
		$s->Idas = array();
		$s->Vueltas = array();
		$query = "SELECT sc.*, ac.City as OrigCity, ac1.City as DestCity from am_op_search_child sc inner join am_codes ac on ac.Air_Code = sc.Orig 
							inner join am_codes ac1 on ac1.Air_Code = sc.Dest where Search_Id = '{$s->Search_Id}'";

		$childs = $this->db->query($query)->result();
		//Procesa CHILDS y PERIODS de Cada Child
		$s->Valid = false;
		$get_all = $rol_id == 1 || $s->HasView;
		foreach ($childs as $c) {
			if ($c->Tipo == 1) {
				$s->Idas[] = $c;
			} else {
				$s->Vueltas[] = $c;
			}
			//Si no es admin o view todos los periodos deben ser validos. De otro modo no devuelve
			$s->Valid = $get_all ? $s->Valid : false;
			$c->periods = $this->getPeriods($op_id, $get_all, $c->Orig, $c->Dest, 3);
			if ($c->periods != null) {
				$s->Valid = true;
				$first_flight = $c->Tipo == 1 ? min($c->periods[0]->fecha_inicio, $first_flight) : $first_flight;
				$last_flight = $c->Tipo == 2 ? max($c->periods[count($c->periods) - 1]->fecha_fin, $last_flight) : $last_flight;

				foreach ($c->periods as $p) {
					$pop_res = $this->populateHelper($mile_price, $c->Tipo, $c->Orig, $c->Dest, $p->fecha_inicio, $p->fecha_fin);
					$last_update = min($pop_res->min_date, $last_update);
					$cheapest[$c->Tipo] = min($pop_res->min_price, $cheapest[$c->Tipo]);
				}
			} else if ($rol_id == 1) { //Me agarra periodos no validos pintados en grises para obtener la informacion.
				$pop_res = $this->populateHelper($mile_price, $c->Tipo, $c->Orig, $c->Dest);
				$not_valid_update = min($pop_res->min_date, $not_valid_update);
				$not_valid_cheapest[$c->Tipo] = min($pop_res->min_price, $not_valid_cheapest[$c->Tipo]);
				$not_valid_first = min($pop_res->min_ida, $not_valid_first);
				$not_valid_last = max($pop_res->max_vta, $not_valid_last);
			}
		}

		if ($s->Valid) {
			$s->MinIda = $cheapest[1];
			$s->MinVta = count($s->Vueltas) > 0 ? $cheapest[2] : 0;
			$s->LastUpdate = $last_update ?? $s->Datelog;
			$s->FDesde = $first_flight;
			$s->FHasta = $last_flight;
		} else {
			$s->MinIda = $not_valid_cheapest[1];
			$s->MinVta = count($s->Vueltas) > 0 ? $not_valid_cheapest[2] : 0;
			$s->LastUpdate = $not_valid_update ?? $s->Datelog;
			$s->FDesde = $not_valid_first;
			$s->FHasta = $not_valid_last;
		}
	}

	private function populateHelper($mile_price, $tipo, $orig, $dest, $inicio = null, $fin = null)
	{
		$fecha_where = "";
		$pop_res = new stdClass();
		$pop_res->min_ida = "2030-12-31";
		$pop_res->max_vta = "2024-01-01";
		if ($inicio != null) {
			$fecha_where = "and Fecha >= '{$inicio}' and Fecha <= '{$fin}'";
		} else if ($tipo == 1) {
			$ff = $this->db->query("Select Min(Fecha) as F_Ida from am_flight_result where Orig ='{$orig}'  and Dest = '{$dest}' and Fecha >= CURDATE() ")->row();
			$pop_res->min_ida = $ff->F_Ida;
		} else if ($tipo == 2) {
			$ff = $this->db->query("Select Max(Fecha) as F_Vta from am_flight_result where Orig ='{$orig}'  and Dest = '{$dest}' and Fecha >= CURDATE() ")->row();
			$pop_res->max_vta = $ff->F_Vta;
		}

		$fl_min_price = $this->db->query("SELECT Min(Millas * {$mile_price} + Tasas) As MinPrice,  Min(SM * {$mile_price} + Tasas + Money) as MinSM from am_flight_result 
										where Orig = '{$orig}' and Dest = '$dest' {$fecha_where}")->row();
		$fl_min_date = $this->db->query("SELECT Min(Datelog) as LastUpdate from am_flight_result where Orig = '{$orig}' and Dest = '$dest' {$fecha_where}")->row();


		$pop_res->min_price = min($fl_min_price->MinPrice, $fl_min_price->MinSM);
		$pop_res->min_date = $fl_min_date->LastUpdate;
		return $pop_res;
	}
}
