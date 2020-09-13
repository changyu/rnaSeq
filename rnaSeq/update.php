<?php
include './head.html';

if(isset($_POST['update']))
  {
    include '../opendb.php';
    $init="update sheet_curr set status='a' where status='c'";
    $deact = "update sheet_curr a left join sheet_new b using (uid) set status='d' where b.uid is null and status!='d'";
    $change = "update sheet_curr a join sheet_new b using (uid) set status='c', a.uid=b.uid,a.fullName=b.fullName,a.loginID=b.loginID,a.email=b.email,a.dept=b.dept,a.descrp=b.descrp,a.office=b.office,a.phone=b.phone, a.md5=b.md5 where a.md5!=b.md5";
    #$replace="replace into sheet_curr (uid,fullName,loginID,email,dept,descrp,office,phone,ts,md5)  select a.uid,a.fullName,a.loginID,a.email,a.dept,a.descrp,a.office,a.phone,a.ts,md5(a.uid,a.fullName,a.loginID,a.email,a.dept,a.descrp,a.office,a.phone) from sheet_new a, sheet_curr b where a.uid=b.uid and a.md5!=b.md5";
    $insert = "insert into sheet_curr (uid,fullName,loginID,email,dept,descrp,office,phone,ts,md5)  select a.uid,a.fullName,a.loginID,a.email,a.dept,a.descrp,a.office,a.phone,a.ts,md5(concat(a.uid,a.fullName,a.loginID,a.email,a.dept,a.descrp,a.office,a.phone)) from sheet_new a left join sheet_curr b using (uid)  where b.uid is null";
    #echo $query;
    $del=$mod=$new=0;
    try {	  
      $conn2->exec($init);
      $del = $conn2->exec($deact);
      $mod = $conn2->exec($change);
      $new = $conn2->exec( $insert);
    } catch(PDOException $e) {
      echo 'ERROR: ' . $e->getMessage();
    }

   
    echo "<h2>File Update Report</h2>";
    echo "<br>Current table is updated.<br>";
    echo "<div><li>New deactived: $del rows.<li>New modified: $mod rows.<li>New added: $new rows.</div>";
    try {	  
      echo "<h4>Stats</h4>";      
      $qry="select (CASE when status='a' then 'active' when status='d' then 'deactive' when status='c' then 'modified' END) Stat, count(*) Num,ts Timestamp from sheet_curr group by Stat,ts";
      $qrys="select (CASE when status='a' then 'active' when status='d' then 'deactive' when status='c' then 'modified' END) Stat, count(*) 'Total Num' from sheet_curr group by Stat";
      echo "Summary".get_tab($conn2,$qrys);
      echo "<br>Details".get_tab($conn2,$qry);
    } catch(PDOException $e) {
      echo 'ERROR: ' . $e->getMessage();
    }
    echo "<br><form action='./dfci_users.php'  method='post' enctype='multipart/form-data'><input type='submit' name='parseusers' value='Parse Users'/></form> <br>";
     include '../closedb.php';
}

include './foot.html';

function get_tab($db,$qr){
    $stmt = $db->prepare("$qr");
    $stmt->execute();
    $first=true;
    $tab="<table class='report'>";
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
	if($first){
	  $td=$th="<tr>";
	  foreach ($row as $ky => $vl){
	    $th.="<th>$ky</th>";
	    $td.="<td>$vl</td>";
	  }
	  $th.="</tr>";
	  $td.="</tr>";
	  $tab.=$th.$td;
	  $first = false;
	}else{
	  $tab.="<tr>";
	  foreach ($row as $k => $val){	  $tab.="<td>$val</td>"; } 
	  $tab.='</tr>';
	}
      }
      $tab.="</table>";
      return $tab;
 }
?>
