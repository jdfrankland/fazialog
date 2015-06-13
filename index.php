<?php
include("../pChart/class/pDraw.class.php");  
include("../pChart/class/pImage.class.php");  
include("../pChart/class/pData.class.php");
include("../pChart/class/pScatter.class.php");

function addParameterOption($param,$selected,$preselect)
{
   print "<option value='$param'";
   if($preselect && ($selected==$param)) print "selected='selected'";
   print ">$param</option>\n";
}

function addParameterOptions($parlist,$selected,$preselect)
{
   foreach($parlist as $param){
      addParameterOption($param,$selected,$preselect);
   }
}

function buildDetectorSelectionForm($detector)
{
   if($detector=="SI1"){
      $params=array("Iread","VreadX",
         "ENER_RISE_REG_QH1","ENER_PLAT_REG_QH1","PEAKING_REG_QH1",
         "WAVER_DEPTH_QH1","WAVER_PRETRG_QH1","TRIG_RISE_REG_QH1","TRIG_PLAT_REG_QH1",
         "LEVEL_L_FAST_QH1","LEVEL_H_FAST_QH1","LEVEL_SLOW_QH1","WAVER_DEPTH_QL1",
         "WAVER_PRETRG_QL1","WAVER_DEPTH_I1","WAVER_PRETRG_I1"
         );
   }
   else if($detector=="SI2"){
      $params=array("Iread","VreadX",
         "ENER_RISE_REG_Q2","ENER_PLAT_REG_Q2","PEAKING_REG_Q2",
         "WAVER_DEPTH_Q2","WAVER_PRETRG_Q2","TRIG_RISE_REG_Q2","TRIG_PLAT_REG_Q2",
         "LEVEL_L_FAST_Q2","LEVEL_H_FAST_Q2","LEVEL_SLOW_Q2"
         );
   }
   else {
      $params=array(
         "ENER_RISE_REG_Q3","ENER_PLAT_REG_Q3","PEAKING_REG_Q3",
         "WAVER_DEPTH_Q3","WAVER_PRETRG_Q3","TRIG_RISE_REG_Q3","TRIG_PLAT_REG_Q3",
         "LEVEL_L_FAST_Q3","LEVEL_H_FAST_Q3","LEVEL_SLOW_Q3",
         "ENER_RISE_REG_Q3Fast","ENER_PLAT_REG_Q3Fast","PEAKING_REG_Q3Fast",
         );
   }
   $blockSel = (isset($_GET['block']) ? $_GET['block'] : "-");
   $detSel = (isset($_GET['detector']) ? $_GET['detector'] : "-");
   $parSel = (isset($_GET['parameter']) ? $_GET['parameter'] : "-");
   
   print "<form action='index.php' method='GET'>\n";
   print "$detector BLOCK: <select name='block'>\n";
   $blocks = array(0,1,2,3);
   addParameterOptions($blocks,$blockSel,$detector==$detSel);
   print "</select>\n";
   print "  <select name='parameter'>\n";
   addParameterOptions($params,$parSel,$detector==$detSel);
   print "</select>\n";
   print "<input type='hidden' name='detector' value='$detector' />\n";
   print "<input type='submit' value='Display'>\n";
   print "</form><br>\n";
}

function openDatabase($dbpath)
{
   // open the database with given path
   // return the database object
   
   $db = new PDO("sqlite:$dbpath");
   return $db;
}

