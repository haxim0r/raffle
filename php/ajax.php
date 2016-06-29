<?php

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    
    if(isset($_POST['ajax'])){
    
        switch($_POST['ajax']){

            case 'SQL':
                
                $mySQL = new db_mysql();

                $resultset = mysqli_query($mySQL->connection, urldecode($_POST['statement']));

                if($resultset){

                    //$resultArray = mysqli_fetch_all($resultset);
                    $resultArray = array();
                    while ($row = $resultset->fetch_assoc()) {
                        
                        $resultArray[] = $row;
                    }
                    mysqli_free_result($resultset);

                    //echo json_encode($resultArray);
                    $darth_message = str_replace('"{', '{', json_encode($resultArray));
                    $darth_message = str_replace('}"', '}', $darth_message);
                    $darth_message = str_replace('\\', '', $darth_message);
                    echo $darth_message;
                }

                break;
        }

        exit;
    }
}
/*else{
    
    if(isset($_GET['ajax'])){
        
        if(isset($_GET['img'])){
            
            $link = mysql_connect("localhost", "username", "password");
            mysql_select_db("testblob");
            $sql = "SELECT image FROM testblob WHERE image_id=0";
            $result = mysql_query("$sql");
            header("Content-type: image/jpeg");
            echo mysql_result($result, 0);
            mysql_close($link);
        }
    }
}*/

class ajax{

    public $action;
    public $input;
    public $output;

    function __construct(){

    }
}

?>
