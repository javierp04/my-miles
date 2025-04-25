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

class View extends Common
{
	function __construct()
	{
		parent::__construct();
		$op = $this->session->get_userdata();
		$this->op_id = $op["Op_Id"];
		if ($this->op_id == null) {
			//Si no esta logueado loguea el usuario de free view en caso de estar habilitado
			if ($this->config->item('is_free_view')) {
				$this->op_id = $this->config->item("view_op_id");
				$op = $this->OperatorModel->getOperatorById($this->op_id);				
				$user_configs = $this->OperatorModel->getUserConfig($this->op_id);
				$this->session->set_userdata((array)$op);
				$this->session->set_userdata('mile_price',  $user_configs->Mile_Price);
			} else {
				redirect(base_url() . "start/not_validated");
			}
		}
		$this->rol_id = $this->session->userdata("Rol_Id");
	}

	public function results($my_search, $from_search = null)
	{		
		$mile_price = $this->input->post("mile_price") ?? $this->session->userdata('mile_price');
		$rol = $this->HelperModel->getRol($this->rol_id);
		session_write_close();

		//MUESTRA LOS RESULTADOS PARA UN OP_SEARCH ID
		$data = array();
		$data["user_request_count"] = $this->HelperModel->getUserRequests($this->op_id) ?? 0;
		$data["total_requests"] = $rol->Req_Day;
		if ($my_search > 0) {
			$search = $this->SmilesModel->getSearch("VIEW", $my_search, $mile_price, $this->rol_id, $this->op_id);			
			if ($search != null) {				
				if ($search->FDesde == null || $search->FHasta == null) {
					redirect(base_url() . "view");
					exit;
				}
				$idas = array();
				$vueltas = array();

				foreach ($search->Idas as $ida) {
					$i = array();
					$i["orig"] = $ida->Orig;
					$i["dest"] = $ida->Dest;
					$idas[] = $i;
				}

				foreach ($search->Vueltas as $vuelta) {

					$i = array();
					$i["orig"] = $vuelta->Orig;
					$i["dest"] = $vuelta->Dest;					
					$vueltas[] = $i;
				}


				$data["t_idas"] = $idas;
				$data["t_vueltas"] = $vueltas;				
				$data["fdesde"] = $search->FDesde;
				$data["fhasta"] = $search->FHasta;
				$data["from_search"] = $from_search;
				$data["is_mobile"] = $this->is_mobile;
				$data["rol_id"] = $this->rol_id;

				$this->load->view('sm_view', $data);
			} else {
				redirect(base_url() . "view");
			}
		}
	}

	public function index()
	{
		$mile_price = $this->session->userdata('mile_price');
		$rol = $this->HelperModel->getRol($this->rol_id);

		session_write_close();
		$searches = $this->SmilesModel->listViewSearches($mile_price);
		$data = array();
		$data["is_mobile"] = $this->is_mobile;
		$data["searches"] = $searches;
		$data["total_requests"] = $rol->Req_Day;
		$data["user_request_count"] = $this->HelperModel->getUserRequests($this->op_id) ?? 0;
		$data["is_free_view"] = $this->op_id == $this->config->item("view_op_id");
		$this->load->view('view_search', $data);
	}

	public function ax_dismiss($a) {
		$this->session->set_userdata("alert{$a}", 1);		
	}

	public function ax_list_results($listType, $tax_res = null) {
		$this->do_ax_list_results($listType, $this->op_id, 1, $tax_res);
	}
}