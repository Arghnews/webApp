<?php
require_once "/var/www/static/common.php";
#$oldPath=getcwd();
echo "Going for reboot...";
try {
	echo "<br>";
	echo "Rebooting now, give me a minute or two then go back to \"http://pi.local\" !";
	shell_exec("sudo /sbin/reboot");
} catch (Exception $e) {
	echo $e;
}
#chdir($oldPath);
?>
