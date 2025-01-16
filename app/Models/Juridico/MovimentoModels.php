<?php 

namespace App\Models\Juridico;

use CodeIgniter\Model;

class MovimentoModels extends Model
{
    protected $table = 'ndi_movimento'; // Nome da tabela ajustado
    protected $primaryKey = 'id_movimento'; // Chave primária da tabela
    

    // Obtém todos os registros ativos
    public function getMovimentoByNDI($idNDI) 
    {
        $sql = "SELECT nm.*, su.username, nf.nm_fase, ns.nm_status
            from ndi_movimento nm 
            LEFT JOIN sys_usuarios su ON nm.id_usuario = su.id_usuario
            LEFT JOIN ndi_fases nf ON nm.id_fase = nf.id_fase
            LEFT JOIN ndi_status ns ON nm.id_status = ns.id_status
            WHERE id_ndi = {$idNDI} 
            ORDER BY dt_movimento DESC";

        return $this->db->query($sql)->getResult();
    }

}
