<?php
	date_default_timezone_set("America/New_York");
	exec("rpicam-still -c /var/www/html/tlapse/live_settings.txt --output /var/www/html/tlapse/latest.jpg");
	sleep(1);
	exec("cp /var/www/html/tlapse/latest.jpg /var/www/html/tlapse/".date("Y-m-d_H-i-s").".jpg");
	$N=10000;
	$MAX_OE_PCT=0.05;
	$TARGET_Y=40;
	$P = 1.0;
	$MAX_SHUTTER = 5000000;
	$MIN_SHUTTER = 50;
	$settingsFile = file_get_contents("/var/www/html/tlapse/live_settings.txt");
	$settingsLines = explode("\n",$settingsFile);
	$settings = array();
	foreach($settingsLines as $line){
		$parts=explode("=",$line);
		if(strlen($parts[0])){
			$settings[$parts[0]]=$parts[1];
		}
	}
	if(file_exists("/var/www/html/tlapse/latest.jpg")){
		$im = imagecreatefromjpeg("/var/www/html/tlapse/latest.jpg");
		//sample N random points and get the average exposure
		$avgY=0;
		$numOE=0;
		for($i=0;$i<$N;$i++){
			//we only care about exposure, so convert RGB to luminance
			$c = imagecolorat($im,rand(0,imagesx($im)-1),rand(0,imagesy($im)-1));
			$r=$c&255;
			$g=($c/256)&255;
			$b=($c/65536)&255;
			$lR = pow($r/255,2.2);
			$lG = pow($g/255,2.2);
			$lB = pow($b/255,2.2);
			$y= 0.299*$r + 0.587*$g + 0.114*$b;
			$lY = 0.299*$lR+0.587*$lG+0.114*$lB;
			$lY = $lY*255;
			if($r>250||$g>250||$b>250){
				$numOE+=1;
			}
			$avgY+=$lY;
			//echo("$c=;r=$r,g=$g,b=$b,y=$y\n");
		}
		$avgY/=$N;
		echo("\n----------------------------------------\n");
		echo("AVERAGE Y:".$avgY." / $TARGET_Y\n");
		echo("NUM OVEREX:".$numOE." (".strval(floor(1000*$numOE/$N)/10.0)."% / ".strval(100.0*$MAX_OE_PCT)."%)\n");
		//modify settings here
		$err = $TARGET_Y/$avgY;
		$correction = $P * $settings['shutter'] * $err;
		if($correction>0 && $numOE>$MAX_OE_PCT*$N){$correction=$P;}
		if(abs($correction)>=100 || true){
			$settings['shutter']=round(($settings['shutter']+$correction)/2);
			if($settings['shutter']<$MIN_SHUTTER){
				$settings['shutter']=$MIN_SHUTTER;
				echo("shutter too fast, lowering gain\n");
				$settings['gain']-=0.5;
			}
			if($settings['shutter']>$MAX_SHUTTER){
				$settings['shutter']=$MAX_SHUTTER;
				echo("shutter too slow, raising gain\n");
				$settings['gain']+=0.5;
			}
			if($settings['gain']<1){
				$settings['gain']=1;
				echo("gain too low, limiting to 1\n");
			}
			if($settings['gain']>8){
				echo("gain too high, limiting to 8\n");
				$settings['gain']=8;
			}
			echo("Updating exposure time to {$settings['shutter']} uS with a $correction uS adjustment\n");
		}
		file_put_contents("/var/www/html/tlapse/ex_stats.txt","avgY:{$avgY},numOE:{$numOE},cor:$correction,shutter:{$settings['shutter']}");
	}
	var_dump($settings);
	
	$settingsString = "";
	foreach($settings as $key=>$val){
		if(strlen($key)){
			$settingsString.="{$key}={$val}\n";
		}
	}
	file_put_contents("/var/www/html/tlapse/live_settings.txt",$settingsString);
