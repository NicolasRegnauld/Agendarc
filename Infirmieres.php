<?php
session_start();
if(!isset($_SESSION['identifiant']))
{
   echo '<script> location.replace("login.php"); </script>';
}
?>
<html lang="fr">
    <head>
        <meta charset="utf-8"> 
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="general_styles.css">
        <script type="text/javascript" src="messagesModal.js"></script>

        <script>

        $(document).ready(function () {
            console.log("back to beginning");
            $("#topMenu").load("topMenu.html", function(){
                $("#topNav").append('<li><a href="logout.php">Déconnexion</a></li>');
            });
            
            showInfirmièresList();
            $("#filtreInf").change(function() {
                showInfirmièresList();
            });
            $("#addInfirmière").click(function () {
                    var res = "<form id=\"newInfirmièreForm\" action=\"nouvelle_infirmiere.php\" method=\"post\">" + 
                        "<div class=\"form-group\">" +
                            "<label for=\"nom\">Nom <span style='color:red'>*</span></label>" +
                            "<input name = \"nom\" type=\"text\" class=\"form-control\" id=\"nom\">" +
                        "</div>" +
                        "<div class=\"form-group\">" +
                            "<label for=\"prénom\">Prénom</label>" +
                            "<input name = \"prénom\" type=\"text\" class=\"form-control\" id=\"prénom\">" +
                        "</div>" +
                        "<div class=\"form-group\">" +
                            "<label for=\"statut\">Statut <span style='color:red'>*</span></label>" +
                            "<select class=\"form-control\" name=\"statut\" id=\"statut\">" +
                                "<option> actif</option>" +
                                "<option> inactif</option>" +
                            "</select>" +
                        "</div>" +
                        "<div class=\"form-group\">" + 
                            "<label for=\"identifiant\">Identifiant <span style='color:red'>*</span></label>" +
                            '<input type="text" name="identifiant" id="identifiant" tabindex="1" class="form-control" value="">' +
                        '</div>' +
                        '<div class="form-group">' + 
                            "<label for=\"email\">Email <span style='color:red'>*</span></label>" +
                            '<input type="email" name="email" id="email" tabindex="2" class="form-control">' +
                        "</div>" +
                        "<div class=\"form-group\">" +
                            "<label for=\"adresse\">Adresse</label>" +
                            "<input name = \"adresse\" type=\"text\" class=\"form-control\" id=\"adresse\">" +
                        "</div>" +
                        "<div class=\"form-group\">" +
                            "<label for=\"tel_fixe\">Tel. fixe</label>" +
                            "<input name = \"tel_fixe\" type=\"text\" class=\"form-control\" id=\"tel_fixe\">" +
                        "</div>" +
                        "<div class=\"form-group\">" +
                            "<label for=\"tel_fixe\">Tel. portable</label>" +
                            "<input name = \"tel_portable\" type=\"text\" class=\"form-control\" id=\"tel_portable\">" +
                        "</div>" +
                        "<div class=\"form-group\">" +
                            "<label for=\"notes\">Notes</label>" +
                            "<textarea name = \"notes\" class=\"form-control\" id=\"notes\"></textarea>" +
                        "</div>" +
                        "<button type=\"submit\" class=\"btn btn-default\">Enregistrer</button>" +
                    "</form>";
            $("#detailInf").html(res);
            $("#detailDispo").hide();
            $("#detailInf").show();
            $('#newInfirmièreForm').on('submit', function (event) {
                event.preventDefault(); // or return false, your choice
                $.post(
                    $(this).attr('action'),
                    $(this).serialize(),
                    function($resMsg) {
                        $res = JSON.parse($resMsg);             
                        if ($res["statut"] == "success"){
                            $("#detailInf").text($res["message"]);
                            showInfirmièresList();
                        }
                        else {
                            alert("Echec de création d' une infirmière: " + $res["message"]);
                        }
                    }
                    
                );
            });
        });

            $("#prevMonth").click(function () {
                $moisNum--;
                if ($moisNum == -1) {
                    $moisNum = 11;
                    $year--;
                    }
                $("#dispoMois").html($mois[$moisNum] + " " + $year);
                refreshCalendar($infId);
                });

            $("#nextMonth").click(function () {
                $moisNum++;
                if ($moisNum == 12) {
                    $moisNum = 0;
                    $year++;
                    }
                $("#dispoMois").html($mois[$moisNum] + " " + $year);
                refreshCalendar($infId);
                });
            });

        function showInfirmièresList() {
                console.log("show inf list");

                $.post('list_infirmieres.php', // location of your php script
                        {filtre: $('#filtreInf').val()},
                        function (data) {  // a function to deal with the returned information

                            $("#infirmièreRows").html(data);
                            $(".infirmière").click(function () {
                                $("td.selected").removeClass('selected');
                                $(this).parent().parent().addClass('selected');
                                $infId = $(this).children("span").html();
                                console.log("hiding detailDispo");
                                $("#detailDispo").hide();
                                $("#detailInf").show();
                                showInfirmière($infId);
                            });
                            $(".dispoInfirmière").click(function () {
                                $("td.selected").removeClass('selected');
                                $(this).parent().parent().addClass('selected');
                                $infId = $(this).siblings("span").children("span").text();
                                $("#detailInf").hide();
                                $("#detailDispo").show();
                                setInfirmièreDispo($infId);

                            });

                        });
            }

        function refreshCalendar(inputId) {
            $.post('agenda_dispo.php',
                {'infId': inputId,
                 'year': $year,
                 'month': $moisNum + 1},
                function (data) {
                    $("#dispoCalendar").html(data);
                    $(".dispoCell").click(setOneDispo);
                }
            );
        }

        function setInfirmièreDispo(inputId) {
            console.log("input to set inf dispo: " + inputId);
            $.post('agenda_dispo.php',
                {'infId': inputId,
                 'year': $year,
                 'month': $moisNum + 1},
                 function (data) {
                    $("#dispoMois").html($mois[$moisNum] + " " + $year);
                    $("#barreMois").show();
                    $("#options").show();
                    $("#dispoCalendar").html(data);
                    $(".dispoCell").click(setOneDispo);
                }
            );
        }

        function setOneDispo() {
            console.log("modif dispo");
            $day = $(this).children().filter(".dispoDay").text();
            $oldVal = $(this).children().filter(".dispoVal").text();
            $newVal = "";
            $('input[name=horaireOption]:checked').each(function(){
                if ($newVal != ""){
                    $newVal += ",";
                }
                $newVal += $(this).val();
            });
            console.log("newVal = " + $newVal);
            $.ajax({ 
                url: 'agenda_set_dispo.php',
                type: 'POST',
                data: { 
                    infId: $infId,
                    year: $year,
                    month: $moisNum + 1,
                    day: $day,
                    newVal: $newVal
                },
                datatype: 'json',
                success: function($resMsg){
                    console.log("retour setdispo");
                    console.log("msg=" + $resMsg.trim() + "done");
                    $res = JSON.parse($resMsg);             
                    if ($res["statut"] == "success"){
                        console.log("dispo updated");
                        refreshCalendar($infId);
                    }
                    else {
                        alert("Erreur " + ($res["message"]));
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log("Une erreur est survenue"); 
                    alert("Une erreur est survenue: " + thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
            console.log("after post");
        }
                
        function showInfirmière(input) {
            console.log("infirmiere selected: " + input);
            $.post('show_infirmiere.php', // location of your php script
                   {infirmièreString: input},
                   function (data) {  // a function to deal with the returned information
                        $("#detailInf").html(data); // affiche les données de l'infirmière dans le formulaire
                        $("#deleteButton").click(function () {
                            if (confirm("Etes-vous sur de vouloir effacer cette infirmière?")) {
                                $.post("delete_infirmiere.php", 
                                    {infId: $("#infirmièreId").val()}, 
                                    function ($resMsg) {
                                        $res = JSON.parse($resMsg);             
                                        if (($res["statut"] == "success") || ($res["statut"] == "abort")){
                                            $("#detailInf").text($res["message"]);
                                            showInfirmièresList();
                                        }
                                        else {
                                            alert($res["message"]);
                                        }
                                });
                             }
               
                             
                            else {
                                return false;
                            }
                        });
                        $(".infirmièreData").keyup(modifInfirmière);
                        $(".infirmièreDataSelect").change(modifInfirmière);
                    }
            );
        }
        
        function modifInfirmière() {
            $("#modifButton").removeClass("disabled");
            $("#modifButton").removeAttr("disabled");
            $("#modifInfirmièreForm").on("submit", function (event) {
                event.preventDefault(); // or return false, your choice
                $.post($(this).attr('action'),
                    $(this).serialize(),
                    function ($resMsg) {
                        $res = JSON.parse($resMsg);             
                        if ($res["statut"] == "success"){
                            $("#detailInf").text($res["message"]);
                            showInfirmièresList();
                        }
                        else {
                            alert($res["message"]);
                                                            return false;

                        }
                    }
                );
            });
        }

        $infId = null;
        $today = new Date(); 
        $year=$today.getFullYear();
        $moisNum=$today.getMonth();
        $mois= ["Janvier","Fébvrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Aout", "Septembre", "Octobre", "Novembre", "Decembre"];

        $.post('agenda_get_horaires.php',
        function (data, status) {
                if (status === 'success') {
                    console.log("options loaded: " + data);
                    $("#options").hide();
                    $("#barreMois").hide();
                    $("#options").html(data);
                } 
                else {
                    console.log("status = " + status + ", data = " + data);
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.status == 0 || jqXHR == 302) {
                    alert('Your session has ended due to inactivity after 10 minutes.\nPlease refresh this page, or close this window and log back in to system.');
                } 
                else {
                    alert('Unknown error returned while saving' + (typeof errorThrown == 'string' && errorThrown.trim().length > 0 ? ':\n' + errorThrown : ''));
                }
            });

        </script>
        <style>
            
            
            .tableHeader{
                top: 150px;
                width:100%;
                background-color: Gainsboro ;
            }
            
            th {
               text-align: center;
               vertical-align: middle;
            }
            .dispoCell{
                background-color : white;
                text-align: center;
            }
            .dispoVal{
                vertical-align: bottom;
                background-color: buttonface ;
            }
            .dispoDay {
                font-weight: bold;
            }
            
            .rightSide {
                background-color : #F1F1F1;
                border: 5px solid white;
            }
            

            .horaireCheckboxOption{
                margin-left : 20px;
                margin-right : 20px;
                margin-bottom : 15px;
                
            }
            

        </style>
    </head>
    <body>
        <div id="topMenu"></div>

        <div class="container-fluid">
            <div class="row titleBar">
                <div class="col-xs-12">
                    <h4>Gestion des infirmières et des disponibilités</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-4 leftSide">
                    <div id="infirmièresList">
                        <table class = "table">
                            <thead>
                                <tr>
                                    
                                    <th>Infirmières <button id="addInfirmière" type="button" style="float:right">+</button></th>
                                </tr>
                                <tr>
                                    <th>
                                    <select id="filtreInf">
                                            <option value="actif">actives</option>
                                            <option value="inactif">inactives</option>
                                            <option value="tous">toutes</option>
                                    </select>
                                        </th>
                                </tr>
                            </thead>
                            
                            <tbody id="infirmièreRows"></tbody>
                        </table>
                    </div>
                </div>
                <div class="rightSide col-xs-8"> 
                    <div id="detailDispo">
                        <div class = "row" id="barreMois">
                            <div class="col-xs-4 glyphicon glyphicon-circle-arrow-left" id="prevMonth" align="right" style="padding-top:4px"></div>
                            <div class="col-xs-4" align="center">
                                <p id="dispoMois"></p>
                            </div>
                            <div class="col-xs-4 glyphicon glyphicon-circle-arrow-right" id="nextMonth" align="left" style="padding-top:4px"></div>
                        </div>    
                        <div class ="row">
                            <div id="options"></div>
                        </div>
                        <div  id="dispoCalendar"></div>      
                    </div>
                    <div id="detailInf"></div>
                </div>
            </div>
        </div>
        <div id="espaceModalMessage"></div>

    </body>
</html>