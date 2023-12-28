/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$visiteTypeOptions = "";
$.post('list_visite_types.php',
    {'action': "just setting something"},
        function (data) {  // a function to deal with the returned information
            $visiteTypeOptions = (data);
         }
    );


$visiteStatutOptions = "";
$.post('list_statuts.php',
        function (data) {  // a function to deal with the returned information
            $visiteStatutOptions = (data);
         }
    );

$infirmièreOptions = "";
$.post('list_nom_infirmieres.php',
        function (data) {  // a function to deal with the returned information
            $infirmièreOptions = (data);
         }
    );

$clientOptions = "";
$.post('list_nom_clients.php',
        {'action': "just setting something"},
        function (data) {  // a function to deal with the returned information
            $clientOptions = (data);
         }
    );
    
    



         
            
function getNomInfirmières(selectedVal) {
    $.post('list_nom_infirmieres.php',
        {'preselect': selectedVal},
        function (data) {  
            $("#infirmière").html(data);
            }
        );
    }
    
function getNomInfirmièresP(selectedVal) {
    $.post('list_nom_infirmieres.php',
        {'preselect': selectedVal},
        function (data) {  
            $("#infirmièreP").html(data);
            }
        );
    }

function getStatuts(selVal) {
    console.log("default status: " + selVal);
    $.post('list_statuts.php',
        {'selected': selVal},
        function (data) { 
            $("#modalStatutOptions").html(data);
            console.log("list statuts: " + data);
            }
        );
    }
    
function getStatutsP(selVal) {
    console.log("default status: " + selVal);
    $.post('list_statuts.php',
        {'selected': selVal},
        function (data) { 
            $("#modalStatutOptionsP").html(data);
            console.log("list statuts: " + data);
            }
        );
    }
    
function getVisiteTypeOptions(selectedVal) {
    $.post('list_visite_types.php',
        {'preselect': selectedVal},
        function (data) {
            $("#modalTypeOptions").html(data);
        }
    );
}

function getVisiteTypeOptionsP(selectedVal) {
    $.post('list_visite_types.php',
        {'preselect': selectedVal},
        function (data) {
            $("#modalTypeOptionsP").html(data);
        }
    );
}
        
function getParentVisiteModalForm(){
    console.log("enter get modal");
    $adresse = "";
    var res =   "<input type=\"hidden\" id=\"modalClientIdP\">" +
                "<div class=\"modal fade\" id=\"parentVisiteModalModif\" role=\"dialog\">" +
                    "<div class=\"modal-dialog\">" +
                        //Modal content
                        "<div class=\"modal-content\">" +
                            "<div class=\"modal-header\">" +
                                "<h4 style=\"color:red;\">Détail de la visite" +
                                "<span><button type= \"submit\"  class=\"btn btn-warning pull-right\" data-dismiss=\"modal\"><span class=\"glyphicon glyphicon-remove\"></span> Annuler</button></span></h4>" +
                            "</div>" +
                            "<div class=\"modal-body\">" +
                                "<input type=\"hidden\" id=\"modalVisiteIdP\">" +
                                "<form role=\"form\">" + 
                                    "<div class=\"form-group\">" +
                                        "<label for=\"modalTitre\">Titre</label>" +
                                        "<input type=\"text\" maxlength=\"25\" class=\"form-control\" name=\"modalTitre\" id=\"modalTitreP\">" +
                                    "</div>" +
                                    "<div class=\"form-group\">" +
                                        "<label for=\"modalType\">Type de visite</label>" +
                                        "<select name = \"modalType\" id = \"modalTypeOptionsP\">" +
                                        "</select>" +
                                    "</div>" +
                                    "<div class=\"form-group\">" +
                                        "<label for=\"modalNotes\">Objet de la visite:</label>" +
                                        "<textarea name=\"modalNotes\" class=\"form-control\" id=\"modalNotesP\"></textArea>" +
                                    "</div>" +
                                    "<div class=\"form-group\">" +
                                        "<label for=\"infirmière\">Infirmière</label>" +
                                        "<select class=\"form-control\" name=\"infirmière\" id=\"infirmièreP\">" +
                                        "</select>" +
                                    "</div>" +
                                    "<div class=\"form-group\">" +
                                        "<label for=\"statut_options\">Statut de la visite</label>" +
                                        "<select class=\"form-control\" name=\"statut_options\" id=\"modalStatutOptionsP\">" +
                                        "</select>" +
                                    "</div>" +
                                    "<div class=\"form-group\">" +
                                        "<label for=\"modalAdresse\">Adresse:</label>" +
                                        "<textarea name=\"modalAdresse\" class=\"form-control\" id=\"modalAdresseP\"></textArea>" +
                                    "</div>" +
                                    "<div class=\"form-group\">" + 
                                        "<div class=\"form-group col-sm-4\">" + 
                                            "<label for=\"modalDate\">Date de début de modification</label><br>" +
                                            "<input id=\"modalDateDébutP\" type=date name=\"modalDate\">" +
                                        "</div>" +
                                        "<div class=\"form-group col-sm-4\">" + 
                                            "<label for=\"modalHeure\">Heure</label>" +
                                            "<select class=\"form-control\" name=\"modalHeure\" id=\"modalHeureP\">" +
                                            "</select>" +
                                        "</div>" +
                                        "<div class=\"form-group col-sm-4\">" +
                                            "<label for=\"modalMinutes\">Minutes:</label>" +
                                            "<select class=\"form-control\" name=\"formMinutes\" id=\"modalMinutesP\">" + 
                                            "</select>" +
                                        "</div>" + 

                                    "</div>" +
                                    "<div class=\"form-group\">" +
                                        "<label for=\"modalDurée\">Durée</label>" +
                                        "<input type=\"text\" class=\"form-control\" name=\"modalDurée\" id=\"modalDuréeP\">" +   
                                    "</div>" +
 
                                    "<div>" +
                                        "<div data-toggle=\"tooltip\" title=\"Laisser blanc pour seulement modifier les visites existantes, sinon toutes les visites entre la dete de début et de fin seront détruites et remplacées par de nouvelles en utilisant cette fréquence!\" class=\"form-group row\">" +
                                            "<div class=\"form-check-label rc-radio col-sm-4\">" +
                                                "<label><input type=\"radio\" id=\"RC-singleToggleP\" onclick=\"rcSelectUniqueP()\" name = \"rc-option\" value=\"\" checked=\"checked\"><strong>Visite unique</strong></label>" +
                                            "</div>" +
                                            "<div class=\"form-check-label rc-radio col-sm-4\">" +
                                                "<label><input type=\"radio\" id=\"RC-regulierToggleP\" onclick=\"rcSelectRegulierP()\" name = \"rc-option\" value=\"\"><strong>Intervalle régulier</strong></label>" +
                                            "</div>" +
                                            "<div class=\"form-check-label rc-radio col-sm-4\">" +
                                                "<label><input type=\"radio\" id=\"RC-fixeToggleP\" onclick=\"rcSelectFixeP()\" name = \"rc-option\" value=\"\"><strong>Jours fixes</strong></label>" +
                                            "</div>" +
                                            
                                        "</div>" +    
                                        
                                        "<div class=\"content-sub-box\" id=\"rcDetailP\">" +

                                        "<div id=\"rcFormRegulierP\" hidden>" +
                                            "<div class=\"form-group \">" +
                                                "<label  for=\"recP\">Fréquence (tous les x jours):</label>" +
                                                "<input name = \"recP\" type=\"text\" class=\"missionData twoDigits\" id=\"modalRecP\" value = \"2\">" +
                                            "</div>" +
                                        "</div>" +
                                        "<div class=\"checkbox-label-vertical-wrapper\" id=\"rcFormFixeP\" hidden>" +
                                            "<label class=\"checkbox-label-vertical\"><input type=\"checkbox\" class=\"jour\" value=\"1\"><strong>Lu</strong></label>" +
                                            "<label class=\"checkbox-label-vertical\"><input type=\"checkbox\" class=\"jour\" value=\"2\"><strong>Ma</strong></label>" +
                                            "<label class=\"checkbox-label-vertical\"><input type=\"checkbox\" class=\"jour\" value=\"3\"><strong>Me</strong></label>" +
                                            "<label class=\"checkbox-label-vertical\"><input type=\"checkbox\" class=\"jour\" value=\"4\"><strong>Je</strong></label>" +
                                            "<label class=\"checkbox-label-vertical\"><input type=\"checkbox\" class=\"jour\" value=\"5\"><strong>Ve</strong></label>" +
                                            "<label class=\"checkbox-label-vertical\"><input type=\"checkbox\" class=\"jour\" value=\"6\"><strong>Sa</strong></label>" +
                                            "<label class=\"checkbox-label-vertical\"><input type=\"checkbox\" class=\"jour\" value=\"7\"><strong>Di</strong></label>" +
                                        "</div>" +
                                        "<div id=\"rcDateFinP\" class=\"form-group \" hidden>" +
                                            "<label  for=\"dateFinP\">Date de fin:</label>" +
                                            "<input id=\"modalDateFinP\" type=date name=\"dateFinP\" class=\"missionData \">" +
                                        "</div>" +
                                        "</div>" +    

                                    "</div>" +
                                    "<div class=\"form-group\">" +
                                        "<label for=\"modalAlerte\">Mettre une alerte pour la visite?</label>" +
                                            "<select name = \"modalAlerte\" id = \"modalAlerteP\">" + 
                                                "<option value = \"1\">Active</option>" +
                                                "<option value = \"2\">Résolue</option>" +
                                                "<option value = \"0\" selected>Non</option>" +
                                            "</select>" +
                                    "</div>" +
                                    "<div class=\"form-group\">" + 
                                        "<label for=\"modalAlerteMessage\">Message d'alerte</label>" + 
                                        "<textarea name = \"modalAlerteMessage\" class=\"form-control\" id=\"modalAlerteMessageP\"></textarea>" +
                                    "</div>" +
                                    "<div class=\"form-group\">" +
                                        "<label for=\"modalRapport\">Rapport de visite:</label>" +
                                        "<textarea id=\"modalRapportP\" class=\"form-control\" name=\"modalRapport\"></textArea>" + 
                                    "</div>" + 
                                "</form>" +
                            "</div>" +
                            "<div class=\"modal-footer\">" +
                                "<button type= \"submit\" onclick = \"saveRdvParent()\" class=\"btn btn-success pull-left\" data-dismiss=\"modal\"><span class=\"glyphicon glyphicon-off\"></span> Valider</button>" +
                                "<button type= \"submit\" onclick = \"deleteRdvParent()\" class=\"btn btn-danger pull-right\" data-dismiss=\"modal\"><span class=\"glyphicon glyphicon-trash\"></span> Supprimer</button>" +                  
                            "</div>" +
                        "</div>" +
                    "</div>" +
                "</div>";
        console.log("return with res =  "+ res);
    return res;    
}

