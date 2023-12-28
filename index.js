/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var currentDate = new Date();
var toggleSave = "";
var ajaxReq = 'ToCancelPrevReq';
var draggedElt = null;
var forceHideNonAffected = false;
var forceShowNonAffected = false;
var heureDebut;
var heureFin;
var intervals;
var interval;

function getIntervals(inter){
    var res= ["00"];
    var cumul = 0;
    for (i=1; i<60/inter; i++){
        cumul += inter;
        res.push(addZero(cumul));
    }
    return res;
}

function getMinutesFitInterval(minutes, intervals){
    var res = Number(intervals[0]);
    var i = 1;
    while ((i < intervals.length ) && (minutes >= Number(intervals[i]))){
        res = Number(intervals[i]);
        i++;
    }
    return addZero(res);
}


function getAbrevs(infTourneesData, infId){
    console.log("inf " + infId + " tournee data: " + JSON.stringify(infTourneesData));
    var res = "";
    for (var i=0; i<infTourneesData.length; i++){
        if (infTourneesData[i].infId == infId)
        {
            var tournees = infTourneesData[i].tournees;
            for (var j=0; j< tournees.length; j++){
                if (res == "")
                res = tournees[j].abr;
            else
                res += "/" + tournees[j].abr;
            }
        }

    }
    return res;
}

/*
 * On cherche à savoir si pour le créneau commencant à startTime (0700 pour 7:00), l'infirmiere infId travaille sur une tournée 
 * qui correspond à l'une de celles listees dans "tournees" 
 * 
 */
function getTourneeClass(startTime, infId, selectedTournees, infTourneesData){
    for (var i=0; i<infTourneesData.length; i++){
        if (infTourneesData[i].infId == infId)
        {
            console.log("cherche inf pour tournee : " + infId);
            var tournees = infTourneesData[i].tournees;
            for (var j=0; j< tournees.length; j++){
                if ((selectedTournees.length == 0) || (selectedTournees.includes(tournees[j].tournee))){
                    console.log("trouvé  tournee : " + tournees[j].tournee);

                    // la tournée en question fait partie des selectionnées, verifions que l'horaire correspond
                    var start = tournees[j].start;
                    startInt = parseInt(start.charAt(0) + start.charAt(1) + start.charAt(3) + start.charAt(4));
                    var end = tournees[j].end;
                    endInt = parseInt(end.charAt(0) + end.charAt(1) + end.charAt(3) + end.charAt(4));
                    console.log ("compare current time " + parseInt(startTime) + " with " + startInt + "  and " + endInt); 
                    if ((parseInt(startTime) >= startInt) && (parseInt(startTime) < endInt))
                        return "dansTournee";
                }
                else
                                        console.log("pas trouvé  tournee : " + tournees[j].tournee + " dans " + JSON.stringify(selectedTournees));

                
            }
        }
    }
    return "horsTournee";
}


