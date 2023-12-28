<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CompteManager
 *
 * @author nicol
 */
class CompteManager {
    private $_db; // instance de PDO
    
    public function setDb (PDO $db){
        $this->_db = $db;
    }
    
    public function __construct ($db){
        $this->setDb($db);
    }
    
    public function add(Compte $compte){
        // preparation d'une requete de type INSERT
        $q = $this->_db->prepare('INSERT INTO ag_compte (libellé, nbUsers, dateFin) VALUES(:lib, :nbUsers, :dateFin)');
       
        // assignation des valeurs à la requete;
        $q->bindValue(':lib', $compte->libellé());
        $q->bindValue(':nbUsers', $compte->nbUsers());
        $q->bindValue(':dateFin', $compte->dateFin());
            
        // execution de la requete
        $q->execute();
     

        
        // hydratation du personnage avec assignation de son identifiant de ses dégats à 0
        $compte->hydrate(array('id' => $this->_db->lastInsertId()));
        
        $this->initialiseTournees($compte->id());
        $this->initialiseConfig($compte->id());
        
    }
    public function delete(Compe $compte){
        $this->_db->exec('DELETE FROM ag_compte WHERE id = '.$compte->id());

    }
    public function update (Compte $compte){
        // preparation d'une requete de type UPDATE
        $q = $this->_db->prepare('UPDATE ag_compte SET nbUsers=:nb, libellé=:libellé, dateFin=:date WHERE id= :id');
        // assignation des valeurs à la requete;
        $q->bindValue(':nb', $compte->nbUsers(), PDO::PARAM_INT);
        $q->bindValue(':id', $compte->id(), PDO::PARAM_INT);
        $q->bindValue(':date', $compte->date(), PDO::PARAM_STR);
        $q->bindValue(':libellé', $compte->libellé(), PDO::PARAM_STR);        
        
        // execution de la requete
        $q->execute();
        
    }

    public function exists($info){
        // en fonction du type du parametre, execute une requete COUNT avec un filtre sur
        // l'identifiant ou le nom, et retourne un booleen
        if (is_int($info)){
            return (bool)$this->_db->query('SELECT COUNT(*) from ag_compe WHERE id = ' . $info)->fetchColumn();
        }
        else {
            $q = $this->_db->prepare('SELECT COUNT(*) FROM ag_compte WHERE libellé=:lib');
            $q->bindValue(':lib', $info, PDO::PARAM_STR);        
            $q->execute();
            
            return (bool) $q->fetchColumn();
        }
    }
    
    public function get($id){
        // execute une requete SELECT avec un filtre sur l'identifiant, 
        // et retourn le Personnage
        if (is_int($id)){
            $q = $this->_db->query('SELECT id, libellé, nbUsers, dateFin FROM ag_compte WHERE id = ' . $id);
            $data = $q ->fetch(PDO::FETCH_ASSOC);
            return new Personnage($data);
        }
        else return null;
    }
    
    private function initialiseConfig($id){
        $q = $this->_db->prepare('INSERT INTO ag_config (compte, agenda_start_time, agenda_end_time, agenda_time_interval, activity_timeout) VALUES (:cpt, :start, :end, :inter, :timeout)');

        $q->bindValue(':cpt', $id);  
        $q->bindValue(':start', 7);  
        $q->bindValue(':end', 22);    
        $q->bindValue(':inter', 10);    
        $q->bindValue(':timeout', 120);    
        $q->execute();
    }
    private function initialiseTournees($id){
        $q = $this->_db->prepare('INSERT INTO ag_tournée (compte, tournée, val_abr, val_full, start_time1, end_time1) VALUES (:cpt, :tournee, :abr, :full, :start, :end)');

        $q->bindValue(':cpt', $id);  
        $q->bindValue(':tournee', 'Soin');  
        $q->bindValue(':abr', 'J');  
        $q->bindValue(':full', 'Visites à domicile toute la journée');  
        $q->bindValue(':start', '07:00:00');  
        $q->bindValue(':end', '22:00:00');          
        $q->execute();
        
        $q->bindValue(':cpt', $id);  
        $q->bindValue(':tournee', 'Soin');  
        $q->bindValue(':abr', 'M');  
        $q->bindValue(':full', 'Visites à domicile le matin');  
        $q->bindValue(':start', '07:00:00');  
        $q->bindValue(':end', '13:00:00');          
        $q->execute();
        
        $q->bindValue(':cpt', $id);  
        $q->bindValue(':tournee', 'Soin');  
        $q->bindValue(':abr', 'S');  
        $q->bindValue(':full', 'Visites à domicile toute le soir');  
        $q->bindValue(':start', '13:00:00');  
        $q->bindValue(':end', '22:00:00');          
        $q->execute();
 
        $q->bindValue(':cpt', $id);  
        $q->bindValue(':tournee', 'Pilulier');  
        $q->bindValue(':abr', 'Pi');  
        $q->bindValue(':full', 'Pilulier à domicile');  
        $q->bindValue(':start', '07:00:00');  
        $q->bindValue(':end', '12:00:00');          
        $q->execute();        
        
        $q->bindValue(':cpt', $id);  
        $q->bindValue(':tournee', 'Permanence');  
        $q->bindValue(':abr', 'P');  
        $q->bindValue(':full', 'Permanence');  
        $q->bindValue(':start', '10:00:00');  
        $q->bindValue(':end', '12:00:00');          
        $q->execute();
        
    }
}
