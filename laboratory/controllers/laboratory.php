<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
  --------------------------------------------------------------------------------
  HHIMS - Hospital Health Information Management System
  Copyright (c) 2011 Information and Communication Technology Agency of Sri Lanka
  <http: www.hhims.org/>
  ----------------------------------------------------------------------------------
  This program is free software: you can redistribute it and/or modify it under the
  terms of the GNU Affero General Public License as published by the Free Software
  Foundation, either version 3 of the License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,but WITHOUT ANY
  WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
  A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.

  You should have received a copy of the GNU Affero General Public License along
  with this program. If not, see <http://www.gnu.org/licenses/>
  ----------------------------------------------------------------------------------
  Date : June 2016
  Author: Mr. Jayanath Liyanage   jayanathl@icta.lk

  Programme Manager: Shriyananda Rathnayake
  URL: http://www.govforge.icta.lk/gf/project/hhims/
  ----------------------------------------------------------------------------------
 */

class Laboratory extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->checkLogin();
        $this->load->library('session');
        if (isset($_GET["mid"])) {
            $this->session->set_userdata('mid', $_GET["mid"]);
        }
    }

    public function index($ops = null) {
        //$this->load->view('patient');
        $new_page = base_url() . "index.php/search/lab_orders/";
        header("Status: 200");
        header("Location: " . $new_page);
    }

    public function receive_sample() {
        $data = array();
        $this->load->vars($data);
        $this->load->view('lab_receive_view');
    }
    
        public function cancel_test($lab_order_id) {
        $this->load->model('mpersistent');
	$st=$this->mpersistent->update("lab_order", "LAB_ORDER_ID",$lab_order_id,array("Status"=>"Cancelled"));
        $new_page = base_url() . "index.php/".$_GET['CONTINUE'];
        header("Status: 200");
        header("Location: " . $new_page);

        
    }

    public function update_sample() {
        $lid = $this->input->post("lid");
        if (!isset($lid) || (!is_numeric($lid) || ($lid <= 0))) {
            echo -1;
            return;
        }
       
        $this->load->model('mpersistent');
       // $this->load->model('mappointment');
        $lab = $this->mpersistent->open_id($lid, "lab_order", "LAB_ORDER_ID");
        if (empty($lab)) {
            echo -3;
            return;
        }
        
       /* $sve_data = array(
            "Status" => "Received",
            "ReceivedUser" => $this->session->userdata("FullName"),
            "ReceivedDate" => date("Y-m-d H:i:s")
            
        );
     
        $status = $this->mpersistent->update('lab_order','LAB_ORDER_ID' , $lid,$sve_data );
        echo $status;
        return;*/
        $status = $this->mpersistent->update_laborder($lid);
        echo $status;
        return;
        
        
    }

    public function get_lab_test($group = null) {
        if (!$group) {
            echo '[]';
            return;
        }
        $this->load->model('mlaboratory');
        $data["list"] = $this->mlaboratory->get_lab_test(urldecode($group));
        $json = json_encode($data["list"]);
        echo $json;
    }
    
        public function result_seen($lab_order_id = null) {
        if (!$lab_order_id) {
            echo '[]';
            return;
        }
         $date = date("Y-m-d H:i:s");
        $this->load->model('mpersistent');
        $order = array(
            "ResultSeenTime" => $date,
            "ResultSeenBy" => $this->session->userdata("FullName")
        );
        $this->mpersistent->update("lab_order", "LAB_ORDER_ID", $lab_order_id, $order);
    }

    function update_order_sample() {
        //print_r($_POST);
        //exit;
        $this->load->model('mpersistent');
        $data["LAB_ORDER_ID"] = $this->input->post("LAB_ORDER_ID");
        
         if ($this->input->post("saveoption") != "next1") { // Save Button
            $order = array(
            "Collection_Status" => $this->input->post("Collection_Status"),
            "CollectBy" => $this->input->post("CollectBy")
        );
        }
        else{ // Print Button
             $order = array(
            "Collection_Status" => "Done",
            "CollectBy" => $this->input->post("CollectBy")
        );
            
        }
       
        $this->mpersistent->update("lab_order", "LAB_ORDER_ID", $this->input->post("LAB_ORDER_ID"), $order);

        if ($this->input->post("saveoption") != "next1") { // Save Button
            $new_page = base_url() . "index.php/search/sample_order";
            header("Status: 200");
            header("Location: " . $new_page);
        } else {
            $data["order_info"] = $this->mpersistent->open_id($data["LAB_ORDER_ID"], "lab_order", "LAB_ORDER_ID");
            if (!empty($data["order_info"])) {
                $data["patient_info"] = $this->mpersistent->open_id($data["order_info"]["PID"], "patient", "PID");
            }
            if($data["order_info"]["Dept"]=='ADM'){
                $data["adm_info"] = $this->mpersistent->open_id($data["order_info"]["OBJID"], "admission", "ADMID");
            }
            $this->load->vars($data);
            $this->load->view('lab_order_bar_code');
        }
    }

    public function save_result() {
        if (!isset($_POST)) {
            $data["error"] = "Order not saved";
            $this->load->vars($data);
            $this->load->view('lab_error');
            return;
        }
        //print_r($_POST);

        $this->load->model('mpersistent');
        $this->load->model('mlaboratory');
        if ($this->input->post("LAB_ORDER_ID")) {
            $data["orederd_test_list"] = $this->mlaboratory->get_ordered_lab_test($this->input->post("LAB_ORDER_ID"));
            //print_r($data);
            if (!empty($data["orederd_test_list"])) {
                for ($i = 0; $i < count($data["orederd_test_list"]);  ++$i) {
                    if ($this->input->post($data["orederd_test_list"][$i]["LAB_ORDER_ITEM_ID"])) {
                        //echo $this->input->post($data["orederd_test_list"][$i]["LAB_ORDER_ITEM_ID"])."<br>";
                        //update($table=null,$key_field=null,$id=null,$data)
                        $result = array(
                            "TestValue" => $this->input->post($data["orederd_test_list"][$i]["LAB_ORDER_ITEM_ID"]),
                            "Status" => "Available"
                        );
                        $this->mpersistent->update("lab_order_items", "LAB_ORDER_ITEM_ID", $data["orederd_test_list"][$i]["LAB_ORDER_ITEM_ID"], $result);
                    }
                }
                $this->mpersistent->update("lab_order", "LAB_ORDER_ID", $this->input->post("LAB_ORDER_ID"), array("Status" => "Available"));
            }
        }
        $new_page = base_url() . "index.php/" . $this->input->post("CONTINUE");
        header("Status: 200");
        header("Location: " . $new_page);
    }

    public function save_order() {
        if (!isset($_POST)) {
            $data["error"] = "Order not saved";
            $this->load->vars($data);
            $this->load->view('lab_error');
            return;
        }
        //print_r($_POST);
        //Array ( [Dept] => CLN [GroupName] => Chest x-rays [Priority] => [CONTINUE] => clinic/open/27 [OBJID] => 27 [PID] => 187 [LAB_ORDER_ID] => [52] => on [53] => on )
        if ($this->input->post("Priority") == "") {
            $Priority = "Normal";
        } else {
            $Priority = $this->input->post("Priority");
        }
        $this->load->model('mpersistent');
        if ($this->input->post("OrderDate") == NULL) {
            $odate = date("Y-m-d H:i:s");
        } else {
            $odate = $this->input->post("OrderDate");
        }
        $order = array(
            "Dept" => $this->input->post("Dept"),
            "OBJID" => $this->input->post("OPDID"),
            "PID" => $this->input->post("PID"),
            "OrderDate" => $odate,
            "OrderBy" => $this->session->userdata("FullName"),
            "Status" => "Pending",
            "Collection_Status" => "Pending",
            "TestGroupName" => $this->input->post("GroupName"),
            "Priority" => $Priority
        );
        $r = $this->mpersistent->create("lab_order", $order);
        if ($r) {
            if ($this->input->post("GroupName")) {
                $this->load->model('mlaboratory');
                $data["test_list"] = $this->mlaboratory->get_lab_test(urldecode($this->input->post("GroupName")));
                if (!empty($data["test_list"])) {
                    for ($i = 0; $i < count($data["test_list"]);  ++$i) {
                        if (isset($_POST[$data["test_list"][$i]["LABID"]]) && ($_POST[$data["test_list"][$i]["LABID"]] == "on")) {
                            $item = array(
                                "LAB_ORDER_ID" => $r,
                                "LABID" => $data["test_list"][$i]["LABID"],
                                "Status" => "Pending",
                                "Active" => 1
                            );
                            $this->mpersistent->create("lab_order_items", $item);
                        }
                    }
                    $new_page = $this->input->post("CONTINUE");
                    header("Status: 200");
                    header("Location: " . $new_page);
                }
            }
        }
        $data["error"] = "Order not saved";
        $this->load->vars($data);
        $this->load->view('lab_error');
        return;
    }

    public function process_result($order_id = null) {
        if (!isset($order_id) || (!is_numeric($order_id) )) {
            $data["error"] = "Order not found";
            $this->load->vars($data);
            $this->load->view('lab_error');
            return;
        }
        $this->load->model('mpersistent');
        $data["oreder_info"] = $this->mpersistent->open_id($order_id, "lab_order", "LAB_ORDER_ID");
        if (empty($data["oreder_info"])) {
            $data["error"] = "Order information not found";
            $this->load->vars($data);
            $this->load->view('lab_error');
            return;
        }

        if ($data["oreder_info"]["Dept"] == "OPD") {
            $this->load->model('mlaboratory');
            $this->load->model('mopd');
            $this->load->model('mpatient');
            $data["mode"] = "process";
            $data["orederd_test_list"] = $this->mlaboratory->get_ordered_lab_test($order_id);
            if (isset($data["oreder_info"]["OBJID"])) {
                $data["opd_visits_info"] = $this->mopd->get_info($data["oreder_info"]["OBJID"]);
                if (isset($data["opd_visits_info"]["visit_type_id"])) {
                    $data["visit_type_info"] = $this->mpersistent->open_id($data["opd_visits_info"]["visit_type_id"], "visit_type", "VTYPID");
                }
                if (isset($data["opd_visits_info"]["PID"])) {
                    $data["patient_info"] = $this->mpersistent->open_id($data["opd_visits_info"]["PID"], "patient", "PID");
                    $data["patient_allergy_list"] = $this->mpatient->get_allergy_list($data["opd_visits_info"]["PID"]);
                } else {
                    $data["error"] = "OPD Patient  not found";
                    $this->load->vars($data);
                    $this->load->view('lab_error');
                    return;
                }
                if (empty($data["patient_info"])) {
                    $data["error"] = "OPD Patient not found";
                    $this->load->vars($data);
                    $this->load->view('lab_error');
                    return;
                }
                if (isset($data["patient_info"]["DateOfBirth"])) {
                    $data["patient_info"]["Age"] = Modules::run('patient/get_age', $data["patient_info"]["DateOfBirth"]);
                }
            } else {
                $data["error"] = "OPD visit not found";
                $this->load->vars($data);
                $this->load->view('lab_error');
                return;
            }

            $data["user_info"] = $this->mpersistent->open_id($this->session->userdata("UID"), "user", "UID");
            $data["PID"] = $data["opd_visits_info"]["PID"];
            $data["OPDID"] = $data["oreder_info"]["OBJID"];
            $this->load->vars($data);
            $this->load->view('opd_order');
            return;
        } else if ($data["oreder_info"]["Dept"] == "CLN") {
            $this->process_clinic_order($data["oreder_info"]);
            return;
        } else if ($data["oreder_info"]["Dept"] == "ADM") {
            $this->process_admission_order($data["oreder_info"]);
            return;
        }
        $data["error"] = "Dept not found";
        $this->load->vars($data);
        $this->load->view('lab_error');
        return;
    }

    private function process_clinic_order($order_info) {

        $this->load->model('mpersistent');
        $this->load->model('mlaboratory');
        $this->load->model('mopd');
        $this->load->model('mpatient');
        $data["oreder_info"] = $order_info;

        $data["mode"] = "process";
        $data["orederd_test_list"] = $this->mlaboratory->get_ordered_lab_test($data["oreder_info"]["LAB_ORDER_ID"]);
        $data["clinic_visits_info"] = $this->mpersistent->open_id($data["oreder_info"]["OBJID"], "clinic_visits", "clinic_visits_id");
        $data["clinic_info"] = $this->mpersistent->open_id($data["clinic_visits_info"]["clinic"], "clinic", "clinic_id");
        //print_r( $data["clinic_visits_info"]);
        //exit;
        if ($data["clinic_visits_info"]["PID"] > 0) {
            $data["patient_info"] = $this->mpersistent->open_id($data["clinic_visits_info"]["PID"], "patient", "PID");
        } else {
            $data["error"] = "Clinic Patient  not found";
            $this->load->vars($data);
            $this->load->view('lab_error');
            return;
        }
        if (empty($data["patient_info"])) {
            $data["error"] = "Clinic Patient not found";
            $this->load->vars($data);
            $this->load->view('lab_error');
            return;
        }
        if (isset($data["patient_info"]["DateOfBirth"])) {
            $data["patient_info"]["Age"] = Modules::run('patient/get_age', $data["patient_info"]["DateOfBirth"]);
        }

        $data["user_info"] = $this->mpersistent->open_id($this->session->userdata("UID"), "user", "UID");
        $data["PID"] = $data["clinic_visits_info"]["PID"];
        $data["clinic_patient_id"] = $data["clinic_visits_info"]["clinic_visits_id"];
        $this->load->vars($data);
        $this->load->view('clinic_order');
        return;
    }

    public function opd_order($opdid, $order_id = null) {
        if (!isset($opdid) || (!is_numeric($opdid) )) {
            $data["error"] = "OPD visit not found";
            $this->load->vars($data);
            $this->load->view('lab_error');
            return;
        }
        $this->load->model('mpersistent');
        $this->load->model('mlaboratory');
        $this->load->model('mopd');
        $this->load->model('mpatient');
        $data["mode"] = '';
        $data["opd_visits_info"] = $this->mopd->get_info($opdid);
        if (isset($data["opd_visits_info"]["visit_type_id"])) {
            $data["visit_type_info"] = $this->mpersistent->open_id($data["opd_visits_info"]["visit_type_id"], "visit_type", "VTYPID");
        }
        if (isset($data["opd_visits_info"]["OPDID"])) {
            $data["episode_lab_order"] = $this->mlaboratory->get_episode_lab_order($data["opd_visits_info"]["OPDID"], "OPD");
        }
        if ($data["opd_visits_info"]["PID"] > 0) {
            $data["patient_info"] = $this->mpersistent->open_id($data["opd_visits_info"]["PID"], "patient", "PID");
            $data["patient_allergy_list"] = $this->mpatient->get_allergy_list($data["opd_visits_info"]["PID"]);
            $data["patient_previous_lab_list"] = $this->mpatient->patient_previous_lab_list($data["opd_visits_info"]["PID"]);
        } else {
            $data["error"] = "OPD Patient  not found";
            $this->load->vars($data);
            $this->load->view('lab_error');
            return;
        }
        if (empty($data["patient_info"])) {
            $data["error"] = "OPD Patient not found";
            $this->load->vars($data);
            $this->load->view('lab_error');
            return;
        }
        if (isset($data["patient_info"]["DateOfBirth"])) {
            $data["patient_info"]["Age"] = Modules::run('patient/get_age', $data["patient_info"]["DateOfBirth"]);
        }

        if ($order_id == null) {
            $data["test_list"] = $this->mlaboratory->get_test_list();
        } else {
            $data["orederd_test_list"] = $this->mlaboratory->get_ordered_lab_test($order_id);
            $data["oreder_info"] = $this->mpersistent->open_id($order_id, "lab_order", "LAB_ORDER_ID");
            $data["mode"] = "view";
        }
        $data["user_info"] = $this->mpersistent->open_id($this->session->userdata("UID"), "user", "UID");
        $data["PID"] = $data["opd_visits_info"]["PID"];
        $data["OPDID"] = $opdid;
        $this->checkNotification($data["opd_visits_info"]["Complaint"],$opdid);
        $this->load->vars($data);
        $this->load->view('opd_order');
    }

    public function clinic_order($clinic_id, $order_id = null) {
        if (!isset($clinic_id) || (!is_numeric($clinic_id) )) {
            $data["error"] = "Clinic visit not found";
            $this->load->vars($data);
            $this->load->view('lab_error');
            return;
        }
        $this->load->model('mpersistent');
        $this->load->model('mlaboratory');
        $this->load->model('mpatient');
        $data["mode"] = '';
        $data["clinic_visits_info"] = $this->mpersistent->open_id($clinic_id, "clinic_visits", "clinic_visits_id");
        $data["clinic_info"] = $this->mpersistent->open_id($data["clinic_visits_info"]["clinic"], "clinic", "clinic_id");
        if ($data["clinic_visits_info"]["PID"] > 0) {
            $data["patient_info"] = $this->mpersistent->open_id($data["clinic_visits_info"]["PID"], "patient", "PID");
            $data["episode_lab_order"] = $this->mlaboratory->get_episode_lab_order($data["clinic_visits_info"]["clinic_visits_id"], "CLN");
        } else {
            $data["error"] = "Clinic Patient  not found";
            $this->load->vars($data);
            $this->load->view('lab_error');
            return;
        }
        if (empty($data["patient_info"])) {
            $data["error"] = "Clinic Patient not found";
            $this->load->vars($data);
            $this->load->view('lab_error');
            return;
        }
        if (isset($data["patient_info"]["DateOfBirth"])) {
            $data["patient_info"]["Age"] = Modules::run('patient/get_age', $data["patient_info"]["DateOfBirth"]);
        }

        if ($order_id == null) {
            $data["test_list"] = $this->mlaboratory->get_test_list();
                    } 
                    else {
            $data["orederd_test_list"] = $this->mlaboratory->get_ordered_lab_test($order_id);
            $data["oreder_info"] = $this->mpersistent->open_id($order_id, "lab_order", "LAB_ORDER_ID");
            $data["mode"] = "view";
        }
        $data["patient_previous_lab_list"] = $this->mpatient->patient_previous_lab_list($data["patient_info"]["PID"]);
        $data["user_info"] = $this->mpersistent->open_id($this->session->userdata("UID"), "user", "UID");
        $data["PID"] = $data["clinic_visits_info"]["PID"];
        $data["clinic_patient_id"] = $data["clinic_visits_info"]["clinic_visits_id"];
        $this->load->vars($data);
        $this->load->view('clinic_order');
    }

    public function admission_order($admid, $order_id = null) {
        if (!isset($admid) || (!is_numeric($admid) )) {
            $data["error"] = "Admission visit not found";
            $this->load->vars($data);
            $this->load->view('lab_error');
            return;
        }
        $this->load->model('mpersistent');
        $this->load->model('mlaboratory');
        $this->load->model('mpatient');
        $data["mode"] = '';
        $data["admission_info"] = $this->mpersistent->open_id($admid, "admission", "ADMID");
        $data["ward_info"] = $this->mpersistent->open_id($data["admission_info"]["Ward"], "ward", "WID");
        $data["doctor_info"] = $this->mpersistent->open_id($data["admission_info"]["Doctor"], "user", "UID");
        if ($data["admission_info"]["PID"] > 0) {
            $data["patient_info"] = $this->mpersistent->open_id($data["admission_info"]["PID"], "patient", "PID");
            $data["episode_lab_order"] = $this->mlaboratory->get_episode_lab_order($data["admission_info"]["ADMID"], "ADM");
        } else {
            $data["error"] = "Admission Patient  not found";
            $this->load->vars($data);
            $this->load->view('lab_error');
            return;
        }
        if (empty($data["patient_info"])) {
            $data["error"] = "Admission Patient not found";
            $this->load->vars($data);
            $this->load->view('lab_error');
            return;
        }
        if (isset($data["patient_info"]["DateOfBirth"])) {
            $data["patient_info"]["Age"] = Modules::run('patient/get_age', $data["patient_info"]["DateOfBirth"]);
        }

        if ($order_id == null) {
            $data["test_list"] = $this->mlaboratory->get_test_list();
        } else {
            $data["orederd_test_list"] = $this->mlaboratory->get_ordered_lab_test($order_id);
            $data["oreder_info"] = $this->mpersistent->open_id($order_id, "lab_order", "LAB_ORDER_ID");
            $data["mode"] = "view";
        }
        $data["user_info"] = $this->mpersistent->open_id($this->session->userdata("UID"), "user", "UID");
        $data["patient_previous_lab_list"] = $this->mpatient->patient_previous_lab_list($data["admission_info"]["PID"]);
        $data["PID"] = $data["admission_info"]["PID"];
        $data["admid"] = $data["admission_info"]["ADMID"];
        $this->load->vars($data);
        $this->load->view('admission_order');
    }

    private function process_admission_order($order_info) {

        $this->load->model('mpersistent');
        $this->load->model('mlaboratory');
        $this->load->model('mopd');
        $this->load->model('mpatient');
        $data["oreder_info"] = $order_info;

        $data["mode"] = "process";
        $data["orederd_test_list"] = $this->mlaboratory->get_ordered_lab_test($data["oreder_info"]["LAB_ORDER_ID"]);
        $data["admission_info"] = $this->mpersistent->open_id($data["oreder_info"]["OBJID"], "admission", "ADMID");
        $data["ward_info"] = $this->mpersistent->open_id($data["admission_info"]["Ward"], "ward", "WID");
        $data["doctor_info"] = $this->mpersistent->open_id($data["admission_info"]["Doctor"], "user", "UID");
        //print_r( $data["admission_info"]);
        //exit;
        if ($data["admission_info"]["PID"] > 0) {
            $data["patient_info"] = $this->mpersistent->open_id($data["admission_info"]["PID"], "patient", "PID");
        } else {
            $data["error"] = "Admission Patient  not found";
            $this->load->vars($data);
            $this->load->view('lab_error');
            return;
        }
        if (empty($data["patient_info"])) {
            $data["error"] = "Admission Patient not found";
            $this->load->vars($data);
            $this->load->view('lab_error');
            return;
        }
        if (isset($data["patient_info"]["DateOfBirth"])) {
            $data["patient_info"]["Age"] = Modules::run('patient/get_age', $data["patient_info"]["DateOfBirth"]);
        }

        $data["user_info"] = $this->mpersistent->open_id($this->session->userdata("UID"), "user", "UID");
	$data["patient_previous_lab_list"] = $this->mpatient->patient_previous_lab_list($data["admission_info"]["PID"]);
        $data["PID"] = $data["admission_info"]["PID"];
        $data["admid"] = $data["admission_info"]["ADMID"];
        $this->load->vars($data);
        $this->load->view('admission_order');
        return;
    }

    public function order($order_id = null) {
        if (!isset($order_id) || (!is_numeric($order_id) )) {
            $data["error"] = "Order not found";
            $this->load->vars($data);
            $this->load->view('lab_error');
            return;
        }
        $this->load->model('mpersistent');
        $this->load->model('mlaboratory');
        $this->load->model('mpatient');
        $data["mode"] = '';
        $data["orederd_test_list"] = $this->mlaboratory->get_ordered_lab_test($order_id);
        $data["oreder_info"] = $this->mpersistent->open_id($order_id, "lab_order", "LAB_ORDER_ID");
        $data["mode"] = "view";
        if ($data["oreder_info"]["PID"] > 0) {
            $data["patient_info"] = $this->mpersistent->open_id($data["oreder_info"]["PID"], "patient", "PID");
        } else {
            $data["error"] = "Patient  not found";
            $this->load->vars($data);
            $this->load->view('lab_error');
            return;
        }
        if (empty($data["patient_info"])) {
            $data["error"] = "Patient not found";
            $this->load->vars($data);
            $this->load->view('lab_error');
            return;
        }
        if (isset($data["patient_info"]["DateOfBirth"])) {
            $data["patient_info"]["Age"] = Modules::run('patient/get_age', $data["patient_info"]["DateOfBirth"]);
        }
        $data["user_info"] = $this->mpersistent->open_id($this->session->userdata("UID"), "user", "UID");
        $data["PID"] = $data["oreder_info"]["PID"];
        $data["OPDID"] = "";
        $this->load->vars($data);
        $this->load->view('view_order');
    }

    public function lab_order_search() {
        $this->load->model('mlaboratory');
        $data["header"] = array('LAB_ORDER_ID', 'PID', 'OrderBy', 'Status', 'Priority', 'TestGroupName', 'CreateDate', 'Collection_Status', 'Dept');
        $data["display_header"] = array('ID', 'PID', 'OrderBy', 'Status', 'Priority', 'TestGroupName', 'CreateDate', 'Collection_Status', 'Dept', 'Option');
        $data["table"] = "lab_order";
        $data["last_column_link"] = "laboratory/lab_order_search/";
        $data["last_column_text"] = "Open";
        $data["config"]['base_url'] = base_url() . 'index.php/pharmacy/opd_presciption_search';
        $data["config"]['total_rows'] = $this->mlaboratory->get_total_active_record();
        $data["config"]['uri_segment'] = 3;
        $data["config"]['per_page'] = 20;

        $data["config"]['first_link'] = 'First';
        $data["config"]['first_tag_open'] = '<span  class="first">';
        $data["config"]['first_tag_close'] = '</span>';
        $data["config"]['last_link'] = 'Last';
        $data["config"]['last_tag_open'] = '<span  class="last">';
        $data["config"]['last_tag_close'] = '</span>';
        $data["config"]['full_tag_open'] = '<p class="page-cont">';
        $data["config"]['full_tag_close'] = '</p>';
        $data["config"]['num_tag_open'] = '<span class="digit">';
        $data["config"]['num_tag_close'] = '</span>';
        $data["config"]['next_link'] = '&#187;';
        $data["config"]['next_tag_open'] = '<span class="next">';
        $data["config"]['next_tag_close'] = '</span>';
        $data["config"]['prev_link'] = '&#171;';
        $data["config"]['prev_tag_open'] = '<span class="prev">';
        $data["config"]['prev_tag_close'] = '</span>';
        $data["config"]['cur_tag_open'] = '<span class="cur">';
        $data["config"]['cur_tag_close'] = '</span>';
        $this->load->vars($data);
        $this->load->view('lab_order_search', 1);
    }
    
                public function checkNotification($complaint,$opdid){
                $this->load->model("mnotification");
		$complaint_data= $this->mnotification->is_complaint_notify($complaint);
                $notification_data= $this->mnotification->is_opd_notifed($opdid);
               // die(print_r($nid));
             if (!empty($complaint_data)){
                 //die(print_r($notification_data));
                   if (empty($notification_data)) {
 
                    $msg = "";
                    $msg .= "<table width=100% style='font-size:10px;'><tr><td colspan=2 align=center style='color:#FF0000;'>Alert!<hr></td></tr>";
                    $msg .= "<tr>";
                    $msg .= "<td  width=5%>Complaint:</td>";
                    $msg .= "<td  ><textarea disabled rows=4>".$complaint."</textarea></td>";
                    $msg .= "</tr>";
                    $msg .= "<tr><td colspan=2 >Do you want to notify this?</td></tr>";
                    $msg .= "<tr><td colspan=2 align=center><a class='btn btn-sm btn-default' href='".site_url('notification/create/opd/'.$opdid.'?CONTINUE=opd/view/'.$opdid)."'>Yes</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href='#' onclick='notification_close();'>Cancel</a></td></tr>";
                    $msg .= "</table>";
                    echo "<div id='n_pop' class='nPop' style='padding:10px;border:1px solid #ff0000;background-color:#fff4f4;z-index:9999999;position:absolute;top:0px;left:50%;width:300px;margin-left:-100px;height:200px;max-height:300px;' >$msg</div>";
                    

                   }
                 
             
  
        }
	
                }

}

//////////////////////////////////////////

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
