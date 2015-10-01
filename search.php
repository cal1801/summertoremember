<?php
require('connect.php');

@session_start();  //open the session
$CurrentSessionId = session_id();


//DEBUGGING VARIABLES
$ShowCalculations = 0;	//set to "1" to see queries and incoming strings

//clear values from session each time (just to avoid any carry-forward confusion)
$_SESSION['address'] = '';
$_SESSION['Req_BedCount_Hotel'] = '';
$_SESSION['Req_BedCount_GroupLocalBath'] = '';
$_SESSION['Req_BedCount_GroupSepBath'] = '';
$_SESSION['Req_BedCount_Rustic'] = '';
$_SESSION['Req_BedCount_TentRV'] = '';



//capture incoming values
$latlng = trim($_GET['latlng']);								//comes in like this:  (29.7632836, -95.3632715)
$address = mysql_real_escape_string( trim($_GET['address']) );	//make sure this is "clean"
//$Req_BedCount_Hotel = intval(trim($_GET['BedCount_Hotel']));									//make sure this is a number
//$Req_BedCount_GroupLocalBath = intval(trim($_GET['BedCount_GroupLocalBath']));				//make sure this is a number
//$Req_BedCount_GroupSepBath = intval(trim($_GET['BedCount_GroupSepBath']));					//make sure this is a number
//$Req_BedCount_Rustic = intval(trim($_GET['BedCount_Rustic']));								//make sure this is a number
//$Req_BedCount_TentRV = intval(trim($_GET['BedCount_TentRV']));								//make sure this is a number

//save incoming values to session
$_SESSION['address'] = $address;
//$_SESSION['Req_BedCount_Hotel'] = $Req_BedCount_Hotel;
//$_SESSION['Req_BedCount_GroupLocalBath'] = $Req_BedCount_GroupLocalBath;
//$_SESSION['Req_BedCount_GroupSepBath'] = $Req_BedCount_GroupSepBath;
//$_SESSION['Req_BedCount_Rustic'] = $Req_BedCount_Rustic;
//$_SESSION['Req_BedCount_TentRV'] = $Req_BedCount_TentRV;


//get parts from latlng for center of search
$latlng = str_replace('(', '', $latlng);
$latlng = str_replace(')', '', $latlng);
$latlng = str_replace(' ', '', $latlng);
$pieces = explode(',', $latlng);
$center_lat = $pieces[0];
$center_lng = $pieces[1];

//return to search with error
if(trim($address)==''){
	header("Location: index.php?error=address");
	exit();
}
if($latlng==''){
	header("Location: index.php?error=latlng");
	exit();
}



if($ShowCalculations==1){
	echo '<strong>Incoming search criteria:</strong><br />';
	echo "Search Location: $address<br />";
	echo "Latitude: $center_lat<br />";
	echo "Longitude: $center_lng<br />";
	echo "BedCount_Hotel: $Req_BedCount_Hotel<br />";
	echo "BedCount_GroupLocalBath: $Req_BedCount_GroupLocalBath<br />";
	echo "BedCount_GroupSepBath: $Req_BedCount_GroupSepBath<br />";
	echo "BedCount_Rustic: $Req_BedCount_Rustic<br />";
	echo "BedCount_TentRV: $Req_BedCount_TentRV<br />";
	echo '<br />';
}


//set a few default values
$MapResults = array();
$ResultsCount = 0;



if($ShowCalculations==1){ echo '<hr /><br />'; }
if($ShowCalculations==1){ echo 'Perform first-tier search:<br />'; }


// FIRST SEARCH LEVEL = "perfect"
// search for camps meeting or exceeding exact bed counts within 100 miles

//set up search criteria
$radius = 100;
$Criteria = "Distance < '$radius' AND BedCount_Hotel>='$Req_BedCount_Hotel' AND BedCount_GroupLocalBath>='$Req_BedCount_GroupLocalBath' AND BedCount_GroupSepBath>='$Req_BedCount_GroupSepBath' AND BedCount_Rustic>='$Req_BedCount_Rustic' AND BedCount_TentRV>='$Req_BedCount_TentRV' ";

$query = sprintf("SELECT *, ( 3959 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS Distance FROM pccca_sitedirectory HAVING $Criteria ORDER BY Distance LIMIT 0 , 20",
	mysql_real_escape_string($center_lat),
	mysql_real_escape_string($center_lng),
	mysql_real_escape_string($center_lat));
