/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function addZero(i) {
    if (i < 10) {
        i = "0" + i;
    }
    return i;
}

/*
 * takes a date Object and returns a string in the form "yyyy-mm-dd"
 */
function getDateString(date){
    var month = addZero(date.getMonth() + 1);
    var day = addZero(date.getDate());
    return date.getFullYear() + "-" + month + "-" + day;
}

function getAllHours() {
    var res = "";
    for (i = 0; i <= 24; i++) {
        if (i < 10)
            res += "<option>0" + i + "</option>";
        else
            res += "<option>" + i + "</option>";
        }
    return res;
    } 
    
function getAllMinutes(inter) {
    var res = "";
    for (i = 0; i < 60; i += inter) {
        res += "<option>" + addZero(i) + "</option>";
    }
    return res;
    } 

// get all the intervals in minutes which can be used to divide an hour into equal intervals
function getAllTimeIntervals () {
    res = "<option>" + 1 + "</option>";
    res += "<option>" + 2 + "</option>";
    res += "<option>" + 3 + "</option>";
    res += "<option>" + 4 + "</option>";
    res += "<option>" + 5 + "</option>";
    res += "<option>" + 6 + "</option>";
    res += "<option>" + 10 + "</option>";
    res += "<option>" + 12 + "</option>";
    res += "<option>" + 15 + "</option>";
    res += "<option>" + 20 + "</option>";
    res += "<option>" + 30 + "</option>";
    res += "<option>" + 60 + "</option>";

    return res;
}