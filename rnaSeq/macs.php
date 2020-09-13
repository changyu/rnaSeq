<?php 
$navsub="MACS";

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

<script>
$('#sqfile').submit(function(e){
  
    e.preventDefault(); // Prevent Default Submission
  
    $.ajax({
 url: 'macs.php',
 type: 'POST',
 data: $(this).serialize(), // it will serialize the form data
        dataType: 'html'
    })
    .done(function(data){
     $('#form-content').fadeOut('slow', function(){
          $('#form-content').fadeIn('slow').html(data);
        });
    })
    .fail(function(){
 alert('Ajax Submit Failed ...'); 
    });
});
$(function(){
    $('#control').change(function(){
        $("#ctrfiles").toggle(this.checked);
    }).change;
});

</script>
<main id="main" class="site-main" role="main">
<h1>Model-based Analysis for ChIP-Seq (MACS)</h1><hr>
<div id="form-content">

<form id='sqfile' action="macs_seq.php" method="post" enctype="multipart/form-data" autocomplete="off">
<b>Project name:</b> <input type="text" name="proj" id="proj"  style="width:40%" value="MACS.<?php echo date("Y-m-d");?>">
<label><b>MACS Version:</b></label> <select name="macs">
  <option value="macs14">1.4</option>
  <option value="macs2">2.0</option>
</select>
<!--input type="hidden" name="macs" value="macs14"-->
<label>PValue:</b></label> <select name="pvalue">
  <option value="1e-1">1e-1</option>
  <option value="1e-3">1e-3</option>
 <option value="1e-5">1e-5</option>
  <option value="1e-7">1e-7</option>
 <option value="1e-9" selected="selected">1e-9</option>
  <option value="1e-11">1e-11</option>
 <option value="1e-13">1e-13</option>
  <option value="1e-15">1e-15</option>
 <option value="1e-17">1e-17</option>
  <option value="1e-19">1e-19</option>

</select>
<!--b>Color:</b> <input type="color" name="color" value="#00000"-->
<input type="hidden" name="color" value="#00000">
<br><br>
<h4>Please paste your sequence files with full path here:</h4>
<div  style="border:1px solid #CCCCCC; line-height:1.5; text-align:left; color:#333333; font-size:80%; padding:4px 40px; margin: 5px 0;" >
<b>Notes:</b> Use comma <b>","</b> to separate different lanes of the same mate (1st or 2nd). Example: '/path/seqfile1.fastq.gz,/path/seqfile2.fastq.gz'. </div>
<label for="chip">ChIP Seq (Sorted Bam file)</label>
<textarea rows="2" cols="20" name="sortbam">/data/share/test/goldStd/LMS20_bam/LMS20_H3K27Ac_hg19.sorted.bam
</textarea>
<label for="ctr">Control (Sorted Bam file): </label><input type="checkbox" id="control" name="control" value="control">
<!--input id="ctrl" name="ctrfiles" type="text"  style="width:95%;display:block;" value="/data/share/test/goldStd/LMS20_bam/LMS20_input_hg19.sorted.bam"-->
<textarea rows="2" cols="20" id="ctrfiles" name="ctrfiles" style="display:none">/data/share/test/goldStd/LMS20_bam/LMS20_input_hg19.sorted.bam
</textarea>
<label>Job Notification:</label><input type="checkbox" id="notify" name="notify" value="notify" >
<select name="gnm">
  <!--option value="zbfish">Danio rerio</option-->
  <option value="human">Homo sapiens</option>
  <!--option value="humanER">Homo sapiens ERCC</option-->
  <option value="mouse">Mus musculus</option>
  <!--option value="mouseER">Mus musculus ERCC</option-->
  <!--option value="rat">Rattus norvegicus</option-->
  <!--option value="ratER">Rattus norvegicus ERCC</option-->
  <!--option value="fly">Drosophila melanogaster</option-->
</select>
<input type="submit" value="Submit" /> 
</form>
</div>
<div class='note'><b>Note:</b><br>
<?php include './macs_txt.php'; ?>
</div>
</main><!-- .site-main -->
<?php include './footer.php';?>