$result = mysql_query($query, $db);
if($ShowCalculations==1){ echo 'Query: '.$query.'<br /><br />'; }
if(!$result)
{
	die("Invalid query: " . mysql_error());
}
while ($row = @mysql_fetch_assoc($result))
{
	extract($row);
	
	$ResultAlphaOrder = substr('ABCDEFGHIJKLMNOPQRSTUVWXYZ', $ResultsCount, 1);  //return the character found at $ResultsCount from the front:  0=A  1=B  2=C  etc
	$ResultsCount++;
	
	$Address = $PhysicalCity.', '.$PhysicalState;
	
	$MapResults["$SiteId"] =  array( SiteName => "$SiteName", Distance => "$Distance", Address => "$Address", lat => "$lat", lng => "$lng", Quality => "perfect", ResultAlphaOrder => "$ResultAlphaOrder" );
	
	if($ShowCalculations==1){ echo 'Site: '.$SiteName.' '; }
	if($ShowCalculations==1){ echo '('.round($Distance).' miles)<br />'; }
}



if($ShowCalculations==1){ echo '<hr /><br />'; }
if($ShowCalculations==1){ echo 'If fewer than 5 results, perform second-tier search:<br />'; }


// SECOND SEARCH LEVEL = "good"
// search for camps within 10 beds in requested categories within 200 miles
if($ResultsCount < 5)
{
	//set up search criteria
	$radius = 200;
	$LowBedCount_Hotel = ($Req_BedCount_Hotel-10 > 0) ? ($Req_BedCount_Hotel-10) : "0";									//ternary assignment:    $variable = condition ? if true : if false
	$LowBedCount_GroupLocalBath = ($Req_BedCount_GroupLocalBath-10 > 0) ? ($Req_BedCount_GroupLocalBath-10) : "0";		//if ($bedcount-10) is less than zero, set to zero...otherwise set to result
	$LowBedCount_GroupSepBath = ($Req_BedCount_GroupSepBath-10 > 0) ? ($Req_BedCount_GroupSepBath-10) : "0";
	$LowBedCount_Rustic = ($Req_BedCount_Rustic-10 > 0) ? ($Req_BedCount_Rustic-10) : "0";
	$LowBedCount_TentRV = ($Req_BedCount_TentRV-10 > 0) ? ($Req_BedCount_TentRV-10) : "0";
	
	$Criteria = "Distance < '$radius' AND BedCount_Hotel>='$LowBedCount_Hotel' AND BedCount_GroupLocalBath>='$LowBedCount_GroupLocalBath' AND BedCount_GroupSepBath>='$LowBedCount_GroupSepBath' AND BedCount_Rustic>='$LowBedCount_Rustic' AND BedCount_TentRV>='$LowBedCount_TentRV' ";
	
	$query = sprintf("SELECT *, ( 3959 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS Distance FROM pccca_sitedirectory HAVING $Criteria ORDER BY Distance LIMIT 0 , 20",
		mysql_real_escape_string($center_lat),
		mysql_real_escape_string($center_lng),
		mysql_real_escape_string($center_lat));
	$result = mysql_query($query, $db);
	if($ShowCalculations==1){ echo 'Query: '.$query.'<br /><br />'; }
	if(!$result)
	{
		die("Invalid query: " . mysql_error());
	}
	while ($row = @mysql_fetch_assoc($result))
	{
		extract($row);
		
		//if( in_array($id,$MapResults) )
		if( array_key_exists("$SiteId", $MapResults) )
		{ 
			if($ShowCalculations==1){ echo "<strong>$SiteName is already in array!</strong><br />"; }
		}
		else
		{
			$ResultAlphaOrder = substr('ABCDEFGHIJKLMNOPQRSTUVWXYZ', $ResultsCount, 1);  //return the character found at $ResultsCount from the front:  0=A  1=B  2=C  etc
			$ResultsCount++;
			
			$Address = $PhysicalCity.', '.$PhysicalState;
			
			//add the new record
			$MapResults["$SiteId"] =  array( SiteName => "$SiteName", Distance => "$Distance", Address => "$Address", lat => "$lat", lng => "$lng", Quality => "good", ResultAlphaOrder => "$ResultAlphaOrder" );
			
			if($ShowCalculations==1){ echo 'Site: '.$SiteName.' '; }
			if($ShowCalculations==1){ echo '('.round($Distance).' miles)<br />'; }
		}
	}
}
//END SECOND-TIER SEARCH




if($ShowCalculations==1){ echo '<hr /><br />'; }
if($ShowCalculations==1){ echo 'If fewer than 5 results, perform third-tier search:<br />'; }


