<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

	public function index()
	{
		$this->load->view('login');
		//echo "string";
	}

	public function signup()
	{
		$this->load->view('signup');
		/*$data = bin2hex("1234");
		print_r($data);*/
	}
	public function registration()
	{
		$data = $this->input->post();
		$config = 	[
			'upload_path' => './assets/upload/',
			'allowed_types' => 'jpg|gif|png|jpeg',
			'max_size' => 100000
			 		//'filename' => $_FILES['project_image']['name']
		];

		$this->load->library('upload', $config);
		$this->upload->initialize($config);
		
		
		$data = array(
			'fname' => $this->input->post('fname'),
			'lname' => $this->input->post('lname'),
			'contact' => $this->input->post('contact'),
			'address' => $this->input->post('address'),
			'state' => $this->input->post('state'),
			'gender' => $this->input->post('gender'),
			'email' => $this->input->post('email'),
			'username' => $this->input->post('username'),
			'password' => bin2hex($this->input->post('password'))
		);
		//echo "<pre>"; print_r($config); 

		if ( !empty($_FILES['cust_image']['name']))
		{	
			$result = $this->Regmodel->reg_data($data);
			$reg_id = $this->Regmodel->get_last_reg_id();
		        $data = array();
		            $filesCount = count($_FILES['cust_image']['name']);
		                      	for($i = 0; $i < $filesCount; $i++)
		                      { 
		                      	$_FILES['file']['name'] = $_FILES['cust_image']['name'][$i];
		                      	$_FILES['file']['type']     = $_FILES['cust_image']['type'][$i];
		                      	$_FILES['file']['tmp_name'] = $_FILES['cust_image']['tmp_name'][$i];
		                      	$_FILES['file']['error']     = $_FILES['cust_image']['error'][$i];
		                      	$_FILES['file']['size']     = $_FILES['cust_image']['size'][$i];
		                      	if($this->upload->do_upload('file') )
		                      	{
                                       // echo "run5";// exit();
                                        $fileData = $this->upload->data();
                                        $uploadData[$i]['cust_img_path'] = $fileData['file_name'];
                                        $uploadData[$i]['id'] = $id;
                                        $data = array('cust_img_path' => $fileData['file_name'],'id' =>$id);
                                        $this->Regmodel->store_image($data);
                                    }
                                   //echo "run 6"; //exit();
                               }
                               $this->session->set_flashdata('success', 'Successfully Register');    
                               redirect(base_url(),'refresh');
                           }else{
                           	$result = $this->Regmodel->reg_data($data);
                           	$this->session->set_flashdata('success', 'Successfully Register');    
                           	redirect(base_url(),'refresh');
                           }

                           $this->session->set_flashdata('warning', 'Failed to Register');                      
                           redirect(base_url(),'refresh');
                       }

                       public function login()
                       {
                       	$username = $this->input->post('username');
                       	$password = bin2hex($this->input->post('password'));

                       	$result = $this->Regmodel->login_valid($username,$password);

                       	if($result){
                       		$newdata = array
                       		(
                       			'id'=>$result->id,
                       			'fname'=>$result->fname,
                       			'lname'=>$result->lname,
                       			'contact'=>$result->contact,
                       			'address'=>$result->address,
                       			'state'=>$result->state,
                       			'gender'=>$result->gender,
                       			'email'=>$result->email,
                       			'username'=>$result->username,
                       			'type'=>$result->type,
                       			'is_active'=>$result->is_active
                       		);
                       		$this->session->set_userdata($newdata);
                       		$this->session->set_flashdata('success', 'Successfully login');
                       		redirect('index.php/dashboard','refresh');
			
                       	}
                       	else{
                       		$this->session->set_flashdata('warning', 'Login Failed, Please check username or password');
                       		redirect(base_url(),'refresh');
                       	}
                       }

                       public function logout()
                       {
                       	$this->session->sess_destroy();

                       	redirect(base_url(),'refresh');
                       }

                       public function dashboard()
                       {
                        if(!($this->session->userdata('id'))) {
                          redirect(base_url(),'refresh'); } 
                       	$result['total_given_loan'] = $this->Regmodel->total_given_loan();
                       	$result['total_receive_loan'] = $this->Regmodel->total_receive_loan();
                       	$result['total_active_loan'] = $this->Regmodel->total_active_loan();
                       	$result['total_closed_loan'] = $this->Regmodel->total_closed_loan();
                       	$result['cust_active_loan'] = $this->Regmodel->cust_active_loan();
                       	$result['cust_inactive_loan'] = $this->Regmodel->cust_inactive_loan();
                       	$result['cust_pending_loan'] = $this->Regmodel->cust_pending_loan();

                       	$result['collector_daily_collection'] = $this->Regmodel->collector_daily_collection();
                       	$result['collector_all_collection'] = $this->Regmodel->collector_all_collection();

                       	$result['lender_all_collection'] = $this->Regmodel->lender_all_collection();



                       	$result['remaining_collection'] = $result['lender_all_collection']->lenderamt - $result['total_receive_loan']->pay_loan_amt;
                       	 /*echo CI_VERSION; exit();*/

		//echo "<pre>";print_r($result); exit();
                       	$this->load->view('header');
                       	$this->load->view('dashboard',$result);
                       	$this->load->view('footer');
                       }

                       public function loan()
                       {
                        if(!($this->session->userdata('id'))) {
                          redirect(base_url(),'refresh'); } 
                       	$result['selfpassbook'] = $this->Regmodel->passbook_data($this->session->userdata('id'));
		//echo "<pre>"; print_r($result); exit();
                       	$this->load->view('header');
                       	$this->load->view('loanform',$result);
                       	$this->load->view('footer');
                       }
                       public function passbook()
                       {
                        if(!($this->session->userdata('id'))) {
                          redirect(base_url(),'refresh'); } 
                       	$result['selfpassbook'] = $this->Regmodel->passbook_data($this->session->userdata('id'));
		//echo "<pre>"; print_r($result); exit();
                       	$this->load->view('header');
                       	$this->load->view('passbook',$result);
                       }
                       public function storeloan()
                       {
                        if(!($this->session->userdata('id'))) {
                          redirect(base_url(),'refresh'); } 
		//lenderamt

                       	$data = $this->input->post();
		//echo "<pre>";print_r($data); exit();
                       	unset($data['fname']);
                       	$result = $this->Regmodel->reg_loan($data);
                       	$this->session->set_flashdata('success', 'Loan request added and pending for approval');
                       	redirect('index.php/loan','refresh');
		//echo "<pre>";print_r($data);
                       }
                       public function editloanamt()
                       {
                        if(!($this->session->userdata('id'))) {
                          redirect(base_url(),'refresh'); } 
                       	$loan_id=$_POST['loan_id'];
                       	$result = $this->Regmodel->loan_data($loan_id);
                       	if($result)
                       	{
                       		echo json_encode($result);
                       	}
                       	else
                       	{
                       		echo "false";
                       	}
                       }
                       public function updateloanamt()
                       {
                        if(!($this->session->userdata('id'))) {
                          redirect(base_url(),'refresh'); } 
                       	$loan_id=$_POST['loan_id'];
                       	$loan_amt=$_POST['loan_amt'];
                       	$lenderamt=$_POST['lenderamt'];

                       	$result = $this->Regmodel->loan_data_update($loan_id,$loan_amt,$lenderamt);
                       	if($result)
                       	{
                       		echo json_encode($result);
                       	}
                       	else
                       	{
                       		echo "false";
                       	}
                       }

                       public function customers()
                       {
                        if(!($this->session->userdata('id'))) {
                          redirect(base_url(),'refresh'); } 
                       	$result['app_cust'] = $this->Regmodel->app_cust_list();

		//echo "<pre>";print_r($result); exit();
                       	$this->load->view('header');
                       	$this->load->view('approve_cust',$result);
                       }
                       public function closedloan()
                       {
                        if(!($this->session->userdata('id'))) {
                          redirect(base_url(),'refresh'); } 
                       	$result['app_cust'] = $this->Regmodel->app_cust_list1();

		//echo "<pre>";print_r($result); exit();
                       	$this->load->view('header');
                       	$this->load->view('close_loan',$result);
                       }
                       
                       public function collectloanamt()
                       {
                        if(!($this->session->userdata('id'))) {
                          redirect(base_url(),'refresh'); } 
                       	$loan_id=$_POST['loan_id'];
                       	$collector_id=$_POST['collector_id'];
                       	$loanamt=$_POST['loanamt'];
                       	$pay_loan_amt=$_POST['pay_loan_amt'];
                       	$loandate=$_POST['loandate'];
                       	$amtdata = array(
                       		'loan_id' => $loan_id,
                       		'collector_id' => $collector_id,
                       		'loanamt' => $loanamt,
                       		'pay_loan_amt' => $pay_loan_amt,
                       		'loandate' => $loandate
                       	);
                       	print($amtdata );
                       	$result = $this->Regmodel->loan_amt_pay($amtdata);
                       	if($result)
                       	{
                       		echo json_encode($result);
                       	}
                       	else
                       	{
                       		echo "false";
                       	}
                       }

                       public  function showpassbook($loan_id)
                       {
                       	$loan_id = base64_decode($loan_id);
                       	$result['pass_entry'] = $this->Regmodel->show_loan_amt_passbkentry($loan_id);
                       	$result['loan_data'] = $this->Regmodel->show_loan_data($loan_id);
                       	$result['collector_data'] = $this->Regmodel->show_collector_data();
                       	$result['subtotal_amt'] = $this->Regmodel->show_subtotal_data($loan_id);
		//echo "<pre>"; print_r($result); exit();
                       	$this->load->view('header');
                       	$this->load->view('showpassbook',$result);
                       }

                       public function daily()
                       {
                        if(!($this->session->userdata('id'))) {
                          redirect(base_url(),'refresh'); } 
		//$curr_date = date("Y m d H:i:s");
                       	$result['dailycollection'] = $this->Regmodel->show_daily_collection();
		//echo "<pre>"; print_r($sum); exit();
                       	$this->load->view('header');
                       	$this->load->view('dailycoll',$result);

                       }
                       public function all()
                       {
                        if(!($this->session->userdata('id'))) {
                          redirect(base_url(),'refresh'); } 
                       	$result['allcollection'] = $this->Regmodel->show_all_collection($this->session->userdata('id'));
		//echo "<pre>"; print_r($result); exit();
                       	$this->load->view('header');
                       	$this->load->view('allcoll',$result);
                       }


                       public function agents()
                       {
                        if(!($this->session->userdata('id'))) {
                          redirect(base_url(),'refresh'); } 
                       	$result['allagents'] = $this->Regmodel->show_all_agents();
		//echo "<pre>"; print_r($result); exit();
                       	$this->load->view('header');
                       	$this->load->view('allagents',$result);
                       	$this->load->view('footer');
                       }
                       public function changestatus()
                       {
                        if(!($this->session->userdata('id'))) {
                          redirect(base_url(),'refresh'); } 
                       	$id=$_POST['id'];
                       	$val=$_POST['val'];
                       	$result = $this->Regmodel->change_status($id,$val);
                       	if($result)
                       	{
                       		echo json_encode($result);
                       	}
                       	else
                       	{
                       		echo "false";
                       	}
                       }
                       public function loanlist()
                       {
                        if(!($this->session->userdata('id'))) {
                          redirect(base_url(),'refresh'); } 
                       	$result['allloan'] = $this->Regmodel->show_all_loan();
                       	$result['allagents'] = $this->Regmodel->show_all_agents();
		//echo "<pre>"; print_r($result); exit();
                       	$this->load->view('header');
                       	$this->load->view('allloan',$result);
                       }
                       public function changeapprove()
                       {
                        if(!($this->session->userdata('id'))) {
                          redirect(base_url(),'refresh'); } 
                       	$id=$_POST['id'];
                       	$val=$_POST['val'];
                       	$result = $this->Regmodel->change_approve($id,$val);
                       	if($result)
                       	{
                       		echo json_encode($result);
                       	}
                       	else
                       	{
                       		echo "false";
                       	}
                       }

                       public function collector()
                       {
                        if(!($this->session->userdata('id'))) {
                          redirect(base_url(),'refresh'); } 
                       	$result['allcollector'] = $this->Regmodel->show_all_collector();
		//echo "<pre>"; print_r($result); exit();
                       	$this->load->view('header');
                       	$this->load->view('allcollector',$result);
                       }
                       public function ind_collection($collector_id)
                       {
                        if(!($this->session->userdata('id'))) {
                          redirect(base_url(),'refresh'); } 
                       	$result['allcollection'] = $this->Regmodel->show_all_collection($collector_id);
		//echo "<pre>"; print_r($result); exit();
                       	$this->load->view('header');
                       	$this->load->view('allcoll',$result);
                       }
                       public  function agentdetails()
                       {
                       	$id=$_POST['id'];
                       	$result = $this->Regmodel->agent_data($id);
                       	if($result)
                       	{
                       		echo json_encode($result);
                       	}
                       	else
                       	{
                       		echo "false";
                       	}
                       }

                       public function loandetails()
                       {
                        if(!($this->session->userdata('id'))) {
                          redirect(base_url(),'refresh'); } 
                       	$id=$_POST['id'];
                       	$result = $this->Regmodel->ind_loan_data($id);
                       	if($result)
                       	{
                       		echo json_encode($result);
                       	}
                       	else
                       	{
                       		echo "false";
                       	}
                       }

                       public function extra()
                       {
                        if(!($this->session->userdata('id'))) {
                          redirect(base_url(),'refresh'); } 
                       	$this->load->view('header');
                       	$this->load->view('extra');
                       }

                       public function custdetails($cust_id)
                       {
                        if(!($this->session->userdata('id'))) {
                          redirect(base_url(),'refresh'); } 
                       	$id = base64_decode($cust_id);
                       	$result['cust_detail'] = $this->Regmodel->cust_details($id);
                       	$result['cust_loan_detail'] = $this->Regmodel->ind_loan_data($id);
		//echo "<pre>"; print_r($result); exit();
                       	$this->load->view('header');
                       	$this->load->view('userprof',$result);

                       }
                       
                       public function resetpass()
                       {
                        $id= $this->input->post('agents_id');
                        $pass = bin2hex($this->input->post('reset_pass'));
                        $result = $this->Regmodel->reset_pass($id,$pass);
                        $this->session->set_flashdata('success', 'Password Update Successfully');
                        redirect('index.php/agents','refresh');
                       }





                   }
