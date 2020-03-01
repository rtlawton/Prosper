<?php 
require_once('config.php');
$yr = $_POST["yr"];
$ac = $_POST["ac"];
$ff = $_POST["ff"];
$total = $_POST["tot"];
$taxFile = fopen(TAX_FILE_PATH . $ac . $yr . ".csv", "w");
if (!$taxFile){
	exit ('File create failed');
}
if (!fwrite($taxFile, ',,,TAX YEAR ' . $yr . '  : ' . $ac . "\n\n")){
	exit ('File write failed');
}
if (!fwrite($taxFile, ',TOTAL: ,' . $total . "\n\n")){
	exit ('File write failed');
}
if (!fwrite($taxFile, $ff)) {
	exit ('File write failed');
}
fclose($taxFile);
exit ('Records successfully exported');
?>