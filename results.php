<?php  
require('connect.php');
@session_start();  //open the session
$CurrentSessionId = session_id();
?>

<!DOCTYPE html>
<html class="no-js" lang="en">
  <head>
    <title>BookARetreat.com - Rent a Retreat Location or Conference Center</title>
    <meta name="keywords" content="retreat center, conference center, business meeting, event location, conference, meeting, retreat, nature, camp">
    <meta name="description" content="Find a retreat location for your next event for free.  We offer a list of conference rental and retreat rental locations at camps and conference centers throughout North America.">
    <meta http-equiv="Content-Language" content="EN">
    <meta name="revisit-after" content="5 days">
    <meta name="copyright" content="All portions of the design of this website are held under international copyright.">
    <meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
    <meta http-equiv="imagetoolbar" content="no">
    <meta name="Expires" content="-1">
    <link rel="stylesheet" href="css/foundation.css" />
    <link href="css/style.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="css/simple-ripple-effect.css" />
    <!--[if lte IE 6]>
    <link rel="stylesheet" type="text/css" media="screen" 
    href="css/ie.css" />   
    <![endif]-->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB0cx8KIfpJTjosKOwuozA9DNVHmxg_wzk&signed_in=true"></script>
    <script type="text/javascript">
      //<![CDATA[
      var map;
      function load() {
        map = new google.maps.Map(document.getElementById('map'), {
          center: new google.maps.LatLng(42, -95),
          zoom: 3,
          mapTypeId: 'roadmap'
        });

        var infoWindow = new google.maps.InfoWindow;

        downloadUrl("generate_map_xml.php", function(data) {
          var xml = data.responseXML;
          var markers = xml.documentElement.getElementsByTagName('marker');

          var bounds = new google.maps.LatLngBounds();
          for (var i = 0; i < markers.length; i++) {
            var name = markers[i].getAttribute('name');
            var address = markers[i].getAttribute('address');
            var distance = parseFloat(markers[i].getAttribute('distance'));
            var point = new google.maps.LatLng(parseFloat(markers[i].getAttribute('lat')),
                                    parseFloat(markers[i].getAttribute('lng')));
            var urlname = name.replace(/ /g, "_");
            var link = 'http://www.bookaretreat.com/' + markers[i].getAttribute('SiteId') + '/' + urlname;
                                    
            //I added a sequential UPPER CASE letter to each marker for use in mapping point lettering
            var letter = markers[i].getAttribute('letter');

            var marker = new google.maps.Marker({
              map: map,
              position: point,
              icon: "http://maps.google.com/mapfiles/marker" + letter + ".png",
            });
            bindInfoWindow(marker, map, infoWindow, link);
            bounds.extend(point);
          }
          map.fitBounds(bounds)
        });
      }

      function bindInfoWindow(marker, map, infoWindow, html) {
        google.maps.event.addListener(marker, 'click', function() {
          infoWindow.setContent(html);
          infoWindow.open(map, marker);
        });
      }

      function downloadUrl(url, callback) {
        var request = window.ActiveXObject ?
            new ActiveXObject('Microsoft.XMLHTTP') :
            new XMLHttpRequest;

        request.onreadystatechange = function() {
          if (request.readyState == 4) {
            request.onreadystatechange = doNothing;
            callback(request, request.status);
          }
        };

        request.open('GET', url, true);
        request.send(null);
      }

      function doNothing() {}
      //]]>
    </script>

  </head>
  <body onload="load()">
    <div class="row full-width">
      <div class="large-12 columns top-bar">
        <div class="row">
          <div class="large-4 small-5 columns">
            <img src="img/logo.png" />
          </div>
          <div class="large-8 small-7 columns menu">
            <a href="index.php"><h3>&#8610; Back to Search</h3></a>
          </div>
        </div>
      </div>
    </div><!--end top-bar-->

        <?php
          //set some default variables
          $TopResultsOutput = '';
          $AdditionalResultsOutput = '';
          
          // retrieve cached results for top quality search results
          $query = "SELECT * FROM bookaretreat_map_resultset WHERE SessionId='$CurrentSessionId' AND (MatchQuality='perfect' OR MatchQuality='good') ORDER BY Label ASC LIMIT 0 , 20; ";
          $result = mysql_query($query);
          if (!$result) {
          die("Invalid query: " . mysql_error());
          }
          while ($row = @mysql_fetch_assoc($result)){
            extract($row);
            
            //$urlname = urlencode( str_replace( ' ', '_', $SiteName ) );
            $StrippedSiteName = preg_replace("/[^a-zA-Z0-9\s]/", '', $SiteName);
            $urlname = urlencode( strtolower(str_replace( ' ', '-', $StrippedSiteName ) ) );
            $urlname = str_replace( '--', '-', $urlname );

            $roomquery = "SELECT ImageOnePath FROM pccca_sitedirectory WHERE SiteId='$SiteId' LIMIT 1; ";
            $roomresult = mysql_query($roomquery, $db);
            if (!$roomresult) {
            die("Invalid query: " . mysql_error());
            }
            $roomrow = @mysql_fetch_assoc($roomresult);
            if($roomrow) {
              extract($roomrow);
            }
            
            if($ImageOnePath!=''){ $SiteImage='http://www.pccca.net/'.$ImageOnePath; }else{ $SiteImage=''; }
        
            $TopResultsOutput .= '<div class="medium-6 small-12 columns" style="margin-bottom: 30px;">';
            $TopResultsOutput .= '<div class="card-container">';
            $TopResultsOutput .= '<img src="'.$SiteImage.'" />';
            $TopResultsOutput .= '<h4 class="name">'.$SiteName.'</h4>';
            $TopResultsOutput .= '<p class="city">'.$Address.'</p>';
            $TopResultsOutput .= '<a href="view_details.php?siteid='.$SiteId.'" class="details results ripple-effect" data-ripple-color="#b44904" data-ripple-limit=".card-container">Find Out More</a>';

            $TopResultsOutput .= '</div>';
            $TopResultsOutput .= '</div><!-- ending of result -->';
          }
          
          $extra = mysql_num_rows($result) % 2;
          switch ($extra) {
            case 1:
              $TopResultsOutput .= '<div class="medium-6 small-12 columns" style="margin-bottom: 30px;"></div>';
          }
          // retrieve cached results for lower quality results
          $PoorResultsQuery = "SELECT * FROM bookaretreat_map_resultset WHERE SessionId='$CurrentSessionId' AND MatchQuality='fair' ORDER BY Label ASC LIMIT 0 , 20; ";
          $PoorResultsResult = mysql_query($PoorResultsQuery);
          if (!$PoorResultsResult) {
          die("Invalid query: " . mysql_error());
          }
          while ($PoorResultsRow = @mysql_fetch_assoc($PoorResultsResult)){
            extract($PoorResultsRow);
            
            //$urlname = urlencode( str_replace( ' ', '_', $SiteName ) );
            $StrippedSiteName = preg_replace("/[^a-zA-Z0-9\s]/", '', $SiteName);
            $urlname = urlencode( strtolower(str_replace( ' ', '-', $StrippedSiteName ) ) );
            $urlname = str_replace( '--', '-', $urlname );

            // retrieve room types and primary photo for this site
              $roomquery = "SELECT ImageOnePath FROM pccca_sitedirectory WHERE SiteId='$SiteId' LIMIT 1; ";
              $roomresult = mysql_query($roomquery, $db);
              if (!$roomresult) {
              die("Invalid query: " . mysql_error());
              }
              $roomrow = @mysql_fetch_assoc($roomresult);
              if($roomrow) {
                extract($roomrow);
              }
                
            if($ImageOnePath!=''){ $SiteImage='http://www.pccca.net/'.$ImageOnePath; }else{ $SiteImage=''; }
        
            $AdditionalResultsOutput .= '<div class="medium-3 small-12 columns" style="margin-bottom: 30px;">';
              $AdditionalResultsOutput .= '<div class="card-container">';
                $AdditionalResultsOutput .= '<img src="'.$SiteImage.'" />';
                $AdditionalResultsOutput .= '<h4 class="name">'.$SiteName.'</h4>';
                $AdditionalResultsOutput .= '<p class="city">'.$Address.'</p>';
                $AdditionalResultsOutput .= '<a href="view_details.php?siteid='.$SiteId.'" class="details results ripple-effect" data-ripple-color="#b44904" data-ripple-limit=".card-container">Find Out More</a>';
              $AdditionalResultsOutput .= '</div>';
            $AdditionalResultsOutput .= '</div><!-- ending of result -->';
          }

          $extra = mysql_num_rows($PoorResultsResult) % 3;
          switch ($extra) {
            case 1:
              $TopResultsOutput .= '<div class="medium-3 small-12 columns" style="margin-bottom: 30px;"></div>';
          }     
        
        
        if($TopResultsOutput==''){
          $TopResultsOutput = '<br />No exact matches were found to meet your exact criteria.<br />You may want to try again with slightly broader search criteria.<br /><br />';
        }
        
        echo '<div class="row" data-equalizer>';
          echo '<!-- Left 8 columns for results -->';
          echo '<div class="large-8 columns text-center results-row" data-equalizer-watch>';
            echo '<div clas="row">';
              echo $TopResultsOutput;
            echo '</div>';
          echo '</div>';
        ?>
        <div class="large-4 columns map" data-equalizer-watch>
          <div id="map"></div><!-- ending of map -->
        </div>
        <?php
        echo '<div style="clear:both"></div>';
        if($AdditionalResultsOutput!=''){
          echo '<div clas="row">';
            echo '<h2 class="additional">Additional Results Close to Meeting Your Criteria</h2>';
            echo $AdditionalResultsOutput;
          echo '</div>';  
        }
        ?>
    </div>

    <script src="js/vendor/jquery.js"></script>
    <script src="js/foundation.min.js"></script>
    <script src="js/simple-ripple-effect.js"></script>
    <script src="js/slick.min.js"></script>
    <script src="js/foundation/foundation.equalizer.js"></script>
    <script>
      $(document).foundation();
    </script>
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-68061932-1', 'auto');
      ga('send', 'pageview');

    </script>
  </body>
</html>
