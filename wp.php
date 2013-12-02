#!/usr/bin/php
<?php
include("includes/db.config.php"); 

//getname() will print out names and workplaces if available


function getname($fr,$firstname, $midinitial, $laststname, $workplace ) {
  if (!is_null($firstname) && !is_null($laststname)) 
   {
  $text = "Author-Name:"; 
  if (!is_null($firstname)) 
     $text = $text." ".$firstname;
      
  if (!is_null($midinitial)) 
     $text = $text." ".$midinitial; 
	 
  if (!is_null($laststname)) 
     $text = $text." ".$laststname; 
   
  fwrite($fr, "$text\n"); 

  if (!is_null($firstname)) 
     {
     $first = "Author-X-Name-First: ". $firstname;
    
  fwrite($fr, "$first\n"); 
     }
  if (!is_null($laststname)) 
	{
     $last = "Author-X-Name-Last: ". $laststname; 

   fwrite($fr, "$last\n"); 
     }

 if (!is_null($workplace)) 
   {
     $work = "Author-Workplace-Name: ". $workplace; 

   fwrite($fr, "$work\n"); 
    }
  }
 
  
}

$series=array( "IRS"=>"indrel", "CEPS"=>"cepsud", "WWSEcon"=>"wwseco", "RPDS"=>"rpdevs");
$series["CACPS"] ="cpanda";
$series["CMD"] = "cmgdev";
$series["CRCW"] = "crcwel";
$series["CHWB"] = "cheawb";
$series["Econometrics"] ="metric";
$series["ERS"] ="edures";
$series["OPR"] ="opopre"; 

$text = "Template-Type: ReDIF-Paper 1.0\n";

while ($element=each($series))
{
 $sectioniddb= trim($element["key"]);
 $shandle= $element["value"];

  $sql = "SELECT *  FROM workingpaper"; 
  $sql = $sql." where SectionURLID = '".$sectioniddb."';";

  
  //print($sql);

 	
  $resultSet = mysql_query($sql);
  $count = mysql_num_rows($resultSet);
  
  if ($count == 0)
  {       
	print ("Warning: There is no records for series: ".$sectioniddb.". Template was complete.\n"); 
  
	exit();
  }
  else
  {
   $filename = "RePEc/pri/".$shandle."/".$shandle.".rdf";
  if (!file_exists($filename)) touch($filename);
  shell_exec("chmod 777 $filename");

  $fr = fopen($filename, 'w');
   if(!$fr) {
      echo "Error! Couldn't open the file.";
      exit;
   } 
  

    while($row = mysql_fetch_row($resultSet))
    {
	if (fwrite($fr, $text) === FALSE) {
       echo "Cannot write to file ($filename)";
       exit;
      }
   
      getname($fr, $row[2], $row[3], $row[4], $row[5] );
      getname($fr,$row[6], $row[7], $row[8], $row[9] );
 	  getname($fr,$row[10], $row[11], $row[12], $row[13] );
 	  getname($fr,$row[14], $row[15], $row[16], $row[17] );
 	  getname($fr,$row[18], $row[19], $row[20], $row[21] );
    
      if (!is_null($row[1])) 
	 {
     $title = "Title: ". $row[1];
     fwrite($fr, "$title\n"); 
      }
   else
      print ("Error: title for ".$row[25]." is empty"); 
   
   if (!is_null($row[22])) 
     {
     $Abstract = "Abstract: ". $row[22];
     fwrite($fr, "$Abstract\n");        
     }
	 
    if (!is_null($row[23])) 
     {
     $Creation = "Creation-Date: ".$row[23];
     fwrite($fr, "$Creation\n");        
     }


    if (!is_null($row[24]) && !is_null($row[25])) 
     { 
 	 $sid=$row[24]; 
	 $papernum = $row[25]; 
         $pos = strpos( $papernum, "http"); 
	 if ( $pos === false )
	 {
      switch ($sid) {
		case "IRS":
   			$url="http://arks.princeton.edu/ark:/88435/".trim($row[25]);
			//$shandle="indrel";
  		 	break;
		case "CEPS":
			$url="http://www.princeton.edu/ceps/workingpapers/".trim($row[25]);
			//$shandle="cepsud";
   			break;
	    case "WWSEcon":
			$url="http://www.princeton.edu/wwseconpapers/papers/".trim($row[25]);
			//$shandle="wwseco";
   			break;
		case "RPDS":
   			$url="http://www.princeton.edu/rpds/papers/".trim($row[25]);
			//$shandle="rpdevs";
  		 	break;
		case "CACPS":
			$url="http://www.princeton.edu/~artspol/".trim($row[25]);
			//$shandle="cpanda";
   			break;
	    case "CMD":
			$url="http://www.princeton.edu/cmd/working-papers/papers/".trim($row[25]);
			//$shandle="cmgdev";
   			break;
		case "CRCW":
   			$url="http://crcw.princeton.edu/workingpapers/".trim($row[25]);
			//$shandle="crcwel";
  		 	break;
		case "CHWB":
            $url="http://wws-roxen.princeton.edu/chwpapers/papers/".trim($row[25]);
			//$shandle="cheawb";
   			break;
	    case "Econometrics":
			$url="http://www.princeton.edu/~erp/ERParchives/archivepdfs/".trim($row[25]);
			//$shandle="metric";
   			break;
		case "ERS":
   			$url="http://www.ers.princeton.edu/workingpapers/".trim($row[25]);
			//$shandle="edures";
  		 	break;
		case "ET":
			$url="http://www.princeton.edu/econtheorycenter/wps/";
   			break;
	    case "OPR":
			$url="http://opr.princeton.edu/papers/".trim($row[25]);
			//$shandle="opopre";
   			break;
		case "RPPE":
			$url="http://www.princeton.edu/econtheorycenter/wps/";
   			break;
		default:
   			echo "invalid Section ID for ".$row[25];
           
        }
      }
      else
      {
         $url= trim($row[25]);
         }

      $url="File-URL: ".$url;

     fwrite($fr, "$url\n");   
    
     }
	 else
      print ("Error: SectionID for ".$row[25]." is empty OR Papernumber is empty. "); 

  


    if (!is_null($row[26])) 
     {
     $version = "File-Function: ". $row[26];
     fwrite($fr, "$version\n");        
     }

     
     $paper = "Number: ". $row[0];
     fwrite($fr, "$paper\n");        
     

	if (!is_null($row[27])) 
     {
     $jel = "Classification-JEL: ". $row[27];
     fwrite($fr, "$jel\n");        
     }

	if (!is_null($row[28])) 
     {
     $key = "Keywords: ". $row[28];
     fwrite($fr, "$key\n");        
     }

   if ( !is_null($shandle)) 
    {
     $phandle=trim($row[0]);

   /*  if (strpos($phandle, ".") > 0) 
     { 
     $paperhandle = substr($phandle, 0, strpos($phandle, "."));  
     }
     else
     {
      $paperhandle = $phandle;  
	  echo "Warning: Paper Number maybe misses a period!";
     }
      */
     $hand="Handle: RePEc:pri:".$shandle.":".$phandle; 
     fwrite($fr, "$hand\n");  
     }
    else
    {
    print ("Error: section handle  is invalid. Abort."); 
    exit; 
    } 
   
	fwrite($fr, "\n"); 
     }
   }
   



//Close the file
   $closereturn=fclose($fr);
   if(!$closereturn) {
      echo "Error! Couldn't close the file.";
   } 

}
mysql_close($db);
?>



