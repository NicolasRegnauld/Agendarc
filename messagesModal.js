/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function getNewMessageModalForm(){
    console.log("enter get new messages modal");
    $adresse = "";
    var res =   "<div class=\"modal fade\" id=\"newMessageModal\" role=\"dialog\">" +
                    "<div class=\"modal-dialog\">" +
                        //Modal content
                        "<div class=\"modal-content\">" +
                            "<div class=\"modal-header\">" +
                                "<h4 style=\"color:red;\">Nouveau message" +
                            "</div>" +
                            "<div class=\"modal-body\">" +
                                "<form role=\"form\">" + 
                                    "<div class=\"form-group\">" +
                                        "<label for=\"modalMsg\">Entrez le contenu du message:</label>" +
                                        "<textarea id=\"modalMsg\" class=\"form-control\" name=\"modalMsg\"></textArea>" + 
                                    "</div>" + 
                                "</form>" +
                            "</div>" +
                            "<div class=\"modal-footer\">" +
                                "<button type= \"submit\" onclick = \"saveMessage()\" class=\"btn btn-success pull-left\" data-dismiss=\"modal\"><span class=\"glyphicon glyphicon-off\"></span> Valider</button>" +
                                "<button type= \"submit\" class=\"btn btn-danger pull-right\" data-dismiss=\"modal\"><span class=\"glyphicon glyphicon-trash\"></span> Annuler</button>" +                  
                            "</div>" +
                        "</div>" +
                    "</div>" +
                "</div>";
        console.log("return with res =  "+ res);
    return res;    
}


function addMessage() {
   console.log("entree dans addMessage");
    $("#newMessageModal").modal({
        focus: this,
        show: true
        });

    }
    
    function deleteMessage($msgId){
        if (confirm("Effacer ce message?")) {
            $.post('delete_message.php', // location of your php script
            {msgId:  $msgId},
            function (data) {  // a function to deal with the returned information
                var jdata = JSON.parse(data);
                if (jdata["statut"] == "success"){
                    console.log("Message effacé");
                    $('#msgBox').load('list_messages.php');
                }    
                else {
                alert (jdata["statut"] + ": " + jdata["message"]);
                }
            });
        }
    }
    

function saveMessage() {

    $.post('nouveau_message.php', // location of your php script
        {messageContent:  $("#modalMsg").val()},
        function (data) {  // a function to deal with the returned information
            var jdata = JSON.parse(data);
            if (jdata["statut"] == "success"){
                console.log("Message créé");
                $('#msgBox').load('list_messages.php');
            }    
            else {
                alert (jdata["statut"] + ": " + jdata["message"]);
            }
                
        });
    }
    