function getModifVisiteModalForm(){
    console.log("enter get modal");
    $adresse = "";
    var res =   "<input type=\"hidden\" id=\"modalParent\">" +
                "<input type=\"hidden\" id=\"modalClientId\">" +
                "<div class=\"modal fade\" id=\"visiteModalModif\" role=\"dialog\">" +
                    "<div class=\"modal-dialog\">" +
                        //Modal content
                        "<div class=\"modal-content\">" +
                            "<div class=\"modal-header\">" +
                                "<h4 style=\"color:red;\">Détail de la visite" +
                                "<span><button type= \"submit\"  class=\"btn btn-warning pull-right\" data-dismiss=\"modal\"><span class=\"glyphicon glyphicon-remove\"></span> Annuler</button></span></h4>" +
                            "</div>" +
                            "<div class=\"modal-body\">" +
                                "<input type=\"hidden\" id=\"modalVisiteId\">" +
                                "<form role=\"form\">" + 
                                    "<div class=\"form-group\">" +
                                        "<label for=\"modalTitre\">Titre</label>" +
                                        "<input type=\"text\" maxlength=\"25\" class=\"form-control\" name=\"modalTitre\" id=\"modalTitre\">" +
                                    "</div>" +
                                    "<div class=\"form-group\">" +
                                        "<label for=\"modalType\">Type de visite</label>" +
                                        "<select name = \"modalType\" id = \"modalTypeOptions\">" +
                                        "</select>" +
                                    "</div>" +
                                    "<div class=\"form-group\">" +
                                        "<label for=\"modalNotes\">Objet de la visite:</label>" +
                                        "<textarea name=\"modalNotes\" class=\"form-control\" id=\"modalNotes\"></textArea>" +
                                    "</div>" +
                                    "<div class=\"form-group\">" +
                                        "<label for=\"infirmière\">Infirmière</label>" +
                                        "<select class=\"form-control\" name=\"infirmière\" id=\"infirmière\">" +
                                        "</select>" +
                                    "</div>" +
                                    "<div class=\"form-group\">" +
                                        "<label for=\"statut_options\">Statut de la visite</label>" +
                                        "<select class=\"form-control\" name=\"statut_options\" id=\"modalStatutOptions\">" +
                                        "</select>" +
                                    "</div>" +
                                    "<div class=\"form-group\">" +
                                        "<label for=\"modalAdresse\">Adresse:</label>" +
                                        "<textarea name=\"modalAdresse\" class=\"form-control\" id=\"modalAdresse\"></textArea>" +
                                    "</div>" +
                                    "<div class=\"form-group\">" + 
                                        "<div class=\"form-group col-sm-4\">" + 
                                            "<label for=\"modalDate\">Date</label><br>" +
                                            "<input id=\"modalDateDébut\" type=date name=\"modalDate\">" +
                                        "</div>" +
                                        "<div class=\"form-group col-sm-4\">" + 
                                            "<label for=\"modalHeure\">Heure</label>" +
                                            "<select class=\"form-control\" name=\"modalHeure\" id=\"modalHeure\">" +
                                            "</select>" +
                                        "</div>" +
                                        "<div class=\"form-group col-sm-4\">" +
                                            "<label for=\"modalMinutes\">Minutes:</label>" +
                                            "<select class=\"form-control\" name=\"formMinutes\" id=\"modalMinutes\">" + 
                                            "</select>" +
                                        "</div>" + 

                                    "</div>" +
                                    "<div class=\"form-group\">" +
                                        "<label for=\"modalDurée\">Durée</label>" +
                                        "<input type=\"text\" class=\"form-control\" name=\"modalDurée\" id=\"modalDurée\">" +   
                                    "</div>" +
                                    "<div class=\"form-group\">" +
                                        "<label for=\"modalAlerte\">Mettre une alerte pour la visite?</label>" +
                                            "<select name = \"modalAlerte\" id = \"modalAlerte\">" + 
                                                "<option value = \"1\">Active</option>" +
                                                "<option value = \"2\">Résolue</option>" +
                                                "<option value = \"0\" selected>Non</option>" +
                                            "</select>" +
                                    "</div>" +
                                    "<div class=\"form-group\">" + 
                                        "<label for=\"modalAlerteMessage\">Message d'alerte</label>" + 
                                        "<textarea name = \"modalAlerteMessage\" class=\"form-control\" id=\"modalAlerteMessage\"></textarea>" +
                                    "</div>" +
                                    "<div class=\"form-group\">" +
                                        "<label for=\"modalRapport\">Rapport de visite:</label>" +
                                        "<textarea id=\"modalRapport\" class=\"form-control\" name=\"modalRapport\"></textArea>" + 
                                    "</div>" + 
                                "</form>" +
                            "</div>" +
                            "<div class=\"modal-footer\">" +
                                "<button type= \"submit\" onclick = \"saveRdv()\" class=\"btn btn-success pull-left\" data-dismiss=\"modal\"><span class=\"glyphicon glyphicon-off\"></span> Valider</button>" +
                                "<button type= \"submit\" onclick = \"deleteRdv()\" class=\"btn btn-danger pull-right\" data-dismiss=\"modal\"><span class=\"glyphicon glyphicon-trash\"></span> Supprimer</button>" +                  
                            "</div>" +
                        "</div>" +
                    "</div>" +
                "</div>";
        console.log("return with res =  "+ res);
    return res;    
}
function getStatutModalForm(){
    var $res =   
        "<div class=\"modal fade\" id=\"statutModal\" role=\"dialog\">" +
            "<div class=\"modal-dialog modal-sm\">" +
            //Modal content
                "<div class=\"modal-content\">" +
                    "<div class=\"modal-header\">" +
                        "<h4>Choix du statut de la visite" +
                    "</div>" +
                    "<div class=\"modal-body\">" +
                        "<input type=\"hidden\" id=\"statutModalVisiteId\">" +
                        "<div class='radio'>" +
                        "<label><input type='radio' class='statutChange' name='optradio' value = '1' checked>Planifiée</label>" +
                        "</div>" + 
                        "<div class='radio'>" +
                        "<label><input type='radio' class='statutChange' name='optradio' value = '3' >Terminée</label>" +
                        "</div>" + 
                        "<div class='radio'>" +
                        "<label><input type='radio' class='statutChange' name='optradio' value = '4' >Annulée</label>" +
                        "</div>" + 
                    "</div>" +
                "</div>" +
            "</div>" +
        "</div>";

    return $res;
}

