<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Log
 *
 * @author nicol
 */
class Log {
    //put your code here
    private $_id,
            $_infId,
            $_cptId,
            $_loginTime,
            $_logoutTime,
            $_activityTime;
    public function __construct(array $donnees){
        $this->hydrate($donnees);
    }
    
    public function hydrate(array $donnees){
        foreach ($donnees as $key => $value){
            $method = 'set'.ucfirst($key);  
            if (method_exists($this, $method)){
                $this->$method($value);
            }                
        }
    }
    
    public function id(){
        return $this->_id;
    }
    
    public function infId(){
        return $this->_infId;
    }
    
    public function cptId(){
        return $this->_cptId;
    }
    
    public function loginTime(){
        return $this->_loginTime;
    }
    
    public function logoutTime(){
        return $this->_logoutTime;
    }
    
    public function activityTime(){
        return $this->_activityTime;
    }
    
    public function setId($id){
        $intId = (int)$id;
        if ($intId > 0){
            $this->_id = $intId;
        }
    }
    
    public function setInfId($id){
        $intId = (int)$id;
        if ($intId > 0){
            $this->_infId = $intId;
        }
    }
    
    public function setCptId($id){
        $intId = (int)$id;
        if ($intId > 0){
            $this->_cptId = $intId;
        }
    }
    
    public function setLoginTime($time){
        if (is_string($ltime)){
            $this->_loginTime = $time;
        }
    }
    
    public function setLogoutTime($time){
        if (is_string($ltime)){
            $this->_logoutTime = $time;
        }
    }
    
    public function setActivityTime($time){
        if (is_string($ltime)){
            $this->_activityTime = $time;
        }
    }
    
    /*
     * fonction qui retourne vrai si activityTime a été modifié, c'est à dire que l'ancienne valeur datait de plus de 3 minutes
     * Si elle retourne vrai, la fonction appelante devra mettre à jour la base de données
     */
    public function logActivity(){
        $changedLog = false;
        $currentTime = new DateTime();
        $lastTime = new DateTime($this->activityTime());
        $interval = $currentTime->diff($lastTime, true);
        // si l'interval avec la dernière activité est supérieur à 3 minutes, on l'enregistre 
        if (($interval->days > 0) || ($interval->h > 0) || ($interval->i > 3)){
             $this->setActivityTime ($currentTime->format ('Y-m-d H:i:s'));
             $changedLog = true;            
        }
        return $changedLog;
    }
    
}
