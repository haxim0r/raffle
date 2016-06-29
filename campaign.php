<?php require 'header.php'; ?>
<?php
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    if(isset($_POST['daAction'])){

        $sql = '';
        
        $imgData = 'null';
        if (isset($_FILES['image_000']) && $_FILES['image_000']['size'] > 0) {
            
            $image = $_FILES['image_000']['tmp_name'];
            $image = file_get_contents($image);
            $imgData = base64_encode($image);
        }
        
        if(isset($_POST["daAction"]) && ((strcasecmp($_POST["daAction"], "create_parent") == 0) || (strcasecmp($_POST["daAction"], "create_child") == 0 ))){

            //$sql = 'insert into raffle.campaign(name, caption, `desc`, href, image_000) values (\''.$_POST['name'].'\', \''.$_POST['caption'].'\', \''.$_POST['desc'].'\', \''.$_POST['href'].'\', \''.$imgData.'\')';
            $sql = "insert into raffle.campaign(name, caption, `desc`, href, image_000) values ('".$_POST['name']."', '".$_POST['caption']."', '".$_POST['desc']."', '".$_POST['href']."', '".$imgData."')";
        }
        elseif(isset($_POST["daAction"]) && (strcasecmp($_POST["daAction"], "remove_node") == 0 )){

            $sql = 'update raffle.campaign set status=\'deleted\' where id='.$_POST["node"];
        }
        elseif(isset($_POST["daAction"]) && (strcasecmp($_POST["daAction"], "edit_node") == 0 )){

            //$sql = 'update raffle.campaign set name=\''.$_POST['name'].'\', caption=\''.$_POST['caption'].'\', `desc`=\''.$_POST['desc'].'\', href=\''.$_POST['href'].'\', image_000=\''.$imgData.'\', status=\'updated\' where id='.$_POST['id'];
            $sql = "update raffle.campaign set name='".$_POST['name']."', caption='".$_POST['caption']."', `desc`='".$_POST['desc']."', href='".$_POST['href']."'".(is_null($imgData)?"":", image_000='".$imgData."'").", status='updated' where id=".$_POST['id'];
        }

        if(strlen($sql) > 0){

            //error_log("c3p0: ".$sql);
            
            $mySQL = new db_mysql();
            
            mysqli_query($mySQL->connection, $sql);
            
            $mySQL->disconnect();
        }
    }
}
?>
<link rel="stylesheet" href="/css/jstree/style.min.css" />
<link rel="stylesheet" href="/css/contextMenu/jquery.contextMenu.css" />
<link rel="stylesheet" href="/css/jquery_ui/jquery-ui.min.css" />

<script src="/js/jquery-1.11.2.min.js"></script>
<script src="/js/jstree.js"></script>
<script src="/js/jquery.contextMenu.js"></script>
<script src="/js/jquery.tabletoCSV.js"></script>
<script src="/js/jquery-ui-1.11.4/jquery-ui.min.js"></script>

<span class='campaign_tree' title="Click to add new Campaign to tree."><b>Campaign Tree</b></span>
<div id="div_jstree"></div>
<div id="dialog-form" title="Campaign Manamgement" style="display: none;">
    <form id='newMenuItemForm' name='newMenuItemForm' method="post" enctype="multipart/form-data">
        <fieldset>
            <a href="#" target="_blank" id="landing_page_link"><p id="landing_page"></p></a>
            <table>
                <tr id='node_id' style="display: none;">
                    <td align='right'><label for="id">id</label></td>
                    <td><input type="text" name="id" id="id" disabled></td>
                </tr>
                <tr>
                    <td align='right'><label for="name">name</label></td>
                    <td><input type="text" name="name" id="name" disabled></td>
                </tr>
                <tr>
                    <td align='right'><label for="caption">caption</label></td>
                    <td><input type="text" name="caption" id="caption"></td>
                </tr>
                <tr>
                    <td align='right'><label for="href">href</label></td>
                    <td>
                        <input type="text" name="href" id="href">
<script>
    $("#href").keyup(function(){
        var daLink = top.location.href+"entry.php?campaign="+$("#href").val();
        $("#landing_page").html(daLink);
        $("#landing_page_link").attr("href",daLink);
    });
</script>
                    </td>
                </tr>
                <tr>
                    <td align='right'><label for="desc">description</label></td>
                    <td><input type="text" name="desc" id="desc"></td>
                </tr>
                <tr>
                    <td align='right' valign="top"><label for="image_000">image</label></td>
                    <td>
                        <!-- MAX_FILE_SIZE must precede the file input field -->
                        <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
                        <input type="file" name="image_000" id="image_000">
                    </td>
                </tr>
                <tr>
                    <td align='right'><label for="status">status</label></td>
                    <td><select name="status" id="status"><option value="new">new</option><option value="updated">updated</option><option value="deleted">deleted</option></select></td>
                </tr>
                <tr>
                    <td colspan='2' align='center'>
                        <!-- Allow form submission with keyboard without duplicating the dialog button -->
                        <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>
