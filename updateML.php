<?php
require_once('config.php');
$protocol = $_POST["pr"];
$db = new PDO(PDO_CONNECT, USER, PSWD, $PDO_OPTIONS);
$stmt = $db->prepare("SHOW TABLES LIKE 'Model_%'");
$stmt->execute(array());
while($result = $stmt->fetch(PDO::FETCH_NUM)) {
    if ($result[0] == "Model_" . $protocol) {
        $cmd =  RSCRIPT_PATH . " ml.R " . $protocol;
        exec($cmd, $output, $return);
        if ($return == 0) {
            echo "ML updated";
        } else {
            echo "Update failed";
        }
        return;
    }
}
echo "No machine learning exists for this fund";

?>