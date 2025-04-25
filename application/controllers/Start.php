<?php
defined('BASEPATH') or exit('No direct script access allowed');

use GuzzleHttp\Client;

/** 
 * @property CI_DB_mysqli_driver $db 
 * @property CI_Loader $load
 * @property CI_Input $input
 * @property CI_Session $session
 * @property CI_Config $config
 * @property OperatorModel $OperatorModel
 * @property HelperModel $HelperModel
 */

class Start extends CI_Controller
{

	function __construct()
	{

		parent::__construct();
		$this->load->model("OperatorModel");
		$this->load->model("HelperModel");
	}

	public function index()
	{

		if ($this->config->item('is_free_view')) {
			redirect(base_url() . "view");
		} else {
			$this->login();
		}
	}


	public function login()
	{
		session_destroy();
		$this->load->view('login');
	}

	public function signup()
	{
		session_destroy();
		$this->load->view('signup');
	}

	public function sign_up_process()
	{
		if ($this->input->post()) {
			$op = new stdClass();
			$op->Op_Email = $this->input->post("email");
			$op->Op_Name = $this->input->post("name");
			$op->Rol_Id = 9;
			$op->Status_Id = 1;
			$op->Password = md5($this->input->post("password"));
			$exists = $this->db->get_where("operator", array("Op_Email" => $op->Op_Email))->row();
			if (!$exists) {
				$loged_op = $this->OperatorModel->insertOperator($op);
				$this->session->set_userdata((array)$loged_op);
				$rol = $this->HelperModel->getRol($op->Rol_Id);
				$this->session->set_userdata("UseServer", $rol->UseServer);
				echo "ok";
			} else {
				echo "El E-Mail ya se encuentra registrado";
			}
		}
	}

	public function rol_disabled()
	{
		if ($this->config->item('is_free_view')) {
			redirect(base_url() . "view");
		} else {
			session_destroy();
			$data["msg"] = "El buscador se encuentra temporalmente deshabilitado en mantenimiento.";
			$this->load->view('login', $data);
		}
	}

	public function not_validated()
	{
		if ($this->config->item('is_free_view')) {
			redirect(base_url() . "view");
		} else {
			session_destroy();
			$data["msg"] = "El usuario no se encuentra validado. Vuela a iniciar sesión.";
			$this->load->view('login', $data);
		}
	}

	function test_curl()
	{
		$url = 'https://api-air-flightsearch-green.smiles.com.br/v1/airlines/search?adults=1&cabinType=all&children=0&currencyCode=ARS&departureDate=2025-06-12&destinationAirportCode=BCN&infants=0&isFlexibleDateChecked=false&originAirportCode=EZE&tripType=2&forceCongener=true&r=ar';
	
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
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$error = curl_error($ch);
		
		curl_close($ch);
	
		header('Content-Type: application/json');
		
		if ($response === false) {
			echo json_encode([
				'error' => 'Curl error',
				'details' => $error,
				'http_code' => $httpCode
			]);
		} else {
			echo $response;
		}
	}

	public function raw_search($orig = null, $dest = null, $fecha = null)
	{
		
		//$r = $this->HelperModel->do_searchSmilesAPI($orig, $dest, $fecha);
		//echo $r->response;

		$url = 'https://api-air-flightsearch-green.smiles.com.br/v1/airlines/search';
		$params = [
			'adults' => 1,
			'cabinType' => 'all',
			'children' => 0,
			'currencyCode' => 'ARS',
			'departureDate' => '2025-07-01',
			'destinationAirportCode' => 'MIA',
			'infants' => 0,
			'isFlexibleDateChecked' => 'false',
			'originAirportCode' => 'BUE',
			'tripType' => 2,
			'forceCongener' => 'true',
			'r' => 'ar',
		];

		// Construir la URL completa con los parámetros
		$url .= '?' . http_build_query($params);

		$headers = [
			'accept: */*',
			'accept-language: es-419,es-US;q=0.9,es;q=0.8,en-US;q=0.7,en;q=0.6',
			'authorization: Bearer lw9orb0kiur8Yhunkb4y7SNYz25C2iamK5awiF7eWgQI6jK17jqbT9',
			'channel: Web',
			'content-type: application/json',
			'language: es-ES',
			'origin: http://miles.local',
			'priority: u=1, i',
			'referer: http://miles.local/',
			'region: ARGENTINA',
			'sec-ch-ua: "Not/A)Brand";v="99", "Chromium";v="115", "Google Chrome";v="115"',
			'sec-ch-ua-mobile: ?0',
			'sec-ch-ua-platform: "Windows"',
			'sec-fetch-dest: empty',
			'sec-fetch-mode: cors',
			'sec-fetch-site: cross-site',
			'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
			'x-api-key: aJqPU7xNHl9qN3NVZnPaJ208aPo2Bh2p2ZV844tw',
		];

		// Inicia una nueva sesión cURL
		$ch = curl_init();

		// Establecer la URL y otras opciones necesarias
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);

