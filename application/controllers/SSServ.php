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

class SSServ extends CI_Controller
{
    public function cronAll() {
        $this->cronDelSRStatusZero();
        $this->cronDelOldFlights();
        $this->cronDelOldFechaSR();
        $this->cronDelOldTax();        
    }
    public function cronDelSRStatusZero()
    {
        $this->db->query("DELETE FROM am_search_response WHERE TIMESTAMPDIFF(MINUTE, datelog, NOW()) > 2 and Status_Id = 0");
        echo "  ok 1  ";
    }

    public function cronDelOldFlights()
    {
        $this->db->query("DELETE FROM am_flight_result WHERE TIMESTAMPDIFF(HOUR, datelog, NOW()) > 72");
        $this->db->query("DELETE FROM am_flight_result WHERE Fecha < CURDATE()");
        echo "  ok 2  ";
    }
    public function cronDelOldTax()
    {
        $this->db->query("DELETE FROM am_fare_tax WHERE TIMESTAMPDIFF(HOUR, datelog, NOW()) > 120");
        echo "  ok 3  ";
    }
    

    public function cronDelOldFechaSR()
    {
        $this->db->query("DELETE FROM am_search_response where Fecha < CURDATE()");
        $this->db->query("DELETE FROM am_op_search_response where Fecha < CURDATE()");
        echo "  ok  4";
    }
}
