<?php 

namespace App\Models\Juridico;

use CodeIgniter\Model;

class fasesModels extends Model
{
    protected $table = 'ndi_fases'; // Nome da tabela ajustado
    protected $primaryKey = 'id_fase'; // Chave primária da tabela
    

    // Obtém todos os registros ativos
    public function getFaseAtivo() 
    {
        $sql = "SELECT * from ndi_fases WHERE is_ativo = 1";

        return $this->db->query($sql)->getResult();
    }

}
