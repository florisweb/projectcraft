<?php
$allowed_ips = explode("\n", file_get_contents("allowed_ips.txt", true));

if(!in_array($_SERVER['REMOTE_ADDR'], $allowed_ips)) {
    header("HTTP/1.0 403 Forbidden");
    die("You do not have access to this page.");
}

if(!isset($_GET["data"])) {
    header("HTTP/1.0 400 Bad Request");
    die("Bad Request.");
}

$data = $_GET['data'];
file_put_contents("updaterlog.txt", $data);

$mysqli = mysqli_connect("localhost", "eelekweb_pcmcweb", "Ad15db62453681f581d9ea90a9f54526", "eelekweb_pcmc");

if (mysqli_connect_errno($mysqli)) {
    error_log("Failed to connect to MySQL (" . mysqli_connect_errno($mysqli) . ")");
    exit;
}

$json_data = json_decode($data, true);

if(!empty($json_data["joined"])) {
    $stmt = mysqli_prepare($mysqli, "INSERT INTO activity (uuid, username, online_from) VALUES (?,?,now())");
    $uuid = "";
    $username = "";
    
    mysqli_stmt_bind_param($stmt, "ss", $uuid, $username);
    
    foreach($json_data["joined"] as $joined) {
        $uuid = str_replace("-", "", $joined["uuid"]);
        $username = $joined["username"];
        
        if(!mysqli_stmt_execute($stmt)) {
            error_log("Unable to send update: " . $stmt);
            exit;
        }
    }
    
    mysqli_stmt_close($stmt);
}

if(!empty($json_data["left"])) {
    $stmt = mysqli_prepare($mysqli, "UPDATE activity SET online_to=now() WHERE uuid=? AND online_to IS NULL");
    $uuid = "";
    mysqli_stmt_bind_param($stmt, "s", $uuid);
    
    foreach($json_data["left"] as $left) {
        $uuid = str_replace("-", "", $left["uuid"]);
        
        if(!mysqli_stmt_execute($stmt)) {
            error_log("Unable to send update: " . $stmt);
            exit;
        }
    }
    
    mysqli_stmt_close($stmt);
}

/*
function escapeHtml(text) {
  var map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
  };

  return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}
*/
?>