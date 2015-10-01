<?php  
require('connect.php');

@session_start();  //open the session
$CurrentSessionId = session_id();


// Start XML file, create parent node
$dom = new DOMDocument("1.0");
$node = $dom->createElement("markers");
$parnode = $dom->appendChild($node);

// retrieve cached results
$query = "SELECT * FROM bookaretreat_map_resultset WHERE SessionId='$CurrentSessionId' ORDER BY Distance LIMIT 0 , 20; ";
$result = mysql_query($query);

if (!$result) {
  die("Invalid query: " . mysql_error());
}

header("Content-type: text/xml");

// Iterate through the rows, adding XML nodes for each
while ($row = @mysql_fetch_assoc($result)){
  $node = $dom->createElement("marker");
  $newnode = $parnode->appendChild($node);
  $newnode->setAttribute("SiteId", $row['SiteId']);
  $newnode->setAttribute("name", $row['SiteName']);
  $newnode->setAttribute("address", $row['Address']);
  $newnode->setAttribute("lat", $row['lat']);
  $newnode->setAttribute("lng", $row['lng']);
  $newnode->setAttribute("distance", round($row['Distance']));
  $newnode->setAttribute("letter", $row['Label']);
}

echo $dom->saveXML();
?>