function getNewVisiteModalForm(){
    console.log("enter get modal");
    $adresse = "";
    var res =   "<input type=\"hidden\" id=\"newModalParent\">" +
                "<div class=\"modal fade\" id=\"visiteModalNew\" role=\"dialog\">" +
                    "<div class=\"modal-dialog\">" +
                        //Modal content
                        "<div class=\"modal-content\">" +
                            "<div class=\"modal-header\">" +
                                "<h4 style=\"color:red;\">Détail de la visite" +
                                "<span><button type= \"submit\"  class=\"btn btn-warning pull-right\" data-dismiss=\"modal\"><span class=\"glyphicon glyphicon-remove\"></span> Annuler</button></span></h4>" +
                            "</div>" +
                            "<div class=\"modal-body\">" +
                                "<input type=\"hidden\" id=\"newModalVisiteId\">" +
                                "<form role=\"form\">" + 
                                    
                                    "<div id = \"clientSection\" class=\"form-group\">" +
                                        "<div class=\"checkbox\">" +
                                            "<label><input type=\"checkbox\" id=\"newClientToggle\" onclick=\"toggleClient(this)\" value=\"\"><strong>Nouveau Client</strong></label>" +
                                        "</div>" +
                                        "<div id=\"oldClientForm\" class=\"form-group\">" +
                                            "<label for=\"client\">Client</label>" +
                                            "<select class=\"form-control\" name=\"client\" id=\"newModalClient\">" +
                                            "</select>" +
                                        "</div>" +
                                        "<div class=\"row\" id=\"newClientForm\" hidden>" +
                                            "<div class=\"form-group col-sm-6\">" +
                                                "<label for=\"clNom\">Nom:</label>" +
                                                "<input name = \"clNom\" type=\"text\" class=\"form-control missionData\" id=\"clNom\" value = ''>" +
                                            "</div>" +
                                            "<div class=\"form-group col-sm-6\">" +
                                                "<label for=\"clPrenom\">Prénom:</label>" +
                                                "<input id=\"clPrenom\" type=\"text\" name=\"clPrenom\" class=\"form-control missionData\">" +
                                            "</div>" +
                                        "</div>" +
                                        "<div class=\"row\" id=\"newClientForm2\" hidden>" +
                                            "<div class=\"form-group col-sm-8\">" +
                                                "<label for=\"clAdr\">Adresse:</label>" +
                                                "<input name = \"clAdr\" type=\"text\" class=\"form-control missionData\" id=\"clAdr\" value = ''>" +
                                            "</div>" +
                                            "<div class=\"form-group col-sm-4\">" +
                                                "<label for=\"clTel\">Tel:</label>" +
                                                "<input id=\"clTel\" type=\"text\" name=\"clTel\" class=\"form-control missionData\">" +
                                            "</div>" +
                                        "</div>" +
                                    "</div>" +
                                    "<div class=\"form-group\">" +
                                        "<label for=\"modalTitre\">Titre</label>" +
                                        "<input type=\"text\" maxlength=\"25\" class=\"form-control\" name=\"modalTitre\" id=\"newModalTitre\">" +
                                    "</div>" +
                                    "<div class=\"form-group\">" +
                                        "<label for=\"modalType\">Type de visite</label>" +
                                        "<select name = \"modalType\" id=\"newModalType\">" +
                                        "</select>" +
                                    "</div>" +
                                    "<div class=\"form-group\">" +
                                        "<label for=\"modalNotes\">Objet de la visite:</label>" +
                                        "<textarea name=\"modalNotes\" class=\"form-control\" id=\"newModalNotes\"></textArea>" +
                                    "</div>" +
                                    "<div class=\"form-group\">" +
                                        "<label for=\"infirmière\">Infirmière</label>" +
                                        "<select class=\"form-control\" name=\"infirmière\" id=\"newModalInfirmière\">" +
                                        "</select>" +
                                    "</div>" +
                                    "<div class=\"form-group\">" +
                                        "<label for=\"statut_options\">Statut de la visite</label>" +
                                        "<select class=\"form-control\" name=\"statut_options\" id=\"newModalStatutOptions\">" +
                                        "</select>" +
                                    "</div>" +
                                    "<div class=\"form-group\">" +
                                        "<label for=\"modalAdresse\">Adresse:</label>" +
                                        "<textarea name=\"modalAdresse\" class=\"form-control\" id=\"newModalAdresse\"></textArea>" +
                                    "</div>" +
                                    "<div class=\"form-group\">" + 
                                        "<div class=\"form-group col-sm-4\">" + 
                                            "<label for=\"modalDate\">Date</label><br>" +
                                            "<input id=\"newModalDateDébut\" type=date name=\"modalDate\">" +
                                        "</div>" +
                                        "<div class=\"form-group col-sm-4\">" + 
                                            "<label for=\"modalHeure\">Heure</label>" +
                                            "<select class=\"form-control\" name=\"modalHeure\" id=\"newModalHeure\">" +
                                            "</select>" +
                                        "</div>" +
                                        "<div class=\"form-group col-sm-4\">" +
                                            "<label for=\"forlMinutes\">Minutes:</label>" +
                                            "<select class=\"form-control\" name=\"formMinutes\" id=\"newModalMinutes\">" + 
                                            "</select>" +
                                        "</div>" + 
                                    "</div>" +
                                    "<div class=\"form-group\">" +
                                        "<label for=\"modalDurée\">Durée</label>" +
                                        "<input type=\"text\" class=\"twoDigits\" name=\"modalDurée\" id=\"newModalDurée\" value = \"30\"> minutes" +   
                                    "</div>" +
                                    "<div>" +
                                        "<div class=\"form-group row\">" +
                                            "<div class=\"form-check-label rc-radio col-sm-4\">" +
                                                "<label><input type=\"radio\" id=\"RC-singleToggle\" onclick=\"rcSelectUnique()\" name = \"rc-option\" value=\"\" checked=\"checked\"><strong>Visite unique</strong></label>" +
                                            "</div>" +
                                            "<div class=\"form-check-label rc-radio col-sm-4\">" +
                                                "<label><input type=\"radio\" id=\"RC-regulierToggle\" onclick=\"rcSelectRegulier()\" name = \"rc-option\" value=\"\"><strong>Intervalle régulier</strong></label>" +
                                            "</div>" +
                                            "<div class=\"form-check-label rc-radio col-sm-4\">" +
                                                "<label><input type=\"radio\" id=\"RC-fixeToggle\" onclick=\"rcSelectFixe()\" name = \"rc-option\" value=\"\"><strong>Jours fixes</strong></label>" +
                                            "</div>" +
                                            
                                        "</div>" +    
                                        
                                        "<div class=\"content-sub-box\" id=\"rcDetail\">" +

                                        "<div id=\"rcFormRegulier\" hidden>" +
                                            "<div class=\"form-group \">" +
                                                "<label  for=\"rec\">Fréquence (tous les x jours):</label>" +
                                                "<input name = \"rec\" type=\"text\" class=\"missionData twoDigits\" id=\"newModalRec\" value = \"2\">" +
                                            "</div>" +
                                        "</div>" +
                                        "<div class=\"checkbox-label-vertical-wrapper\" id=\"rcFormFixe\" hidden>" +
                                            "<label class=\"checkbox-label-vertical\"><input type=\"checkbox\" class=\"jour\" value=\"1\"><strong>Lu</strong></label>" +
                                            "<label class=\"checkbox-label-vertical\"><input type=\"checkbox\" class=\"jour\" value=\"2\"><strong>Ma</strong></label>" +
                                            "<label class=\"checkbox-label-vertical\"><input type=\"checkbox\" class=\"jour\" value=\"3\"><strong>Me</strong></label>" +
                                            "<label class=\"checkbox-label-vertical\"><input type=\"checkbox\" class=\"jour\" value=\"4\"><strong>Je</strong></label>" +
                                            "<label class=\"checkbox-label-vertical\"><input type=\"checkbox\" class=\"jour\" value=\"5\"><strong>Ve</strong></label>" +
                                            "<label class=\"checkbox-label-vertical\"><input type=\"checkbox\" class=\"jour\" value=\"6\"><strong>Sa</strong></label>" +
                                            "<label class=\"checkbox-label-vertical\"><input type=\"checkbox\" class=\"jour\" value=\"7\"><strong>Di</strong></label>" +
                                        "</div>" +
                                        "<div id=\"rcDateFin\" class=\"form-group \" hidden>" +
                                            "<label  for=\"dateFin\">Date de fin:</label>" +
                                            "<input id=\"newModalDateFin\" type=date name=\"dateFin\" class=\"missionData \">" +
                                        "</div>" +
                                        "</div>" +    

                                    "</div>" +
                                    "<div class=\"form-group\">" +
                                        "<label for=\"modalAlerte\">Mettre une alerte pour la visite?</label>" +
                                            "<select name = \"modalAlerte\" id=\"newModalAlerte\">" + 
                                                "<option value = \"1\">Active</option>" +
                                                "<option value = \"2\">Résolue</option>" +
                                                "<option value = \"0\" selected>Non</option>" +
                                            "</select>" +
                                    "</div>" +
                                    "<div class=\"form-group\">" + 
                                        "<label for=\"modalAlerteMessage\">Message d'alerte</label>" + 
                                        "<textarea name = \"modalAlerteMessage\" class=\"form-control\" id=\"newModalAlerteMessage\"></textarea>" +
                                    "</div>" +
                                "</form>" +
                            "</div>" +
                            "<div class=\"modal-footer\">" +
                                "<button type= \"submit\" onclick = \"saveNewRdv()\" class=\"btn btn-success pull-left\"><span class=\"glyphicon glyphicon-off\"></span> Valider</button>" +
                             "</div>" +
                        "</div>" +
                    "</div>" +
                "</div>";
        console.log("return with res =  "+ res);
    return res;    
}

