<?php

namespace App\Controllers;

//os recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action {

    public function index(){

        session_start();

            if(!isset($_SESSION['id']) || $_SESSION['id'] == '') {
                return header('Location: /?login=erro');
            }

        $this->render('timeline');
    }

}