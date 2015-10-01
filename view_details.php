<?php  
require('connect.php');
@session_start();  //open the session
$CurrentSessionId = session_id();

// site id comes in like this:  conference-123 so we strip the first 11 characters off (they are there for SEO only)
$SiteId = trim($_GET['siteid']);

// retrieve details for this site
$query = "SELECT * FROM pccca_sitedirectory WHERE SiteId='$SiteId' LIMIT 1; ";
$result = mysql_query($query, $db);
if (!$result) {
  die("Invalid query: " . mysql_error());
}
$row = @mysql_fetch_assoc($result);
if($row) {
  extract($row);
}
else
{
  //redirect to home
  header("Location: index.php");
  exit();
}
?>

<!DOCTYPE html>
<html class="no-js" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Best Summer Yet | Dogwood Acres</title>
    <!--<link href='http://fonts.googleapis.com/css?family=Fjalla+One' rel='stylesheet' type='text/css'>-->
    <link href='http://fonts.googleapis.com/css?family=Montserrat:700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="css/foundation.css" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="css/simple-ripple-effect.css" />
    <link rel="stylesheet" href="css/slick.css">
    <link rel="stylesheet" href="css/slick-theme.css">
    <script src="js/vendor/modernizr.js"></script>
  </head>
  <body>
    
    <div class="large-12 columns top-bar">
      <div class="large-4 small-5 columns">
        <img src="img/logo.png" />
      </div>
      <div class="large-8 small-7 columns menu">
        <a href="javascript:history.back()"><h3>&#8610; Back to Results</h3></a>
      </div>
    </div><!--end top-bar-->
    
    <div class="row">
      <div class="large-12 columns camp-container">
        <div class="row">
          <div class="large-10 columns">
            <h1 style="margin: 0;"><?php echo $SiteName; ?></h1>
            <?php echo $PhysicalCity .', '. $PhysicalState ?><br />
          </div>
          <div class="large-2 columns text-right">
            <?php if($PcccaMember=='1'){ echo '<a href="http://www.pccca.net"><img src="/PCCCA/bestsummeryet/img/member_pccca.png" alt="PCCCA Member" class="pccca" /></a>'; } ?>
          </div>
        </div>
      </div>
      <div class="large-12 columns camp-container">
        <p class="description"><?php echo $SiteDescription; ?></p>
      </div>
      <div class="large-12 columns camp-container">
        <div class="row camp-images">
          <div class="large-3 columns">
            <?php if($ImageOnePath!=''){ echo '<img src="http://www.pccca.net/'.$ImageOnePath.'" alt="'.$SiteName.'" />'; } ?>
          </div>
          <div class="large-3 columns">
            <?php if($ImageTwoPath!=''){ echo '<img src="http://www.pccca.net/'.$ImageTwoPath.'" alt="'.$SiteName.'" />'; } ?>
          </div>
          <div class="large-3 columns">
            <?php if($ImageThreePath!=''){ echo '<img src="http://www.pccca.net/'.$ImageThreePath.'" alt="'.$SiteName.'" />'; } ?>
          </div>
          <div class="large-3 columns website">
            <?php echo '<a href="'.$WebsiteAddress.'" class="ripple-effect details" data-ripple-color="#f2c84b">Learn how to apply for a staff position</a>' ?>
          </div>
        </div>
      </div>
    </div>

    <script src="js/vendor/jquery.js"></script>
    <script src="js/foundation.min.js"></script>
    <script src="js/foundation.equalizer.js"></script>
    <script src="js/slick.min.js"></script>
    <script src="js/simple-ripple-effect.js"></script>
    <script>
      $(document).foundation();
    </script>
    <!--<script>
      $(document).ready(function(){
        $('.camp-images').slick({
          
        });
      });
    </script>-->
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

<?php 
//////////////////////////////////////////
// OBFUSCATE EMAIL ADDRESSES FOR ANTI_SPAM
//////////////////////////////////////////
function findEmails($string, $MakeLink=1, $UseJavaScript=1)
{
  //NOTE: if UseJavaScript is turned on (1), all email addresses are forced as links (regardless of "MakeLink" value)

  //start by removing any existing mailto links so we can work with plain text
  $string = stripMailtoLink($string);
  
  $pattern = "[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,4})";
  preg_match_all("/$pattern/", $string, $matches);
  foreach ($matches[0] as $value)
  {
    $MatchedEmail = $value;
    $AsciiEmail = convertToAscii($MatchedEmail);
    if($UseJavaScript==1)
    {
      $NewEmail = jsEmail($MatchedEmail);
    }
    elseif($MakeLink==1)
    {
      $NewEmail = '<a href="mailto:' . $AsciiEmail . '">'.$AsciiEmail.'</a>';
    }
    else
    {
      $NewEmail = $AsciiEmail;
    }
    $string = str_replace($MatchedEmail,$NewEmail,$string);
  }
  return $string;
}

function jsEmail($RawEmail)
{
  // We split username and domain into separate strings - otherwise the bot will have no trouble finding the email address

  // Split the email into user name and domain
  list($user, $domain) = explode('@', $RawEmail);

  // Form the href attribute
  $mailtouser = "mailto:$user";

  $user = convertToAscii($user);        //plain user
  $domain = convertToAscii($domain);      //plain domain
  $mailtouser = convertToAscii($mailtouser);  //href user (mailto added)

  // Generate output
  $output = <<<EOT
  <script>
  document.write('<a href="$mailtouser' + '&#64;');
  document.write('$domain' + '"');
  document.write('>$user' + '&#64;');
  document.write('$domain</a>');
  </script>
EOT;
  return $output;
}

function convertToAscii($text){
  $output = '';
  for($i = 0; $i < strlen($text); $i++)
  {
    $output .= '&#' . ord($text[$i]) . ';';
  }
  return $output;
} 

function stripMailtoLink($string)
{
  $pattern = '|(<a href="mailto:)([\w-\.]+@([\w-]+\.)+[\w-]{2,4})(">)[\._a-zA-Z0-9@-]*(</a>)|';
  preg_match_all($pattern, $string, $matches, PREG_SET_ORDER);
  foreach ($matches as $value)
  {
    $MatchedLinkString = $value[0];
    $MatchedEmail = $value[2];
    $string = str_replace($MatchedLinkString,$MatchedEmail,$string);
  }
  return $string;
}
?>
