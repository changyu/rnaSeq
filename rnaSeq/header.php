<?php 
 require_once($_SERVER['DOCUMENT_ROOT'].'/web/wp-blog-header.php'); 
 //require_once 'wp-blog-header.php';
// $mydt = new DateTime("now", new DateTimeZone('America/New_York'));
 date_default_timezone_set("America/New_York"); 
 global $current_user;
      //get_currentuserinfo();
      if ('' == $current_user->ID ) {
         //no user logged in
        //auth_redirect();
	 wp_safe_redirect( wp_login_url(site_url()."/".basename(__DIR__) ) ); 
         exit();
      }
 //get_header();
 get_header("NGSTools");
 $powerusers=array('cf916@dfci.harvard.edu', 'Admin');
 #$puser=in_array($current_user->user_login, $powerusers);
 $puser=current_user_can('publish_pages');
 $curr_user= $current_user->user_login;
// echo 'Username: '.$current_user->user_login.'<br />';
//    echo 'User email: '.$current_user->user_email.'<br />';
//    echo 'User first name: '.$current_user->user_firstname.'<br />';
//    echo 'User last name: '.$current_user->user_lastname.'<br />';
//    echo 'User display name: '.$current_user->display_name.'<br />';
//    echo 'User ID: '.$current_user->ID.'<br />';

include_once("./localdata.php");

?>
<a name="mcenter"></a>
<link rel="stylesheet" href=".images/.style.css">
<script src=".images/.sorttable.js"></script>
<script src=".images/jquery-3.2.1.min.js"></script>
<script type="text/javascript">
<!--
jQuery(function($) {
                $('#sidebar').hide();
        });

function SetAllCheckBoxes(FormName, FieldName, CheckValue)
{
        if(!document.forms[FormName]) return;
        var objCheckBoxes = document.forms[FormName].elements[FieldName];
        if(!objCheckBoxes)
                return;
        var countCheckBoxes = objCheckBoxes.length;
        if(!countCheckBoxes)
                objCheckBoxes.checked = CheckValue;
        else
                // set the check value for all check boxes
                for(var i = 0; i < countCheckBoxes; i++)
                        objCheckBoxes[i].checked = CheckValue;
}

// -->
</script>
<div class=aligncenter >
<b><?php echo 'Login User: '.ucfirst($current_user->user_login).'</b>  @ '.date("Y-m-d h:i:s A") ;  ?>   
<a href="<?php echo wp_logout_url(); ?>" class='alignright'>Logout</a>
</div>
<div class="nav1">
<li class="<?php echo ($navsub=='bcl2fastq')?'active':'' ?>"> <a href="./bcl2fastq.php#mcenter" title="Converts BCL files (generated by Illumina sequencing systems) to standard FASTQ file formats">BCL2FASTQ</a></li>
<li class="<?php echo ($navsub=='RNASeq')?'active':'' ?>"> <a href="./rnaseq.php#mcenter" title="RNA-Seq (RNA sequencing),or whole transcriptome shotgun sequencing (WTSS)">RNASeq</a></li>
<!--li class="<?php echo ($navsub=='ChIPSeq')?'active':'' ?>"> <a href="./chipseq.php#mcenter" title="">ChIPSeq</a></li-->
<li class="<?php echo ($navsub=='FastQC')?'active':'' ?>" ><a href="./fastqc.php#mcenter"  title="A quality control tool for high throughput sequence data">FastQC</a></li>
<li class="<?php echo ($navsub=='ROSE')?'active':'' ?>" ><a href="./rose.php#mcenter" title="Rank Ordering of Super-Enhancers (ROSE)">ROSE</a></li>
<li class="<?php echo ($navsub=='MACS')?'active':'' ?>" ><a href="./macs.php#mcenter" title="Model-based Analysis for ChIP-Seq (MACS)">MACS</a></li>
<li class="<?php echo ($navsub=='Align')?'active':'' ?>" ><a href="./align.php#mcenter" title="Run sequence alignments">Align</a></li>
<li class="<?php echo ($navsub=='Status')?'active':'' ?>" ><a href="./status.php#mcenter" title="Check job and server status">Status</a></li>
<li class="<?php echo ($navsub=='Browse')?'active':'' ?>" ><a href="./browse_results.php#mcenter" title="Navigate through project folders to view/download results.">Browse</a></li>
<li class="<?php echo ($navsub=='About')?'active':'' ?>"> <a href="./index.php#mcenter">About</a></li>
</div>

<div id='box'>

