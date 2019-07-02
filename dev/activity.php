<?php
include_once("DB.php");

$mysqli = $DB->connect("eelekweb_pcmc");

if ($mysqli === false)
{
    header("HTTP/1.0 500 Internal Server Error");
    die("An error occured.");
}

$set = $mysqli->execute("SELECT username FROM activity WHERE online_to IS NULL ORDER BY username");

if ($set === false)
{
    header("HTTP/1.0 500 Internal Server Error");
    die("An error occured.");
}

$currently_online = "";

if (sizeof($set) == 0)
{
    $currently_online = "-";
}
else
{
    foreach ($set as $row)
    {
        $currently_online .= $row["username"] . ", ";
    }
}

$user_data = "";

if (isset($_GET["username"]))
{
    $username = $_GET["username"];
    
    if($username === "Thebluepin") {
        $user_data = "<div>Beep boop I'm a bot. <div class=\"infinity\">I'm here to AFK.<div class=\"reverse\"><b>Play time: ∞</b></div></div></div>";
    } else {
      $stmt = $mysqli->execute("SELECT time_format(time_played, \"%H hours, %i minutes and %S seconds.\") as time_played FROM v_time_played WHERE username=?", array($username));
    
      if ($stmt === false) 
      {
          error_log("Unable to query database. Line 37.");
          header("HTTP/1.0 500 Internal Server Error");
          die("An error occured.2");
      }
    
      if (sizeof($stmt) == 0)
      {
          $user_data = "User " . $username . " could not be found.";
      } 
      else if (sizeof($stmt) == 1)
      {
          $row = $stmt[0];
          $user_data = "Player " . $username . " has played for: " . $row["time_played"];
      }
      else
      {
          error_log("Too many rows for user " . $username . ".");
          echo "An error occured.3";
          exit;
      }    
    }
}

$mysqli->close();
?>
<html>
  <head>
    <style>
      body {
          overflow: hidden;
          text-overflow: ellipsis;
          white-space: nowrap;
      }
      
      div.reverse {
        -webkit-animation:spin 2s cubic-bezier(.65,.05,.36,1) infinite reverse;
        -moz-animation:spin 2s cubic-bezier(.65,.05,.36,1) infinite reverse;
        animation:spin 2s cubic-bezier(.65,.05,.36,1) infinite reverse;
        position: relative;
        margin: 10px;
      }
      
      div.infinity {
        -webkit-animation:spin 4s cubic-bezier(.68,-0.55,.27,1.55) infinite;
        -moz-animation:spin 4s cubic-bezier(.68,-0.55,.27,1.55) infinite;
        animation:spin 4s cubic-bezier(.68,-0.55,.27,1.55) infinite;
        
        position: fixed;
        margin: 10px;
      }
      
      @-moz-keyframes spin { 0% { -moz-transform: rotateZ(0deg); } 100% { -moz-transform: rotateZ(360deg); } }
      @-webkit-keyframes spin { 0% { -webkit-transform: rotateZ(0deg); } 100% { -webkit-transform: rotateZ(360deg); } }
      @keyframes spin { 0% { -webkit-transform: rotateZ(0deg); transform:rotateZ(0deg); } 100% { -webkit-transform: rotateZ(360deg); transform:rotateZ(360deg); } }
    </style>
  </head>
  <body>
    Currently online: <?php echo rtrim($currently_online, ", "); ?>.
    <br>
    <br>
    <form action="activity.php" method="get">
      Find a user: <input type="text" name="username">
      <br>
      <input type="submit" value="Search">
    </form>
    <br>
    <br>
    <?php echo $user_data; ?>
  </body>
</html>