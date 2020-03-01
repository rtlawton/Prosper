<?php 
require_once('config.php');
require_once('utilities.php');
require_once('fpdf.php');
try {
    $db = new PDO(PDO_CONNECT, USER, PSWD, $PDO_OPTIONS);
    $stmt = $db->prepare(SQL_GETBANKFEES);
    $stmt->execute(array());
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
catch(PDOException $ex) {
    echo $ex->getMessage();
}
$db = null; 
//create array of fundnames
$funds = array();
foreach ($result as $row) {
    array_push($funds,$row['shortname']);
}
$funds = array_values(array_unique($funds));
//create crosstab array
$cross = array();
$yr = 0;
$mn = 0;
foreach ($result as $row) {
    if ($row['yr'] != $yr || $row['mn'] != $mn) {
        $yr = $row['yr'];
        $mn = $row['mn'];
        $cross_row = array($row['yr'] . ' ' . $row['mname'],array_fill(0,count($funds),0),0);
    } else {
        $cross_row = array_pop($cross);
    }
    $row['shortname'] = trim($row['shortname']);
    $ind = array_search($row['shortname'],$funds);
    $cross_row[1][$ind] = 'R ' . number_format($row['tot'],2);
    $cross_row[2] += $row['tot'];
    array_push($cross, $cross_row);
}
// create pdf
$pdf = new FPDF();
$pdf->AddPage('L');
$pdf->SetFont('Arial','B',24);
$pdf->Cell(200,20,'BANK FEES REPORT',0);
$pdf->Ln();
$pdf->SetFont('Arial','B',12);
$pdf->Cell(30,5,'',1);
foreach($funds as $fund) {
        $pdf->Cell(20,5,$fund,1);
}
$pdf->Cell(20,5,'TOTAL',1);
$pdf->SetFont('Arial','',10);
$pdf->Ln();
foreach($cross as $cross_row) {
    $pdf->Cell(30,5,$cross_row[0],1);
    $tots = $cross_row[1];
    foreach($tots as $tot){
        $pdf->Cell(20,5,$tot,1,0,'R');
    }
    $pdf->Cell(20,5,'R ' . number_format($cross_row[2]),1,0,'R');
    $pdf->Ln();
}
$pdf->Output('F','BKRP.pdf');
?>