<?php require 'header.php'; ?>
<?php
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    if(isset($_POST['daAction'])){

        $sql = '';
        
        if(strcasecmp($_POST["daAction"], "create_raffle_entry") == 0){

            $sql = "insert into raffle.entry(campaign, firstname, lastname, email) values ('".$_POST['campaign']."','".$_POST['firstname']."', '".$_POST['lastname']."', '".$_POST['email']."')";
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
<script src="/js/jquery-ui-1.11.4/jquery-ui.min.js"></script>

<div id="campaign-form" title="Raffle entry form" style="display: none;">
    <form id='campaignEntryForm' name='campaignEntryForm' method="post">
        <input type="hidden" name="campaign" id="campaign">
        <fieldset>
            <table>
                <tr>
                    <td align='right'><label for="firstname">firstname</label></td>
                    <td><input type="text" name="firstname" id="firstname"></td>
                </tr>
                <tr>
                    <td align='right'><label for="lastname">lastname</label></td>
                    <td><input type="text" name="lastname" id="lastname"></td>
                </tr>
                <tr>
                    <td align='right'><label for="email">e-mail</label></td>
                    <td><input type="text" name="email" id="email"></td>
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

<?php
if(isset($_GET["campaign"])){
    
    $mySQL = new db_mysql();

    $sql = "select * from raffle.campaign where href='".$_GET["campaign"]."'";

    $resultset = mysqli_query($mySQL->connection, $sql);

    if(mysqli_num_rows($resultset) == 0){

        echo "<script>document.write('No raffle association for the requested campaign id: '+location.href.split('?')[1])</script>";
    }
    else{
        
        while ($row = mysqli_fetch_object($resultset)) {
            echo "<script>document.getElementById('campaign').value = '".$row->id."';</script>";
            echo '<a href="javascript: displayForm();"><img width="800" height="600" src="data:image;base64,'.$row->image_000.'"></a>';
        }
    }
    
    mysqli_free_result($resultset);
    
    $mySQL->disconnect();
}
?>

<script>
    function displayForm(){
        
        //$("#campaign-form").show();
        var dialog, daButtons, form;
        
        daButtons = {
            Enter: function(){

                $('input').removeAttr('disabled');
                submitEntryRaffleEntry();
            },
            Cancel: function(){

                dialog.dialog("close");
            }
        };

        dialog = $("#campaign-form").dialog({
            autoOpen: false,
            width: '580',
            modal: true,
            buttons: daButtons,
            close: function(){
                //form[ 0 ].reset();
                //allFields.removeClass("ui-state-error");
            }
        });
        
        form = dialog.find("#campaignEntryForm").on("submit", function(event){

            event.preventDefault();
            $('input').removeAttr('disabled');
            submitEntryRaffleEntry();
        });
        dialog.dialog("open");
        
        
        function submitEntryRaffleEntry(){

            var daData = new FormData(document.getElementById("campaignEntryForm"));
            daData.append("daAction", "create_raffle_entry");

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
                alert("Entry Accepted. Please check your email for Raffle entry confirmation.");
                top.grayOut(false);
                location.reload();
            });
            
            request.fail(function(jqXHR, textStatus){

                alert("Request failed: " + textStatus);
                top.grayOut(false);
            });
            
            dialog.dialog("close");
        }
    }
</script>
<?php include 'footer_nested.php'; ?>