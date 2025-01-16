<?php 

namespace App\Models\Financeiro;

use CodeIgniter\Model;

class LanParcelaModels extends Model
{
    protected $table = 'fin_lancamentos_parcela';  // Certifique-se de que o nome da tabela está correto
    protected $primaryKey = 'id_parcela';  
 
    public function getParcelasLan($id_lan_pai) 
    {
        // Atualizar o nome da tabela para 'fin_formapagto'
        $sql = "SELECT * FROM fin_lancamentos_parcela WHERE id_lan = {$id_lan_pai}";

        return $this->db->query($sql)->getResult();
    }

    public function getParcelasByID($id_parcela) 
    {
        // Atualizar o nome da tabela para 'fin_formapagto'
        $sql = "SELECT 
                flp.*,
                fl.criado_por,
                fl.id_ndi
            FROM 
                fin_lancamentos_parcela flp
            INNER JOIN fin_lancamentos fl ON flp.id_lan = fl.id_lan 
            WHERE flp.id_parcela = {$id_parcela}";

        return $this->db->query($sql)->getResult();
    }

}
