<?php
set_time_limit(0); 
ob_implicit_flush(true);
ob_end_flush();

$resp=array();

$resp['data']="<div>".date("h:i:s A")."</div> <pre>".shell_exec("qstat -f -u www-data")."</pre>";
    
echo json_encode($resp);

