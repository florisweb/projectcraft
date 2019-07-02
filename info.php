<?php
    $players = explode(PHP_EOL, file_get_contents("uploads/players.txt"));
?>

<!DOCTYPE html>
<html>
    <head>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0' name='viewport'/>
        <link rel="stylesheet" type="text/css" href="main.css">
        <title>ProjectCraftMC | Info.</title>
    </head>
    <body style="overflow: hidden; background-color: black;" class="noselect">
        <div id="topBar">
            <img src="images/homeIcon.svg" class="button" onclick="window.location.replace('index.php')">
            <div class="shadowBackground"></div>
        </div>
        
        <img style="-webkit-filter: blur(40px); filter: blur(40px); animation-direction: reverse;" id="homeScreen_backgroundImage" src="<?php
            $files = glob("uploads/images/homescreen/*.png");
            $length = sizeof($files);
            $index = rand(0, $length-1);
            echo $files[$index];
        ?>">
        
        
        <div class="text" id="homescreen_projectCraftLogo">
            PROJECTCRAFT
        </div>
        
        <div class="text-only homescreen_projectCraftInfo" style="animation-delay: 6s; top: 125px;">
            ProjectCraft is a vanilla SMP server, for Java Edition 1.13.2, ran by JarPlayGo.
        </div>
        
        <div class="text-only homescreen_projectCraftInfo" style="animation-delay: 9s; top: 150px;">
            It started in 2016. This is season 3, which went live in December of 2018.
        </div>
        
        <div class="text-only homescreen_projectCraftInfo" style="animation-delay: 12s; top: 175px;">
            It currently has an active playerbase of <?php echo sizeOf($players); ?> players.
        </div>
        
        <div class="avatars homescreen_projectCraftInfo" style="animation-delay: 15s; top: 225px;">
        <?php
            foreach($players as $player) {
                echo "<div class=\"avatar text\"><img src=\"heads.php?type=body&scale=10&username=".$player."\" class=\"avatar\"><br>".$player."</div>\n\r";
            }
        ?>
        </div>
        
        <div class="text-only homescreen_projectCraftInfo" style="animation-delay: 20s; bottom: 10px; margin-top: 20px;">
            <a class="discord" href="https://discord.gg/ekXGedb">Come join us on our discord server.</a>
        </div>
    </body>
</html>