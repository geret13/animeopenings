<?php

  include_once("names.php");
  include_once("eggs.php");
  $videos = $names + $eggs;

  // Check if a specific video has been requested
  if (isset($_GET["video"])) {
    $filename = $_GET["video"];
  } else { // Else, pick a random video
    $filename = array_rand($videos);
  }

  // Error handling, QuadStyle™
  if (!file_exists("video/" . $filename)) {
    header("HTTP/1.0 404 Not Found");
    echo file_get_contents("backend/pages/notfound.html");
    die;
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSS and JS external resources block -->
    <link rel="stylesheet" href="font-awesome-4.4.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="CSS/main.css">

    <!-- For the crawlers -->
    <meta name="description" content="<?php // Echo data if using a direct link, else use a generic description.
      if(isset($_GET["video"])) {
        if ($videos[$filename]["title"] == "???") echo "Secret~";
        else echo $videos[$filename]["title"] . " from " . $videos[$filename]["source"];
      }
      else echo "Anime Openings from hundreds of series in high-quality"; ?>">

    <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
    <script src="main.js"></script>
    <title><?php // Echo data if using a direct link, else use a generic title.
      if(isset($_GET["video"])) {
        if ($videos[$filename]["title"] == "???") echo "Secret~";
        else echo $videos[$filename]["title"] . " from " . $videos[$filename]["source"];
      }
      else echo "Anime Openings"; ?></title>

    <!-- Meta tags for web app usage -->
    <meta content="#E58B00" name="theme-color">
    <meta content="yes" name="mobile-web-app-capable">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black-translucent" name="apple-mobile-web-app-status-bar-style">

    <!-- Logo links -->
    <link href="/assets/logo/152px.png" rel="apple-touch-icon">
    <link href="/assets/logo/16px.png" rel="icon" sizes="16x16">
    <link href="/assets/logo/32px.png" rel="icon" sizes="32x32">
    <link href="/assets/logo/64px.png" rel="icon" sizes="64x64">
    <link href="/assets/logo/152px.png" rel="icon" sizes="152x152">
    <!-- oversized because lol -->
    <link href="/assets/logo/512px.png" rel="icon" sizes="512x512">
  </head>

  <body>
    <video id="bgvid" loop preload="none" onended="onend();">
      <source src="video/<?php echo $filename; ?>" type="video/webm">
      lol lern 2 webm faggot
    </video>

    <div id="progressbar" class="progressbar">
      <div class="progress">
        <div id="bufferprogress" class="progress"></div>
        <div id="timeprogress" class="progress"></div>
      </div>
    </div>

    <a id="menubutton" href="/hub/faq.php" class="quadbutton fa fa-bars"></a>

    <div id="site-menu" hidden>
      <span id="closemenubutton" onclick="hideMenu()" class="quadbutton fa fa-times"></span>

      <p id="title">
        <?php

        // If we have the data, echo it
        if (array_key_exists($filename, $videos)) {
          echo $videos[$filename]["title"];
        }
        else { // Give a generic reply otherwise
          echo "???";
        }

        ?>
      </p>
      <p id="source">
        <?php

        // If we have the data, echo it
        if (array_key_exists($filename, $videos)) {
          echo "From " . $videos[$filename]["source"];
        }
        else { // Give a generic reply otherwise
          echo "From ???";
        }

        ?>
      </p>
      <span id="song">
        <?php

        // If we have the data, echo it
        if (array_key_exists("song", $videos[$filename])) {
          echo "Song: &quot;" . $videos[$filename]["song"]["title"] . "&quot; by " . $videos[$filename]["song"]["artist"];
        }
        else { // Otherwise, let's just pretend it never existed... or troll the user.
          if ($videos[$filename]["title"] == "???" || mt_rand(0,100) == 1)
            echo "Song: &quot;Sandstorm&quot; by Darude";
          else
            echo "";
        }

        ?>
      </span>

      <ul id="linkarea">
        <li class="link"<?php if ($videos[$filename]["title"] == "???") echo " hidden"; ?>>
          <a href="/?video=<?php if ($videos[$filename]["title"] != "???") echo $filename; ?>" id="videolink">Link to this video</a>
        </li>
        <li class="link"<?php if ($videos[$filename]["title"] == "???") echo " hidden"; ?>>
          <a href="video/<?php if ($videos[$filename]["title"] != "???") echo $filename; ?>" id="videodownload" download>Download this video</a>
        </li>
        <li class="link">
          <a href="/list">Video list</a>
        </li><li class="link">
          <a href="/hub">Hub</a>
        </li>
      </ul>

      <p class="betanote">
        This site is in beta. Request openings/endings and report errors by mentioning @QuadPiece on Twitter.
      </p>

      <p id="keybindings">Keyboard bindings</p>
      <ul class="keybinds-list">
        <li><span class="keycap">N</span> New video</li>
        <li><span class="keycap"><span class="fa fa-arrow-left"></span>/<span class="fa fa-arrow-right"></span></span> Back/Forward 10 seconds</li>
        <li><span class="keycap">Space</span> Pause/Play</li>
        <li><span class="keycap">F</span> Toggle Fullscreen</li>
        <li><span class="keycap">Page Up/Down or Scroll Wheel</span> Volume</li>
      </ul>
    </div>

    <div class="displayTopRight"></div>

    <div id="tooltip" class="is-hidden"></div>

    <div class="controlsleft">
      <span id="openingsonly" class="quadbutton fa fa-circle" onclick="toggleOpeningsOnly()" onmouseover="tooltip(this.id)" onmouseout="tooltip()"></span>
      <span id="getnewvideo" class="quadbutton fa fa-refresh" onclick="retrieveNewVideo()" onmouseover="tooltip(this.id)" onmouseout="tooltip()"></span>
      <span id="autonext" class="quadbutton fa fa-toggle-off" onclick="toggleAutonext()" onmouseover="tooltip(this.id)" onmouseout="tooltip()"></span>
    </div>

    <div class="controlsright">
      <span id="skip-left" class="quadbutton fa fa-arrow-left" onclick="skip(-10)" onmouseover="tooltip(this.id)" onmouseout="tooltip()"></span>
      <span id="skip-right" class="quadbutton fa fa-arrow-right" onclick="skip(10)" onmouseover="tooltip(this.id)" onmouseout="tooltip()"></span>
      <span id="pause-button" class="quadbutton fa fa-play" onclick="playPause()" onmouseover="tooltip(this.id)" onmouseout="tooltip()"></span>
      <span id="fullscreen-button" class="quadbutton fa fa-expand" onclick="toggleFullscreen()" onmouseover="tooltip(this.id)" onmouseout="tooltip()"></span>
    </div>

    <?php
    include_once("backend/includes/botnet.html");
    ?>
  </body>
</html>
