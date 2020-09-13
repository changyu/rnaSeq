<?php
function gdownload($f_name, $f_size, $r_name,$t_name){
// header_remove();  
 header('Content-Description: File Transfer');
 #header('Content-Type:'.$x_name);
 #header('Content-Type: application/octet-stream');
 header("Content-Transfer-Encoding: Binary"); 
 header('Content-Length: ' . $f_size);
 header('Content-Disposition: attachment; filename=' . $f_name);
 readfile($r_name);
 exit;
 }
 session_start();
 $user_dirname= (isset($_SESSION['curr_dir']))?$_SESSION['curr_dir']: $_SESSION['user_dir'];
 $bnf=$_GET['f'];
 $ext = pathinfo($bnf, PATHINFO_EXTENSION);
 
 $mdf=$user_dirname."/".$bnf;
 $typ=mime_content_type($mdf);
  //echo "$mdf=======!!!";
  
 if(is_dir($mdf)){
   $_SESSION['curr_dir']=$mdf;
   header("Location: ./browse_results.php"); /* Redirect browser */
   exit();
 }else{
 $szf=filesize($mdf);
# echo "--------$typ---------";
 if($szf<2000000 && $ext!='png' && $ext!='pdf'){
  include("./header.php"); 
  //echo "$mdf====$user_dirname";
  $txt=file_get_contents($mdf);
  echo "<h4>$bnf</h4><hr/>";
  //echo nl2br(preg_replace("/\/[a-zA-Z0-9\/_]*\//","",file_get_contents($mdf)));
  echo nl2br(str_replace($user_dirname,"/mydata_path",$txt));
  echo "<br><br><hr/>";
  if(strlen($txt)<1){
?>
  <script>
 setTimeout(function(){
   window.location.reload(1);
}, 1000);
</script>
<?php
}
  include("./footer.php"); 
}else{
  //ini_set('memory_limit', '10240M');  
  //echo "File is too big. File Size:  $szf";
  //$data = implode("", file($mdf));
  //$gzdata = gzencode($data, 9);
   
  gdownload($bnf,$szf,$mdf,$typ);
}
}
?>

