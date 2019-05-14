<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customers extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('customers_model','customers');
	}

	public function index()
	{
		$this->load->helper('url');
		$this->load->helper('form');
		
		$countries = $this->customers->get_list_countries();

		$opt = array('' => 'All Country');
		foreach ($countries as $country) {
			$opt[$country] = $country;
		}

		$data['form_country'] = form_dropdown('',$opt,'','id="country" class="form-control"');
		$this->load->view('customers_view', $data);
	}

	public function ajax_list()
	{   $country_dropdown = array("Australia","Austria","Belgium","Canada");
		$list = $this->customers->get_datatables();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $customers) {
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $customers->FirstName;
			$row[] = $customers->LastName;
			$row[] = $customers->phone;
			$row[] = $customers->address;
			$row[] = $customers->city;
			
			$country_row = "<select class='form-control' id='sel1".$customers->id."' name='country".$customers->id."'><option value=''>--Select--</option>";
			foreach ($country_dropdown as $key => $value) {
				 $country_con = $value == $customers->country ? "Selected":"";
                 $country_row .= "<option value='".$value."' ".$country_con." >$value</option>";
				
			}
            $country_row .="</select>";

			$row[] =  $country_row;
			$row[] = "<button onClick=saveAssesment(this) data-uniqueid=".$customers->id." id=".$customers->id." class='btn btn-primary' >Save</button>";



			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->customers->count_all(),
						"recordsFiltered" => $this->customers->count_filtered(),
						"data" => $data,
				       );
		//output to json format
		echo json_encode($output);
	}

}