function toggleRépétition(checkBox){
    if (checkBox.checked)
        $("#répétitionForm").show();
    else
        $("#répétitionForm").hide();
}

function toggleClient(checkBox){
    if (checkBox.checked){
        $("#oldClientForm").hide();
        $("#newClientForm").show();
        $("#newClientForm2").show();
    }
    else{
        $("#oldClientForm").show();
        $("#newClientForm").hide();
        $("#newClientForm2").hide();
    }
}

function rcSelectUnique(){
    $("#rcFormFixe").hide();
    $("#rcFormRegulier").hide();
    $("#rcDateFin").hide();
    $("#rcDetail").hide();
    $("#RC-singleToggle").parent().css("background-color", "#f1f1f1");
    $("#RC-regulierToggle").parent().css("background-color", "inherit");
    $("#RC-fixeToggle").parent().css("background-color", "inherit");
}
function rcSelectRegulier(){
    $("#rcFormFixe").hide();
    $("#rcDetail").show();
    $("#rcFormRegulier").show();
    $("#rcDateFin").show();
    $("#RC-regulierToggle").parent().css("background-color", "#f1f1f1");
    $("#RC-fixeToggle").parent().css("background-color", "inherit");
    $("#RC-singleToggle").parent().css("background-color", "inherit");
}

