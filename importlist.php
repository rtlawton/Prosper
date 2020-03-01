<?php 
require_once('utilities.php');
$fu = $_POST["fu"];

try {
    $db = new PDO(PDO_CONNECT, USER, PSWD, $PDO_OPTIONS);
    $stmt = $db->prepare('SELECT filenamestart, protocol FROM funds WHERE id = ?');
    $stmt->execute(array($fu)); 
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    exec("chmod -R ugo+r " . IMPORT_PATH);
    $files = glob(IMPORT_PATH .'/' . $row['filenamestart'] . '*.csv');
    echo "<option value='xxx'>Select statement</option>";
    foreach($files as $filename) {
        if (strpos($filename,'(') == false) {
            $offset = strlen($row['filenamestart']) + strlen(IMPORT_PATH) + 1;
            $datepart = substr($filename,$offset);
            $datepart = substr($datepart,0,strlen($datepart) - 4);
            if ($row['protocol'] == 'Standard') {
                $year = substr($datepart,0,4);
                $month = substr($datepart,4,2);
                $day = substr($datepart,6,2);
                /*$year = '0000';
                $month = '00';
                $day = '00';*/
            } else {
                $dateparts = explode("_",$datepart);
                $year = $dateparts[2];
                $month = $dateparts[0];
                $day = $dateparts[1];
            }
            $date = $day .'/'. $month .'/'. $year;
            echo "<option value='" . $filename . "'>" . $date . "</option>";
        }
    }
}
catch(PDOException $ex) {
    echo $ex->getMessage();
}
$db = null; 
?>