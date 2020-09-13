<?php 
$navsub="ROSE";

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
 url: 'aligns.php',
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
    $('#paired').change(function () {
        $("#filepath2").toggle(this.checked);
    }).change();
});

</script>
<main id="main" class="site-main" role="main">
<h1>Rank Ordering of Super-Enhancers (ROSE)</h1>
<hr>
<div id="form-content">
<form id='sqfile' action="rose_seq.php" method="post" enctype="multipart/form-data" autocomplete="off">
<b>Project name:</b> <input type="text" name="proj" id="proj"  style="width:40%" value="ROSE.<?php echo date("Y-m-d");?>">
<h4>Please paste your sequence files with full path here:</h4>
<label for="gff">Input Region GFF or BED file </label>
<input name="gff" type="text"  style="width:95%;display:block;" value="/data/share/test/rose2/GISTT1_DMSO_H3K27Ac_peaks.bed">
<label for="rankby">Rankby BAM file</label>
<input name="rankby" type="text"  style="width:95%;display:block; "  value="/data/share/test/rose2/GISTT1_DMSO_H3K27Ac_hg19.sorted.bam">
<label for="rankby">Control file</label>
<input name="ctrl" type="text"  style="width:95%;display:block; "  value="/data/share/test/rose2/GISTT1_DMSO_input_hg19.sorted.bam">
<label for="tss">TSS exclusion zone size: </label>
<input name="tss" type="text"  style="width:20%;margin:0 20px 30px 0; "  value="2500">
<label>Job Notification:</label><input type="checkbox" id="notify" name="notify" >

<select name="gnm">
  <!--option value="zbfish">Danio rerio</option-->
  <option value="human">Homo sapiens</option>
  <option value="humanER">Homo sapiens ERCC</option>
  <option value="mouse">Mus musculus</option>
  <!--option value="mouseER">Mus musculus ERCC</option>
  <option value="rat">Rattus norvegicus</option>
  <option value="ratER">Rattus norvegicus ERCC</option>
  <option value="fly">Drosophila melanogaster</option-->
</select>
<input type="submit" value="Submit" /> 
</form>
</div>
<div class='note'><b>Note:</b><br>
<?php include './rose_txt.php'; ?>
</div>
</main><!-- .site-main -->
<?php include './footer.php';?>
