<?php
function getConnectionDetails() {
    $servername = 'db762514446.hosting-data.io';
    $database = 'db762514446';
    $username = 'dbo762514446';
    $password = 'jd7Ui9(re3';
  $connectionDetails = array($servername, $username, $password, $database);
  return $connectionDetails;
}
function getPDO(){
    return new PDO('mysql:host=db762514446.hosting-data.io;dbname=db762514446', 'dbo762514446', 'jd7Ui9(re3');
}