<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

App::uses('AppController', 'Controller');

/**
 * CakePHP GamesController
 * @author Jorge Moreno
 */
class GamesController extends AppController {
    public $components = array('RequestHandler');
    public function index($id) {
        
    }
    /**
     * Retorna todos los partidos en la base de datos
     * direccion:   games/getgames.xml
     * Parametros
     *      Ninguno
     * Respuesta:
     * -->datos
     *      -->Game
     *          id
     *          local
     *          visitante
     *          goles_local
     *          goles_visitante
     *          fecha
     *          finalizo
     */
    public function getgames() 
    {
        $this->layout="webservice";
        $datos=  $this->Game->find("all");
        $this->set(array(
            'datos' => $datos,
            '_serialize' => array('datos')
        ));
    }
    /**
     * Se encarga de listar ls partidos del campeonato, agrupado por grupos
     * y ordenados por fecha
     * direccion:   games/getgamesorderbygroup.xml
     * Parametros:
     *  ninguno
     * Respuesta
     * -->datos
     *      -->Game
     *          id
     *          local
     *          visitante
     *          goles_local
     *          goles_visitante
     *          fecha
     *          finalizo
     *          fase
     *      -->Local
     *          id
     *          nombre
     *          bandera
     *          grupo
     *      -->Visitante
     *          id
     *          nombre
     *          bandera
     *          grupo
     */
    public function getgamesorderbygroup() 
    {
        $this->layout="webservice";
        $options=array(
            'joins'=>array(
                array(
                    'table' => 'teams',
                    'alias' => 'Team',
                    'type' => 'LEFT',
                    'foreignKey' => FALSE,
                    'conditions' => array(
                        'Game.local = Team.id'
                    )
                ),
            ),
            'group'=>array(
                'Team.grupo'
            ),
            'order'=>array(
                'Game.fecha'
            )
        );
        $datos=  $this->Game->find("all");
        $this->set(array(
            'datos' => $datos,
            '_serialize' => array('datos')
        ));
    }
    /**
     * Retorna todos los partidos en la base de datos
     * direccion:   games/getgamesbyuser.xml
     * Parametros
     *      idUsuario:
     *      idBet:
     * Respuesta:
     * -->datos
     *      -->Forecast
     *          id
     *          user_id
     *          marcador_local,
     *          marcador_visitante,
     * 
     *      -->Game
     *          id
     *          goles_local
     *          goles_visitante
     *          fecha
     *          fechaSistema
     *      -->Local
     *          nombre
     *      -->Visitante
     *          nombre
     *      -->Stadistic
     */
    public function getgamesbyuser() 
    {
        $idUsuario=  $this->request->data["idUsuario"];
        $idBet=  $this->request->data["idBet"];
        $this->Game->virtualFields['fechaSistema'] = "NOW()";
        $this->layout="webservice";
        $options=array(
            "fields"=>array(
                "Forecast.id",
                "Game.id",
                "Game.fechaSistema",
                "Forecast.user_id",
                "Game.goles_local",
                "Game.goles_visitante",
                "Game.fecha",
                "Local.nombre",
                "Visitante.nombre",
                "Forecast.marcador_local",
                "Forecast.marcador_visitante",
            ),
            "joins"=>array(
                array(
                    'table' => 'forecasts',
                    'alias' => 'Forecast',
                    'type' => 'LEFT',
                    'foreignKey' => FALSE,
                    'conditions' => array(
                        'Game.id = Forecast.game_id',
                        'Forecast.user_id='.$idUsuario,
                        'Forecast.bet_id='.$idBet,
                    )
                ),
            ),
            'order' => array('Game.fecha'),
        );
        $datos=  $this->Game->find("all",$options);
        $this->set(array(
            'datos' => $datos,
            '_serialize' => array('datos')
        ));
    }

}
