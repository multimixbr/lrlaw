<?php 

namespace App\Models\Juridico;

use CodeIgniter\Model;

class ServicosModels extends Model
{
    protected $table = 'ndi_servicos'; // Nome da tabela ajustado
    protected $primaryKey = 'id_servico'; // Chave primária da tabela
    

    // Obtém todos os registros ativos
    public function getAllServicos() 
    {
        $sql = "SELECT * from ndi_servicos WHERE is_ativo = 1";

        return $this->db->query($sql)->getResult();
    }

}
