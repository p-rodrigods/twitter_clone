<?php

namespace App\Controllers;

//os recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action {

    public function validarSession(){

        session_start();

        if(!isset($_SESSION['id']) || $_SESSION['id'] == '') {
            return header('Location: /?login=erro');
        }

        return true;

    }

    public function index(){

        if(!$this->validarSession()){
            return false;
        }

        $tt = Container::getModel('tweet');

        $tt->__set('id_usuario', $_SESSION['id']);
    
        $this->view->tweets =  $tt->selecionarTodosRegistros();

        $this->render('timeline');
    }

    public function tweet(){

        if(!$this->validarSession()){
            return false;
        }

        $tweet = Container::getModel('tweet');

        $tweet->__set('id_usuario', $_SESSION['id']);
        $tweet->__set('tweet', $_POST['tweet']);

        $tweet->salvar();

        header('Location: /timeline');

    }

    public function quemSeguir(){
        
        if(!$this->validarSession()){
            return false;
        }
        
        $pesquisarPor = isset($_GET['pesquisarPor']) ? $_GET['pesquisarPor'] : '';

        $usuario = array();



        if($pesquisarPor != ''){

            $user = Container::getModel('usuario');
            $user->__set('nome', $pesquisarPor);
            $user->__set('id', $_SESSION['id']);
            $usuario = $user->getAll();

        }

        $this->view->usuarios = $usuario;

        $this->render('quemSeguir');

    }

    public function acao(){
        
        if(!$this->validarSession()){
            return false;
        }

        $acao = isset($_GET['acao']) ? $_GET['acao'] : ''; 
        $id_usuario_seguindo = isset($_GET['id']) ? $_GET['id'] : '';

        $usuario = Container::getModel('usuario');
        $usuario->__set('id', $_SESSION['id']);

        if($acao == 'seguir'){
            $usuario->seguirUsuario($id_usuario_seguindo);
            
        }

        if($acao == 'deixar_de_seguir'){
            $usuario->deixarDeSeguir($id_usuario_seguindo);
        }

        header('Location: /quem_seguir');
        
    }

    public function remover(){

        if(!$this->validarSession()){
            return false;
        }

        $id_tweet = isset($_GET['id']) ? $_GET['id'] : '';

        $tt = Container::getModel('tweet');
        $tt->__set('id', $id_tweet);

        $tt->removerTweet();
         
        return header('Location: /timeline');
        
    }
}