</div>
<div id="dialog-report" title="Campaign entries report" style="display: none;">
    <table id="report-table"></table>
</div>
<script>
<?php
$mySQL = new db_mysql();

//$sql = 'select id, name, caption, \'{"class":"campaign_tree"}\' a_attr from raffle.campaign order by name';
//$sql = "select id, name, caption, '{\"class\":\"campaign_tree\"}' a_attr from raffle.campaign order by name";
$sql = "select id, '#' parent, concat(name, ' - ', caption) text, '{\"class\":\"campaign_tree\"}' a_attr from raffle.campaign where status!='deleted' order by name";

$resultset = mysqli_query($mySQL->connection, $sql);

if($resultset){

    //$resultArray = mysqli_fetch_all($resultset);
    $resultArray = array();
    while ($row = $resultset->fetch_assoc()) {

        $resultArray[] = $row;
    }
    mysqli_free_result($resultset);
    
    $darth_message = str_replace('"{', '{', json_encode($resultArray));
    $darth_message = str_replace('}"', '}', $darth_message);
    $darth_message = str_replace('\\', '', $darth_message);
    
    echo "$('#div_jstree').jstree({ 'core' : { 'data' : ".$darth_message." } });";
}

$mySQL->disconnect();
?>

    $('#div_jstree').bind('ready.jstree', function(e, data){
        // invoked after jstree has loaded
        resizeIframe();
    });
    $('#div_jstree').on("after_open.jstree", function(e, data){
        // invoked after jstree node is expanded
        resizeIframe();
    });
    //-=-=-=-=-=-=-=-=-=-=-=-=- CONTEXT MENU BLOCK -=-=-=-=-=-=-=-=-=-=-=-=-
    $.contextMenu({
        selector: '.campaign_tree',
        trigger: 'left',
        build: function($trigger, e){
            $('#div_jstree').jstree(true).deselect_all();
            var daMenu = {};
            var daFirstChild = $trigger.children(":first")[0];
            if(daFirstChild.innerHTML === "Campaign Tree"){
                daMenu = {
                    callback: function(key, options){

                        contextMenuActionSelected($trigger, key, options);
                        //grayOut(false);
                    },
                    items: {
                        "create_parent": {name: "Create", icon: "add"}
                    }
                };
            }
            else{
                $('#div_jstree').jstree(true).select_node($trigger);
                daMenu = {
                    callback: function(key, options){

                        contextMenuActionSelected($trigger, key, options);
                        //grayOut(false);
                    },
                    items: {
                        //"create_child": {name: "Add", icon: "add"},
                        "edit_node": {name: "Edit", icon: "edit"},
                        "entries": {name: "Entries", icon: "paste"},
                        "sep1": "---------",
                        "remove_node": {name: "Remove", icon: "delete"}
                    }
                };
            }

            return daMenu;
        }
    });
    //-=-=-=-=-=-=-=-=-=-=-=-=- Context Menu Action Block -=-=-=-=-=-=-=-=-=-=-=-=-
    function contextMenuActionSelected(daThing, key, options){
        $("#landing_page").html("");
        var dialog, form, daData, daButtons;
        var daNodeId = $(daThing).attr("id")?$(daThing).attr("id").split("_")[0]:'';
        
        var windowAction = 'default';
        
        if(key === "remove_node"){ //-=-=-=-=-=-=-=- Delete Navigation Node (set record status = 'deleted')

            daData = {daAction: key, node: daNodeId};
            var request = $.ajax({
                method: "POST",
                data: daData
            });
            request.done(function(msg){

                console.log(msg);
                top.location.reload();
            });
            request.fail(function(jqXHR, textStatus){

                alert("Request failed: " + textStatus);
            });
        }
        else{
            
            $('input').attr('size', '42');
            $('input').removeAttr('disabled');
            $('input').val('');
            $("#image").remove();
            
            if(key === "entries"){
                
                windowAction = "report_entries";
                        
                daButtons = {
                    Download: function(){
                        
                        $("#report-table").tableToCSV();
                    },
                    Cancel: function(){

                        dialog.dialog("close");
                    }
                };
            }
            else if(key === "edit_node"){

                daButtons = {
                    Update: function(){

                        $('input').removeAttr('disabled');
                        createMenuNode();
                    },
                    Cancel: function(){

                        dialog.dialog("close");
                    }
                };
                
                top.grayOut(true);
                
                //-=-=-=- get form data with ajax
                var sql = 'select * from raffle.campaign where id=' + daNodeId;
                daData = {ajax: 'SQL', statement: sql};
                var request = $.ajax({
                    method: "POST",
                    data: daData
                });
                request.done(function(msg){ //-=-=-=- set form data

                    var formData = JSON.parse(msg);
                    for(var i = 0; i < formData.length; i++){

                        var obj = formData[i];
                        for(var key in obj){

                            var attrName = key;
                            var attrValue = obj[key];

                            if(document.getElementById(attrName).type.indexOf('select') === 0){

                                for(var aOption in document.getElementById(attrName).options){

                                    if(document.getElementById(attrName).options[aOption].value === attrValue)
                                        document.getElementById(attrName).options[aOption].selected = 'true';
                                }
                            }
                            else if(document.getElementById(attrName).type.indexOf('file') === 0){
                                
                                var campaignImage = new Image();
                                campaignImage.id = "image";
                                campaignImage.width = 420;
                                campaignImage.height = 360;
                                campaignImage.src = "data:image;base64,"+attrValue;
                                
                                document.getElementById(attrName).parentNode.appendChild(campaignImage);
                            }
                            else{

                                document.getElementById(attrName).value = attrValue;
                            }
                        }
                    }
                    
                    $('#href').attr('disabled', true);
                    var daLink = top.location.href+"entry.php?campaign="+$("#href").val();
                    $("#landing_page").html(daLink);
                    $("#landing_page_link").attr("href",daLink);
                    resizeIframe();
                    top.grayOut(false);
                });
                request.fail(function(jqXHR, textStatus){
                    top.grayOut(false);
                    alert("Request failed: " + textStatus);
                    resizeIframe();
                });

                $('#node_id')[0].style = "display: show;";
            }
            else{ //-=-=-=-=- Node Creation Block

                daButtons = {
                    Create: function(){

                        $('input').removeAttr('disabled');
                        createMenuNode();
                    },
                    Cancel: function(){

                        dialog.dialog("close");
                    }
                };

                $('#node_id')[0].style = "display: none;";
                //$("#parent")[0].value = $(daThing).attr("id")?$(daThing).attr("id").split("_")[0]:'#';

            }
            
            if(windowAction != "default"){
                top.grayOut(true);
                var sql = 'select * from raffle.entry where campaign=' + daNodeId +' order by lastname, firstname';
                daData = {ajax: 'SQL', statement: sql};
                var request = $.ajax({
                    method: "POST",
                    data: daData
                });
                var daReportTable = document.getElementById("report-table");
                daReportTable.innerHTML = "";
                request.done(function(msg){ //-=-=-=- set report data
                    var formData = JSON.parse(msg);
                    for(var i = 0; i < formData.length; i++){
                        var obj = formData[i];
                        if(i == 0){
                            var newRow = daReportTable.insertRow();
                            for(var key in obj){
                                var attrName = key;
                                var newCell = newRow.insertCell();
                                var newText  = document.createTextNode(attrName);
                                newCell.appendChild(newText);
                            }
                        }
                        newRow = daReportTable.insertRow();
                        for(var key in obj){
                            var attrValue = obj[key];
                            
                            var newCell = newRow.insertCell();
                            var newText  = document.createTextNode(attrValue);
                            newCell.appendChild(newText);
                        }
                    }
                    resizeIframe();
                    top.grayOut(false);
                });
                request.fail(function(jqXHR, textStatus){

                    alert("Request failed: " + textStatus);
                    top.grayOut(false);
                });
                
                dialog = $("#dialog-report").dialog({
                    autoOpen: false,
                    dialogClass: 'rez_ui',
                    width: '580',
                    modal: true,
                    buttons: daButtons,
                    close: function(){
                        //form[ 0 ].reset();
                        //allFields.removeClass("ui-state-error");
                    }
                });
                
                dialog.dialog("open");
            }
            else{
                
                $('#id').attr('disabled', true);
                $('#status').attr('disabled', true);

                dialog = $("#dialog-form").dialog({
                    autoOpen: false,
                    dialogClass: 'rez_ui',
                    width: '580',
                    modal: true,
                    buttons: daButtons,
                    close: function(){
                        //form[ 0 ].reset();
                        //allFields.removeClass("ui-state-error");
                    }
                });

                form = dialog.find("#newMenuItemForm").on("submit", function(event){

                    event.preventDefault();
                    $('input').removeAttr('disabled');
                    createMenuNode();
                });

                dialog.dialog("open");

                resizeIframe();
            }
            
            
            function createMenuNode(){
                
                //daData = {action: key, form: $('#newMenuItemForm').serialize()};
                var daData = new FormData(document.getElementById("newMenuItemForm"));
                daData.append("daAction", key);
                
                var request = $.ajax({
                    method: "POST",
                    data: daData,
                    cache: false,
                    contentType: false,
                    processData: false
                });
                
                grayOut(true);
                
                request.done(function(msg){

                    //console.log(msg);
                    location.reload();
                });
                request.fail(function(jqXHR, textStatus){

                    alert("Request failed: " + textStatus);
                });
                dialog.dialog("close");
            }
        }
    }
</script>
<?php include 'footer_nested.php'; ?>