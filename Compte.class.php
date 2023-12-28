<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Compte
 *
 * @author nicol
 */
class Compte {
    //put your code here
    private $_id,
            $_libellé,
            $_dateFin,
            $_nbUsers;
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
    
    public function libellé(){
        return $this->_libellé;
    }
    
    public function dateFin(){
        return $this->_dateFin;
    }
    
    public function nbUsers(){
        return $this->_nbUsers;
    }
    
    public function setId($id){
        $intId = (int)$id;
        if ($intId > 0){
            $this->_id = $intId;
        }
    }
    
    public function setLibellé($lib){
        if (is_string($lib)){
            $this->_libellé = $lib;
        }
    }
    
    public function setDateFin($date){
        if (is_string($date)){
            $this->_dateFin = $date;
        }
    }
    
    public function setNbUsers($nb){
        $intNb = (int)$nb;
        if ($intNb >= 0){
            $this->_nbUsers = $intNb;
        }
    }
    
    
}