// THIRD SEARCH LEVEL - "fair"
// search for camps with enough beds of similar style within 300 miles
//if($ResultsCount < 6)
//{
	//set up search criteria
	//$radius = 300; //original radius
	$radius = 1000;
	$RealRoomRealBath = $Req_BedCount_Hotel + $Req_BedCount_GroupLocalBath;
	$RemoteOrNoBath = $Req_BedCount_GroupSepBath + $Req_BedCount_Rustic + $Req_BedCount_TentRV;

	if($ShowCalculations==1){
		echo '$Req_BedCount_Hotel:'.$Req_BedCount_Hotel.'<br />';
		echo '$Req_BedCount_GroupLocalBath:'.$Req_BedCount_GroupLocalBath.'<br />';
		echo '$RealRoomRealBath:'.$RealRoomRealBath.'<br /><br />';
		echo '$Req_BedCount_GroupSepBath:'.$Req_BedCount_GroupSepBath.'<br />';
		echo '$Req_BedCount_Rustic:'.$Req_BedCount_Rustic.'<br />';
		echo '$Req_BedCount_TentRV:'.$Req_BedCount_TentRV.'<br />';
		echo '$RemoteOrNoBath:'.$RemoteOrNoBath.'<br />';
	}

	$Criteria = "Distance < '$radius' AND ( BedCount_Hotel + BedCount_GroupLocalBath >= '$RealRoomRealBath' ) AND ( BedCount_GroupSepBath + BedCount_Rustic + BedCount_TentRV >= '$RemoteOrNoBath' ) ";
	
	$query = sprintf("SELECT *, ( 3959 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS Distance FROM pccca_sitedirectory HAVING $Criteria ORDER BY Distance LIMIT 0 , 17",
		mysql_real_escape_string($center_lat),
		mysql_real_escape_string($center_lng),
		mysql_real_escape_string($center_lat));
	$result = mysql_query($query, $db);
	if($ShowCalculations==1){ echo 'Query: '.$query.'<br /><br />'; }
	if(!$result)
	{
		die("Invalid query: " . mysql_error());
	}
	while ($row = @mysql_fetch_assoc($result))
	{
		extract($row);
		
		//if( in_array($id,$MapResults) )
		if( array_key_exists("$SiteId", $MapResults) )
		{ 
			if($ShowCalculations==1){ echo "<strong>$SiteName is already in array!</strong><br />"; }
		}
		else
		{
			$ResultAlphaOrder = substr('ABCDEFGHIJKLMNOPQRSTUVWXYZ', $ResultsCount, 1);  //return the character found at $ResultsCount from the front:  0=A  1=B  2=C  etc
			$ResultsCount++;
			
			$Address = $PhysicalCity.', '.$PhysicalState;
			
			//add the new record
			$MapResults["$SiteId"] =  array( SiteName => "$SiteName", Distance => "$Distance", Address => "$Address", lat => "$lat", lng => "$lng", Quality => "fair", ResultAlphaOrder => "$ResultAlphaOrder" );
			
			if($ShowCalculations==1){ echo 'Site: '.$SiteName.' '; }
			if($ShowCalculations==1){ echo '('.round($Distance).' miles)<br />'; }
		}
	}
//}
//END THIRD-TIER SEARCH

if($ShowCalculations==1){ echo '<hr /><br />'; }
if($ShowCalculations==1){ var_dump($MapResults); }

if($ShowCalculations==1){ echo '<hr /><br />'; }
if($ShowCalculations==1){ echo 'Deleting old search (if any)<br />'; }

//we need to clear the database of any previous results
$DeleteSql = "DELETE FROM bookaretreat_map_resultset WHERE SessionId='$CurrentSessionId' ";
mysql_query($DeleteSql, $db);


if($ShowCalculations==1){ echo '<hr /><br />'; }
if($ShowCalculations==1){ echo 'Saving current search values<br />'; }

// now we insert the results into a database so that each time the user hits the back button to the search results, we do not have to re-calculate
$count = 0;	//default...variable is used to "letter" each result
foreach ($MapResults as $SiteId => $SiteDataArray) {
	$SiteName = mysql_escape_string($SiteDataArray['SiteName']);
	$Address = mysql_escape_string($SiteDataArray['Address']);
	$lat = $SiteDataArray['lat'];
	$lng = $SiteDataArray['lng'];
	$Distance = $SiteDataArray['Distance'];
	$Quality = $SiteDataArray['Quality'];
	$CurrentLetterLabel = $SiteDataArray['ResultAlphaOrder'];

	$sql = "INSERT INTO bookaretreat_map_resultset ( SessionId, SiteId, SiteName, Address, lat, lng, Distance, Label, MatchQuality, Timestamp ) 
		VALUES ( '$CurrentSessionId', '$SiteId', '$SiteName', '$Address', '$lat', '$lng', '$Distance', '$CurrentLetterLabel', '$Quality', now() ); ";
	$result = mysql_query($sql, $db);
	if(!$result)
	{
		die("Invalid query: " . mysql_error());
	}
}


if($ShowCalculations==1){ echo '<hr /><br />'; }
if($ShowCalculations==1){ echo 'DONE!<br />'; }

if($ShowCalculations==1){ echo '<hr /><br />'; }


if($ShowCalculations==1)
{
	echo '<a href="results.php">View Results</a>';
}
else
{
	/*header("Location: http://www.calfindeiss.com/PCCCA/bestsummeryet/results.php");*/
	header("Location: http://localhost:8888/bestsummeryet/results.php");
	exit();
}


?>