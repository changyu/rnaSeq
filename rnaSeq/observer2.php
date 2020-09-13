<?php
set_time_limit(0); 
ob_implicit_flush(true);
ob_end_flush();

$msg=$_POST['msg'];
$jnm=$_POST['jnm'];
$resp=array();
$resp['key']=$msg;
$resp['data']=strtoupper($msg).date("H:i:s A")."<pre>".shell_exec("qstat -f -u www-data")."</pre>";
    
echo json_encode($resp);

