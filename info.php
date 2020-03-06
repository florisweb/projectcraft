<?php
    include "PHP/config.php";
?>

<!DOCTYPE html>
<html>
    <head>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0' name='viewport'/>
        <link rel="stylesheet" type="text/css" href="css/main.css?antiCache=7">
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
                transition: all .5s;

                z-index: 11;

                cursor: pointer;
            }


            #blogHolder .page:not(#placeHolderPage).hide {
                pointer-events: none;
                opacity: 0;
            }

            #blogHolder .page:nth-child(2n - 1) {
                background-image: url("images/bookPageLeft.png");
                background-repeat: no-repeat;
                background-size: 100% 100%;

                transform-origin: bottom right;
            }

            #blogHolder .page:nth-child(2n - 1).flip {
                transform: rotateY(180deg);
                opacity: .2;
            }


            #blogHolder.closed .page:nth-child(2n) {
                left: 25%;
                z-index: 100;
            }




            #blogHolder .page:nth-child(2n) {
                left: 50%;

                background-image: url("images/bookPageRight.png");
                background-repeat: no-repeat;
                background-size: 100% 100%;

                transform-origin: bottom left;
            }
            
            #blogHolder .page:nth-child(2n).flip {
                transform: rotateY(-180deg);
                opacity: .2;
            }




            #blogHolder #placeHolderPage.page {
                z-index: 10;
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

           <div id="blogHolder" class="text closed">
    
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

         
                <?php
                    $members = json_decode(file_get_contents($CONFIG["memberData-url"]), true);
                    
                    for ($i = 0; $i < sizeof($members); $i += 6)
                    {
                        echo '<div class="page memberHolder hide">';
                        if ($i == 0) echo "<h1>Members</h1>";
                        
                        for ($ri = 0; $ri < 6; $ri++)
                        {
                            $index = $i + $ri;
                            if (!$members[$index]) continue;

                            echo "<div class='avatarHolder'>" . 
                                    "<img src='PHP/heads.php?type=body&scale=10&username=" . $members[$index][0] . "'' class='avatar'>" . 
                                    "<div class='text'>" . $members[$index][0] . "</div>" . 
                                "</div>";
                        }

                        echo '</div>';
                    }
                ?>
    

                <div class="page hide" id="placeHolderPage">
                </div>
           </div>
        </div>
        
        <script type="text/javascript">

            let Book = new function() {
                let This = {
                    curPage: 0,
                    openPage: openPage,
                    openNextPage: next,
                    openPrevPage: previous,
                    openBook: openBook
                };
                const HTML = {
                    pages: $("#blogHolder .page"),
                    blogHolder: $("#blogHolder")[0],
                }

                HTML.pages[0].classList.add("flip");
             
                
                for (let i = 0; i < HTML.pages.length; i++)
                {
                    HTML.pages[i].onclick = function() {
                        if (i % 2 == 0) return previous();
                        next();
                    }
                }


                function openBook() {
                    HTML.blogHolder.classList.remove("closed");
                    setTimeout(function() {
                        HTML.pages[0].classList.remove("flip");
                        Book.openPage(0);
                    }, 500);
                }




                function next() {
                    openPage(This.curPage + 1, true);
                }
                function previous() {
                    openPage(This.curPage - 1, false);
                }


                let animating = false;
                function openPage(_index, _nextPage = true) {
                    if (_index * 2 > HTML.pages.length - 1 || _index < 0 || animating) return;
                    animating = true;
                        
                    let openPages = $("#blogHolder .page:not(.hide)");
                    if (openPages.length)
                    {
                       let flipPage = openPages[1];
                       let stationairyPage = openPages[0];
                       if (!_nextPage) 
                       {
                            flipPage = openPages[0];
                            stationairyPage = openPages[1];
                       }

                        flipPage.classList.add("flip");

                        setTimeout(function() {
                            stationairyPage.classList.add("hide");

                            flipPage.classList.add("hide");
                            removeFlip(flipPage);

                            animating = false;
                        }, 500);
                    } else animating = false;

                
                    HTML.pages[_index * 2].classList.remove("hide");    
                    HTML.pages[_index * 2 + 1].classList.remove("hide");

                    This.curPage = _index;
                }

                function removeFlip(_element) {
                    _element.style.transition = "none";
                    _element.classList.remove("flip");

                    setTimeout(function() {
                        _element.style.transition = "";
                    }, 10);
                }

                return This;
            }


            setTimeout(Book.openBook, 2000, 0);
        </script>
    </body>
</html>

