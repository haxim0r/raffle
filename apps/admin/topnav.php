<?php include '../../header.php'; ?>
<?php
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $sql = '';

    if(isset($_POST['form'])){

        $daPOST = Array();

        foreach(explode("&", $_POST['form']) as $kvp){

            $arrayKVP = explode("=", $kvp);
            $daPOST[$arrayKVP[0]] = urldecode($arrayKVP[1]);
        }
    }

    if(isset($_POST["action"]) && ((strcasecmp($_POST["action"], "create_parent") == 0) || (strcasecmp($_POST["action"], "create_child") == 0 ))){

        $sql = 'insert into d2.site_topnav(text, href, title, sort, parent)values (\''.$daPOST['text'].'\', \''.$daPOST['href'].'\', \''.$daPOST['title'].'\', \''.$daPOST['sort'].'\', \''.$daPOST['parent'].'\')';
    }
    elseif(isset($_POST["action"]) && (strcasecmp($_POST["action"], "remove_node") == 0 )){

        $sql = 'update d2.site_topnav set status=\'deleted\' where id='.$_POST["node"];
    }
    elseif(isset($_POST["action"]) && (strcasecmp($_POST["action"], "edit_node") == 0 )){

        $sql = 'update d2.site_topnav set text=\''.$daPOST['text'].'\', href=\''.$daPOST['href'].'\', title=\''.$daPOST['title'].'\', sort=\''.$daPOST['sort'].'\', parent=\''.$daPOST['parent'].'\', status=\'updated\' where id='.$daPOST['id'];
    }

    if(strlen($sql) > 0){

        $pg = new db_pg();

        pg_query($pg->connection, $sql);

        $pg->disconnect();

        exit;
    }
}
?>
<link rel="stylesheet" href="/css/jstree/style.min.css" />
<link rel="stylesheet" href="/css/contextMenu/jquery.contextMenu.css" />
<link rel="stylesheet" href="/css/jquery_ui/jquery-ui.min.css" />

<script src="/js/jquery-1.11.2.min.js"></script>
<script src="/js/jstree.js"></script>
<script src="/js/jquery.contextMenu.js"></script>
<script src="/js/jquery-ui-1.11.4/jquery-ui.min.js"></script>

<span class='navigation_node'><b>Top Navigation Menu</b></span>
<div id="div_jstree"></div>
<div id="dialog-form" title="Site Navigation Node" style="display: none;">
    <form id='newMenuItemForm'>
        <fieldset>
            <table>
                <tr>
                    <td align='right'><label for="parent">parent</label></td>
                    <td><input type="text" name="parent" id="parent" disabled></td>
                </tr>
                <tr id='node_id' style="display: none;">
                    <td align='right'><label for="id">id</label></td>
                    <td><input type="text" name="id" id="id" disabled></td>
                </tr>
                <tr>
                    <td align='right'><label for="text">text</label></td>
                    <td><input type="text" name="text" id="text"></td>
                </tr>
                <tr>
                    <td align='right'><label for="href">href</label></td>
                    <td><input type="text" name="href" id="href"></td>
                </tr>
                <tr>
                    <td align='right'><label for="title">title</label></td>
                    <td><input type="text" name="title" id="title"></td>
                </tr>
                <tr>
                    <td align='right'><label for="sort">sort</label></td>
                    <td><input type="text" name="sort" id="sort"></td>
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
        </fieldset>
    </form>
</div>

<script>
<?php
$pg = new db_pg();

$sql = 'select id, parent, text, \'{"class":"navigation_node"}\' a_attr from d2.site_topnav order by parent, sort';

$resultset = pg_query($pg->connection, $sql);

if($resultset){

    $resultArray = pg_fetch_all($resultset);
    pg_free_result($resultset);
    $darth_message = str_replace('"{', '{', json_encode($resultArray));
    $darth_message = str_replace('}"', '}', $darth_message);
    $darth_message = str_replace('\\', '', $darth_message);

    echo "$('#div_jstree').jstree({ 'core' : { 'data' : ".$darth_message." } });";
}

$pg->disconnect();
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
        selector: '.navigation_node',
        trigger: 'left',
        build: function($trigger, e){
            $('#div_jstree').jstree(true).deselect_all();
            var daMenu = {};
            var daFirstChild = $trigger.children(":first")[0];
            if(daFirstChild.innerHTML === "Top Navigation Menu"){
                daMenu = {
                    callback: function(key, options){

                        contextMenuActionSelected($trigger, key, options);
                    },
                    items: {
                        "create_parent": {name: "Add", icon: "add"}
                    }
                };
            }
            else{
                $('#div_jstree').jstree(true).select_node($trigger);
                daMenu = {
                    callback: function(key, options){

                        contextMenuActionSelected($trigger, key, options);
                    },
                    items: {
                        "create_child": {name: "Add", icon: "add"},
                        "edit_node": {name: "Edit", icon: "edit"},
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

        var dialog, form, daData, daButtons;
        var daNodeId = $(daThing).attr("id")?$(daThing).attr("id").split("_")[0]:'';
        if(key === "remove_node"){ //-=-=-=-=-=-=-=- Delete Navigation Node (set record status = 'deleted')

            daData = {action: key, node: daNodeId};
            var request = $.ajax({
                method: "POST",
                data: daData
            });
            request.done(function(msg){

                //console.log(msg);
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
            if(key === "edit_node"){

                daButtons = {
                    Update: function(){

                        $('input').removeAttr('disabled');
                        createMenuNode();
                    },
                    Cancel: function(){

                        dialog.dialog("close");
                    }
                };
                //-=-=-=- get form data with ajax
                var sql = 'select * from d2.site_topnav where id=' + daNodeId;
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
                            else{

                                document.getElementById(attrName).value = attrValue;
                            }
                        }
                    }
                });
                request.fail(function(jqXHR, textStatus){

                    alert("Request failed: " + textStatus);
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
                $("#parent")[0].value = $(daThing).attr("id")?$(daThing).attr("id").split("_")[0]:'#';

            }

            $('#parent').attr('disabled', true);
            $('#id').attr('disabled', true);
            $('#status').attr('disabled', true);

            function createMenuNode(){

                daData = {action: key, form: $('#newMenuItemForm').serialize()};
                var request = $.ajax({
                    method: "POST",
                    data: daData
                });
                request.done(function(msg){

                    //console.log(msg);
                    top.location.reload();
                });
                request.fail(function(jqXHR, textStatus){

                    alert("Request failed: " + textStatus);
                });
                dialog.dialog("close");
            }

            dialog = $("#dialog-form").dialog({
                autoOpen: false,
                width: '540',
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
    }
</script>

<?php include '../../footer_nested.php'; ?>