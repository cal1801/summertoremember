<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
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
	<link href="css/feedback_notices.css" rel="stylesheet" type="text/css" />
	<link href="css/style.css" rel="stylesheet" type="text/css" />
	<!--[if lte IE 6]>
	<link rel="stylesheet" type="text/css" media="screen" 
	href="css/ie.css" />   
	<![endif]-->
	
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB0cx8KIfpJTjosKOwuozA9DNVHmxg_wzk&signed_in=true&callback=initMap" async defer></script>
	<script type="text/javascript">
	    //<![CDATA[
	    var geocoder;

	    function load() {
			geocoder = new google.maps.Geocoder();
		}

		function searchLocations() {
			geocoder = new google.maps.Geocoder();
		  	var address = document.getElementById('addressInput').value;
		  	geocoder.geocode({'address': address}, function(results, status) {
		    if (status === google.maps.GeocoderStatus.OK) {
		    	latlng = results[0].geometry.location
		    	document.SearchForm.latlng.value = latlng;
		    	document.getElementById("SearchForm").submit();
	      	} else {
		      alert('Geocode was not successful for the following reason: ' + status);
		    };
		  });
		}
	    //]]>
	</script>
	
</head>

<body onload="load()">
	<div class="content">
		<img src="img/logo.png" />
		<?php
			if( trim($_GET['error'])!='' )
			{
				switch( trim($_GET['error']) ) 
				{
				    case "address":
				        echo '<div class="error">You did not select a location for the search.</div>';
				        break;
				    case "latlng":
				        echo '<div class="error">Unable to calculate location.  This may be caused if you have JavaScript disabled.</div>';
				        break;
				}
			}
		?>
		<form id="SearchForm" action="search.php" method="get" name="SearchForm">
			<div class="row">
	          <div class="large-12 columns">
	            <label><h1>Where do you want your child to go to camp this summer?</h1><br>
	              <input class="large-12 columns" type="text" placeholder="(Example: Anytown, State or Provence)" name="address" id="addressInput">
	              <input type="submit" value="Search For A Camp" class="btn" onclick="searchLocations()">
	            </label>
	          </div>
	        </div>
			<input type="hidden" name="latlng" value="" />
		</form>

		<div id="pccca_map" style="width: 500px; height: 600px;"></div>
		<div id="clicked-state"></div>
	</div><!-- ending of content -->
	<div class="overlay"></div>

    <video id="my-video" class="video" autoplay muted loop>
    <source src="media/campvideo2.mp4" type="video/mp4">
    <source src="media/demo.ogv" type="video/ogg">
    <source src="media/demo.webm" type="video/webm">
    </video><!-- /video -->
	<script>
		document.getElementById("SearchForm").addEventListener('submit', function(event) {
        event.preventDefault();

        searchLocations()

    	}, true);
	</script>
	<script src="js/vendor/jquery.js"></script>
	<script src="js/raphael-min.js"></script>
  	<script src="js/jquery.usmap.js"></script>
    <script src="js/foundation.min.js"></script>
    <script>
      $(document).foundation();
    </script>

    <script>
		$('#pccca_map').usmap({
			'stateSpecificStyles': {
				'CA': {fill: '#CC3433'},
				'OR': {fill: '#CC3433'},
				'WA': {fill: '#CC3433'},
				'ID': {fill: '#CC3433'},
				'NV': {fill: '#CC3433'},
				'AK': {fill: '#CC3433'},
				'HI': {fill: '#CC3433'},
				'CO': {fill: '#CDCC00'},
				'MT': {fill: '#CDCC00'},
				'WY': {fill: '#CDCC00'},
				'UT': {fill: '#CDCC00'},
				'AZ': {fill: '#006599'},
				'NM': {fill: '#006599'},
				'TX': {fill: '#006599'},
				'OK': {fill: '#006599'},
				'AR': {fill: '#006599'},
				'LA': {fill: '#006599'},
				'KS': {fill: '#9966FF'},
				'MO': {fill: '#9966FF'},
				'IL': {fill: '#9966FF'},
				'IN': {fill: '#9966FF'},
				'ND': {fill: '#990033'},
				'SD': {fill: '#990033'},
				'NE': {fill: '#990033'},
				'MN': {fill: '#990033'},
				'IA': {fill: '#990033'},
				'WI': {fill: '#990033'},
				'AL': {fill: '#CD6601'},
				'MS': {fill: '#CD6601'},
				'KY': {fill: '#CD6601'},
				'TN': {fill: '#CD6601'},
				'MI': {fill: '#336601'},
				'OH': {fill: '#336601'},
				'FL': {fill: '#96CA05'},
				'GA': {fill: '#96CA05'},
				'SC': {fill: '#96CA05'},
				'WV': {fill: '#999A01'},
				'PA': {fill: '#999A01'},
				'NY': {fill: '#2C0092'},
				'NJ': {fill: '#2C0092'},
				'RI': {fill: '#2C0092'},
				'CT': {fill: '#2C0092'},
				'MA': {fill: '#2C0092'},
				'VT': {fill: '#2C0092'},
				'NH': {fill: '#2C0092'},
				'ME': {fill: '#2C0092'},
				'NC': {fill: '#96CA05'},
				'VA': {fill: '#96CA05'},
				'DC': {fill: '#96CA05'},
				'MD': {fill: '#96CA05'},
				'DE': {fill: '#96CA05'},
			},
			'stateSpecificLabelBackingStyles': {
				'DE': {fill: '#96CA05'},
				'VT': {fill: '#2C0092'},
				'NH': {fill: '#2C0092'},
				'MA': {fill: '#2C0092'},
				'NJ': {fill: '#2C0092'},
				'RI': {fill: '#2C0092'},
				'CT': {fill: '#2C0092'},
				'DC': {fill: '#96CA05'},
				'MD': {fill: '#96CA05'},
			},
			'labelTextStyles': {
				fill: "#ffffff",
			},
			'stateStyles': {
				fill: "#4ECDC4",
				stroke: "#41A59B",
				"stroke-width": 1,
				"stroke-linejoin": "round",
				scale: [1, 1]
			},
			'stateHoverStyles': {
				fill: "#FFFFFF",
				stroke: "#ADCC56",
				scale: [2, 2]
			},
				'labelBackingStyles': {
				fill: "#4ECDC4",
				stroke: "#41A59B",
				"stroke-width": 1,
				"stroke-linejoin": "round",
				scale: [1, 1]
			},

			// The styles for the hover
			'labelBackingHoverStyles': {
				fill: "#C7F464",
				stroke: "#ADCC56",
			},
			'labelTextStyles': {
				fill: "#222",
				'stroke': 'none',
				'font-weight': 300,
				'stroke-width': 0,
				'font-size': '10px'
			},
		  // The click action
		  mouseover: function(event, data) {
		    $('#clicked-state')
		      .text('You clicked: '+data.name)
		  }
		});
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