function rcSelectFixe(){
    $("#rcFormFixe").show();
    $("#rcFormRegulier").hide();
    $("#rcDateFin").show();
    $("#rcDetail").show();
    $("#RC-fixeToggle").parent().css("background-color", "#f1f1f1");
    $("#RC-regulierToggle").parent().css("background-color", "inherit");
    $("#RC-singleToggle").parent().css("background-color", "inherit");
}

function rcSelectUniqueP(){
    $("#rcFormFixeP").hide();
    $("#rcFormRegulierP").hide();
    $("#rcDateFinP").hide();
    $("#rcDetailP").hide();
    $("#RC-singleToggleP").parent().css("background-color", "#f1f1f1");
    $("#RC-regulierToggleP").parent().css("background-color", "inherit");
    $("#RC-fixeToggleP").parent().css("background-color", "inherit");
}
function rcSelectRegulierP(){
    $("#rcFormFixeP").hide();
    $("#rcDetailP").show();
    $("#rcFormRegulierP").show();
    $("#rcDateFinP").show();
    $("#RC-regulierToggleP").parent().css("background-color", "#f1f1f1");
    $("#RC-fixeToggleP").parent().css("background-color", "inherit");
    $("#RC-singleToggleP").parent().css("background-color", "inherit");
}

function rcSelectFixeP(){
    $("#rcFormFixeP").show();
    $("#rcFormRegulierP").hide();
    $("#rcDateFinP").show();
    $("#rcDetailP").show();
    $("#RC-fixeToggleP").parent().css("background-color", "#f1f1f1");
    $("#RC-regulierToggleP").parent().css("background-color", "inherit");
    $("#RC-singleToggleP").parent().css("background-color", "inherit");
}

function getDefaultDurée(){
    $.ajax({
        url: 'get_agenda_config_params.php', 
        type: 'POST',
        data: {fromsession: ""},
        dataType: 'JSON',
        success: function(agendaTimeData) {
            console.log("config retrieved successfully")
            $("#newModalDurée").val(Number(agendaTimeData["agendaTimeInterval"]));
        }
    });
}

function addVisite($interval) {
    $clientId = $("td.selected").find("span").html();
    $date = new Date();
    $month = addZero($date.getMonth() + 1);
    $day = addZero($date.getDate());
    $dateString = $date.getFullYear() + "-" + $month + "-" + $day;
    
console.log("adding visite for client " + $clientId);
    getDefaultDurée();
    $("#newModalHeure").html(getAllHours());
    $("#newModalMinutes").html(getAllMinutes($interval));
    $("#newModalInfirmière").html($infirmièreOptions);
    $("#clientSection").hide();
    $("#newModalClient").html($clientOptions);
    $("#newModalClient").val($clientId);
    $("#newClientToggle").attr("checked", false); 
    $("#newModalStatutOptions").html($visiteStatutOptions);
    $("#newModalType").html($visiteTypeOptions);
    $("#newModalParent").val("Planning");
    rcSelectUnique();
    $("#visiteModalNew").modal({
        focus: this,
        show: true
        });

    }
    
function addVisiteToday($infirmièreId, $timeClass, $date, $interval) {
   
    console.log("inf: " + $infirmièreId + ", timeclass: " + $timeClass + ", date = " + $date + ", interval = " + $interval);
    $month = addZero($date.getMonth() + 1);
    $day = addZero($date.getDate());
    $dateString = $date.getFullYear() + "-" + $month + "-" + $day;
  
 console.log("date = " + $dateString);
    getDefaultDurée();
    rcSelectUnique();

    $("#newModalDateDébut").val($dateString);
    $("#newModalHeure").html(getAllHours());
    $("#newModalMinutes").html(getAllMinutes($interval));
    $("#newModalInfirmière").html($infirmièreOptions);
    $("#newModalInfirmière").val($infirmièreId.substr(3));
    $("#newModalClient").html($clientOptions);
    $("#newModalStatutOptions").html($visiteStatutOptions);
    $("#newModalType").html($visiteTypeOptions);
    $("#newModalHeure").val($timeClass.substr(1, 2));
    $("#newModalMinutes").val($timeClass.substr(3, 2));
    $("#newModalParent").val("Agenda");
    $("#visiteModalNew").modal({
        focus: this,
        show: true
        });

    }

