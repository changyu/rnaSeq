<?php include './header.php';

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

$(document1).ready(function(){
    $('#paired').change(function(){
        if(this.checked)
            $('#filepath2').fadeIn('slow');
        else
            $('#filepath2').fadeOut('slow');

    });
});
</script>
        <main id="main" class="site-main" role="main">


<div id="form-content">
<i>Notes:</i><br>
<!--For paired sequences, use 4 colons  <b>"::::"</b> to separate the mates.<br>
use comma <b>","</b> to separate different lanes of the same mate (1st or 2nd).<br> 
Example: '/path/a1.fastq.gz,/path/a2.fastq.gz::::/path/b1.fastq.gz,/path/b2.fastq.gz'.-->
<form id='sqfile' action="aligns.php" method="post" enctype="multipart/form-data" autocomplete="off">
<label for="filepath">
<h5>Please paste your sequence files with full path here</h5>
</label>
<!--input name="filepath" type="textarea" rows="4" cols="50"  value="/home/cf916/20160417-GISTT1-input-DMSO-1-ML2961_S4_R1_001.fastq.gz"/-->
<textarea rows="2" cols="20" name="filepath">/home/cf916/20160417-GISTT1-input-DMSO-1-ML2961_S4_R1_001.fastq.gz</textarea>
<input type="checkbox" id="paired"><label>Paired</label>
<textarea rows="2" cols="20" name="filepath2"i id="filepath2">/home/cf916/20160417-GISTT1-input-DMSO-1-ML2961_S4_R2_001.fastq.gz</textarea>

<input type="submit" value="Submit" /></form>
</div>


<!--h1>Upload Employee Information in tsv File into Database</h1>
<hr>
Please check the tsv file has the right columns.<br>
<i>uid,fullName,loginID,email,dept,descrp,office,phone</i>
<hr>
<form action="upload.php" method="post" enctype="multipart/form-data">
<label for="fileselect">File to upload:</label>
<input name="fileselect" type="file" />
<input name="upload" type="submit" value="Submit" /></form-->
        </main><!-- .site-main -->
<script>
$(document).ready(function(){
    $("button").click(function(){
        $("p").toggle();
    });
});
</script>
</head>
<body>

<p>This is a paragraph.</p>

<button>Toggle between hide() and show()</button>
<?php include './footer.php';?>
