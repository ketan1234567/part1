<?php
$conn = new mysqli("localhost","root","","ourcashproindia");
//$conn = new mysqli("localhost","myshopzi_cash","cashproindia@2022?","myshopzi_kuku");
	
	/*For All data of tbl_masterloanreport*/
	$sql = "SELECT * FROM tbl_masterloanreport";
	$result = mysqli_query($conn,$sql);
	//$row=$result->fetch_assoc();
	//$capital=$row['capital'];
    echo "Cron-1";
	while($row=$result->fetch_assoc())
	{							
		echo  $sku=$row['tbl_masterloanreport_id']; echo "<br>";
		echo  $loan_id=$row['loan_id']; echo "<br>";
		echo  $loan_amount=$row['loan_amount']; echo "<br>";
		echo  $collected_amount=$row['collected_amount']; echo "<br>";
		echo  $remaining_amount=$row['remaining_amount']; echo "<br>";

		
		$updatequery ="UPDATE tbl_upcoming_report SET collected_amount='$collected_amount',remaining_amount = '$remaining_amount'  WHERE loan_id='$loan_id'";
		$result1 = mysqli_query($conn,$updatequery);

		$updatequery1 ="UPDATE tbl_upcoming_report SET days_left = remaining_amount / daily_target WHERE loan_id='$loan_id'";
		$result11 = mysqli_query($conn,$updatequery1);

		$select = "select days_left from tbl_upcoming_report WHERE loan_id='$loan_id'";
		$select_result = mysqli_query($conn,$select);
		$row=$select_result->fetch_assoc();
		$days_left=$row['days_left']; echo "<br>";
		/*$date1 = date('Y-m-d');
	    $aprx_date = strtotime("+".$days_left." day", $date1);*/
	    $days_left1 = round($days_left);
	    
	    $date1 = strtotime("+".$days_left1." day");
		//$aprx_date = date('M d, Y', $date1);
		$aprx_date = date('Y-m-d', $date1);

		$updatequery11 ="UPDATE tbl_upcoming_report SET aprx_date = '$aprx_date' WHERE loan_id='$loan_id'";
		$result111 = mysqli_query($conn,$updatequery11);
	}
		///BI formula
	$sql2 =  "SELECT tbl_upcoming_report_id FROM tbl_upcoming_report ORDER BY aprx_date limit 1";
	$result2 = mysqli_query($conn,$sql2);
	$row2=$result2->fetch_assoc();
	$lowest_id=$row2['tbl_upcoming_report_id'];



	$sql3 = "SELECT * FROM tbl_upcoming_report ORDER BY aprx_date";
	$result3 = mysqli_query($conn,$sql3);
	while($row3=$result3->fetch_assoc())
	{
		
		/*Total Receive Loan*/
		$total_receive_loan = "SELECT SUM(pay_loan_amt) AS payloanamt FROM tbl_pay";
		$resultfortotalreceiveloan = mysqli_query($conn,$total_receive_loan);
		$row=$resultfortotalreceiveloan->fetch_assoc();
		$total_receive_loan1=$row['payloanamt'];

		/*Re-invest amt*/
		$re_invest_amt = "SELECT SUM(reinvest_amt) AS reinvestamt FROM tbl_loan_type_amt";
		$resultforreinvestamt = mysqli_query($conn,$re_invest_amt);
		$row=$resultforreinvestamt->fetch_assoc();
		$re_invest_amt1=$row['reinvestamt'];
		
		/*Expenses*/
		//$expenses_amt = "SELECT SUM(amount) AS expamt FROM tbl_exp";
		$expenses_amt = "SELECT SUM(amount) AS expamt FROM tbl_exp where cap_type = '2'";
		$resultforexpamt = mysqli_query($conn,$expenses_amt);
		$row=$resultforexpamt->fetch_assoc();
		$expenses_amt1=$row['expamt'];

		/*Cash in Bank*/
		$cash_in_bank = $total_receive_loan1 - $re_invest_amt1 - $expenses_amt1 ;
		$id=$row3['tbl_upcoming_report_id']; 

		/*To cash In bank*/
		if ($id == $lowest_id) {
			$updatequeryforcashinbank ="UPDATE tbl_upcoming_report SET cash_in_bank = '$cash_in_bank' WHERE tbl_upcoming_report_id='$id'";
			$resultforcashinbank = mysqli_query($conn,$updatequeryforcashinbank);
		}else{
			$updatequeryforcashinbank ="UPDATE tbl_upcoming_report SET cash_in_bank = 0 WHERE tbl_upcoming_report_id='$id'";
			$resultforcashinbank = mysqli_query($conn,$updatequeryforcashinbank);
		}	

		/*To Make up*/
		if ($id == $lowest_id) {
			$to_makeup = $row3['loan_amount'] - $cash_in_bank;
			$updatequeryfortomakeup ="UPDATE tbl_upcoming_report SET to_makeup = '$to_makeup' WHERE tbl_upcoming_report_id='$id'";
			$resultfortomakeup = mysqli_query($conn,$updatequeryfortomakeup);
		}else{
			$to_makeup = $row3['loan_amount'] - 0 ;
			$updatequeryfortomakeup ="UPDATE tbl_upcoming_report SET to_makeup = '$to_makeup' WHERE tbl_upcoming_report_id='$id'";
			$resultfortomakeup = mysqli_query($conn,$updatequeryfortomakeup);
		}
		
		/*For Currnt month sum data*/

		$currntmonthsumdata = "SELECT SUM(pay_loan_amt) AS sumdata FROM tbl_pay WHERE MONTH(loandate) = MONTH(CURRENT_DATE()) AND YEAR(loandate) = YEAR(CURRENT_DATE())";
		$resultforsumdata = mysqli_query($conn,$currntmonthsumdata);
		$row=$resultforsumdata->fetch_assoc();
		$currntmonthsumdata1=$row['sumdata'];
		/*For Currnt month date data*/
		$first_day_this_month = date('Y-m-01'); // hard-coded '01' for first day
		$last_day_this_month  = date('Y-m-t');
		$currentmonthdatedata = "SELECT CAST(loandate AS DATE) AS datecnt FROM tbl_pay WHERE loandate BETWEEN '$first_day_this_month' AND '$last_day_this_month' GROUP BY CAST(loandate AS DATE)";
		$resultfordatedata = mysqli_query($conn,$currentmonthdatedata);
		$cnt = 0;
		while($row = $resultfordatedata->fetch_assoc()){
			$cnt++;
		}

		/*For Currnt month AVG data*/

		$currentmonthavgdata = $currntmonthsumdata1 / $cnt ;
		$roundof_value = round($currentmonthavgdata);
		$updatequeryforavgmonth ="UPDATE tbl_upcoming_report SET month_avg = '$roundof_value'";
		$resultforavgmonth = mysqli_query($conn,$updatequeryforavgmonth);

		/* Apox days to save*/
		$apox_days_to_save = $to_makeup / $roundof_value ;
		$roundof_apox_days_to_save = round($apox_days_to_save);
		$updatequeryfor_apoxsave ="UPDATE tbl_upcoming_report SET apox_days_to_save = '$roundof_apox_days_to_save' WHERE  tbl_upcoming_report_id='$id'";
		$result_updatequeryfor_apoxsave = mysqli_query($conn,$updatequeryfor_apoxsave);
	}

	/*For GAP*/
	$sql4 = "SELECT * FROM tbl_upcoming_report ORDER BY aprx_date";
	$result4 = mysqli_query($conn,$sql4);
	$temp = "1901-01-01";
	while($row4=$result4->fetch_assoc())
	{
		
		$id=$row4['tbl_upcoming_report_id']; 
		$same_date = $row4['aprx_date'];
		$diff = strtotime($same_date) - strtotime($temp); 
		$gap = abs(round($diff / 86400));  echo "<br>";
		$temp = $same_date;
		$updatequeryfogap ="UPDATE tbl_upcoming_report SET gap = '$gap' WHERE tbl_upcoming_report_id='$id'";
		$result_updatequeryfogap = mysqli_query($conn,$updatequeryfogap);

	}



	/*For Project 1*/

	$sql5 = "SELECT * FROM tbl_upcoming_report ORDER BY aprx_date";
	$result5 = mysqli_query($conn,$sql5);
	while($row5=$result5->fetch_assoc())
	{
		
		$id=$row5['tbl_upcoming_report_id']; 
		
		$date = $row5['aprx_date'];
		$projected1 = date('Y-m-d', strtotime($date.'-'. $row5['apox_days_to_save'].'days'));

		$updatequeryfor_projected_1 ="UPDATE tbl_upcoming_report SET project_1 = '$projected1' WHERE tbl_upcoming_report_id='$id'";
		$result_updatequeryfor_projected_1 = mysqli_query($conn,$updatequeryfor_projected_1);
		
	}

	/*For Project_1 constant date*/

	$sql6 =  "SELECT project_1 FROM tbl_upcoming_report ORDER BY aprx_date limit 1";
	$result6 = mysqli_query($conn,$sql6);
	$row6=$result6->fetch_assoc();
	$cons_date=$row6['project_1'];

	/*For Project 2*/
	$sql6 = "SELECT * FROM tbl_upcoming_report ORDER BY aprx_date";
	$result6 = mysqli_query($conn,$sql6);
	while($row6=$result6->fetch_assoc())
	{
		
		$id=$row6['tbl_upcoming_report_id']; 
		
		$f_p2 = date('Y-m-d', strtotime($cons_date.'-'. $row6['apox_days_to_save'].'days'));
		$s_p2 = date('Y-m-d', strtotime($f_p2.'+'. $row6['gap'].'days'));

		if ($id == $lowest_id) {
			$updatequeryforproj2 ="UPDATE tbl_upcoming_report SET project_2 = '$cons_date' WHERE tbl_upcoming_report_id='$id'";
			$resultforproj2 = mysqli_query($conn,$updatequeryforproj2);
		}else{
			$updatequeryforproj2 ="UPDATE tbl_upcoming_report SET project_2 = '$s_p2' WHERE tbl_upcoming_report_id='$id'";
			$resultforproj2 = mysqli_query($conn,$updatequeryforproj2);
		}	


	}
	
	/*Update cash bank in table "tbl_tbl_cashinbank"*/

	/*Total Receive Loan*/
		$total_receive_loan = "SELECT SUM(pay_loan_amt) AS payloanamt FROM tbl_pay";
		$resultfortotalreceiveloan = mysqli_query($conn,$total_receive_loan);
		$row=$resultfortotalreceiveloan->fetch_assoc();
		$total_receive_loan1=$row['payloanamt'];

		/*Re-invest amt*/
		$re_invest_amt = "SELECT SUM(reinvest_amt) AS reinvestamt FROM tbl_loan_type_amt";
		$resultforreinvestamt = mysqli_query($conn,$re_invest_amt);
		$row=$resultforreinvestamt->fetch_assoc();
		$re_invest_amt1=$row['reinvestamt'];
		
		/*Expenses*/
		$expenses_amt = "SELECT SUM(amount) AS expamt FROM tbl_exp where cap_type = 2";
		//$expenses_amt = "SELECT SUM(amount) AS expamt FROM tbl_exp";
		$resultforexpamt = mysqli_query($conn,$expenses_amt);
		$row=$resultforexpamt->fetch_assoc();
		$expenses_amt1=$row['expamt'];

		/*Cash in Bank*/
		echo"Cash In Bank  ="; echo $cash_in_bank_new = $total_receive_loan1 - $re_invest_amt1 - $expenses_amt1 ; echo "<br>";echo "==================="; echo "<br>";		
		$updatequerycib ="UPDATE tbl_cashinbank SET cashinbank='$cash_in_bank_new' WHERE id='1'";
		$result1 = mysqli_query($conn,$updatequerycib);		
	
?>

<!-- Crons Job Path-->
<!-- php -q /home/thespoon/public_html/cashproindia/application/controllers/Cron_job1.php -->