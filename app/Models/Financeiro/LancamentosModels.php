<?php 

namespace App\Models\Financeiro;

use CodeIgniter\Model;

class LancamentosModels extends Model
{
    protected $table = 'fin_lancamentos';  // Certifique-se de que o nome da tabela está correto
    protected $primaryKey = 'id_lan';  

    public function insertDados($data) 
    {
        return $this->insert($data);
    }

    public function getAllLancamentos() 
    {
        $sql = "SELECT *
                FROM fin_lancamentos
                WHERE is_ativo = 1
                  AND situacao = 'A'
                  AND dt_baixa IS NULL";
        return $this->db->query($sql)->getResult();
    }

    public function getLancamentosFiltro($filtros)
    {
        $sql = "SELECT fin_lancamentos.*, 
                       glb_pessoa.nm_pessoa, 
                       fin_formapagto.dsc_forma_pagto AS forma_pagamento
                FROM fin_lancamentos
                INNER JOIN glb_pessoa ON fin_lancamentos.id_pessoa = glb_pessoa.id_pessoa
                INNER JOIN fin_formapagto ON fin_lancamentos.id_forma_pagto = fin_formapagto.id_formapagto
                WHERE fin_lancamentos.is_ativo = 1";

        if (!empty($filtros['id_lan'])) {
            $sql .= " AND fin_lancamentos.id_lan = '{$filtros['id_lan']}'";
        }
        if (!empty($filtros['id_ndi'])) {
            $sql .= " AND fin_lancamentos.id_ndi = '{$filtros['id_ndi']}'";
        }
        if (!empty($filtros['tp_lancamento'])) {
            $sql .= " AND fin_lancamentos.tp_lancamento = '{$filtros['tp_lancamento']}'";
        }
        if (!empty($filtros['nm_pessoa'])) {
            $sql .= " AND glb_pessoa.nm_pessoa LIKE '%{$filtros['nm_pessoa']}%'";
        }
        if (!empty($filtros['num_doc'])) {
            $sql .= " AND fin_lancamentos.num_doc LIKE '%{$filtros['num_doc']}%'";
        }
        if (!empty($filtros['dt_vencimento'])) {
            $sql .= " AND fin_lancamentos.dt_vencimento = '{$filtros['dt_vencimento']}'";
        }
        if (!empty($filtros['is_conferido'])) {
            $sql .= " AND fin_lancamentos.is_conferido = '{$filtros['is_conferido']}'";
        }
        if (!empty($filtros['is_aprovado'])) {
            $sql .= " AND fin_lancamentos.is_aprovado = '{$filtros['is_aprovado']}'";
        }
        if (!empty($filtros['situacao'])) {
            $sql .= " AND fin_lancamentos.situacao = '{$filtros['situacao']}'";
        }
        if (!empty($filtros['id_forma_pagto'])) {
            $sql .= " AND fin_lancamentos.id_forma_pagto = '{$filtros['id_forma_pagto']}'";
        }
        if (!empty($filtros['vl_conta'])) {
            $sql .= " AND fin_lancamentos.vl_original = '{$filtros['vl_conta']}'";
        }

        $sql .= " ORDER BY fin_lancamentos.id_lan DESC";

        return $this->db->query($sql)->getResult();
    }

    public function getFormaPgto() 
    {
        $sql = "SELECT * FROM fin_formapagto WHERE is_ativo = 1";
        return $this->db->query($sql)->getResult();
    }

    public function getFormaPgtoByID($id_pgto) 
    {
        $sql = "SELECT dsc_forma_pagto FROM fin_formapagto WHERE is_ativo = 1 AND id_formapagto = {$id_pgto}";
        return $this->db->query($sql)->getRow()->dsc_forma_pagto;
    }

    public function getLanFilho($id)
    {
        // Se o registro for filho, usamos o id_lan_pai; senão, usamos o próprio id
        $registro = $this->find($id);
        $id_lan = ($registro && !empty($registro['id_lan_pai'])) ? $registro['id_lan_pai'] : $id;

        $sql = "SELECT *
                FROM fin_lancamentos 
                WHERE id_lan = ? OR id_lan_pai = ?";
        return $this->db->query($sql, [$id_lan, $id_lan])->getResultArray();
    }

