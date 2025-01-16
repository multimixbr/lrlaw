<?php 

namespace App\Models\Financeiro;

use CodeIgniter\Model;

class AnexosModel extends Model
{
    protected $table = 'fin_anexos';
    protected $primaryKey = 'id_anexo';

    protected $useTimestamps = false;
    
    public function getAnexosLancamentosID($id_lan)
    {
        $sql = "SELECT * FROM fin_anexos WHERE id_lancamento = '{$id_lan}'";

        return $this->db->query($sql)->getResult();
    }

    public function getAnexosParcelasID($id_parcela)
    {
        $sql = "SELECT * FROM fin_anexos WHERE id_parcela = '{$id_parcela}'";

        return $this->db->query($sql)->getResult();
    }
}
