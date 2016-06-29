<?php
class db_mysql{

    protected $server = 'localhost';
    protected $port = '3306';
    protected $dbname = 'raffle';
    protected $username = 'daRaffle';
    protected $password = 'darthJason';
    public $connection;

    public function __construct(){

        $this->connection = mysqli_connect($this->server, $this->username, $this->password);

        if (!$this->connection) {
            die("Connection failed: " . mysqli_connect_error());
        }
    }
    
    public function disconnect(){

        $this->connection->close();
    }
}

class template{

    public $html;

    function __construct($daTemplate){

        $path_parts = pathinfo($_SERVER['DOCUMENT_ROOT']);
        $x = $path_parts['dirname'];

        if(substr_startswith($daTemplate, 'html/')){

            //$filename = realpath($x . "/template/" . $daTemplate);
            //$filename = $x . "/template/" . $daTemplate;
            $filename = "template/".$daTemplate;

            $handle = fopen($filename, "r");

            $this->html = fread($handle, filesize($filename));

            fclose($handle);
        }
        elseif(substr_startswith($daTemplate, 'php/')){

            //include realpath($x . "/template/" . $daTemplate);
            //include $x . "/template/" . $daTemplate;
            include "template/".$daTemplate;
        }
    }
}

function substr_startswith($haystack, $needle){

    return substr($haystack, 0, strlen($needle)) === $needle;
}
?>
