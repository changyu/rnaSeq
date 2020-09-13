<?php
set_time_limit(0); 
ob_implicit_flush(true);
ob_end_flush();

session_start();
$pid=$_SESSION["pid"];
$bam=$_SESSION["bf"];
$sam=$_SESSION["sf"];
$alg=$_SESSION["ag"];

$re="";
$resp=array();

 if(file_exists("/proc/$pid")){ 
  	$re=strtoupper($alg)." PID: $pid <pre>".preg_replace("/\/[a-z0-9\/]*\//","",shell_exec("ps -p ".$pid." -o %cpu,%mem,etime,cmd "))."</pre>";
  	$re.="Current running processes:<br> <pre>".shell_exec("pstree -p ".$pid." -n  ")."</pre>";
 }else{
	if($alg=="samtools"){
		$resp['done']=2; 
		$re="Job done! Please check the generated data in your folder.";
		//$cmd="samtools sort -m 20G -@ 20 ".$bam." ".$bam.".sort";
		$f=exec("nohup cp ".$sam." ".$bam." /home/dmp/ &");
		if($f){
			$re.="Also /home/dmp";
		}else{
			$re.="<br>nohup cp".$sam." ".$bam." /home/dmp/ &";
		}
	}else{
		$resp['done']=1; 
		$re="Aligment is done! Please check the generated data in your folder.";
	}
 }
$resp['re']=$re;
    
echo json_encode($resp);