function loadAgenda() {
    var users = $(".agendaToggle:checked");

    var dateString = getDateString(currentDate);
    
    $.ajax({
        url: 'get_inf_tournees_for_date.php', 
        type: 'POST',
        data: {date: dateString},
        dataType: 'JSON',
        success: function(infTourneesData) {


            console.log("start time = " + heureDebut + ", end time: " + heureFin + ", interval: " + interval);
        
            var tableHeader = "<div class=\"table-responsive\">" +
                    "<table class=\"table table-bordered\">" +
                    "<thead class=\"tableHeader\">";
            var headerRow = "<tr>" + "<td class = \"col-xs-1\">Horaire</td>";

            var colRemain = 11 % users.length;
            var colWidth = (11 - colRemain)/users.length;
            var colClassHoraire = "col-xs-1";
            var colClassAutre = "col-xs-" + colWidth;
            var colClassLast = "col-xs-" + (colWidth + colRemain);
            console.log("col 1 width = " + colClassHoraire + ", autres col width: " + colClassAutre);

            for (i = 0; i < users.length; i++) {
                var abrs = getAbrevs(infTourneesData, users[i].value);
                if (i < users.length - 1)
                    headerRow += "<td class = \"" + colClassAutre + "\">" + users[i].parentNode.textContent + "<br>" + abrs + "</td>";
                else
                    headerRow += "<td class = \"" + colClassLast + "\">" + users[i].parentNode.textContent + "<br>" + abrs + "</td>";
            };

            headerRow += "</tr>"; 
            tableHeader += headerRow +
                    "</thead></table></div>";

            var tableAgenda = "<div class=\"table-responsive\">" +
                    "<table class=\"table table-bordered \">" +
                    "<thead class=\"tableHeader\">";
            var headerRow = "<tr hidden>" + "<td class = \"col-xs-1\">Horaire</td>";

            var colRemain = 11 % users.length;
            var colWidth = (11 - colRemain)/users.length;
            var colClassHoraire = "col-xs-1";
            var colClassAutre = "col-xs-" + colWidth;
            var colClassLast = "col-xs-" + (colWidth + colRemain);
            console.log("col 1 width = " + colClassHoraire + ", autres col width: " + colClassAutre);

            // get the selected tournées
            var tournees= [];
            $('.tourneeToggle:checked').each(function() {
                tournees.push($(this).val());
            });

            for (i = 0; i < users.length; i++) {
                if (i < users.length - 1)
                    headerRow += "<td class = \"" + colClassAutre + "\">" + users[i].parentNode.textContent + "</td>";
                else
                    headerRow += "<td class = \"" + colClassLast + "\">" + users[i].parentNode.textContent + "</td>";
            };

            headerRow += "</tr>"; 
            tableAgenda += headerRow +
                    "</thead>" +
                    "<tbody>";
            // à chaque case on donne une classe correspondant à l'heure (H1215 pour 12:15), et une correspondant au nom utilisateur
            for (i = heureDebut; i < heureFin; i++) {
                console.log ("i= " + addZero(i) + ", debut : " + heureDebut + ", fin: " + heureFin);
                row = "<tr class = \"active\">" +
                        "<td class = \"" + colClassHoraire + " H" + addZero(i) + "00\">" + i + ":00" + "</td>";
                for (j = 0; j < users.length; j++) {
                    var tourneeClass = getTourneeClass(addZero(i)+"00", users[j].value, tournees, infTourneesData);
                    if (j < users.length - 1)
                        row += "<td class = \"dragCell " + colClassAutre + " " + tourneeClass + " H" + addZero(i) + "00 Inf" + users[j].value + "\">" + "</td>";
                    else
                        row += "<td class = \"dragCell " + colClassLast + " " + tourneeClass + " H" + addZero(i) + "00 Inf" + users[j].value + "\">" + "</td>";

                };
                row += "</tr>";
                tableAgenda += row;
                for (k = 1; k < intervals.length; k++) {
                    row = "<tr>" +
                            "<td class = \"" + colClassHoraire + " H" + addZero(i) + intervals[k].toString() + "\">" + "&nbsp&nbsp:" + intervals[k] + "</td>";
                    for (j = 0; j < users.length; j++) {
                        var tourneeClass = getTourneeClass(addZero(i)+intervals[k].toString(), users[j].value, tournees, infTourneesData);
                        if (j < users.length - 1)
                            row += "<td class = \"dragCell " + colClassAutre + " " + tourneeClass + " H" + addZero(i) + intervals[k] + " Inf" + users[j].value + "\">" + "</td>";
                        else
                            row += "<td class = \"dragCell " + colClassLast + " " + tourneeClass + " H" + addZero(i) + intervals[k] + " Inf" + users[j].value + "\">" + "</td>";
                    }
                    ;
                    row += "</tr>";
                    tableAgenda += row;
                }

            };


            tableAgenda += "</tbody>" +
                    "</table></div>";

            $("#agendaTableHeader").html(tableHeader);
            $("#agendaTableContent").html(tableAgenda);


            ajaxReq = $.ajax({
                url: 'get_rdvs.php', 
                type: 'POST',
                data: {date: dateString},
                dataType: 'JSON',
                beforeSend : function() {
                    if(ajaxReq != 'ToCancelPrevReq' && ajaxReq.readyState < 4) {
                        ajaxReq.abort();
                    }
                },
                success: function(rdvData) {

        //                var rdvData = JSON.parse(data2);
                        console.log(rdvData);
                        console.log(rdvData.length);
                        var nonAffecte = false;
                        for (i = 0; i < rdvData.length; i++) {
                            var rdv = "<div>" + rdvData[i].nom + " - " + rdvData[i].prénom + " - " + rdvData[i].titre + "<div hidden>" + rdvData[i].id + "</div>";

                            console.log("titre: " + rdvData[i].titre);

                            if (rdvData[i].statut_visite == 3){ // visite terminée
                                rdv += "<button type=\"button\" class=\"changeStatutButton btn btn-success btn-xs glyphicon glyphicon-ok\" style=\"float:right\"><span hidden>" + rdvData[i].id + "</span></button>";
                            }
                            else if (rdvData[i].statut_visite == 1){ // visite planifiée
                                rdv += "<button type=\"button\" class=\"changeStatutButton btn btn-xs glyphicon glyphicon-time\" style=\"float:right\"><span hidden>" + rdvData[i].id + "</span></button>";
                            }
                            else if (rdvData[i].statut_visite == 4){ // visite annulée
                                rdv += "<button type=\"button\" class=\"changeStatutButton btn btn-warning btn-xs glyphicon glyphicon-remove\" style=\"float:right\"><span hidden>" + rdvData[i].id + "</span></button>";
                            }
                            if (rdvData[i].alerte == 1){
                                console.log("alerte active");
                                rdv += "<button type=\"button\" class=\"btn btn-danger btn-xs glyphicon glyphicon-exclamation-sign\" style=\"float:right\"></button>";
                            }
                            else if (rdvData[i].alerte == 2){
                                console.log("alerte inactive");
                                rdv += "<button type=\"button\" class=\"btn btn-default btn-xs glyphicon glyphicon-exclamation-sign\" style=\"float:right\"></button>";
                            }
                            else 
                                console.log("pas d'alerte");
                            rdv += "</div>";
                            var jstart = 0;
                            var minStart = rdvData[i].heure.substr(3, 2);
                            var minStartFit = getMinutesFitInterval(minStart, intervals);
                            // dans les cas ou le rdv ne tombe pas sur les créneaux de n'agenda, 
                            // il faut compenser le "démarage anticipé" pour que le calcul de l'heure de fin 
                            // fonctionne
                            var boxstyle = "2px double grey";
                            if (minStart != minStartFit){
                                jstart = minStart - (minStartFit + interval);
                                boxstyle = "2px dashed red";
                            }
                            if ((rdvData[i].durée % interval) != 0)
                                boxstyle = "2px dashed red";
                                                            
                            console.log("minutes: " + minStart + " , jstart = " + jstart);
                            var minutes = rdvData[i].heure.substr(3, 2) * 1;
                            for (j = jstart; j < rdvData[i].durée; j += interval){

                                var heures = rdvData[i].heure.substr(0, 2);
                                
                                if (minutes >= 60){
                                    minutes -= 60;
                                    heures = addZero(heures * 1 + 1);
                                }
                                var minutesFit = getMinutesFitInterval(minutes, intervals);
                                console.log("minutes: " + minutes + ", fit: " + minutesFit);
                                var timeString = ".H" + heures + minutesFit;
                                var mySelector = timeString + ".Inf" + rdvData[i].infirmière_id;
                                if (rdvData[i].infirmière_id == "999")
                                    nonAffecte = true;
                                console.log("rdv: " + rdv);
                                console.log("selector : " + mySelector);
                                console.log("classes: " + $(mySelector).classList);
                                $(mySelector).css("border-left", boxstyle);
                                $(mySelector).css("border-right", boxstyle);
                                if (j <= 0){
                                    $(mySelector).css("border-top", boxstyle);
                                    $(mySelector).append(rdv);
                                    var elts  = $(mySelector);
                                    for (k=0; k<elts.length; k++){
                                            elts[k].classList.add("setVisite");
                                    }
                                }
                                if ((j+interval) >= rdvData[i].durée){
                                    $(mySelector).css("border-bottom", boxstyle);
                                }
                                minutes += interval;

                            }
                        }
                        if (nonAffecte){
                            console.log("non affectes presents");
                            $("label.nonAffecte").css({"color" : "red", "font-weight": "bold"});
                            if (($("label.nonAffecte").children("input:first").prop('checked') == false) && !forceHideNonAffected){
                                $("label.nonAffecte").children("input:first").prop('checked', true);
                                loadAgenda();
                            }
                        }
                        else{
                            console.log("pas de non affectes");
                            $("label.nonAffecte").css({"color" : "black", "font-weight": "normal"});
                            if (($("label.nonAffecte").children("input:first").prop('checked') == true) && !forceShowNonAffected){
                                $("label.nonAffecte").children("input:first").prop('checked', false);
                                loadAgenda();
                            }
                        }

                        

                        // on reactive les toggles 
                        $(".agendaToggle").prop("disabled", false);
                        $(".tourneeToggle").prop("disabled", false);
                        
                        $(".changeStatutButton").on("click", function(){
                            console.log("bouton change statut: " + $(this).parent().find("div:hidden").text());
                            $("#statutModalVisiteId").val($(this).parent().find("div:hidden").text());
                            $("#statutModal").modal({
                                focus: this,
                                show: true
                            });
                        });
                        console.log("BEFORE adding touchmove ");

                        var elts = document.getElementsByClassName("setVisite");
                        for (i=0; i< elts.length; i++){
                                console.log("adding touchmove to " + elts[i]);
                                elts[i].addEventListener("touchmove", touchMoveVisite, false);
                                elts[i].addEventListener("mousedown", startDragVisite, false);
                        }
                        $('.dragCell').on("touchend", function(evt){
                                                handleEnd(evt);
                                                });
                        $('.dragCell').on("mousemove", function(evt){
                            handleDragVisite(evt);
                            });
                        $('.dragCell').on("mouseup", function(evt){
                            handleDropVisite(evt);
                            });	

                        var touchtime = 0;
                        $('.dragCell').click(function (evt) {
                            console.log("dblclick, interval: " + interval + ", " + this);
                            evt.preventDefault();
                            evt.stopPropagation();
                            if (touchtime == 0) {
                                // set first click
                                touchtime = new Date().getTime();
                            } else {
                                // compare first click to this click and see if they occurred within double click threshold
                                if (((new Date().getTime()) - touchtime) < 800) {
                                    // double click occurred
                                    touchtime = 0;
                                    
                                    var classList = $(this).attr('class').split(/\s+/);
                                    var timeClass = "";
                                    var infirmiere = "";
                                    $.each(classList, function (index, item) {
                                        if (item.startsWith("H") === true) {
                                            timeClass = item;
                                        }
                                        if (item.startsWith("Inf") === true) {
                                            infirmiere = item;
                                        }
                                    });

                                    var visitesList = $(this).children("div").children("div");
                                    console.log("dblclick, params interval: " + interval);
                                    if (visitesList.length > 0){
                                        $.each(visitesList, function (index, item) {
                                            $visiteId = item.innerHTML;
                                            console.log("modification de la visite3 " + $visiteId, + "interval = " + interval);
                                            modifyRdv($visiteId, "Agenda",  interval);

                                        });

                                    }
                                    else {
                                        console.log("création de visite"); 
                                        addVisiteToday(infirmiere, timeClass, currentDate, interval);
                                    }

                                } else {
                                    // not a double click so set as a new first click
                                    touchtime = new Date().getTime();
                                }
                            }
                        });
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    if(thrownError == 'abort' || thrownError == 'undefined') return;
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            }); //end ajaxReq
        },
        error: function(xhr, ajaxOptions, thrownError) {
            if(thrownError == 'abort' || thrownError == 'undefined') return;
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });


}


function initAgenda(){
    $.ajax({
        url: 'get_agenda_config_params.php', 
        type: 'POST',
        data: {fromsession: ""},
        dataType: 'JSON',
        success: function(agendaTimeData) {
            heureDebut = Number(agendaTimeData["agendaStartTime"]);
            heureFin = Number(agendaTimeData["agendaEndTime"]);
            interval = Number(agendaTimeData["agendaTimeInterval"]);
            intervals = getIntervals(interval);     //["00", "15", "30", "45"] 
            loadAgenda();
            $(".statutChange").on("click", changeStatut);

        }
    });
}

$(document).ready(function () {
    ajaxReq = 'ToCancelPrevReq';
    $("#topMenu").load("topMenu.html", function(){
        $("#topNav").append('<li><a href="logout.php">Déconnexion</a></li>');
    });

    showDate();
    showAgendaSelect();
    showTourneeSelect();
    initAgenda();
    $("#prevDay").click(function () {
        currentDate.setTime(currentDate.getTime() - (24 * 60 * 60 * 1000));
        showDate();
        forceShowNonAffected = false;
        forceHideNonAffected = false;
        selectInfForTournee(currentDate);
    });
    $("#nextDay").click(function () {
        currentDate.setTime(currentDate.getTime() + (24 * 60 * 60 * 1000));
        showDate();
        forceShowNonAffected = false;
        forceHideNonAffected = false;
        selectInfForTournee(currentDate);
    });
    $("#calendar").change(function () {
        var tmpDate = $("#calendar").val().split("-");
        day = tmpDate[2];
        month = tmpDate[1]-1;
        year = tmpDate[0];
        currentDate.setDate(day);
        currentDate.setMonth(month);
        currentDate.setFullYear(year);
        showDate();
        forceShowNonAffected = false;
        forceHideNonAffected = false;
        selectInfForTournee(currentDate);

    });
    $("#espaceModalModif").html(getModifVisiteModalForm());
    $("#espaceModalCreate").html(getNewVisiteModalForm());
    $("#espaceModalStatut").html(getStatutModalForm());


});

function startDragVisite(evt){
	// The dataTransfer.setData() method sets the data type and the value of the dragged data
	evt.preventDefault();
	console.log("in start drag");

	draggedElt = evt.target;
	console.log("exit start drag");

}

function handleDragVisite(evt){
	evt.preventDefault();
	if (draggedElt != null){
		console.log("in drag");
                var classList = $(this).attr('class').split(/\s+/);
                $.each(classList, function (index, item) {
                    if (item.startsWith("Inf") === true) {
                        $('.dragCell').removeClass('hover');
                        $(evt.target).addClass("hover"); 
                    }
                    });		
	}

}

function handleDropVisite(evt){ // drop for mouse event
    if(draggedElt != null){
            	console.log("moved elt on drop  : " + $(evt.target).html());

		evt.preventDefault();
		if (draggedElt != evt.target){
			moveVisite(draggedElt, evt.target);
		}
		$('.dragCell').removeClass('hover');
		draggedElt = null;
	}
}

function touchMoveVisite(evt){
	  evt.preventDefault();
	console.log ("x=" + evt.touches[0].clientX + ", y=" + evt.touches[0].clientY);
	var hoverElt = document.elementFromPoint(evt.touches[0].clientX,evt.touches[0].clientY);
	console.log("moved3 to " + hoverElt.tagName + ", " + hoverElt.toString);
	$('.dragCell').removeClass('hover');
    $(hoverElt).addClass("hover");        
}

function handleEnd(evt){
	var newElt = document.elementFromPoint(evt.changedTouches[0].clientX,evt.changedTouches[0].clientY);
    	console.log("moved elt on touch end2 : " + ($(evt.target).html()=="") + ".");
	
    if (($(evt.target).html()!="") && (evt.target != newElt)){
		moveVisite(evt.target, newElt);
	}
}
	
function moveVisite(eltSource, eltTarget){	
	var classList = $(eltTarget).attr('class').split(/\s+/);
	var visiteTime = "";
	var infId = "";
	$.each(classList, function (index, item) {
		console.log("classList: " + classList);
		if (item.startsWith("H") === true) {
			visiteTime = item;
		} else if (item.startsWith("Inf") === true) {
			infId = item;
		}
	});

	if ((visiteTime != "") && (infId != "")) {
		// copy the content of the dragged cell to the new one
		$(eltTarget).html($(eltSource).html());

		// clears the content of the previous cell (from which the info is dragged) 
		$(eltSource).html("");
                $dateString = getDateString(currentDate);

		console.log("rdv modif id: " + $(eltTarget).find("div:hidden").text() + ", infirmiere: " + infId + ", time: " + visiteTime);
		$.ajax({url : 'modif_visite.php',
				type: 'POST',
				data: {
					date: $dateString,
					heure: visiteTime,
					infirmièreId: infId,
					visiteId: $(eltTarget).find("div:hidden").text()
					},
				datatype: 'json',
				success: function($resMsg){
					console.log("msg=" + $resMsg.trim() + "done");
					$res = JSON.parse($resMsg);             
					console.log("success, visite modifiée, statut: " +  $res["statut"] + ", msg: " + $res["message"]); 
					
					if ($res["statut"] == "OK"){
						$("#espaceMessages").text("Visite modifiée - retour php: " + $res["message"] + "client: " + $("#newModalClient").val());   
						
					}
					else {
						alert("Modification impossible: " + $res["statut"]);
					}
					loadAgenda();
				}
			});
		}
         else {
		$(eltSource).removeClass("hover");       
                $(eltTarget).removeClass("hover");    
                }
	}

function saveRdv() {
    $("#mainNomClient").val($("#modalNomClient").val());
    console.log("tmp nom : " + $("#modalNomClient").val());
    console.log("tmp notes : " + $("#modalNotes").val());
    console.log("tmp time : " + $("#modalTimeVisite").val());
    console.log("date : " + currentDate);
    console.log("inf : " + $("#modalInfId").val());

    var dateString = getDateString(currentDate);


    $.post('nouvelle_visite_partiel.php',
            {
                nomClient: $("#modalNomClient").val(),
                notes: $("#modalNotes").val(),
                heure: $("#modalTimeVisite").val(),
                infirmiere: $("#modalInfId").val(),
                date: dateString
            },
            function (data, status) {
                if (status === 'success') {
                    
                } else
                    alert("failure in php");
            }).fail(function (jqXHR, textStatus, errorThrown) {
        if (jqXHR.status == 0 || jqXHR == 302) {
            alert('Your session has ended due to inactivity after 10 minutes.\nPlease refresh this page, or close this window and log back in to system.');
        } else {
            alert('Unknown error returned while saving' + (typeof errorThrown == 'string' && errorThrown.trim().length > 0 ? ':\n' + errorThrown : ''));
        }
    });
    console.log("apres php call");
}


function showDate() {
    month = currentDate.getMonth()+1;
    day = currentDate.getDate();
    if (month < 10)
        month = "0" + month;
    if (day < 10)
        day = "0" + day;
    $("#calendar").val(currentDate.getFullYear() + "-" + month + "-" + day);
}

function changeStatut(){
//    alert("statut changed " + $("#statutModalVisiteId").val());
    $.ajax({url : 'modif_visite.php',
        type: 'POST',
        data: {
                visiteId: $("#statutModalVisiteId").val(),
                statut: $(this).val(),
                },
        datatype: 'json',
        success: function($resMsg){
                console.log("msg=" + $resMsg.trim() + "done");
                $res = JSON.parse($resMsg);             
                console.log("success, statut de la visite modifié, statut: " +  $res["statut"] + ", msg: " + $res["message"]); 

                if ($res["statut"] == "OK"){
            
                }
                else {
                        alert("Modification impossible: " + $res["statut"]);
                }
                loadAgenda();
        }
});
    $('#statutModal').modal('hide');
}
function showAgendaSelect() {
    $.post('get_nom_id_infirmieres.php', // location of your php script
            function (data) {  // a function to deal with the returned information
                var infData = JSON.parse(data);
                var res = "<form>";
                var x = "";
                for (x in infData) {
                    if (x == "999")
                        res += "<label class=\"checkbox-inline nonAffecte\"><input type=\"checkbox\" class=\"agendaToggle\" value=\"" + x + "\">" + infData[x] + "</label>";
                    else
                        res += "<label class=\"checkbox-inline\"><input type=\"checkbox\" class=\"agendaToggle\" value=\"" + x + "\">" + infData[x] + "</label>";
                }
                res += "</form>";
                $("#selectAgenda").html(res);
                $(".agendaToggle").click(toggleInf);
            });
}

function showTourneeSelect() {
    $.post('get_tournees.php', // location of your php script
            function (data) {  // a function to deal with the returned information
                var tourneeData = JSON.parse(data);
                var res = "<form>";
                var x = "";
                for (x in tourneeData) {
                    res += "<label class=\"checkbox-inline\"><input type=\"checkbox\" class=\"tourneeToggle\" value=\"" + tourneeData[x] + "\">" + tourneeData[x] + "</label>";
                }
                res += "</form>";
                $("#selectTournee").html(res);
                $(".tourneeToggle").click(toggleTournee);
            });
}



function toggleInf(){
    // pendant qu'on recharge l'agenda apres le dernier click, on desactive les toggles en atendant que l'agenda ait chargé
    $(".agendaToggle").prop("disabled", true);
    if ($(this).parent().hasClass("nonAffecte")){
        if ($(this).prop("checked") == true)
            forceShowNonAffected = true;
        else
            forceHideNonAffected = true;
    } 
    loadAgenda();

}

function selectInfForTournee(date){

    var dateString = getDateString(date);

    console.log("checked toggle tournees " + $(".tourneeToggle[checked=true]"));
    var tournees= [];
    $('.tourneeToggle:checked').each(function() {
        tournees.push("'" + $(this).val() + "'");
    });
    
    if (tournees.length > 0){
//    var tourneesString = tournees.join(",");
        // commencer par désélectionner toutes les infirmières
        $(".agendaToggle").prop("checked", false);

        console.log( "apres: " + tournees);
        // ensuite on reselectionne les infirmieres qui sont sur cette tournée ce jour ci
        $.post('get_inf_for_tournee.php', // location of your php script
            {
            tournees:  tournees,
            date: dateString,
            },
            function (data) {  // a function to deal with the returned information
                var tourneeData = JSON.parse(data);
                var x = "";
               
                for (x in tourneeData) {
                    console.log("tournee : " + tourneeData[x]);
                    $(".agendaToggle[value=" + tourneeData[x] + "]").prop("checked", true);
                }
                loadAgenda();
            });
    }
    else {
        loadAgenda();
    }
    
            
}

function toggleTournee(){
    // pendant qu'on recharge l'agenda apres le dernier click, on desactive les toggles en atendant que l'agenda ait chargé
    console.log("dans toggle tournee" + $(this).val());
    $(".tourneeToggle").off("click");
    $(".tourneeToggle").prop("disabled", true);
    selectInfForTournee(currentDate);
    
}


// When the user clicks on the button, scroll to the top of the document
function scrolltopFunction() {
  document.body.scrollTop = 0; // For Safari
  document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
}
