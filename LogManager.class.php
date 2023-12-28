<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LogManager
 *
 * @author nicol
 */
class LogManager {
    private $_db; // instance de PDO
    
    public function setDb (PDO $db){
        $this->_db = $db;
    }
    
    public function __construct ($db){
        $this->setDb($db);
    }
    
    public function add(Log $log){
        // preparation d'une requete de type INSERT
        $dateNow = (new DateTime())->format('Y-m-d H:i:s');
        $q = $this->_db->prepare('INSERT INTO ag_log (inf_id, cpt_id, login_time, logout_time, activity_time) VALUES(:infId, :cptId, :loginTime, :logoutTime, :activityTime)');
       
        // assignation des valeurs à la requete;
        $q->bindValue(':infId', $log->infId());
        $q->bindValue(':cptId', $log->cptId());
        $q->bindValue(':loginTime', $dateNow);
        $q->bindValue(':logoutTime', null);
        $q->bindValue(':activityTime', $dateNow);
            
        // execution de la requete
        $q->execute();
        
        // hydratation du log
        $log->hydrate(array('id' => $this->_db->lastInsertId()));
        
    }
    
    public function delete(Log $log){
        $this->_db->exec('DELETE FROM ag_log WHERE id = '.$log->id());

    }
    public function update (Log $log){
        // preparation d'une requete de type UPDATE
        $q = $this->_db->prepare('UPDATE ag_log SET inf_id=:infId, cpt_id=:cptId, login_time=:loginTime, logout_time=:logoutTime, activity_time=:activityTime');
        // assignation des valeurs à la requete;
        $q->bindValue(':infId', $log->infId(), PDO::PARAM_INT);
        $q->bindValue(':cptId', $log->cptId(), PDO::PARAM_INT);
        $q->bindValue(':loginTime', $log->loginTime(), PDO::PARAM_STR);
        $q->bindValue(':logoutTime', $log->logoutTime(), PDO::PARAM_STR);
        $q->bindValue(':activityTime', $log->activityTime(), PDO::PARAM_STR);   
        
        // execution de la requete
        $q->execute();
    }

    public function exists($infId, $cptId){        
        $q = $this->_db->prepare('SELECT COUNT(*) FROM ag_log WHERE cpt_id=:cptId AND inf_id=:infId');
        $q->bindValue(':infId', $infId, PDO::PARAM_INT);
        $q->bindValue(':cptId', $cptId, PDO::PARAM_INT);
        $q->execute();
            
        return (bool) $q->fetchColumn();
    }
    
    public function get($id){
        // execute une requete SELECT avec un filtre sur l'identifiant, 
        // et retourn le Personnage
        if (is_int($id)){
            $q = $this->_db->query('SELECT id, inf_id, cpt_id, login_time, logout_time, activity_time FROM ag_log WHERE id = ' . $id);
            $data = $q ->fetch(PDO::FETCH_ASSOC);
            return new Log($data);
        }
        else return null;
    }
}