function modifyRdv(clickedVisitId, provenance, interval) {
    console.log("mod rdv:" + clickedVisitId + ", "+ provenance + ", " + interval);

    $.post('get_visit_details.php', // location of your php script
        {visitId: clickedVisitId},
        function (data) {  // a function to deal with the returned information
            console.log("retour visite details: " + data);
            var jdata = JSON.parse(data);
            if (jdata["statut"] == "success"){
                var rdvData = jdata["message"]; 
                console.log("retour visite details2: " + rdvData);
                $("#modalVisiteId").val(clickedVisitId);
                $("#modalNotes").val(rdvData.notes);
                $("#modalRapport").val(rdvData.rapport);
                $("#modalHeure").html(getAllHours());
                $("#modalHeure").val(rdvData.heure.substr(0, 2));
                $("#modalMinutes").html(getAllMinutes(interval));
                $("#modalMinutes").val(rdvData.heure.substr(3, 2));
                $("#modalDateDébut").val(rdvData.date);
                $("#modalTitre").val(rdvData.titre);
                $("#infirmière").html(getNomInfirmières(rdvData.infirmière));
                $("#modalStatutOptions").html(getStatuts(rdvData.statutText));
                $("#modalDurée").val(rdvData.durée);
                getVisiteTypeOptions(rdvData.tournée);
                $("#modalAlerte").val(rdvData.alerte);
                $("#modalAlerteMessage").val(rdvData.alerteMessage);
                $("#modalAdresse").val(rdvData.adresse);
                $("#modalClientId").val(rdvData.clientId);
                $("#modalParent").val(provenance);

                $("#visiteModalModif").modal({
                    focus: this,
                    show: true
                    });
            }    
            else {
                alert (jdata["statut"] + ": " + jdata["message"]);
            }
                
        });
    }
    
function modifyParentRdv(clickedVisitId, interval) {
    $.post('get_visit_details.php', 
        {visitId: clickedVisitId},
        function (data) {  
            console.log("retour visite details: " + data);
            var jdata = JSON.parse(data);
            if (jdata["statut"] == "success"){
                var rdvData = jdata["message"]; 
                rcSelectUniqueP();
                console.log("retour visite details2: " + rdvData);
                $("#modalVisiteIdP").val(clickedVisitId);
                $("#modalNotesP").val(rdvData.notes);
                $("#modalRapportP").val(rdvData.rapport);
                $("#modalHeureP").html(getAllHours());
                $("#modalHeureP").val(rdvData.heure.substr(0, 2));
                $("#modalMinutesP").html(getAllMinutes(interval));
                $("#modalMinutesP").val(rdvData.heure.substr(3, 2));
                $("#modalDateDébutP").val(rdvData.date);
                $("#modalTitreP").val(rdvData.titre);
                $("#infirmièreP").html(getNomInfirmièresP(rdvData.infirmière));
                $("#modalStatutOptionsP").html(getStatutsP(rdvData.statutText));
                $("#modalDuréeP").val(rdvData.durée);
                getVisiteTypeOptionsP(rdvData.type);
                $("#modalAlerteP").val(rdvData.alerte);
                $("#modalAlerteMessageP").val(rdvData.alerteMessage);
                $("#modalAdresseP").val(rdvData.adresse);
                $("#modalClientIdP").val(rdvData.clientId);
                $("#parentVisiteModalModif").modal({
                    focus: this,
                    show: true
                    });
            }    
            else {
                alert (jdata["statut"] + ": " + jdata["message"]);
            }

        });
    }

