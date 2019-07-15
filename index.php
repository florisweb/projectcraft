<!--
	CREDITS:
	- Thanks to Crafatar.com for the avatars and heads.
-->
<!DOCTYPE html>
<html>
	<head>
		<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0' name='viewport'/>
		<link rel="stylesheet" type="text/css" href="css/main.css?antiCache=2">
		<title>ProjectCraftMC</title>
	</head>

	<body style="overflow: hidden;" class="noselect">
		<div id="homeScreen">
			<img style="animation-fill-mode: forwards;" id="homeScreen_backgroundImage" src="<?php
                    $files = glob("uploads/images/*.png");
					$length = sizeof($files);
					$index = rand(0, $length-1);
                    echo $files[$index];
				?>">
			<div class="text" id="homescreen_projectCraftLogo">
				PROJECTCRAFT
				<div style="font-size: 15px; opacity: 0.5">
					Written and maintained by 
					<a href="https://eelekweb.tk" style="color: #fff">eelekweb.tk</a> 
					and 
					<a href="https://florisweb.tk" style="color: #fff">florisweb.tk</a>
				</div>
			</div>

			<div id="homeScreen_navigationHolder"> 
				<div class="navigationItem" onclick="window.location.replace('info.php')">
					<img src="heads.php?username=MHF_Question&type=head&scale=10" class="iconHolder">
					<div class="text itemTitle">INFO</div>
				</div>
                
				<div class="navigationItem" onclick="window.location.replace('map.php')">
					<img src="heads.php?username=0qt&type=head&scale=10" class="iconHolder">
					<div class="text itemTitle">MAP</div>
				</div>
			</div>
		</div>
        
        <script type="text/javascript">
            window.onload = function() {
                if (getCookie("visit") != "") document.body.classList.add("quickStartAnimation");
                document.cookie = "visit=true";
            }
            
            function getCookie(cname) {
                var name = cname + "=";
                var ca = document.cookie.split(';');
                for(var i = 0; i < ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0) == ' ') {
                        c = c.substring(1);
                    }
                    
                    if (c.indexOf(name) == 0) {
                        return c.substring(name.length, c.length);
                    }
                }
                return "";
            }
        </script>
	</body>
</html>
