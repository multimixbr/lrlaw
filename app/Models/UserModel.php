<?php 

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'sys_usuarios'; // Tabela correspondente
    protected $primaryKey = 'id_usuario';  // Chave primária da tabela

    protected $useTimestamps = false;

    public function insertDados($data) 
    {
        return $this->insert($data);
    }

    public function getAllUsers() 
    {
        return $this->findAll(); 
    }

    public function getAllUsersAtivos() 
    {
        $sql = "SELECT * FROM sys_usuarios WHERE is_ativo = 1";

        $result = $this->db->query($sql)->getResult();

        return $result; 
    }

    public function getUserById($idUser) 
    {
        $sql = "SELECT * FROM sys_usuarios WHERE id_usuario = {$idUser}";

        $result = $this->db->query($sql)->getRow();

        return $result; 
    }

    public function getPermissoes() 
    {
        $sql = "SELECT 
            m.id_modulo, 
            m.nm_modulo, 
            m.dsc_resumo AS modulo_descricao, 
            m.icone AS modulo_icone, 
            m.ordem, 
            m.is_ativo AS modulo_ativo
        FROM sys_modulos m
        WHERE m.is_ativo = 1";

        $modulos = $this->db->query($sql)->getResultArray();

        foreach ($modulos as &$modulo) {
            $sqlFuncionalidades = "SELECT 
                f.id_funcionalidade, 
                f.nm_funcionalidade, 
                f.dsc_resumo, 
                f.icone, 
                f.is_ativo 
            FROM sys_funcionalidades f 
            WHERE f.id_modulo = ?";

            $modulo['funcionalidades'] = $this->db->query($sqlFuncionalidades, [$modulo['id_modulo']])->getResultArray();

            foreach ($modulo['funcionalidades'] as &$funcionalidade) {
                $sqlOperacoes = "SELECT 
                    fo.id_funcionalidade_oper, 
                    fo.id_operacao,
                    so.nm_operacao
                FROM sys_funcionalidades_operacoes fo 
                LEFT JOIN sys_operacoes so ON fo.id_operacao = so.id_operacao
                WHERE fo.id_funcionalidade = ?";

                $funcionalidade['operacoes'] = $this->db->query($sqlOperacoes, [$funcionalidade['id_funcionalidade']])->getResultArray();
            }
        }

        return $modulos;
    }

    public function getPermissoesUser($idUser) 
    {
        $sql = "SELECT 
                sup.*, 
                sfo.id_funcionalidade, 
                sfo.id_operacao
                FROM sys_usuario_permissoes sup 
                LEFT JOIN sys_funcionalidades_operacoes sfo ON sup.id_funcionalidade_oper = sfo.id_funcionalidade_oper
                WHERE sup.id_usuario = {$idUser}";

        $permissoes = $this->db->query($sql)->getResultArray();
        
        return $permissoes;
    }
}