function saveRdv() {
    console.log("<br> notes a envoyer: " + $("#modalNotes").val() + "<br>");
    console.log("<br>adresse a envoyer: " + $("modalAdresse").val() + "<br>");
    
    $clientId = $("#modalClientId").val();

    $.ajax({
        url: 'modif_visite_full.php', 
        type: 'POST',
        data: {
            visiteId: $("#modalVisiteId").val(),
            notes: $("#modalNotes").val(),
            date: $("#modalDateDébut").val(),
            heure: $("#modalHeure").val() + ":" + $("#modalMinutes").val(),
            rapport: $("#modalRapport").val(),
            adresse: $("#modalAdresse").val(),
            tournée: $("#modalTypeOptions").val(),
            titre: $("#modalTitre").val(),
            statut: $("#modalStatutOptions").val(),
            infirmièreId: $("#infirmière").val(),
            durée: $("#modalDurée").val(),
            alerte: $("#modalAlerte").val(),
            alerteMessage: $("#modalAlerteMessage").val(),
            parentId: null,
            dateFin: null
        },
        datatype: 'json',
        success: function($resMsg){
            console.log("msg=" + $resMsg.trim() + "done");
            $res = JSON.parse($resMsg);             
            console.log("success, visite modifiée, statut: " +  $res["statut"] + ", msg: " + $res["message"]); 
            if ($res["statut"] == "OK"){
//                $("#espaceMessages").text("Visite modifiée - retour php: " + $res["message"] + "client: " + $clientId);
                if ( $("#modalParent").val() == "Agenda")
                    loadAgenda();
                else
                    showClientVisites($clientId);
                }
            else
            {
                alert("Modification impossible: " + $res["message"]);

            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            console.log("some sort of error"); 
            if(thrownError == 'abort' || thrownError == 'undefined') return;
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });
    console.log("apres php call");
    }
    
    
function saveRdvParent() {
//    var patt = /\d{4}-\d{2}-\d{2}/g;
//    var res1 = patt.test($("#modalDateDébutP").val());
//    patt = /\d{4}-\d{2}-\d{2}/g;
//    var res2 = patt.test($("#modalDateFinP").val());
    
    var jourNodes = $(".jour:checked");
    var rec = 1; // intervalle des visites pour les visites récurentes régulières
    var jours = null; // liste des jours de visites pour les visites récurents à jours fixes. 1 -> lundi, ...  7 pour dimanche
    
    $dateDebut = $("#modalDateDébutP").val();
   
    if (!$("#RC-singleToggleP").is(":checked")){
         // On n'est pas sur une visite simple, alors on récupère la date fin entrée par l'utilisateur
        console.log("répétition checked");
        $dateFin = $("#modalDateFinP").val();
        // si elle est vide, on interrompt, et on demande à l'utilisateur d'en renseigner une
        if ($dateFin == ""){
            alert("pour modifier les dates ou horraire ou durée des visites récurentes, les dates de débuts et de fin doivent être renseignées");
            return false;
        }
        if ($dateFin < $dateDebut){
            alert("Vous avez choisi une date de fin antérieure à la date de début pour votre visite récurente. Veuillez modifier l'une des dates.");
            return false;
        }
        
        if ($("#RC-fixeToggleP").is(":checked")){
            // visite récurente à jours fixes:
            // charger les jours de visite dans "jours"
            if (jourNodes.length == 0)
            {
                alert("Vous avez choisi une visite à jours fixe, mais vous n'avez séléctionné aucun jour de visite");
                $("#parentVisiteModalModif").modal({
                    focus: this,
                    show: true
                });
                return false;
            }
            jours = [];
            for (i=0; i< jourNodes.length; i++){
               jours.push(jourNodes[i].value);
            }
        }
        else {
            // visite récurente à intervalle régulier
            // charger l'intervalle dans "rec"
            rec = $("#modalRecP").val();
            if ((rec != parseInt(rec, 10)) || (rec <=0)){ // check that rec is an integer strictly positif
                alert("Vous avez choisi une visite récurente à intervaux réguliers, vous devez saisir une fréquence numérique positive (tous les x jours)");
                $("#parentVisiteModalModif").modal({
                    focus: this,
                    show: true
                });
                return false;
            }         
        }
    
        if (confirm("Détruire toutes les visites entre le " + $("#modalDateDébutP").val() + " et le " + $("#modalDateFinP").val() + "?")) {        
            $.post("delete_visite_cascade.php", 
                {
                    visiteId: $("#modalVisiteIdP").val(),
                    dateDebut: $dateDebut,
                    dateFin: $dateFin,
                    delParent: 0
                }, 
                function (data, status, xhr) {
                    if (status === 'success') {
                        $res = JSON.parse(data);
                        if ($res["statut"] == "success"){
                            var y=document.createElement('span');
                            y.innerHTML=$res["message"];
                            alert("succes2: " + y.innerHTML);
                            
                            $.ajax({
                                url: 'nouvelle_visite.php', 
                                type: 'POST',
                                data: {
                                    "dateDébut": $dateDebut,
                                    "dateFin": $dateFin,
                                    "rec": rec,
                                    "notes": $("#modalNotesP").val(),
                                    "heures": $("#modalHeureP").val(),
                                    "minutes": $("#modalMinutesP").val(),
                                    "adresse": $("#modalAdresseP").val(),
                                    "tournée": $("#modalTypeOptionsP").val(),
                                    "titre": $("#modalTitreP").val(),
                                    "statut": $("#modalStatutOptionsP").val(),
                                    "infirmièreId": $("#infirmièreP").val(),
                                    "durée": $("#modalDuréeP").val(),
                                    "alerte": $("#modalAlerteP").val(),
                                    "alerteMessage": $("#modalAlerteMessageP").val(),
                                    "clientId": $("#modalClientIdP").val(),
                                    "parentId": $("#modalVisiteIdP").val(),
                                    "clientNom": null,
                                    "clientPrenom": null,
                                    "clientAdr": null,
                                    "clientTel": null,
                                    "jours": jours
                                },
                                datatype: 'json',
                                success: function($resMsg){
                                    console.log("retour nouvelle visites pour parent");
                                    console.log("msg=" + $resMsg.trim() + "done");
                                    $res = JSON.parse($resMsg.substring(0, $resMsg.indexOf("}")+1));             
                                    console.log("success, visite crée, statut: " +  $res["statut"] + ", msg: " + $res["message"]); 
                                    if ($res["statut"] == "OK"){
                                        if ($res["message"] != ""){
                                           alert("Attention: " + $res["message"]);
                                        }
//                                        $("#espaceMessages").text("Visites ajoutées - retour php: " + $res["message"] + "client: " + $("#newModalClient").val());
                                        if ( $("#newModalParent").val() == "Agenda")
                                            loadAgenda();
                                        else
                                            showClientVisites($clientId);
                                        }        
                                },
                                error: function(xhr, ajaxOptions, thrownError) {
                                    console.log("some sort of error"); 
                                    if(thrownError == 'abort' || thrownError == 'undefined') return;
                                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                                }
                            });
                         
                        }
                        else {
                            alert($res["statut"] + ": problème lors de l'annulation de visites: " + $res["message"]);
                        }
                        showClientVisites($clientId);                   
                    }
                    else
                        alert('Erreur: ' + status);
                });
            } 
        else {
            return false;
        }
    }
    else {
    // on ne modifie pas les sous visites, simplement les valeurs statiques (autre que duréee et heures qui nécessitent de recréer les sous visites
    // pour s'assurer de la disponibilité des infirmières
        console.log("before crash2");
        $.ajax({
            url: 'modif_visite_full.php', 
            type: 'POST',
            data: {
                visiteId: $("#modalVisiteIdP").val(),
                notes: $("#modalNotesP").val(),
                date: $("#modalDateDébutP").val(),
                heure: $("#modalHeureP").val() + ":" + $("#modalMinutesP").val(),
                rapport: $("#modalRapportP").val(),
                adresse: $("#modalAdresseP").val(),
                tournée: $("#modalTypeOptionsP").val(),
                titre: $("#modalTitreP").val(),
                statut: $("#modalStatutOptionsP").val(),
                infirmièreId: $("#infirmièreP").val(),
                durée: $("#modalDuréeP").val(),
                alerte: $("#modalAlerteP").val(),
                alerteMessage: $("#modalAlerteMessageP").val(),
                parentId : $("#modalVisiteIdP").val(), 
                dateFin: $dateFin
            },
            datatype: 'json',
            success: function($resMsg){
                console.log("retour");
                console.log("msg=" + $resMsg.trim() + "done");
                $res = JSON.parse($resMsg);             
                console.log("success, visite modifiée, statut: " +  $res["statut"] + ", msg: " + $res["message"]); 
                if ($res["statut"] == "OK"){
                    $("#espaceMessages").text("Visites ajoutées - retour php: " + $res["message"]);
                    showClientVisites($clientId);
                }
                else{    
                    alert($res["statut"] + ": problème lors de l'ajout de visites: " + $res["message"]);
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                console.log("some sort of error"); 
                if(thrownError == 'abort' || thrownError == 'undefined') return;
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }

    
}    

function saveNewRdv() {
    console.log("entréee dans enregistrement nouvelle visite, client: " + $("#newModalClient").val());
    var jourNodes = $(".jour:checked");
    var rec = 1; // intervalle des visites pour les visites récurentes régulières
    var jours = null; // liste des jours de visites pour les visites récurents à jours fixes. 1 -> lundi, ...  7 pour dimanche
    
    // la date de fin est initialisée à celle du début, ce qu'on veut pour les visites simples
    $dateDebut = $("#newModalDateDébut").val();
    $dateFin = $dateDebut;
    
   
    if (!$("#RC-singleToggle").is(":checked")){
         // On n'est pas sur une visite simple, alors on récupère la date fin entrée par l'utilisateur
        console.log("répétition checked");
        $dateFin = $("#newModalDateFin").val();
        // si elle est vide, on interrompt, et on demande à l'utilisateur d'en renseigner une
        if ($dateFin == ""){
            alert("Vous avez choisi une visite récurente, mais vous avez ommis de renseigner la date de fin, veuillez en saisir une");
            return false;
        }
        if ($dateFin < $dateDebut){
            alert("Vous avez choisi une date de fin antérieure à la date de début pour votre visite récurente. Veuillez modifier l'une des dates.");
            return false;
        }
        
        if ($("#RC-fixeToggle").is(":checked")){
            // visite récurente à jours fixes:
            // charger les jours de visite dans "jours"
            if (jourNodes.length == 0)
            {
                alert("Vous avez choisi une visite à jours fixe, mais vous n'avez séléctionné aucun jour de visite");
                $("#visiteModalNew").modal({
                    focus: this,
                    show: true
                });
                return false;
            }
            jours = [];
            for (i=0; i< jourNodes.length; i++){
               jours.push(jourNodes[i].value);
            }
        }
        else {
            // visite récurente à intervalle régulier
            // charger l'intervalle dans "rec"
            rec = $("#newModalRec").val();
            if ((rec != parseInt(rec, 10)) || (rec <=0)){ // check that rec is an integer strictly positif
                alert("Vous avez choisi une visite récurente à intervaux réguliers, vous devez saisir une fréquence numérique positive (tous les x jours)");
                $("#visiteModalNew").modal({
                    focus: this,
                    show: true
                });
                return false;
            }                
        }
    }
    
        console.log("répétition not checked");
    
    
    
    if ($("#newClientToggle").is(":checked")){
        console.log("new client checked");
        $clientId =  null;
        if ($("#clNom").val() == ""){
            alert("Vous avez choisi de créer un nouveau client, mais vous n'avez pas entré de nom pour celui-ci. Veuillez en saisir un.");
            return false;
        }
    }
    else {
        $clientId =  $("#newModalClient").val();
        if ($clientId == null){
            alert("Un problème est survenu, client non trouvé, veuillez réessayer");
            return false;
        }
    }
    
    console.log("client id avant creeer visite: " + $clientId);
    
    $( "#visiteModalNew" ).modal( "hide" );
    
    

    $.ajax({
        url: 'nouvelle_visite.php', 
        type: 'POST',
        data: {
            "clientId": $clientId,

            "dateDébut": $dateDebut,
            "dateFin": $dateFin,
            "rec": rec,
            "notes": $("#newModalNotes").val(),
            "heures": $("#newModalHeure").val(),
            "minutes": $("#newModalMinutes").val(),
            "adresse": $("#newModalAdresse").val(),
            "tournée": $("#newModalType").val(),
            "titre": $("#newModalTitre").val(),
            "statut": $("#newModalStatutOptions").val(),
            "infirmièreId": $("#newModalInfirmière").val(),
            "durée": $("#newModalDurée").val(),
            "alerte": $("#newModalAlerte").val(),
            "alerteMessage": $("#newModalAlerteMessage").val(),
            "clientNom": $("#clNom").val(),
            "clientPrenom": $("#clPrenom").val(),
            "clientAdr": $("#clAdr").val(),
            "clientTel": $("#clTel").val(),
            "parentId": null,
            "jours": jours
        },
        datatype: 'json',
        success: function($resMsg){
            console.log("retour nouvelle visite");
            console.log("msg=" + $resMsg.trim() + "done");
            $res = JSON.parse($resMsg.substring(0, $resMsg.indexOf("}")+1));             
            console.log("success, visite crée, statut: " +  $res["statut"] + ", msg: " + $res["message"]); 
            if ($res["statut"] == "OK"){
                if ($res["message"] != ""){
                   alert("Attention: " + $res["message"]);
                }
                if ( $("#newModalParent").val() == "Agenda")
                    loadAgenda();
                else
                    showClientVisites($clientId);
            }
            else {
                alert("Attention: la visite n'apas pu être créée: " + $res["message"])
            }
                
        },
        error: function(xhr, ajaxOptions, thrownError) {
            console.log("some sort of error"); 
            if(thrownError == 'abort' || thrownError == 'undefined') return;
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });
     
    console.log("apres php call");
    }

function deleteRdv() {
    if (confirm("Want to delete2?")) {
        $.post("delete_visite.php", {visiteId: $("#modalVisiteId").val()}, function (data, status) {
            if (status === 'success') {
                alert(data);
                if ( $("#modalParent").val() == "Agenda")
                    loadAgenda();
                else
                    showClientVisites($clientId);                   
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.status == 0 || jqXHR == 302) {
                    alert('Your session has ended due to inactivity after 10 minutes.\nPlease refresh this page, or close this window and log back in to system.');
                    } 
                else {
                    alert('Unknown error returned while saving' + (typeof errorThrown == 'string' && errorThrown.trim().length > 0 ? ':\n' + errorThrown : ''));
                    }
                 });
        } 
    else {
        return false;
        }
    }
    
function deleteRdvParent() {
    var patt = /\d{4}-\d{2}-\d{2}/g;
    var res1 = patt.test($("#modalDateDébutP").val());
    patt = /\d{4}-\d{2}-\d{2}/g;
    var res2 = patt.test($("#modalDateFinP").val());
    if (!res1 || !res2){
        alert("pour détruire des visites récurentes, les dates de débuts et de fin doivent être renseignées: " + $("#modalDateDébutP").val() + ", " + $("#modalDateFinP").val() + res1 + res2);
        return;
    }
    else {
        if (confirm("Détruire toutes les visites entre le " + $("#modalDateDébutP").val() + " et le " + $("#modalDateFinP").val() + "?")) {        
            $.post("delete_visite_cascade.php", 
                {
                    visiteId: $("#modalVisiteIdP").val(),
                    dateDebut: $("#modalDateDébutP").val(),
                    dateFin: $("#modalDateFinP").val(),
                    delParent: 1
                }, 
                function (data, status, xhr) {
                    if (status === 'success') {
                        $res = JSON.parse(data);
                        if ($res["statut"] == "success"){
                            var y=document.createElement('span');
                            y.innerHTML=$res["message"];
                            alert("succes2: " + y.innerHTML);
                        }
                        else {
                            alert($res["statut"] + ": problème lors de l'annulation de visites: " + $res["message"]);
                        }
                        showClientVisites($clientId);                   
                    }
                    else
                        alert('Erreur: ' + status);
                });
            } 
        else {
            return false;
        }
    }
}