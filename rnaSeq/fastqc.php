<?php 
$navsub="FastQC";

include './header.php';

//$usercode=md5($current_user->user_login.$current_user->user_email);
//echo "$usercode=$current_user->user_login+$current_user->user_email";
//$user_dirname =  getcwd().'/user_data/'.$usercode;

if ( ! file_exists( $user_dirname ) ) {
    wp_mkdir_p( $user_dirname );
    //echo "user name == ".$user_dirname ;
}

?>

<script type="text/javascript" src="http://code.jquery.com/jquery.min.js"></script>


<main id="main" class="site-main" role="main">
<div id="form-content">

<form id='sqfile' action="fastqc_seq.php" method="post" enctype="multipart/form-data" autocomplete="off">
	<b>Project name:</b> <input type="text" name="proj" id="proj"  style="width:40%" value="FastQC.<?php echo date("Y-m-d");?>">
<br><br>
	<label for="fastqc">Please paste your sequence files (fastq or bam) with full path here:</label>
<!--input name="fastqc" type="text" id="fastqc" style="width:95%;display:block; "  value="/data/share/test/rose2/GISTT1_DMSO_H3K27Ac_hg19.sorted.bam"-->
<textarea rows="2" cols="20" name="fastqc" id="fastqc">/data/share/test/rose2/GISTT1_DMSO_H3K27Ac_hg19.sorted.bam, /data/share/test/rose2/GISTT1_DMSO_input_hg19.sorted.bam</textarea>
 <div style = "width:40%;float:right;" >
<label>Job Notification:</label><input type="checkbox" id="notify" name="notify" >
<input type="submit" value="Submit" /> </div>
</form>
</div>
<div class='note'><b>Note:</b> <br>
<?php include 'fastqc_txt.php'; ?>
</div>
</main><!-- .site-main -->
<?php include './footer.php';?>
