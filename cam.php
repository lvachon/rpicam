<?php
echo("HELLO!?");
$gain = floatval($_GET['gain']);
$shutter = intval($_GET['shutter']);
echo($gain."\n".$shutter."\n");
if($gain && $shutter){
	echo("still\n");
	$ou = array();
	$command =("rpicam-still --gain {$gain} --shutter {$shutter} --raw --immediate --nopreview --exposure=long --awb=daylight --output /var/www/html/snap.jpg ");
	file_put_contents("./commandqueue.txt",$command);
}