		// Habilitar la solicitud OPTIONS explícitamente
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'OPTIONS');
		curl_setopt($ch, CURLOPT_HEADER, true); // Incluir headers en la respuesta

		// Ejecutar la solicitud OPTIONS y obtener la respuesta
		$response = curl_exec($ch);

		// Verificar errores
		if ($response === false) {
			echo 'Error de cURL: ' . curl_error($ch);
		} else {
			// Imprimir la respuesta de la solicitud OPTIONS
			echo "Respuesta de OPTIONS:\n" . $response . "\n";

			// Extraer el código de estado de la respuesta
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			echo "Código de estado de OPTIONS: " . $http_code . "\n";

			// Si la solicitud OPTIONS fue exitosa, continuar con la solicitud GET
			if ($http_code == 204) {
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
				$response = curl_exec($ch);

				if ($response === false) {
					echo 'Error de cURL: ' . curl_error($ch);
				} else {
					// Imprimir la respuesta de la solicitud GET
					echo "Respuesta de GET:\n" . $response . "\n";
				}
			}
		}

		// Cerrar la sesión cURL
		curl_close($ch);
	}

	public function mp_ok()
	{
		require_once 'vendor/autoload.php';

		$payment_id = $_GET['payment_id'];
		$req_order_id = $_GET['merchant_order_id'];

		MercadoPago\SDK::setAccessToken($this->config->item('mp_access_token'));
		$payment = MercadoPago\Payment::get($payment_id);
		if ($payment == null || $payment->order->id != $req_order_id) {
			echo "INVALIDO - SALI DE ACA BOBO";
			exit;
		}

		echo '<pre>';
		print_r($payment);
		echo '</pre>';
	}

	public function mp_fail()
	{
		echo "MP FAIL";
	}

	public function mp_test()
	{
		require_once 'vendor/autoload.php';

		MercadoPago\SDK::setAccessToken($this->config->item('mp_access_token'));
		$preference = new MercadoPago\Preference();
		$preference->back_urls = array(
			"success" => base_url() . "start/mp_ok",
			"failure" => base_url() . "start/mp_fail",
			"pending" => base_url() . "start/mp_fail",
		);
		$productos = [];
		$item = new MercadoPago\Item();
		$item->title = "Acceso a Resultados de Búsqueda Semanal";
		$item->quantity = 1;
		$item->unit_price = 1000;
		array_push($productos, $item);
		$preference->items = $productos;
		$preference->auto_return = "approved";
		$preference->binary_mode = true;
		$preference->save();

		$data["preference"] = $preference;
		$this->load->view('mp-test', $data);
	}

	public function login_process()
	{

		if ($this->input->post()) {
			$email = $this->input->post('inputEmail');
			$password = md5($this->input->post('inputPassword'));
			$operator = $this->OperatorModel->loginOperator($email, $password);
			if ($operator != null) {
				$this->session->set_userdata((array)$operator);
				$rol = $this->HelperModel->getRol($operator->Rol_Id);
				$this->session->set_userdata("UseServer", $rol->UseServer);

				if ($operator->DisabledUntil < date("Y-m-d")) {
					$this->session->set_userdata("user_disabled", 1);
				}
				echo "ok";
			} else {
				echo "El usuario o la contraseña es inválido"; // wrong details 
			}
		}
	}

	public function logout()
	{
		session_destroy();
		redirect(base_url());
	}

	public function header_test()
	{

		echo "<pre>";
		echo htmlspecialchars(print_r(getallheaders(), true));
		echo "\n\n\n";
		$response_headers = headers_list();
		echo htmlspecialchars(print_r($response_headers, true));
	}

	public function puppet_test($param = null, $prx = null)
	{

		$headers = array(
			"Accept: application/json, text/plain, */*",
			"Accept-Encoding: gzip, deflate, br",
			"Accept-Language: es-US,es;q=0.9,en-US;q=0.8,en;q=0.7,es-419;q=0.6",
			"Authorization: Bearer XmqTzRUOHXAdqEKg9l21Z8A8Dbz2Ybfww9QKnAXEeY3s3mmd0luCZ4",
			"Channel: Web",
			"Language: es-ES",
			"Origin: https://www.smiles.com.ar",
			"Referer: https://www.smiles.com.ar/",
			"Region: ARGENTINA",
			"Sec-Ch-Ua: \"Not A(Brand\";v=\"99\", \"Google Chrome\";v=\"121\", \"Chromium\";v=\"121\"",
			"Sec-Ch-Ua-Mobile: ?0",
			"Sec-Ch-Ua-Platform: \"Windows\"",
			"Sec-Fetch-Dest: empty",
			"Sec-Fetch-Mode: cors",
			"Sec-Fetch-Site: cross-site",
			"User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36",
			"X-Api-Key: aJqPU7xNHl9qN3NVZnPaJ208aPo2Bh2p2ZV844tw"
		);

		require 'vendor/autoload.php';
		$client = new Client();

		$url = "https://api-airlines-boarding-tax-prd.smiles.com.br/v1/airlines/flight/boardingtax?adults=1&children=0&infants=0&fareuid=648d2ef9e6&uid=43648d2ef9e6537ae6f4&type=SEGMENT_1&highlightText=SMILES_CLUB";
		//$url = "https://api-air-flightsearch-prd.smiles.com.br/v1/airlines/search?adults=1&cabinType=all&children=0&currencyCode=ARS&departureDate=2024-09-20&destinationAirportCode=MIA&infants=0&isFlexibleDateChecked=false&originAirportCode=BUE&tripType=2&forceCongener=true&r=ar";
		//$url = "https://ifconfig.me/";
		///$url = "https://my-miles.online/start/header_test";
		
		if ($prx) {
			$proxy = "http://{$prx}";
		}		

		$requestBody = [
			'url' => $url,
			'headers' => [
				'Region' => 'ARGENTINA',
				'Language' => 'es-ES',
				'sec-ch-ua-platform' => '"Windows"',
				'Authorization' => 'Bearer XmqTzRUOHXAdqEKg9l21Z8A8Dbz2Ybfww9QKnAXEeY3s3mmd0luCZ4',
				'Accept-Language' => 'es-419',
				'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Safari/537.36',
				'x-api-key' => 'aJqPU7xNHl9qN3NVZnPaJ208aPo2Bh2p2ZV844tw'
			]
		];

		// Añadir el proxy al request body si está definido
		if ($proxy) {
			$requestBody['proxy'] = $proxy;
		}

		$response = $client->post('http://localhost:3000/fetch', [
			'json' => $requestBody
		]);

		$body = $response->getBody()->getContents();
		echo $body;
	}

	private function require_all()
	{
		require_once APPPATH . "libraries/Mercadopago/SDK.php";
		require_once APPPATH . "libraries/Mercadopago/RestClient.php";
		require_once APPPATH . "libraries/Mercadopago/Http/HttpRequest.php";
		require_once APPPATH . "libraries/Mercadopago/Http/CurlRequest.php";
		require_once APPPATH . "libraries/Mercadopago/Config/AbstractConfig.php";
		require_once APPPATH . "libraries/Mercadopago/Config/Config.php";
		require_once APPPATH . "libraries/Mercadopago/Manager.php";
		require_once APPPATH . "libraries/Mercadopago/MetaDataReader.php";
	}

	// Llamada a la función para incluir todos los archivos PHP dentro del directorio

}
