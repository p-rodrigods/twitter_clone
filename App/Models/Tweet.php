<?php
namespace App\Models;
use MF\Model\Model;
use PDO;

class Tweet extends Model {
    private $id;
    private $id_usuario;
    private $tweet;
    private $data;

    public function __get($atributo) {
        return $this->$atributo;
    }

    public function __set($atributo, $valor) {
        $this->$atributo = $valor;
    }

    public function salvar(){

        $query = "insert into tweets(id_usuario, tweet) values (:id_usuario, :tweet)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
        $stmt->bindValue(':tweet', $this->__get('tweet'));
        $stmt->execute();

        return $this;

    }

    public function selecionarTodosRegistros(){

        $query = "SELECT u.nome, t.id, t.tweet, DATE_FORMAT(t.data, '%d/%m/%Y %H:%i') as data
                  FROM tweets AS t
                  INNER JOIN usuarios AS u ON t.id_usuario = u.id
                  WHERE t.id_usuario = :id_usuario order by t.data DESC";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue('id_usuario', $this->__get('id_usuario'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


}