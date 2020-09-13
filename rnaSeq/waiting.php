<?php
$navsub=$_GET['sub'];
include './header.php';
$jnam=explode(",", urldecode($_GET['jnam']));
$log=  explode(",",urldecode($_GET['log']));
$mesg=  urldecode($_GET['mesg']);

?>
<h3><?php echo stripslashes(implode("",$jnam)); ?>! </h3>
     <div style="margin:20px;float:left;">
     <?php
	for($i=0; $i<sizeof($log);$i++){
     	echo "<li> Check <a href='./download.php?f=".$log[$i]."' target='_blank'>log file $i</a>";
     }
     ?>
      <li> Browse <a href='./browse_results.php'  target='_blank' > <?php echo ucfirst($current_user->user_login);?>'s Folder</a>
       </div>
 
 <div id="status"  style=" ">
        <h3>Console</h3>
           
</div> </div>
<script>
 var mycheck = setInterval(get_status, 1000,<?php echo "'$mesg' , '$jnam'";?> );
 function get_status(msg,jnm){
	    var params='msg='+msg+'&jnm='+jnm;
            var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
           xhr.onreadystatechange = function() {
                    try{
                        if (xhr.readyState == 4 && xhr.status == 200){
			var new_response = xhr.responseText;
                        var result = JSON.parse( new_response );
                        document.getElementById("status").innerHTML = result.data + "<br />";
 			/*if(result.key=="done"){
				clearInterval(mycheck);
				 document.forms["wkf"].submit();
			 }elseif(result.key=="bad"){
				clearInterval(mycheck);
				document.getElementById("nxt1").style.visibility='visible';
			 }	*/
                        } 
                    }catch (e){ alert("[XHR STATECHANGE] Exception: " + e); }                     
                };
                xhr.open("POST", "observer2.php", true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.send(params);
        }

//console.log("test");
 </script>


<?php include './footer.php'; ?>