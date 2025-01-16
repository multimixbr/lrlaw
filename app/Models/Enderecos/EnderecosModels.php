<?php 

namespace App\Models\Enderecos;

use CodeIgniter\Model;

class EnderecosModels extends Model
{
    protected $table = 'glb_endereco';
    protected $primaryKey = 'id_endereco';
    
    public function insertEndereco($data) 
    {
        return $this->insert($data);
    }

    public function getAllEnderecosByPessoaID($id_pessoa) 
    {
        $sql = "SELECT * FROM glb_endereco WHERE id_pessoa = {$id_pessoa}";

        return $this->db->query($sql)->getRow();
    }

    public function getEnderecoByID($id_endereco) 
    {
        $sql = "SELECT * FROM glb_endereco WHERE id_endereco = {$id_endereco}";

        return $this->db->query($sql)->getRow();
    }

    public function getEnderecoCompletoByID($id_endereco) 
    {
        $sql = "SELECT CONCAT(rua, ', ', numero, ', ', complemento, ', ', bairro, ', ', cidade, ' - ', estado) AS endereco_completo 
                FROM glb_endereco 
                WHERE id_endereco = {$id_endereco}";

        return $this->db->query($sql)->getRow()->endereco_completo;
    }

    public function getEstadosBrasil()
    {
        $apiUrl = "https://servicodados.ibge.gov.br/api/v1/localidades/estados";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $estados = json_decode($response, true);
        
        return $estados;
    }

    public function getCidadesByEstado($uf)
    {
        $apiUrl = "https://servicodados.ibge.gov.br/api/v1/localidades/estados/{$uf}/municipios";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $cidades = json_decode($response, true);

        return $cidades;
    }
}
