<?php 

namespace App\Controllers;

//os recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class AuthController extends Action {

    public function autenticar(){
 
        $usuario = Container::getModel('Usuario');

        $usuario->__set('email', $_POST['email']);
        $usuario->__set('senha', $_POST['senha']);

        if($usuario->validarLogin()){

            if($usuario->auth()){

                session_start();

                $_SESSION['id'] = $usuario->__get('id');
                $_SESSION['nome'] = $usuario->__get('nome');

                return header ('Location: /timeline');
            } 
        }

        return header('Location: /?login=erro');
    }

    public function logout(){
        session_start();
        session_destroy();

        return header('Location: /');
    }
}
