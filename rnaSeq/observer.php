<?php
set_time_limit(0); 
ob_implicit_flush(true);
ob_end_flush();

$msg=$_POST['msg'];
$pid=$_POST['pid'];
$resp=array();
$resp['key']=$msg;
$re="";
if(is_int($pid)){
 if(file_exists("/proc/$pid")){
        $re=$msg." + ".$pid."========";
  	$re=strtoupper($msg)." PID: $pid <pre>".preg_replace("/\/[a-z0-9\/]*\//","",shell_exec("ps -p ".$pid." -o %cpu,%mem,etime,cmd "))."</pre>";
  	$re.="Current running processes:<br> <pre>".shell_exec("pstree -p ".$pid." -n  ")."</pre>";
 }else{
        $resp['key']="done";
	$re="$pid is done!";
             
 }
}else{
$resp['key']="bad";
$re="pid='$pid'; And '$msg' failed.";
}
$resp['data']=$re;
    
echo json_encode($resp);

