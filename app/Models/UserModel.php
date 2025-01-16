<?php 

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'sys_usuarios'; // Tabela correspondente
    protected $primaryKey = 'id_usuario';  // Chave primária da tabela

    protected $useTimestamps = false;

    public function insertDados($data) 
    {
        return $this->insert($data);
    }

    public function getAllUsers() 
    {
        return $this->findAll(); 
    }

    public function getAllUsersAtivos() 
    {
        $sql = "SELECT * FROM sys_usuarios WHERE is_ativo = 1";

        $result = $this->db->query($sql)->getResult();

        return $result; 
    }
}
