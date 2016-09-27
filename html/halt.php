<?php
require_once "/var/www/static/common.php";
#$oldPath=getcwd();
p( "Going for reboot...");
try {
	p("<br>");
	p("Rebooting now, give me a minute or two then go back to \"http://pi.local\" !");
	header("Location: /");
	shell_exec("sudo /sbin/reboot -p");
} catch (Exception $e) {
	p($e);
}
#chdir($oldPath);
?>
