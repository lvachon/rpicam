<?php
error_reporting(E_ERROR);

while(1){
	$cmd = file_get_contents("/var/www/html/commandqueue.txt");
	if(strlen($cmd)>10){
		clearstatcache();
		$ofs = filesize("/var/www/html/snap.jpg");
		exec($cmd);
		clearstatcache();
		$nfs = filesize("/var/www/html/snap.jpg");
		echo("$ofs -> $nfs \n");
		if($nfs!=$ofs){
			file_put_contents("/var/www/html/commandqueue.txt","");
		}
	}
	sleep(1);
}
?>
