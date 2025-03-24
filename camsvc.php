<?php
error_reporting(E_ERROR);
while(1){
	$cmd = file_get_contents("/var/www/html/commandqueue.txt");
	if(strlen($cmd)>10){
		exec($cmd);
		file_put_contents("/var/www/html/commandqueue.txt","");
	}
	sleep(1);
}
?>
