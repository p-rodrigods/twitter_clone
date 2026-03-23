<?php
namespace App\Models;
use MF\Model\Model;

class Usuario extends Model {

    private $id;
    private $nome;
    private $email;
    private $senha;

    public function __get($atributo) {
        return $this->$atributo;
    }

    public function __set($atributo, $valor) {
        $this->$atributo = $valor;
    }

    // Metodo para salvar
    public function salvar() {
        $query = "insert into usuarios(nome, email, senha)values(:nome, :email, :senha)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nome', $this->__get('nome'));
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->bindValue(':senha', $this->__get('senha'));
        $stmt->execute();

        return $this;
    }

    // Metodo para validar se o cadastro é valido
    public function validarCadastro() {
        
        $valido = true;
        
        if (strlen($this->__get('nome')) < 3) {
            $valido = false;
        }   

        if (strlen($this->__get('email')) < 3) {
            $valido = false;
        }

        if (strlen($this->__get('senha')) < 3) {
            $valido = false;
        }

        return $valido;
    }

    // Metodo para recuperar usuario por email
    public function getUsuarioPorEmail(){
        $query = "select nome, email from usuarios where email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Metodo para validar login

    public function validarLogin() {
        $valido = true;

        if (strlen($this->__get('email')) < 3) {
            $valido = false;
        }

        if (strlen($this->__get('senha')) < 3) {
            $valido = false;
        }

        return $valido;
    }

    // Metodo para autenticar usuario

    public function auth(){
        
        $query = "select id, nome, email, senha from usuarios where email = :email limit 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->execute();
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);


        if($user && password_verify($this->__get('senha'), $user['senha'])) {
       
            $this->__set('id', $user['id']);
            $this->__set('nome', $user['nome']);
            
            return true;

        } 

        return false;
        
    }

    public function getAll(){

        $query = " select u.id, u.nome, u.email, (
                        select count(*) from usuarios_seguidores as us 
                        where us.id_usuario = :id_usuario and us.id_usuario_seguindo = u.id
                    ) as seguindo_sn 
                    from usuarios as u
                    where u.nome like :nome and u.id != :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nome', '%'.$this->__get('nome').'%');
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);

    }

    public function seguirUsuario($id_usuario_seguindo){
       $query = "insert into usuarios_seguidores (id_usuario, id_usuario_seguindo) values (:id_usuario, :id_usuario_seguindo)";
       $stmt = $this->db->prepare($query);
       $stmt->bindValue(':id_usuario', $this->__get('id'));
       $stmt->bindValue(':id_usuario_seguindo', $id_usuario_seguindo);
       $stmt->execute();

       return true;
    }

    public function deixarDeSeguir($id_usuario_seguindo){
        $query = "delete from usuarios_seguidores where id_usuario = :id_usuario and id_usuario_seguindo = :id_usuario_seguindo";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->bindValue(':id_usuario_seguindo', $id_usuario_seguindo);
        $stmt->execute();
        
        return true;
    }

    // Informações do Usuario
    public function getInfoUsuarios(){
        $query = "select nome from usuarios where id= :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();
        
        return $stmt->fetch(\PDO::FETCH_ASSOC)['nome'];
    }
    

    //Total de Tweets
    public function getTotalTweets(){
        $query = "select count(*) as total_tweet from tweets where id_usuario = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();

        $retorno = $stmt->fetch(\PDO::FETCH_ASSOC);

        $resultado =  $retorno['total_tweet'] ? $retorno['total_tweet'] : '0';
         
        return $resultado;
    }
    
    //Total de usuarios que estamos seguindo
    public function getTotalSeguindo(){
        $query = "select count(*) as total_seguindo from usuarios_seguidores where id_usuario = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();
        
        $retorno = $stmt->fetch(\PDO::FETCH_ASSOC);

        $resultado =  $retorno['total_seguindo'] ? $retorno['total_seguindo'] : '0';
         
        return $resultado;
    }
    

    //Total de sequidores
    public function getTotalSeguidores(){
        $query = "select count(*) as total_seguidores from usuarios_seguidores where id_usuario_seguindo = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();
        
        $retorno = $stmt->fetch(\PDO::FETCH_ASSOC);

        $resultado =  $retorno['total_seguidores'] ? $retorno['total_seguidores'] : '0';
         
        return $resultado;
    }
}