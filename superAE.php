<?php
	date_default_timezone_set("America/New_York");
	//exec("echo none > /sys/class/leds/PWR/trigger");
	echo("CAPTURING IMAGE\n");
	$ot = hrtime(true);
	exec("rpicam-still -c /var/www/html/tlapse/live_settings.txt --output /var/www/html/tlapse/latest.jpg");
	$t = hrtime(true);
	$dt = ($t-$ot)/1000000000.0;
	echo("IMAGE CAPTURED ($dt)\n");
	sleep(1);
	exec("cp /var/www/html/tlapse/latest.jpg /var/www/html/tlapse/".date("Y-m-d_H-i-s").".jpg");
	echo("IMAGE COPIED, PROCESSING...\n");
	$N=10000;
	$MAX_OE_PCT=0.03;
	$TARGET_Y=64;
	$P = 1.0;
	$MAX_SHUTTER = 50000000;
	$MIN_SHUTTER = 10;
	$MAX_GAIN = 8;
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
		//compute error, absolute target, and correction target
		$err = $TARGET_Y/$avgY;//How exposed are we 50%, 100%, etc...
		$target = $settings['shutter'] * $err;//Choose new shutter to correct for previous exposure
		$correction = ($target - $settings['shutter']) * $P +$settings['shutter'];//Move setting towards that shutter based on our P value
		$oshutter = $settings['shutter'];
		//if($correction>0 && $numOE>$MAX_OE_PCT*$N){$correction=min($correction,$settings['shutter']*0.75);}//If there are too many overexposed pixels, make sure the shutter lowers by at least 25%
		if($settings['gain']>1 && $correction<$settings['shutter']){//If we're lowering the shutter and gain is non-unity, lower gain instead
			$settings['gain']-=(1.0-$correction/$settings['shutter']);
			$correction=$settings['shutter'];
			echo("Preferring gain over shutter when lowering exposure\n");
		}
		//Apply correction
		$settings['shutter']=$correction;
		//Apply limits
		if($settings['shutter']<$MIN_SHUTTER){//Shutter to fast, lowering gain if we can
			$settings['shutter']=$MIN_SHUTTER;
			echo("shutter too fast, lowering gain\n");
			$settings['gain']-=($MIN_SHUTTER-$settings['shutter'])/$MIN_SHUTTER;
		}
		if($settings['shutter']>$MAX_SHUTTER){//Shutter too high, raising gain if we can
			$settings['gain']+=(($settings['shutter']-$MAX_SHUTTER)/$MAX_SHUTTER);
			$settings['shutter']=$MAX_SHUTTER;
			echo("shutter too slow, raising gain\n");
		}
		if($settings['gain']<1){
			$settings['gain']=1;
			echo("gain too low, limiting to 1\n");
		}
		if($settings['gain']>$MAX_GAIN){
			echo("gain too high, limiting to $MAX_GAIN\n");
			$settings['gain']=$MAX_GAIN;
		}
		echo("PROCESSING DONE\n");
		echo("Updating exposure time from {$oshutter}uS to {$settings['shutter']}uS with a target of {$target}uS \n");
		
		$ou = array();
		exec("vcgencmd measure_temp",$ou);
		$temp = floatval(explode("=",$ou[0])[1]);
		file_put_contents("/var/www/html/tlapse/ex_stats.txt","{\"time\":".strval(time()).",\"avgY\":{$avgY},\"numOE\":{$numOE},\"shutter\":{$settings['shutter']},\"target\":{$target},\"gain\":{$settings['gain']},\"rpicam_time\":{$dt},\"temp\":{$temp}},\n",FILE_APPEND);
	}
	var_dump($settings);
	
	$settingsString = "";
	foreach($settings as $key=>$val){
		if(strlen($key)){
			$settingsString.="{$key}={$val}\n";
		}
	}
	file_put_contents("/var/www/html/tlapse/live_settings.txt",$settingsString);
