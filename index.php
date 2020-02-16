<?php
    include "PHP/config.php";
?>

<!DOCTYPE html>
<html>
	<head>
		<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0' name='viewport'/>
		<link rel="stylesheet" type="text/css" href="css/main.css?antiCache=6">
		<title><?php echo $CONFIG["server"]["name"]; ?></title>
	</head>

	<body class="noselect">
		<div id="homeScreen">
			<div class="background" style="
			background-image: url(<?php
                $files = glob("uploads/images/*");
				$length = sizeof($files);
				$index = rand(0, $length - 1);
                echo $files[$index];
			?>)"></div>

			<div class="text" id="homescreen_projectCraftLogo">
				<?php echo strtoupper($CONFIG["server"]["name"]); ?>
				<div style="font-size: 15px; opacity: .8">
					Written and maintained by 
					<a href="https://eelekweb.tk" style="color: #fff">eelekweb.tk</a> 
					and 
					<a href="https://florisweb.tk" style="color: #fff">florisweb.tk</a>
				</div>
			</div>

			<div id="homeScreen_navigationHolder"> 
				<a href="info.php" style='text-decoration: none'>
					<div class="navigationItem">
						<img src="PHP/heads.php?username=MHF_Question&type=head&scale=10" class="iconHolder">
						<div class="text itemTitle">INFO</div>
					</div>
				</a>
                
                <a href="map.php" style='text-decoration: none'>
					<div class="navigationItem">
						<img src="PHP/heads.php?username=0qt&type=head&scale=10" class="iconHolder">
						<div class="text itemTitle">MAP</div>
					</div>
				</a>
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
