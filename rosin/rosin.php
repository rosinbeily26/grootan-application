<?php
$file = $_FILES["file"]["name"];
$table = $_POST['table'];
$pass='';


ini_set('auto_detect_line_endings',TRUE);
$handle = fopen($file,'r');

if ( ($data = fgetcsv($handle) ) === FALSE ) {
    echo "Cannot read from csv $file";die();
}
$fields = array();
$field_count = 0;
for($i=0;$i<count($data); $i++) {
    $f = strtolower(trim($data[$i]));
  
    if ($f) {
    $f = substr(preg_replace ('/[^0-9a-z]/', '_', $f), 0, 20);
        $field_count++;
        $fields[] = $f.' VARCHAR(50)';
    if($data[$i]=='password' || $data[$i]=='Password' ){
      $pass=$data[$i];
        
    }

    }
}
$con=mysqli_connect("localhost","root","","csv");
$sql1 = "CREATE TABLE $table (" . implode(', ', $fields) . ')';
$reg1= mysqli_query($con,$sql1);

$bb="LOAD DATA LOCAL INFILE !".$file."!INTO TABLE ".$table."FIELDS TERMINATED by !,!LINES TERMINATED BY !\n!IGNORE 1 LINES ";
mysqli_query($con,$bb);
if($pass!=''){
$sql2="UPDATE ".$table." SET ".$pass." = AES_ENCRYPT(".$pass.", 'encryption_key');";
$reg2= mysqli_query($con,$sql2);
}
$sql4="SELECT * FROM ".$table;
$result = mysqli_query($con,$sql4);
$rows = mysqli_num_rows($result);



fclose($handle);
ini_set('auto_detect_line_endings',FALSE);
if($reg2){
?>
<body style="background-image: linear-gradient(-90deg, blue, rgb(34, 39, 39));margin-top:20%;color:white">    
   
<h1 style="text-align: center;">Successfully Uploded</h1>
<h1 style="text-align: center;"><?php echo $rows; echo " "; ;?> Number of Record Processed </h1>
<?php
}
else{?>
  <body style="background-image: linear-gradient(-90deg, rgb(87, 123, 15), rgb(98, 39, 39));margin-top:20%;color:white">    
   
  <h1 style="text-align: center;">Somenthing went Worng...........................</h1>  
  <?php
  }
?>