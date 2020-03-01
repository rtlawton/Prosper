<?php
require_once('config.php');

$return_var = NULL;
$output = NULL;
date_default_timezone_set(TIME_ZONE);
$now = date("YmdHis");
$command = SQL_DUMP_PATH . " --user=" . USER . " --password=" . PSWD ." " . D_BASE . " > " . BACKUP_PATH . $now . ".sql";
exec($command, $output, $return_var);
if ($return_var != 0) {
    echo $return_var . '/XX/' . $command;
} else {
    echo '0';
};
?>