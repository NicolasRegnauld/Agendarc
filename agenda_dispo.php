<?php

// Connect to our database (Step 2a)
require_once 'connexion.php';
require_once 'dispo_functions.php';



function getAgenda($year,$month,$infId){
// Create connection
$connectDetails = getConnectionDetails();
$conn = new mysqli($connectDetails[0], $connectDetails[1], $connectDetails[2], $connectDetails[3]);
$conn->query('SET NAMES utf8');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$tmpString = $year . "-" . $month . "-01";
$tmpDate = date_create($tmpString);

$res = "<div class=\"table-responsive\">" .
         "<table class=\"table table-bordered\">" .
         "<thead class=\"tableHeader\">" .
         "<tr>" .
            "<th>Lun</th>" .
            "<th>Mar</th>" .
            "<th>Mer</th>" .
            "<th>Jeu</th>" .
            "<th>Ven</th>" .
            "<th>Sam</th>" .
            "<th>Dim</th>" .
        "</tr>" .
        "</thead>" .
        "<tbody>";

$day = date_format($tmpDate, "N"); //monday 1 to Sunday 7
$res .= "<tr>";
for ($x = 1; $x < $day; $x++) {
    //loop filling in the days until the first of the month
    $res .= "<td></td>";
}
$weekDay = $day;
for ($x = 1; $x <= cal_days_in_month(CAL_GREGORIAN, $month, $year); $x++) {
    // loop covering all days of the month
    if ($x<10)
       $dateString = "\"" . $year . "-" . $month . "-0" . $x . "\"";
    else
        $dateString = "\"" . $year . "-" . $month . "-" . $x . "\"";
    $sql = "SELECT horaires, compte from ag_dispo " . 
            "WHERE date = $dateString AND infId = $infId";
    $result = $conn->query($sql);
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $val = getHoraireAbrevsFromIds($conn, $row['horaires'], $row['compte']);
    }
    else {
      $val = "";
    }
    $res .= "<td class=\"dispoCell\"><div class=\"dispoDay\">$x</div><div class=\"dispoVal\">$val</div></td>";
    if (($weekDay % 7) == 0) {
        // end of week, close the row and start new one
        $res .= "</tr><tr>";
        $weekDay = 1;
    } else {
        $weekDay ++;
    }
}


$res .= "</tr></table></div>";

$conn->close();
return $res;
}

if (isset($_POST['year'])&&
      isset($_POST['month'])&&
      isset($_POST['infId'])){
    $month = filter_input(INPUT_POST, 'month', FILTER_SANITIZE_STRING);
    $year = filter_input(INPUT_POST, 'year', FILTER_SANITIZE_STRING);
    $infId = filter_input(INPUT_POST, 'infId', FILTER_SANITIZE_NUMBER_INT);
    echo getAgenda($year,$month,$infId);
}
else 
    echo "missing arguments for getAgenda";
    
?>