<?php
date_default_timezone_set("America/New_York");
exec("find /var/www/html/tlapse/ -type f -size 0b -delete");
for($i=1;$i<30;$i++){
	$pastTime = time()-86400*$i;
	$pastString = date("Y-m-d",$pastTime);
	$commandString = "scp -i /home/brock/brock__scp_key /var/www/html/tlapse/{$pastString}_*.jpg brock@192.168.1.14:/archive/cams/sky/";
	$killString = "rm /var/www/html/tlapse/{$pastString}_*.jpg";
	echo "Copying $pastString\n";
	exec($commandString);
	echo "Killing $pastString\n";
	exec($killString);
	//echo($commandString."\n".$killString."\n\n");
}
echo("Tail-truncating exposure log\n");
exec("tail -1440 /var/www/html/tlapse/ex_stats.txt | sponge /var/www/html/tlapse/ex_stats.txt");


