<?php 
$navsub="Status";
include './header.php'; ?>
     <h1> Job Status </h1> <hr>
       <a href='./browse_results.php'  target='_blank' > Browse <?php echo ucfirst($current_user->user_login);?>'s Folder</a>

<div>
        <h3>Job Status</h3>
            <div id="status"   ></div>
</div>
<div class='note'><b>Note</b>: 
<br><?php include './status_txt.php';?>
</div>
<script>
 var mycheck = setInterval(get_status, 1000);
 function get_status(){
           var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
           xhr.onreadystatechange = function() {
                    try{
                        if (xhr.readyState == 4 && xhr.status == 200){
			var new_response = xhr.responseText;
                        var result = JSON.parse( new_response );
                        document.getElementById("status").innerHTML = result.data + "<br />";
                        } 
                    }catch (e){ alert("[XHR STATECHANGE] Exception: " + e); }                     
                };
                xhr.open("POST", "observer1.php", true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.send();
        }
</script>
<?php include './footer.php'; ?>
