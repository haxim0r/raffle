<?php if(isset($_SESSION['auth'])){ ?>
    <script type="text/javascript" language="javascript 1.3">
        /*-=-=-=-=-=-=-=-=-=-=-=-=-=-=- START NAV CODE -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-*/
        function daNavElement(){

            this.daLevel = -1;
            this.daTitle = new String();
            this.daValue = new String();
            this.daURL = new String();
            this.daSibz = new Array();
            this.daParent = new String();
        }

        function daTier(daNavItem, daToggle, maxTimerIndex){

            for(var daCounter = maxTimerIndex; daCounter > 0; daCounter--){

                if(daToggle == daCounter){

                    var timerReferance = 'daTimer' + daCounter;

                    clearTimeout(eval(timerReferance));

                    break;
                }
            }

            var daCurrentHoverContainer = document.getElementById(daNavItem);

            if(daCurrentHoverContainer.style.display == 'none' || daCurrentHoverContainer.style.display == ''){

                daCurrentHoverContainer.style.display = 'block';
                daCurrentHoverContainer.style.visibility = 'visible';
            }
        }

        function daHide(daNavItem){

            if(daNavItem.style.display == 'block'){

                daNavItem.style.visibility = 'hidden';
                daNavItem.style.display = 'none';
            }
        }

        function navigate(daDestination){
            
            var destinationIframe = document.getElementById("iframe_daBody");

            destinationIframe.src = daDestination;
        }


        var daMenu = new Array();

    <?php
    
    $mySQL = new db_mysql();

    $sql = 'select * from raffle.site_topnav where parent=\'#\' and status!=\'deleted\' order by sort';

    $resultset = mysqli_query($mySQL->connection, $sql);

    if($resultset){

        $timersNeededforDHTMLmenu = 1;

        $daMenuString = '';

        while($data = mysqli_fetch_object($resultset)){

            $daMenuString .= 'var daTimer'.$timersNeededforDHTMLmenu++.';';

            $daMenuString .= 'var daRoots = new daNavElement();';

            $daMenuString .= 'daRoots.daLevel = 0;';
            $daMenuString .= 'daRoots.daTitle = "'.$data->title.'";';
            $daMenuString .= 'daRoots.daValue = "'.$data->text.'";';
            $daMenuString .= 'daRoots.daURL = "'.$data->href.'";';

            $sql_inner = 'select * from raffle.site_topnav where parent=\''.$data->id.'\' and status!=\'deleted\' order by sort';

            $resultset_inner = mysqli_query($mySQL->connection, $sql_inner);

            if($resultset_inner){

                while($data_inner = mysqli_fetch_object($resultset_inner)){

                    $daMenuString .= 'var daFirstDegree = new daNavElement();';

                    $daMenuString .= 'daFirstDegree.daLevel = 1;';
                    $daMenuString .= 'daFirstDegree.daTitle = "'.$data_inner->title.'";';
                    $daMenuString .= 'daFirstDegree.daValue = "'.$data_inner->text.'";';
                    $daMenuString .= 'daFirstDegree.daURL = "'.$data_inner->href.'";';
                    $daMenuString .= 'daFirstDegree.daParent = "'.$data_inner->parent.'";';

                    $daMenuString .= 'daRoots.daSibz.push(daFirstDegree);';
                }
            }

            $daMenuString .= 'daMenu.push(daRoots);';
        }

        echo $daMenuString;
    }

    $mySQL->disconnect();
    
    ?>

        var daTimeoutValue = 725;

        daMenuCode = "<div id='daMenuDiv' class='daMenuDiv'>";

        for(var daCounter = 1; daCounter <= daMenu.length; daCounter++){

            var daLink = new daNavElement();

            daLink = daMenu[daCounter - 1];

            var daRootElementID = "rootElement" + daCounter;

            for(var daCounter_inner = daMenu.length; daCounter_inner > 0; daCounter_inner--){

                if(daCounter % daCounter_inner == 0){

                    daMenuCode += "<div title='" + daLink.daTitle + "' class='daRootLevel' ONMOUSEOVER='daTier(\"" + daRootElementID + "\", " + daCounter_inner + ", " + daMenu.length + ");' ONMOUSEOUT=\"daTimer" + daCounter_inner + "=setTimeout('daHide(" + daRootElementID + ")', " + daTimeoutValue + ")\">";
                    daMenuCode += "<center><A CLASS='daMenuLink' HREF='javascript: navigate(\"" + ((daLink.daURL.length > 0)?daLink.daURL:"#") + "\");'>" + daLink.daValue + "</A></center>";
                    daMenuCode += "<div class='daNextLevel' ID='" + daRootElementID + "' ONMOUSEOVER='clearTimeout(daTimer" + daCounter_inner + ")' ONMOUSEOUT='clearTimeout(daTimer" + daCounter_inner + ")'>";

                    for(var daSecondCounter = 0; daSecondCounter < daLink.daSibz.length; daSecondCounter++){

                        var daSiblingLink = new daNavElement();

                        daSiblingLink = daLink.daSibz[daSecondCounter];

                        daMenuCode += "<A CLASS='daMenuLink' HREF='" + ((daLink.daSibz[daSecondCounter].daURL.length > 0)?daLink.daSibz[daSecondCounter].daURL:"#") + "'>" + daLink.daSibz[daSecondCounter].daValue + "</A>";
                    }

                    daMenuCode += "</div>";
                    daMenuCode += "</div>";

                    break;
                }
            }
        }

        daMenuCode += "</div>";

        document.write(daMenuCode);
        /*-=-=-=-=-=-=-=-=-=-=-=-=-=-=- END NAV CODE -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-*/
    </script>
<?php } ?>