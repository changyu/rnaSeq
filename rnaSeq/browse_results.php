<?php 
$navsub="Browse";
include './header.php';

function delfiles($target) {
    if(is_dir($target)){
	if (substr($target, strlen($target) - 1, 1) != '/') {$target .= '/';}
        $files = glob( $target . '*', GLOB_MARK ); //GLOB_MARK adds a slash to directories returned
	//echo "<br>Dir==[". print_r($files)."]==<br>";;
	foreach( $files as $file ){  
		delfiles( $file ); //echo "<br>go--[$file]<br>";
	}
        rmdir( $target );
    }elseif(is_file($target)) {
         unlink( $target );
    }else{unlink($target);}
}

function deleteDir($dirPath) {
    if (! is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
}

function findexts ($filename) {
          $filename=strtolower($filename);
          $exts = pathinfo($filename, PATHINFO_EXTENSION);
          return $exts;
}

//main

$my_dirname= empty( $_SESSION['curr_dir'] )? $_SESSION['user_dir']:$_SESSION['curr_dir'];
if($_POST['sub']=="root") {
	$_SESSION['curr_dir']=$_SESSION['user_dir'];
	$my_dirname=$_SESSION['curr_dir'];
}elseif($_POST['sub']=="upper") {
	if(strpos($my_dirname,$_SESSION['user_dir'])==0){
	if($_SESSION['curr_dir']!=$_SESSION['user_dir']) $_SESSION['curr_dir']=dirname($_SESSION['curr_dir'],1);
	$my_dirname=$_SESSION['curr_dir'];
	}else{
	$_SESSION['curr_dir']=$_SESSION['user_dir'];
	$my_dirname=$_SESSION['curr_dir'];
	}
}
$myDirectory=opendir($my_dirname);
while($entryName=readdir($myDirectory)) { $dirArray[]=$entryName; }
        closedir($myDirectory);
        $indexCount=count($dirArray);
        sort($dirArray);

if(!empty($_POST['myfiles'])&&$_POST['sub']=="delt") {
    foreach($_POST['myfiles'] as $check) {
	//print_r($_POST['myfiles']);
        echo "<li>Deleting $check....</li>";
	delfiles($my_dirname."/".$check);
	if(($key = array_search($check, $dirArray)) !== false) {
    		unset($dirArray[$key]);
		$indexCount--;sort($dirArray);
	}
    }
}elseif($_POST['sub']=="delta" && sizeof($dirArray)){
   foreach($dirArray as $check) {
        if($check{0}!="."){
        echo "<li>Deleting $check....";
        delfiles($my_dirname."/".$check);
	if(($key = array_search($check, $dirArray)) !== false) {
                unset($dirArray[$key]);
                $indexCount--;sort($dirArray);
        }
    }}
}elseif(!empty($_POST['myfiles'])&&$_POST['sub']=="copyt") {
   $dmp="/home/".$current_user->user_login."/dmp";
   if(!file_exists($dmp)){
	echo "Please create $dmp folder with umask 002";
	}
   foreach($_POST['myfiles'] as $check) {
     echo "<li>Copying $check....";
         // copy($my_dirname."/".$check,$dmp."/".$check);
      exec("nohup rsync -a $my_dirname/$check $dmp/$check &");
    }
}elseif(!empty($_POST['myfiles'])&&$_POST['sub']=="copyz") {
   $dmp="/home/".$current_user->user_login."/dmp";
   if(!file_exists($dmp)){
        echo "Please create $dmp folder with umask 0002";
        //echo shell_exec("sudo mkdir ".$dmp); 
        //mkdir($dmp, 0777, true); 
        }
   $fs="";
   $zp=$dmp."/".$current_user->user_login."_".date("YmdHis").".tgz";
   foreach($_POST['myfiles'] as $check) {
     echo "<li>Copying $check....";
     $fs.=$check." ";
    }
   exec("nohup tar -C $my_dirname -czf $zp $fs &");
}
//print_r($_POST['myfiles']);
?>

 <div id="container">
    <h1>Data Directories</h1><hr>
    <!--button onclick="javascript: SetAllCheckBoxes('directory', 'myfiles', 'checked');">Select All</button-->
    <table class="sortable">
      <thead>
        <tr>
          <th>Filename</th>
          <th>Size <small>(bytes)</small></th>
          <th>Type</th>
          <th>Date Modified</th>
        </tr>
      </thead>
      <tbody>
      <?php
        echo "<form action=".$_SERVER['PHP_SELF']."#mcenter name='directory' method='post'>";
        // Loops through the array of files
        for($index=0; $index < $indexCount; $index++) {
          // Allows ./?hidden to show hidden files
          if($_SERVER['QUERY_STRING']=="hidden"){
		$hide="";
          	$ahref="./";
          	$atext="Hide";
	  }else{
		$hide=".";
          	$ahref="./?hidden";
          	$atext="Show";
		}
          if(substr("$dirArray[$index]", 0, 1) != $hide) {

          // Gets File Names
          $name=$dirArray[$index];
          //$namehref="display.php?f=".$dirArray[$index];
          $namehref="download.php?f=".$dirArray[$index];
          $namedel="delete.php?f=".$dirArray[$index];
          // Gets Extensions
          $extn=findexts($dirArray[$index]);
          // Gets file size
          $size=number_format(filesize($my_dirname."/".$dirArray[$index]));
          //Gets Date Modified Data
          $modtime=date("M j Y h:i:s A", filemtime($my_dirname."/".$dirArray[$index]));
          $timekey=date("YmdHis", filemtime($my_dirname."/".$dirArray[$index]));

          // Prettifies File Types, add more to suit your needs.
          switch ($extn){
            case "png": $extn="PNG Image"; break;
            case "jpg": $extn="JPEG Image"; break;
            case "svg": $extn="SVG Image"; break;
            case "gif": $extn="GIF Image"; break;
            case "ico": $extn="Windows Icon"; break;
            case "txt": $extn="Text File"; break;
            case "log": $extn="Log File"; break;
            case "htm": $extn="HTML File"; break;
            case "php": $extn="PHP Script"; break;
            case "js": $extn="Javascript"; break;
            case "css": $extn="Stylesheet"; break;
            case "pdf": $extn="PDF Document"; break;
            case "gz": $extn="GunZIP Archive"; break;
            case "zip": $extn="ZIP Archive"; break;
            case "bak": $extn="Backup File"; break;
            case "": $extn="Folder"; break;
            default: $extn=strtoupper($extn)." File"; break;
          }

          // Separates directories
          if(is_dir($dirArray[$index])) {
            $extn="&lt;Directory&gt;";
            $size="&lt;Directory&gt;";
            $class="dir";
          } else {
            $class="file";
          }

          // Cleans up . and .. directories
          if($name=="."){$name=". (Current Directory)"; $extn="&lt;System Dir&gt;";}
          if($name==".."){$name=".. (Parent Directory)"; $extn="&lt;System Dir&gt;";}

          // Print 'em
          print("
          <tr class='$class'>
            <td><a href='./$namehref'>$name</a></td>
            <td><a href='./$namehref'>$size</a></td>
            <td><a href='./$namehref'>$extn</a></td>
            <td sorttable_customkey='$timekey'><label><a href='./$namehref'>$modtime</a></label> <input class='chk' type='checkbox' name='myfiles[]' value='$name'></td>
          </tr>");
          }
        }

echo "Path: <i>".str_replace($_SESSION['user_dir'],"/mydata_path",$my_dirname)."</i>";
      ?>
 
<div class='subt'><button  name="sub" type="submit" value="delt">Delete</button>
<!--button  name="sub" type="submit" value="delta">Delete All</button-->
<button  name="sub" type="submit" value="refs">Refresh</button>
<button type="button" name="sel"  onclick="$('input:checkbox').attr('checked', false);">Unselect</button>
<button type="button" name="sel"  onclick="$('input:checkbox').attr('checked', true);">Select All</button>
<button  name="sub" type="submit" value="upper">Upper</button>
<?php if( $_SESSION['user_dir']!=$_SESSION['curr_dir']){ ?><button  name="sub" type="submit" value="root">Root</button><?php }?>
<!--button  name="sub" type="submit" value="copyt">Copy2Dmp</button>
<button  name="sub" type="submit" value="copyz">Copy&Tar</button-->
</div>	</form>
      </tbody>
    </table>
</div>
<div class='note'><b>Note:</b><br> 
<?php include './browse_txt.php'; ?>
</div>
<?php include './footer.php';
