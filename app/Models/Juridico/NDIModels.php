<?php 

namespace App\Models\Juridico;

use CodeIgniter\Model;

class NDIModels extends Model
{
    protected $table = 'ndi'; // Nome da tabela ajustado
    protected $primaryKey = 'id_ndi'; // Chave primária da tabela
    
    // Insere um novo registro
    public function insertNDI($data) 
    {
        return $this->insert($data);
    }

    // Obtém todos os registros ativos
    public function getAllNDIS() 
    {
        $sql = "SELECT *
                FROM ndi 
                WHERE is_ativo = 1";

        return $this->db->query($sql)->getResult();
    }

    public function getVisualizarNDI($idUser) 
    {
        $sql = "SELECT ndi.*, 
                gp.nm_pessoa AS cliente, 
                su.username AS responsavel,
                nf.nm_fase,
                ns.nm_status,
                nser.nm_servico
                FROM ndi 
                LEFT JOIN glb_pessoa gp ON ndi.id_cliente = gp.id_pessoa
                LEFT JOIN sys_usuarios su ON ndi.id_responsavel = su.id_usuario
                LEFT JOIN ndi_fases nf ON ndi.id_fase = nf.id_fase
                LEFT JOIN ndi_status ns ON ndi.id_status = ns.id_status
                LEFT JOIN ndi_servicos nser ON ndi.id_servico = nser.id_servico
                WHERE ndi.is_ativo = 1
                AND ndi.situacao = 'A'
                AND ndi.id_responsavel = {$idUser}";

        return $this->db->query($sql)->getResult();
    }

    public function getNDIsFiltro($filtros)
    {
        // SQL base
        $sql = "SELECT ndi.*, 
                       gp.nm_pessoa AS cliente, 
                       su.username AS responsavel,
                       nf.nm_fase,
                       ns.nm_status,
                       nser.nm_servico
                FROM ndi
                LEFT JOIN glb_pessoa gp ON ndi.id_cliente = gp.id_pessoa
                LEFT JOIN sys_usuarios su ON ndi.id_responsavel = su.id_usuario
                LEFT JOIN ndi_fases nf ON ndi.id_fase = nf.id_fase
                LEFT JOIN ndi_status ns ON ndi.id_status = ns.id_status
                LEFT JOIN ndi_servicos nser ON ndi.id_servico = nser.id_servico
                WHERE ndi.is_ativo = 1";

        // Adiciona filtros dinamicamente
        if (!empty($filtros['id_ndi'])) {
            $sql .= " AND ndi.id_ndi = {$filtros['id_ndi']}";
        }

        if (!empty($filtros['assunto'])) {
            $sql .= " AND ndi.assunto LIKE '%{$filtros['assunto']}%'";
        }

        if (!empty($filtros['processo'])) {
            $sql .= " AND ndi.processo LIKE '%{$filtros['processo']}%'";
        }

        if (!empty($filtros['id_cliente'])) {
            $sql .= " AND ndi.id_cliente = '{$filtros['id_cliente']}'";
        }

        if (!empty($filtros['id_responsavel'])) {
            $sql .= " AND ndi.id_responsavel = '{$filtros['id_responsavel']}'";
        }

        if (!empty($filtros['prioridade'])) {
            $sql .= " AND ndi.prioridade = '{$filtros['prioridade']}'";
        }

        if (!empty($filtros['dt_abertura'])) {
            $sql .= " AND ndi.dt_abertura = '{$filtros['dt_abertura']}'";
        }

        if (!empty($filtros['id_fase'])) {
            $sql .= " AND ndi.id_fase = '{$filtros['id_fase']}'";
        }

        if (!empty($filtros['id_status'])) {
            $sql .= " AND ndi.id_status = '{$filtros['id_status']}'";
        }

        if (!empty($filtros['id_servico'])) {
            $sql .= " AND ndi.id_servico = '{$filtros['id_servico']}'";
        }
        if (!empty($filtros['situacao'])) {
            $sql .= " AND ndi.situacao = '{$filtros['situacao']}'";
        }

        // Ordenação
        $sql .= " ORDER BY ndi.id_ndi DESC";

        return $this->db->query($sql)->getResult();
    }

    public function getNDIByID($id) 
    {
        $sql = "SELECT *
                FROM ndi 
                WHERE id_ndi = {$id}";

        return $this->db->query($sql)->getRow();
    }

    public function getDetalharNDI($id) 
    {
        $sql = "SELECT ndi.*, 
                gp.nm_pessoa AS cliente, 
                su.username AS responsavel,
                nf.nm_fase,
                ns.nm_status,
                nser.nm_servico
                FROM ndi 
                LEFT JOIN glb_pessoa gp ON ndi.id_cliente = gp.id_pessoa
                LEFT JOIN sys_usuarios su ON ndi.id_responsavel = su.id_usuario
                LEFT JOIN ndi_fases nf ON ndi.id_fase = nf.id_fase
                LEFT JOIN ndi_status ns ON ndi.id_status = ns.id_status
                LEFT JOIN ndi_servicos nser ON ndi.id_servico = nser.id_servico
                WHERE ndi.id_ndi = {$id}";

        return $this->db->query($sql)->getRow();
    }

    public function updateNDI($id, $data) 
    {
        return $this->update($id, $data);
    }

    public function inactivateNDI($id) 
    {
        $data = [
            'is_ativo' => 0,
            'dt_alteracao' => date('Y-m-d H:i:s'),
            'alterado_por' => session()->get('username')
        ];
        return $this->update($id, $data);
    }

    public function getTituloCompletoByID($id) 
    {
        $sql = "SELECT CONCAT('Assunto: ', assunto, ' - Processo Nº', processo) AS titulo_completo 
                FROM ndi 
                WHERE id_ndi = {$id}";

        return $this->db->query($sql)->getRow()->titulo_completo;
    }
}
