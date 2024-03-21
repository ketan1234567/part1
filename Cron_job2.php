<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron_job2 extends CI_Controller {

	public function dbbackup()
	{
		//echo "string";
		$this->load->dbutil();

		$prefs = array(     
			'format'      => 'zip',             
			'filename'    => 'CashProIndia_BK.sql'
		);


		$backup =& $this->dbutil->backup($prefs); 

		$db_name = 'backup-on-'. date('d-m-Y') .'.zip';
		$save = 'database_bk/'.$db_name;
		$save1 = 'Desktop/'.$db_name;

		$this->load->helper('file');
		write_file($save, $backup); 
		write_file($save1, $backup);


		 $this->load->helper('download');
		// force_download($db_name, $backup);
	}

}

?>