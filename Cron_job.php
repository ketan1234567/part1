<?php

$conn = new mysqli("localhost","root","","ourcashproindia");
	
	/*For Capital*/
	$sql = "SELECT SUM(loanamt) as capital FROM tbl_loan ";
	$result = mysqli_query($conn,$sql);
	$row=$result->fetch_assoc();
	$capital=$row['capital'];
	echo "Capital : ".$capital; echo "<br>";

	/*For Daily Collected Total */
	$sql1 = "SELECT SUM(pay_loan_amt) as daily_collected_total FROM tbl_pay ";
	$result1 = mysqli_query($conn,$sql1);
	$row1=$result1->fetch_assoc();
	$Daily_Collected_Total=$row1['daily_collected_total'];
	echo "Daily_Collected_Total : ".$Daily_Collected_Total;echo "<br>";

	/*For lender amt*/
	$sql2 = "SELECT SUM(lenderamt) as lender_amt FROM tbl_loan ";
	$result2 = mysqli_query($conn,$sql2);
	$row2=$result2->fetch_assoc();
	$lender_amt=$row2['lender_amt'];
	echo "lender_amt : ".$lender_amt;echo "<br>";

	/*Interest*/
	$interest = $lender_amt - $capital;
	echo "interest : ".$interest;echo "<br>";

	/*Cash In Market*/
	$cash_in_market = $lender_amt - $Daily_Collected_Total;
	echo "cash_in_market : ".$cash_in_market;echo "<br>";

	


	/*Re-Invest*/
	$sql3 = "SELECT SUM(reinvest_amt) as reinvest_amt FROM tbl_loan_type_amt ";
	$result3 = mysqli_query($conn,$sql3);
	$row3=$result3->fetch_assoc();
	$reinvest_amt=$row3['reinvest_amt'];
	echo "reinvest_amt : ".$reinvest_amt;echo "<br>";

	/*Expenses*/
	
	$sql4 = "SELECT SUM(amount) as expenses FROM tbl_exp";
	$result4 = mysqli_query($conn,$sql4);
	$row4=$result4->fetch_assoc();
	$expenses=$row4['expenses'];
	echo "expenses : ".$expenses;echo "<br>";

	/*Cash In Bank*/
	$cib = $Daily_Collected_Total - $reinvest_amt - $expenses;
	echo "cib : ".$cib;echo "<br>";

	/*Profit And Loss*/
	$pnl = $lender_amt - $capital - $expenses;
	echo "pnl : ".$pnl;echo "<br>";

	$date1 = date("Y-m-d");
	$yrdata= strtotime($date1);
    $cur_date = date('d-M-Y', $yrdata);
	echo "cur_date : ".$cur_date;echo "<br>";

	/*Capital*/
	$sql5 = "SELECT SUM(capital_amt) as capital FROM tbl_loan_type_amt";
	$result5 = mysqli_query($conn,$sql5);
	$row5=$result5->fetch_assoc();
	$capitalnew=$row5['capital'];
	echo "capitalnew : ".$capitalnew;echo "<br>";
	
	/*Exp Capital*/
    $sql13 = "SELECT SUM(amount) as capital FROM tbl_exp where tbl_exp_id != 6";
    $result13 = mysqli_query($conn,$sql13);
    $row13=$result13->fetch_assoc();
    $exp_capitalnew=$row13['capital'];
    echo "exp_capitalnew : ".$exp_capitalnew;echo "<br>";

    $capitalnew = $capitalnew + $exp_capitalnew ;
    echo "New Capital : ".$capitalnew;echo "<br>";

	/*Unused Capital*/
	$sql6 = "SELECT SUM(total_amount) as unusedcapital FROM tbl_total_addcapital";
	$result6 = mysqli_query($conn,$sql6);
	$row6=$result6->fetch_assoc();
	$unusedcapital=$row6['unusedcapital'];
	echo "unusedcapital : ".$unusedcapital;echo "<br>";

	/*Cash in Bank Amt*/
	$sql7 = "SELECT SUM(cashinbank) as cashinbankamt FROM tbl_cashinbank";
	$result7 = mysqli_query($conn,$sql7);
	$row7=$result7->fetch_assoc();
	$cashinbankamt=$row7['cashinbankamt'];
	echo "cashinbankamt : ".$cashinbankamt;echo "<br>";

	/*Total Active Loan*/
	$sql8 = "SELECT count(*) as tal FROM tbl_loan where is_active = 0 AND is_approve = 1 ";
	$result8 = mysqli_query($conn,$sql8);
	$row8=$result8->fetch_assoc();
	$totalactiveloan=$row8['tal'];
	echo "totalactiveloan : ".$totalactiveloan;echo "<br>";

	/*Total Closed Loan*/
	$sql9 = "SELECT count(*) as tcl FROM tbl_loan where is_active = 1 AND is_approve = 1 ";
	$result9 = mysqli_query($conn,$sql9);
	$row9=$result9->fetch_assoc();
	$totalclosedloan=$row9['tcl'];
	echo "totalclosedloan : ".$totalclosedloan;echo "<br>";

	/*Project date*/
	$sql10 = "SELECT project_2 FROM tbl_upcoming_report ORDER BY project_2 ASC limit 1  ";
	$result10 = mysqli_query($conn,$sql10);
	$row10=$result10->fetch_assoc();
	$projected_date=$row10['project_2'];
	//echo "totalclosedloan : ".$totalclosedloan;echo "<br>";
	$yrdata1= strtotime($projected_date);
    $projected_date1 = date('d-M-Y', $yrdata1);
	echo "projected_date1 : ".$projected_date1;echo "<br>";

	/*20000*/
	$sql11 = "SELECT month_avg FROM tbl_upcoming_report ORDER BY project_2 ASC limit 1  ";
	$result11 = mysqli_query($conn,$sql11);
	$row11=$result11->fetch_assoc();
	$projected_date=$row11['month_avg'];
	//echo "projected_date : ".$projected_date;echo "<br>";
    $test = (20000 - $cashinbankamt ) / $projected_date; 
    $test1 = round($test);
    $curr_date = date('Y-m-d');
    $f_value = date('Y-m-d', strtotime($curr_date.'+'. $test1.'days'));
    $yrdata= strtotime($f_value);
    $twentythousand = date('d-M-Y', $yrdata);
	echo "20000 : ".$twentythousand;echo "<br>";
	/*30000*/
	$test = (30000 - $cashinbankamt ) / $projected_date; 
    $test1 = round($test);
    $curr_date = date('Y-m-d');
    $f_value = date('Y-m-d', strtotime($curr_date.'+'. $test1.'days'));
    $yrdata= strtotime($f_value);
    $thirtythousand = date('d-M-Y', $yrdata);
	echo "30000 : ".$thirtythousand;echo "<br>";
	/*50000*/
	$test = (50000 - $cashinbankamt ) / $projected_date; 
    $test1 = round($test);
    $curr_date = date('Y-m-d');
    $f_value = date('Y-m-d', strtotime($curr_date.'+'. $test1.'days'));
    $yrdata= strtotime($f_value);
    $fiftythousand = date('d-M-Y', $yrdata);
	echo "50000 : ".$fiftythousand;echo "<br>";

	/*Last & days collection*/
	$sql12 = "SELECT loandate, SUM(pay_loan_amt) AS amount FROM tbl_pay Group BY loandate ORDER BY loandate DESC limit 7";
	$result12 = mysqli_query($conn,$sql12);
	
	$array = array();
	$cnt = 1;
	$cnt1 = 1;
	

	while ( $row = $result12->fetch_assoc()) {
	 	
	 	$array[]=$row;
	 	
	 }

	
		$to  = 'spoonfuelbistro@gmail.com';
		//$to  = 'pmalvi@ondot.com';
		//$to  = 'spatil@ondot.com';

        // subject
        $subject = 'Summary for '.$cur_date;

        // message
        $message = '
        <h3>This is the summary for '.$cur_date.' . </h3>
        <br> 
        <h4>Dashboard:</h4>
        <br>
        <table border="1" style="width:75%">
            
            <tr>
            	<td><b>Total Loan</b></td>
            	<td>'.$capital.'</td>
            </tr>
            <tr>
            	<td><b>Total Collection</b></td>
            	<td>'.$Daily_Collected_Total.'</td>
            </tr>
            <tr>
            	 <td><b>Interest</b></td>
            	 <td>'.$interest.'</td>
            </tr>
            <tr>
            	<td><b>Cash In Market</b></td>
            	<td>'.$cash_in_market.'</td>
            </tr>
            <tr>
            	<td><b>Capital</b></td>
            	<td>'.$capitalnew.'</td>
            </tr>
            <tr>
            	<td><b>Unused Capital</b></td>
            	<td>'.$unusedcapital.'</td>
            </tr>
            <tr>
            	<td><b>Re-Invest</b></td>
            	<td>'.$reinvest_amt.'</td>
            </tr>
            <tr>
            	<td><b>Expenses</b></td>
            	<td>'.$expenses.'</td>
            </tr>
            <tr>
            	<td><b>Cash In bank</b></td>
            	<td>'.$cashinbankamt.'</td>
            </tr>
            <tr>
            	<td><b>Profit And Loss</b></td>
            	<td>'.$pnl.'</td>
            </tr>
            <tr>
            	<td><b>Active Loan</b></td>
            	<td>'.$totalactiveloan.'</td>
            </tr>
            <tr>
            	<td><b>Closed Loan</b></td>
            	<td>'.$totalclosedloan.'</td>
            </tr>
            <tr>
            	<td><b>Projected Date</b></td>
            	<td>'.$projected_date1.'</td>
            </tr>
            <tr>
            	<td><b>20000</b></td>
            	<td>'.$twentythousand.'</td>
            </tr>
            <tr>
            	<td><b>30000</b></td>
            	<td>'.$thirtythousand.'</td>
            </tr>
            <tr>
            	<td><b>50000</b></td>
            	<td>'.$fiftythousand.'</td>
            </tr>
        </table> 
        <br>
        <h4>Last 7 Days Collection:</h4>
        <br>
        <table border="1" style="width:75%">
            
            <tr>
            	<th><b>Date</b></th>
            	<th><b>Amount</b></th>
            </tr>
            
            <tr>
            	<td><b>'.$array[0]['loandate'].'</b></td>
            	<td>'.$array[0]['amount'].'</td>
            </tr>
            <tr>
            	<td><b>'.$array[1]['loandate'].'</b></td>
            	<td>'.$array[1]['amount'].'</td>
            </tr>
            <tr>
            	<td><b>'.$array[2]['loandate'].'</b></td>
            	<td>'.$array[2]['amount'].'</td>
            </tr>
            <tr>
            	<td><b>'.$array[3]['loandate'].'</b></td>
            	<td>'.$array[3]['amount'].'</td>
            </tr>
            <tr>
            	<td><b>'.$array[4]['loandate'].'</b></td>
            	<td>'.$array[4]['amount'].'</td>
            </tr>
            <tr>
            	<td><b>'.$array[5]['loandate'].'</b></td>
            	<td>'.$array[5]['amount'].'</td>
            </tr>
            <tr>
            	<td><b>'.$array[6]['loandate'].'</b></td>
            	<td>'.$array[6]['amount'].'</td>
            </tr>

            
        </table> 
        <br>
        Thanks,
        <br>
		Cashpro India


                    ';
       	// To send HTML mail, the Content-type header must be set
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";

        // Additional headers
        $headers .= 'From: Cashproindia <cashproindia@cash.com>' . "\r\n";

        // Mail it
        mail($to, $subject, $message, $headers);
	
	
?>

<!-- Crons Job Path-->
<!-- php -q /home/thespoon/public_html/cashproindia/application/controllers/Cron_job.php -->