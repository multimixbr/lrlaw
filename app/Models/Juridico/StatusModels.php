<?php 

namespace App\Models\Juridico;

use CodeIgniter\Model;

class statusModels extends Model
{
    protected $table = 'ndi_status'; // Nome da tabela ajustado
    protected $primaryKey = 'id_status'; // Chave primária da tabela
    

    public function getStatusAtivo() 
    {
        $sql = "SELECT * from ndi_status WHERE is_ativo = 1";

        return $this->db->query($sql)->getResult();
    }

    public function getStatusByFase($idFase) 
    {
        $sql = "SELECT * from ndi_status WHERE id_fase = {$idFase}";

        return $this->db->query($sql)->getResult();
    }

}
