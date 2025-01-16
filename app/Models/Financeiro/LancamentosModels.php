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
        // Atualizar o nome da tabela para 'fin_lancamentos'
        $sql = "SELECT *
                FROM fin_lancamentos
                WHERE is_ativo = 1
                    AND situacao = 'A'
                    AND dt_baixa IS NULL";

        return $this->db->query($sql)->getResult();
    }

    function getLancamentosFiltro($filtros)
    {
        // SQL base
        $sql = "SELECT fin_lancamentos.*, 
                       glb_pessoa.nm_pessoa, 
                       fin_formapagto.dsc_forma_pagto AS forma_pagamento
                FROM fin_lancamentos
                INNER JOIN glb_pessoa ON fin_lancamentos.id_pessoa = glb_pessoa.id_pessoa
                INNER JOIN fin_formapagto ON fin_lancamentos.id_forma_pagto = fin_formapagto.id_formapagto
                WHERE fin_lancamentos.is_ativo = 1";

        // Adiciona filtros dinamicamente
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

        // Ordenação
        $sql .= " ORDER BY fin_lancamentos.id_lan DESC";

        return $this->db->query($sql)->getResult();
    }

    public function getFormaPgto() 
    {
        // Atualizar o nome da tabela para 'fin_formapagto'
        $sql = "SELECT * FROM fin_formapagto WHERE is_ativo = 1";

        return $this->db->query($sql)->getResult();
    }

    public function getFormaPgtoByID($id_pgto) 
    {
        // Atualizar o nome da tabela para 'fin_formapagto'
        $sql = "SELECT dsc_forma_pagto FROM fin_formapagto WHERE is_ativo = 1 AND id_formapagto = {$id_pgto}";

        return $this->db->query($sql)->getRow()->dsc_forma_pagto;
    }

    public function getParcelasLan($id_lan_pai) 
    {
        // Atualizar o nome da tabela para 'fin_formapagto'
        $sql = "SELECT * FROM fin_lancamentos WHERE id_lan_pai = {$id_lan_pai}";

        return $this->db->query($sql)->getResult();
    }

    public function getTotalByTypeAndStatus($tp_lancamento, $situacao)
    {
        $sql = "SELECT SUM(
                    CASE 
                        WHEN flp.id_parcela IS NOT NULL AND flp.situacao = '{$situacao}' THEN 
                            flp.vl_parcela
                        WHEN flp.id_parcela IS NULL AND fl.situacao = '{$situacao}' THEN 
                            fl.vl_original
                        ELSE 0
                    END
                ) AS total
                FROM fin_lancamentos fl
                LEFT JOIN fin_lancamentos_parcela flp ON fl.id_lan = flp.id_lan
                WHERE fl.tp_lancamento = '{$tp_lancamento}'";

        $query = $this->db->query($sql);
        $result = $query->getRow();
        return $result->total ? $result->total : 0;
    }

    public function getTotalByStatus($situacao)
    {
        $sql = "SELECT SUM(
                    CASE 
                        WHEN flp.id_parcela IS NOT NULL AND flp.situacao = '{$situacao}' THEN 
                            CASE 
                                WHEN flp.vl_baixa > 0 THEN flp.vl_baixa 
                                ELSE flp.vl_parcela 
                            END
                        WHEN flp.id_parcela IS NULL AND fl.situacao = '{$situacao}' THEN 
                            CASE 
                                WHEN fl.vl_baixado > 0 THEN fl.vl_baixado 
                                ELSE fl.vl_original 
                            END
                        ELSE 0
                    END
                ) AS total
                FROM fin_lancamentos fl
                LEFT JOIN fin_lancamentos_parcela flp ON fl.id_lan = flp.id_lan
                WHERE (flp.situacao = '{$situacao}' OR fl.situacao = '{$situacao}')";

        $query = $this->db->query($sql);
        $result = $query->getRow();
        return $result->total ? $result->total : 0;
    }

    public function getTotalByStatusAndConta($situacao, $id_conta)
    {
        $sql = "SELECT SUM(
                    CASE 
                        WHEN flp.id_parcela IS NOT NULL AND flp.situacao = '{$situacao}' AND flp.id_conta = '{$id_conta}' THEN 
                            CASE 
                                WHEN flp.vl_baixa > 0 THEN flp.vl_baixa 
                                ELSE flp.vl_parcela 
                            END
                        WHEN flp.id_parcela IS NULL AND fl.situacao = '{$situacao}' AND fl.id_conta = '{$id_conta}' THEN 
                            CASE 
                                WHEN fl.vl_baixado > 0 THEN fl.vl_baixado 
                                ELSE fl.vl_original 
                            END
                        ELSE 0
                    END
                ) AS total
                FROM fin_lancamentos fl
                LEFT JOIN fin_lancamentos_parcela flp ON fl.id_lan = flp.id_lan
                WHERE (flp.situacao = '{$situacao}' OR fl.situacao = '{$situacao}')
                  AND (flp.id_conta = '{$id_conta}' OR fl.id_conta = '{$id_conta}')";

        $query = $this->db->query($sql);
        $result = $query->getRow();
        return $result->total ? $result->total : 0;
    }

    public function getTotalRecebidosMensal($mes, $ano)
    {
        $sql = "SELECT SUM(
                    CASE 
                        WHEN flp.id_parcela IS NOT NULL THEN 
                            IF(flp.vl_baixa > 0, flp.vl_baixa, flp.vl_parcela)
                        ELSE 
                            IF(fl.vl_baixado > 0, fl.vl_baixado, fl.vl_original)
                    END
                ) AS total
                FROM fin_lancamentos fl
                LEFT JOIN fin_lancamentos_parcela flp ON fl.id_lan = flp.id_lan
                WHERE fl.tp_lancamento = 'R'
                AND fl.is_ativo = 1
                AND (
                    (flp.id_parcela IS NOT NULL AND flp.situacao = 'B' AND MONTH(flp.dt_baixa) = ? AND YEAR(flp.dt_baixa) = ?)
                    OR
                    (flp.id_parcela IS NULL AND fl.situacao = 'B' AND MONTH(fl.dt_baixa) = ? AND YEAR(fl.dt_baixa) = ?)
                )";
    
        $params = [$mes, $ano, $mes, $ano];
    
        $query = $this->db->query($sql, $params);
        $result = $query->getRow();
    
        return $result->total ? $result->total : 0;
    }
    
    public function getTotalDespesasMensal($mes, $ano)
    {
        $sql = "SELECT SUM(
                    CASE 
                        WHEN flp.id_parcela IS NOT NULL THEN 
                            IF(flp.vl_baixa > 0, flp.vl_baixa, flp.vl_parcela)
                        ELSE 
                            IF(fl.vl_baixado > 0, fl.vl_baixado, fl.vl_original)
                    END
                ) AS total
                FROM fin_lancamentos fl
                LEFT JOIN fin_lancamentos_parcela flp ON fl.id_lan = flp.id_lan
                WHERE fl.tp_lancamento = 'D'
                AND fl.is_ativo = 1
                AND (
                    (flp.id_parcela IS NOT NULL AND flp.situacao = 'B' AND MONTH(flp.dt_baixa) = ? AND YEAR(flp.dt_baixa) = ?)
                    OR
                    (flp.id_parcela IS NULL AND fl.situacao = 'B' AND MONTH(fl.dt_baixa) = ? AND YEAR(fl.dt_baixa) = ?)
                )";
    
        $params = [$mes, $ano, $mes, $ano];
    
        $query = $this->db->query($sql, $params);
        $result = $query->getRow();
    
        return $result->total ? $result->total : 0;
    }
}
