<?php 

namespace App\Models\Juridico;

use CodeIgniter\Model;

class AnexosModels extends Model
{
    protected $table = 'ndi_movimento_anexos'; // Nome da tabela ajustado
    protected $primaryKey = 'id_anexo'; // Chave primária da tabela
    
    public function getAnexosByMovimento($idMovimento) 
    {
        $sql = "SELECT * from ndi_movimento_anexos WHERE id_movimento = {$idMovimento}";

        return $this->db->query($sql)->getResult();
    }

}
