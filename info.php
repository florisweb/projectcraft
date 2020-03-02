<?php
    include "PHP/config.php";
?>

<!DOCTYPE html>
<html>
    <head>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0' name='viewport'/>
        <link rel="stylesheet" type="text/css" href="css/main.css?antiCache=6">
        <title><?php echo $CONFIG["server"]["name"] . " - Info"; ?></title>
        <script type="text/javascript" src="js/jQuery.js"></script>


        <style>

            #blogHolder {
                position: relative;
                top: 20px;
                margin: auto;
                
                width: 80vw;
                height: 100vh;

                color: #333;
                line-height: 20px;
            }

            #blogHolder .page {
                position: absolute;
                float: left;

                width: calc(40vw - 60px * 2);
                height: calc((40vw - 60px * 2) * 1.6);
                max-height: calc(95vh - 60px * 2);

                padding: 60px;

                overflow: hidden;
                transition: all .3s;

                cursor: pointer;
            }

            #blogHolder .page.hide {
                pointer-events: none;
            }

            #blogHolder .page:nth-child(2n - 1) {
                background-image: url("images/bookPageLeft.png");
                background-repeat: no-repeat;
                background-size: 100% 100%;
            }

            #blogHolder .page:nth-child(2n - 1).hide {
                top: 100vh;
            }

            #blogHolder .page:nth-child(2n) {
                left: calc(50% - 10px);
                
                background-image: url("images/bookPageRight.png");
                background-repeat: no-repeat;
                background-size: 100% 100%;

                transform-origin: top left;
            }

            #blogHolder .page:nth-child(2n).hide {
                transform: rotateY(180deg);
                opacity: 0;
            }



            #blogHolder a {
                opacity: .8;
            }

             #blogHolder h4 {
                line-height: 10px;
            }

        </style>
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

           <div id="blogHolder" class="text">
    
                <?php
                    // Use [newLine] for a new line


                    $data = file_get_contents("uploads/testBlog.html");
                    $charsPerPage = 1000;
                    $sentences = explode(".", $data);


                    $curChars = 0;
                    $pageText = "";
                    foreach ($sentences as $sentence)
                    {
                        $newLine = explode("[newPage]", $sentence);
                        
                        if (strlen($sentence) + $curChars < $charsPerPage && sizeof($newLine) == 1)
                        {
                            $pageText .= $sentence . ".";
                            $curChars += strlen($sentence);
                            continue;
                        }

                        if (sizeof($newLine) != 1) $pageText .= $newLine[0];


                        echo '<div class="page hide">' . $pageText . '</div>';
                        
                        $pageText = "";
                        $curChars = 0;
                        
                        if (sizeof($newLine) != 1)
                        {
                            $pageText = $newLine[1] . ".";
                            $curChars = strlen($pageText);
                        }
                    }

                    if ($curChars !== 0) echo '<div class="page hide">' . $pageText . '</div>';
                ?>

            
                <div class="page hide">
                    <div id='info_memberHolder'>
                        <?php
                            $c = 0;
                            $members = json_decode(file_get_contents($CONFIG["memberData-url"]), true);
                            foreach($members as $player) {
                                echo    "<div class='avatarHolder'>" . 
                                            "<img src='PHP/heads.php?type=body&scale=10&username=" . $player[0] . "'' class='avatar'>" . 
                                            "<div class='text'>" . $player[0] . "</div>" . 
                                        "</div>";
                                $c++;
                            }
                        ?>
                    </div>
                </div>
           </div>
        </div>
        
        <script type="text/javascript">

            let Page = new function() {
                let This = {
                    curPage: 0,
                    openPage: openPage,
                    openNextPage: next,
                    openPrevPage: previous,
                };
                const HTML = {
                    pages: $("#blogHolder .page")
                }
                
                for (let i = 0; i < HTML.pages.length; i++)
                {
                    HTML.pages[i].onclick = function() {
                        if (i % 2 == 0) return previous();
                        next();
                    }
                }

                function next() {
                    openPage(This.curPage + 1);
                }
                function previous() {
                    openPage(This.curPage - 1);
                }


                function openPage(_index) {
                    if (_index > HTML.pages.length || _index < 0) return;
                    
                    let curPages = $("#blogHolder .page:not(.hide)");
                    for (page of curPages) page.classList.add("hide");

                    HTML.pages[_index * 2].classList.remove("hide");
                    HTML.pages[_index * 2 + 1].classList.remove("hide");
                    This.curPage = _index;
                }

                return This;
            }


            setTimeout(Page.openPage, 100, 0);
        </script>
    </body>
</html>




<!-- 




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
        
      
        <div class="text-only homescreen_projectCraftInfo" style="position: relative; left: 0px; animation-delay: 15s; bottom: 0px; margin-top: 20px;">
            <a class="discord" href="<?php echo $CONFIG["server"]["discordLink"]; ?>">Come join us on our discord server.</a>
        </div>
    </body>
</html> -->