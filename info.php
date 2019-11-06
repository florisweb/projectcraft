<?php
    include "PHP/config.php";
?>

<!DOCTYPE html>
<html>
    <head>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0' name='viewport'/>
        <link rel="stylesheet" type="text/css" href="css/main.css">
        <title><?php echo $CONFIG["server"]["name"] . " | Info"; ?></title>
    </head>
    <body style="overflow-x: hidden; overflow-y: auto; background-color: black;" class="noselect">
        <div id="topBar">
            <img src="images/homeIcon.svg" class="button" onclick="window.location.replace('index.php')">
            <div class="shadowBackground"></div>
        </div>
        
        <img style="-webkit-filter: blur(40px); filter: blur(40px); animation-direction: reverse; height: 100vh;" id="homeScreen_backgroundImage" src="<?php
            $files = glob("uploads/images/*");
            $length = sizeof($files);
            $index = rand(0, $length - 1);
            echo $files[$index];
        ?>">
        
        
        <div class="text" id="homescreen_projectCraftLogo">
            <?php echo strtoupper($CONFIG["server"]["name"]); ?>
        </div>
        
        <?php
            $descriptionLines = explode("\n", $CONFIG["server"]["description"]);

            for ($l = 0; $l < sizeof($descriptionLines); $l++)
            {
                echo '<div class="text-only homescreen_projectCraftInfo" style="animation-delay: ' . $l . 's; top: 125px;">' .
                    $descriptionLines[$l] .
                '</div>';
            }
        ?>

    
        
        <div class="avatars homescreen_projectCraftInfo" style="display: grid; animation-delay: 12s; top: 225px;">
        <?php
            $c = 0;
            echo "<div style=\"display: flex;\">";
            foreach($CONFIG["server"]["members"] as $player) {
                if($c > 9) {
                  echo "</div>\r\n<div style=\"display: flex;\">";   
                  $c = 0;
                }
                echo "<div class=\"avatar text\"><img style=\"margin-top: 10px;\" src=\"PHP/heads.php?type=body&scale=10&username=".$player[0]."\" class=\"avatar\"><br>".$player[0]."</div>\n\r";
                $c++;
            }
            echo "</div>";
        ?>
        
        <div class="text-only homescreen_projectCraftInfo" style="position: relative; left: 0px; animation-delay: 15s; bottom 0px; margin-top: 20px;">
            <a class="discord" href="<?php echo $CONFIG["server"]["discordLink"]; ?>">Come join us on our discord server.</a>
        </div>
        </div>
    </body>
</html>