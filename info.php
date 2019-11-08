<?php
    include "PHP/config.php";
?>

<!DOCTYPE html>
<html>
    <head>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0' name='viewport'/>
        <link rel="stylesheet" type="text/css" href="css/main.css?a=16">
        <title><?php echo $CONFIG["server"]["name"] . " | Info"; ?></title>
    </head>
    <body style="background: #000;" class="noselect">
        <div id="topBar">
            <img src="images/homeIcon.svg" class="button" onclick="window.location.replace('index.php')">
        </div>
        
       
        <div id="homeScreen" style="
            background-image: url(<?php
                $files = glob("uploads/images/*");
                $length = sizeof($files);
                $index = rand(0, $length - 1);
                echo $files[$index];
            ?>); z-index: -1; opacity: .4"></div>
        
        
        <div class="text" id="homescreen_projectCraftLogo">
            <?php echo strtoupper($CONFIG["server"]["name"]); ?>
        </div>
        
        <?php
            $descriptionLines = explode("\n", $CONFIG["server"]["description"]);

            for ($l = 0; $l < sizeof($descriptionLines); $l++)
            {
                echo '<br><div class="text-only homescreen_projectCraftInfo" style="animation-delay: ' . $l / 4 . 's">' .
                    $descriptionLines[$l] .
                '</div>';
            }
        ?>

    
        
        <div id='info_memberHolder'>
            <?php
                $c = 0;
                foreach($CONFIG["server"]["members"] as $player) {
                    echo    "<div class='avatarHolder'>" . 
                                "<img src='PHP/heads.php?type=body&scale=10&username=" . $player[0] . "'' class='avatar'>" . 
                                "<div class='text'>" . $player[0] . "</div>" . 
                            "</div>";
                    $c++;
                }
            ?>
        </div>
        
        <div class="text-only homescreen_projectCraftInfo" style="position: relative; left: 0px; animation-delay: 15s; bottom: 0px; margin-top: 20px;">
            <a class="discord" href="<?php echo $CONFIG["server"]["discordLink"]; ?>">Come join us on our discord server.</a>
        </div>
    </body>
</html>