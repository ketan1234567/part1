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
		$this->load->view('footer');
		/*$data = bin2hex("1234");
		print_r($data);*/
	}
	public function registration()
	{
	    $recaptchaSecretKey = "6LftS64ZAAAAAD1ttb56OT_2sJVD_3Gno4v4wHFY";
        $recaptchaResponse = $this->input->post('g-recaptcha-response');

        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret' => $recaptchaSecretKey,
            'response' => $recaptchaResponse,
        ];

        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
            ],
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $jsonResult = json_decode($result, true);

        if ($jsonResult['success']) {
            // reCAPTCHA verification successful, continue with registration
            // Your registration processing code here
        } else {
            // reCAPTCHA verification failed
            echo "reCAPTCHA verification failed. Please try again.";
        }
        
        
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
		echo "<pre>"; print_r($config); 



    if ( !empty($_FILES['cust_image']['name']))
    {	
     $result = $this->Regmodel->reg_data($data);
     // print_r($result); //exit();
     $reg_id = $this->Regmodel->get_last_reg_id();
      //print_r($reg_id); //exit();
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
                                        //echo "<pre>"; print_r($fileData); exit();
        $uploadData[$i]['cust_img_path'] = $fileData['file_name'];
        $uploadData[$i]['id'] = $reg_id;
                                        //echo "<pre>"; print_r($uploadData); exit();
        $data = array('cust_img_path' => $fileData['file_name'],'id' =>$reg_id);
                                       // echo "<pre>"; print_r($data); //exit();
        $this->Regmodel->store_image($data);
                                        // echo "run 6"; //exit();
      }


    }
    $this->session->set_flashdata('success', 'Successfully Register');    
    redirect(base_url(),'refresh');
  }else{
    $result = $this->Regmodel->reg_data($data);
    $this->session->set_flashdata('success', 'Successfully Register');    
    redirect(base_url(),'refresh');
  }

                            	//echo "run88";

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
    $this->Regmodel->master_table();
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

    $result['re_invest'] = $this->Regmodel->reinvest_loan();
    $result['capital'] = $this->Regmodel->capital_loan();
    $result['expenses'] = $this->Regmodel->expenses();
    /*echo CI_VERSION; exit();*/
    $result['projected_date'] = $this->Regmodel->projected_date();
    
    $result['current_month_avg'] = $this->Regmodel->current_month_avg();
    /*chart*/
    $result['grp_by_date'] = $this->Regmodel->grp_by_date(); //07-11-2019
		//echo "<pre>";print_r($result['grp_by_date']); exit();
    $result['addcapital'] = $this->Regmodel->unusedaddcapital_amt();
    $result['cashinbank'] = $this->Regmodel->cashinbank_amt();
    
    $result['total_unusedaddcapital'] = $this->Regmodel->total_unusedaddcapital();
    $result['capital_exp'] = $this->Regmodel->exp_capital_amt();
    
    /*For chart 2*/
    $result['grp_by_month'] = $this->Regmodel->grp_by_month();

    $result['fines'] = $this->Regmodel->fines();
    
    $this->load->view('header');
    $this->load->view('dashboard',$result);
    $this->load->view('footer');
  }



       
        
        

       }