function getDetectorValues($db, $parameter, $block, $quartet, $telescope, $detector)
{
   // return sqlite $db result for $parameter in SCdetectors
   
   $result = $db->query("SELECT time, value FROM SCdetectors WHERE parameter = '$parameter'
                   AND block = '$block' AND telescope = '$telescope' AND quartet = '$quartet' AND detector = '$detector'");
   return $result;
}

function getElectronicsValues($db, $parameter, $block, $card, $module)
{
   // return sqlite $db result for $parameter in SCelectronics
   
   $result = $db->query("SELECT time, value FROM SCelectronics WHERE parameter = '$parameter'
                   AND block = '$block' AND card = '$card' AND module = '$module'");
   return $result;
}

function drawPicture($myData, $width, $par, $title, $file)
{
   $w=$width; $h=$w*3/4; 
   $tb=50*$w/1200;
   $wp=0.85; $hp=0.8;
   $offw=(1.05-$wp)*$w/2;
   $offh=(1-$hp)*($h-$tb)/2+$tb;
   $myPicture = new pImage($w, $h,$myData); // width, height, dataset  
   
   /* Turn off Antialiasing */
   $myPicture->Antialias = TRUE; 
   
   /* Draw the background */
   $Settings = array("R"=>240, "G"=>240, "B"=>240);
//   $Settings = array("R"=>230, "G"=>240, "B"=>240, "Dash"=>1, "DashR"=>190, "DashG"=>203, "DashB"=>107);
   $myPicture->drawFilledRectangle(0,0,$w,$h,$Settings); 
   
   /* Overlay with a gradient */
   //$Settings = array("StartR"=>219, "StartG"=>100, "StartB"=>139, "EndR"=>1, "EndG"=>1, "EndB"=>68, "Alpha"=>50);
   //$myPicture->drawGradientArea(0,0,$w,$h,DIRECTION_VERTICAL,$Settings);
   $myPicture->drawGradientArea(0,0,$w,$tb,DIRECTION_VERTICAL,array("StartR"=>0,"StartG"=>0,"StartB"=>0,"EndR"=>50,"EndG"=>50,"EndB"=>50,"Alpha"=>80));     
   
   /* Add a border to the picture */
   $myPicture->drawRectangle(0,0,$w-1,$h-1,array("R"=>0,"G"=>0,"B"=>0));
 
   /* Write the chart title */ 
   $myPicture->setFontProperties(array("FontName"=>"../pChart/fonts/calibri.ttf","FontSize"=>12*$w/800,"R"=>255,"G"=>255,"B"=>255));
   $myPicture->drawText(10,$tb*0.8,$title,array("FontSize"=>20*$w/800,"Align"=>TEXT_ALIGN_BOTTOMLEFT));
   
   /* Set the default font */
   $myPicture->setFontProperties(array("FontName"=>"../pChart/fonts/calibri.ttf","FontSize"=>16*$w/800,"R"=>0,"G"=>0,"B"=>0));
    
    /* Define the chart area */ 
   $myPicture->setGraphArea($offw,$offh,$w-$offw,$h-$offh-$tb); // (x,y) top left, (x,y) bottom right 
      
   /* Turn on Antialiasing */
   $myPicture->Antialias = TRUE; 
   
   /* Enable shadow computing */
   $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
 
   /* Draw the scatter plot */
   $parDataScatter = new pScatter($myPicture,$myData); 
   $parDataScatter->drawScatterScale();/*array("LabelingMethod"=>LABELING_DIFFERENT, 
      "Mode"=>SCALE_MODE_MANUAL, 
      "ManualScale"=>array(0=>array("Min"=>$myData->getMin("Date"),
                                    "Max"=>$myData->getMax("Date")),
                           1=>array("Min"=>$myData->getMin($par),
                                    "Max"=>$myData->getMax($par)))));*/
   $parDataScatter->drawScatterLineChart();
   $parDataScatter->drawScatterPlotChart();

    /* Write the chart legend */
   $myPicture->drawLegend($w/3,$tb/2,
         array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL,"FontR"=>255,"FontG"=>255,"FontB"=>255));
   
   $myPicture->render("plots/$file");
}

function drawScatter($width, $par, $times, $values, $title, $file)
{
   // draw the 'time' and 'value' arrays as a scatter plot
   $myData = new pData();  
   /* Add data in your dataset */  
   $myData->addPoints($values,$par);
   $myData->addPoints($times,"Date");
   
   $myData->setAxisName(0,"Date");
   $myData->setAxisXY(0,AXIS_X);
   $myData->setAxisPosition(0,AXIS_POSITION_BOTTOM);
   $myData->setSerieOnAxis("Date",0);
   $myData->setAxisDisplay(0,AXIS_FORMAT_DATE,"j/n:G\h");

   $myData->setAxisName(1,$par);
   $myData->setAxisXY(1,AXIS_Y);
   $myData->setAxisPosition(1,AXIS_POSITION_LEFT);    
   $myData->setSerieOnAxis($par,1);
 
   $myData->setScatterSerie("Date",$par,0);
   $myData->setScatterSerieColor(0,array("R"=>0,"G"=>0,"B"=>150));
 
   drawPicture($myData, $width, $par, $title, $file);
}

function drawScatters($width, $parArray, $times, $valuesArray, $title, $file)
{
   // draw the 'time' and 'value' arrays as a scatter plot
   $myData = new pData();  
   $myData->addPoints($times,"Date");
   
   $myData->setAxisName(0,"Date");
   $myData->setAxisXY(0,AXIS_X);
   $myData->setAxisPosition(0,AXIS_POSITION_BOTTOM);
  $myData->setSerieOnAxis("Date",0);
   $myData->setAxisDisplay(0,AXIS_FORMAT_DATE,"j/n:G\h");

   $i=0;
   foreach($parArray as $par){
      $values=$valuesArray[$i];
      $myData->addPoints($values,$par);
      $myData->setScatterSerie("Date",$par,$i);
      $myData->setSerieOnAxis($par,1); 
      $i++;
   }
   $myData->setAxisName(1,$parArray[0]);
   $myData->setAxisXY(1,AXIS_Y);
   $myData->setAxisPosition(1,AXIS_POSITION_LEFT);    
   drawPicture($myData, $width, $parArray[0], $title, $file);
}

function drawDetPar($db,$width, $par,$blk,$qrt,$tel,$det)
{
   $result = getDetectorValues($db, $par, $blk, $qrt, $tel, $det);
   $i=0;
      foreach($result as $row)
      {
         $times[$i]=strtotime($row['time']);
         $values[$i]=$row['value'];
         $i+=1;
      }
      if(!$i){
         $times[0]=time();
         $values[0]=0;
         }
      $title="$par B00$blk-Q$qrt-T$tel-$det";
      $file="$par$blk$qrt$tel$det.png";
         drawScatter($width, $par, $times, $values, $title, $file);
         print "<img src=\"plots/$file\">\n";
}


function drawElecPar($db,$width, $par,$blk,$card,$mod)
{
   $result = getElectronicsValues($db, $par, $blk, $card,$mod);
   $i=0;
      foreach($result as $row)
      {
         $times[$i]=strtotime($row['time']);
         $values[$i]=$row['value'];
         $i+=1;
      } 
      if(!$i){
         $times[0]=time();
         $values[0]=0;
         }
      $title="$par B00$blk-$card-$mod";
      $file="$par$blk$card$mod.png";
         drawScatter($width, $par, $times, $values, $title, $file);
         print "<img src=\"plots/$file\">\n";
}

function drawDetParBlock($db, $par, $blk, $det)
{
   $w=400;
   print "<h1>$par values for Block$blk $det</h1>\n";
   drawDetPar($db,$w,$par,$blk,1,1,$det);
   drawDetPar($db,$w,$par,$blk,1,2,$det);
   drawDetPar($db,$w,$par,$blk,2,1,$det);
   drawDetPar($db,$w,$par,$blk,2,2,$det);
   print("<br>\n");
   drawDetPar($db,$w,$par,$blk,1,4,$det);
   drawDetPar($db,$w,$par,$blk,1,3,$det);
   drawDetPar($db,$w,$par,$blk,2,4,$det);
   drawDetPar($db,$w,$par,$blk,2,3,$det);
   print("<br>\n");
   drawDetPar($db,$w,$par,$blk,4,1,$det);
   drawDetPar($db,$w,$par,$blk,4,2,$det);
   drawDetPar($db,$w,$par,$blk,3,1,$det);
   drawDetPar($db,$w,$par,$blk,3,2,$det);
   print("<br>\n");
   drawDetPar($db,$w,$par,$blk,4,4,$det);
   drawDetPar($db,$w,$par,$blk,4,3,$det);
   drawDetPar($db,$w,$par,$blk,3,4,$det);
   drawDetPar($db,$w,$par,$blk,3,3,$det);
}

function drawBlockTemperatures($db,$blk)
{
   $feelist=array("FE0","FE1","FE2","FE3","FE4","FE5","FE6","FE7");
   foreach($feelist as $fee){
      $result = getElectronicsValues($db, "T1", $blk, $fee,"FETemp");
      $i=0;
      foreach($result as $row)
      {
         $times[$i]=strtotime($row['time']);
         $values[$i]=$row['value'];
         $i+=1;
      } 
      if(!$i){
         $times[0]=time();
         $values[0]=0;
      }
      $parArray[]=$fee;
      $valArray[]=$values;
   }
   drawScatters(900, $parArray, $times, $valArray, "BLOCK0 FETemp", "temperatures.png");
   print "<img src=\"plots/temperatures.png\">\n";
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="fazialog_viewer.css">
</head>
<body>
<div class="main">
<form action='index.php' method='GET'>
BLOCK: <select name='block'>
<option value='0'>0</option>
<option value='1'>1</option>
<option value='2'>2</option>
<option value='3'>3</option>
</select>
<input type='hidden' name='temperature' value='yes' />
<input type='submit' value='Temperatures'>
</form><br>
<?php
buildDetectorSelectionForm("SI1");
buildDetectorSelectionForm("SI2");
buildDetectorSelectionForm("CSI");
if(isset($_GET['parameter'])&&isset($_GET['block'])&&isset($_GET['detector'])){
   $db = openDatabase("fazia.db");
   drawDetParBlock($db, $_GET['parameter'], $_GET['block'], $_GET['detector']);
   $db = null;
}
else if(isset($_GET['parameter'])&&isset($_GET['block'])&&isset($_GET['card'])&&isset($_GET['module'])){
   $db = openDatabase("fazia.db");
   drawElecPar($db,800,$_GET['parameter'],$_GET['block'],$_GET['card'],$_GET['module']);
   $db = null;
}
else if(isset($_GET['block'])&&isset($_GET['temperature'])){
   $db = openDatabase("fazia.db");
   drawBlockTemperatures($db,$_GET['block']);
   $db = null;
}
?>
</div>
</body>
</html> 
