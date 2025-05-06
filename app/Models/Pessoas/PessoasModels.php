<?php 

namespace App\Models\Pessoas;

use CodeIgniter\Model;

class PessoasModels extends Model
{
    protected $table = 'glb_pessoa';
    protected $primaryKey = 'id_pessoa';  
    
    public function insertDados($data) 
    {
        return $this->insert($data);
    }

    public function getAllPessoas() 
    {
        $sql = "SELECT * FROM glb_pessoa WHERE is_ativo = 1";

        return $this->db->query($sql)->getResult();
    }

    public function getPessoasFiltro($filtros = []) 
    {
        // Inicia o builder do CodeIgniter
        $builder = $this->db->table($this->table);
        
        // Aplica o filtro de 'is_ativo' = 1
        $builder->where('is_ativo', 1);

        // Verifica e aplica o filtro por ID da pessoa
        if (!empty($filtros['id_pessoa'])) {
            $builder->where('id_pessoa', $filtros['id_pessoa']);
        }

        // Verifica e aplica filtros dinamicamente
        if (!empty($filtros['nm_pessoa'])) {
            $builder->like('nm_pessoa', $filtros['nm_pessoa']);
        }

        if (!empty($filtros['tp_pessoa'])) {
            $builder->where('tp_pessoa', $filtros['tp_pessoa']);
        }

        if (!empty($filtros['documento'])) {
            // Remove caracteres especiais do documento para a busca
            $documentoSemFormatacao = $this->removerCaracteresEspeciais($filtros['documento']);
            $builder->where("REPLACE(REPLACE(REPLACE(documento, '.', ''), '-', ''), '/', '') LIKE", "%{$documentoSemFormatacao}%");
        }

        if (!empty($filtros['tel_1'])) {
            // Remove caracteres especiais do telefone para a busca
            $telefoneSemFormatacao = $this->removerCaracteresEspeciais($filtros['tel_1']);
            $builder->where("REPLACE(REPLACE(REPLACE(REPLACE(tel_1, '(', ''), ')', ''), '-', ''), ' ', '') LIKE", "%{$telefoneSemFormatacao}%");
        }

        if (!empty($filtros['email'])) {
            $builder->like('email', $filtros['email'], 'both');
        }

        // Executa a query e retorna o resultado
        return $builder->get()->getResult();
    }

    private function removerCaracteresEspeciais($valor) 
    {
        return preg_replace('/[^0-9]/', '', $valor); // Mantém apenas os números
    }

    public function getPessoaByID($id_pessoa) 
    {
        $sql = "SELECT * FROM glb_pessoa WHERE is_ativo = 1 AND id_pessoa = {$id_pessoa}";

        return $this->db->query($sql)->getRow();
    }

    public function getPessoasParte() 
    {
        $sql = "SELECT * FROM glb_pessoa WHERE is_ativo = 1 AND tp_cad_parte = 1";

        return $this->db->query($sql)->getResult();
    }

    public function getPessoasAdv() 
    {
        $sql = "SELECT * FROM glb_pessoa WHERE is_ativo = 1 AND tp_cad_adv = 1";

        return $this->db->query($sql)->getResult();
    }

    public function getNomePessoaByID($id_pessoa) 
    {
        $sql = "SELECT nm_pessoa FROM glb_pessoa WHERE id_pessoa = {$id_pessoa}";

        return $this->db->query($sql)->getRow()->nm_pessoa;
    }

    public function getEnderecoByPessoaID($id_pessoa) 
    {
        $sql = "SELECT * FROM glb_endereco WHERE id_pessoa = {$id_pessoa}";

        return $this->db->query($sql)->getRow();
    }
    
    public function getPessoaByNDI($id_ndi) 
    {
        $sql = "SELECT ndi.id_ndi, ndi.id_cliente, gp.nm_pessoa FROM ndi LEFT JOIN glb_pessoa gp ON ndi.id_cliente = gp.id_pessoa WHERE ndi.id_ndi = {$id_ndi}";

        return $this->db->query($sql)->getRow();
    }
}
