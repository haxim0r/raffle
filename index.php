<?php include 'header.php'; ?>
<script>document.body.className = "masterFrameBody";</script>
<div id='div_daHead'>
    <?php
    $topNav = new template('php/topnav.php');

    echo $topNav->html;
    ?>
</div>
<br /><br />
<div id='div_daBody'>
    <?php
    $daView = new template('php/login.php');

    echo $daView->html;
    ?>
    <iframe id='iframe_daBody' name='iframe_daBody' frameborder='0' allowtransparency='true'></iframe>
    <script>
    /*var x = document.getElementById("iframe_daBody");
    var y = (x.contentWindow.document || x.contentDocument);
    y.write("<p>Welcome</p>");
    y.close();*/
    </script>
</div>
<br />
<?php
if(isset($_GET["campaign"])){
    
    echo "<script>navigate('entry.php?campaign=".$_GET["campaign"]."')</script>";
}
?>
<?php include 'footer.php'; ?>