    public function getTotalByTypeAndStatus($tp_lancamento, $situacao)
    {
        $sql = "SELECT SUM(vl_original) AS total
                FROM fin_lancamentos
                WHERE tp_lancamento = ? 
                  AND situacao = ?
                  AND is_ativo = 1";
        $query = $this->db->query($sql, [$tp_lancamento, $situacao]);
        $result = $query->getRow();
        return $result->total ? $result->total : 0;
    }

    public function getTotalByStatus($situacao)
    {
        $sql = "SELECT SUM(vl_original) AS total
                FROM fin_lancamentos
                WHERE situacao = ? 
                  AND is_ativo = 1";
        $query = $this->db->query($sql, [$situacao]);
        $result = $query->getRow();
        return $result->total ? $result->total : 0;
    }

    public function getTotalByStatusAndConta($situacao, $id_conta)
    {
        $sql = "SELECT SUM(vl_original) AS total
                FROM fin_lancamentos
                WHERE situacao = ? 
                  AND id_conta = ?
                  AND is_ativo = 1";
        $query = $this->db->query($sql, [$situacao, $id_conta]);
        $result = $query->getRow();
        return $result->total ? $result->total : 0;
    }

    public function getTotalRecebidosMensal($mes, $ano)
    {
        $sql = "SELECT SUM(
                    CASE 
                        WHEN vl_baixado > 0 THEN vl_baixado
                        ELSE vl_original
                    END
                ) AS total
                FROM fin_lancamentos
                WHERE tp_lancamento = 'R'
                  AND is_ativo = 1
                  AND situacao = 'B'
                  AND MONTH(dt_baixa) = ? 
                  AND YEAR(dt_baixa) = ?";
    
        $params = [$mes, $ano];
        $query = $this->db->query($sql, $params);
        $result = $query->getRow();
        return $result->total ? $result->total : 0;
    }
    
    public function getTotalDespesasMensal($mes, $ano)
    {
        $sql = "SELECT SUM(
                    CASE 
                        WHEN vl_baixado > 0 THEN vl_baixado
                        ELSE vl_original
                    END
                ) AS total
                FROM fin_lancamentos
                WHERE tp_lancamento = 'D'
                  AND is_ativo = 1
                  AND situacao = 'B'
                  AND MONTH(dt_baixa) = ? 
                  AND YEAR(dt_baixa) = ?";
    
        $params = [$mes, $ano];
        $query = $this->db->query($sql, $params);
        $result = $query->getRow();
        return $result->total ? $result->total : 0;
    }
    
    public function getContasAVencerHoje($dataHoje)
    {
        $sql = "SELECT *
                FROM fin_lancamentos
                WHERE is_ativo = 1
                  AND dt_vencimento = ?
                  AND dt_baixa IS NULL
                  AND situacao != 'B'
                ORDER BY dt_vencimento ASC";
        return $this->db->query($sql, [$dataHoje])->getResult();
    }

    public function getContasAVencerMes($mes, $ano)
    {
        $sql = "SELECT *
                FROM fin_lancamentos
                WHERE is_ativo = 1
                  AND MONTH(dt_vencimento) = ?
                  AND YEAR(dt_vencimento) = ?
                  AND dt_baixa IS NULL
                  AND situacao != 'B'
                ORDER BY dt_vencimento ASC";
        return $this->db->query($sql, [$mes, $ano])->getResult();
    }

    public function getContasVencidas($dataHoje)
    {
        $sql = "SELECT *
                FROM fin_lancamentos
                WHERE is_ativo = 1
                  AND dt_vencimento < ?
                  AND dt_baixa IS NULL
                  AND situacao != 'B'
                ORDER BY dt_vencimento ASC";
        return $this->db->query($sql, [$dataHoje])->getResult();
    }

    public function getContasPagas($mes, $ano)
    {
        $sql = "SELECT *
                FROM fin_lancamentos
                WHERE is_ativo = 1
                  AND situacao = 'B'
                  AND MONTH(dt_baixa) = ?
                  AND YEAR(dt_baixa) = ?
                ORDER BY dt_baixa ASC";
        return $this->db->query($sql, [$mes, $ano])->getResult();
    }

}
