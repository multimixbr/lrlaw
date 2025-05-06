<?php 

namespace App\Models\Admin;

use CodeIgniter\Model;

class PermissoesModels extends Model
{
    
    public function atualizarPermissoes($idUser, $operacoes)
    {
        $id_funcionalidade_oper = [];

        $dtCriacao = date('Y-m-d H:i:s');
        $criadoPor = session()->get('username'); 

        try {
            foreach ($operacoes as $op) {
                $idFuncionalidade = $op['id_funcionalidade'];
                $idOperacao = $op['id_operacao'];

                $sql = "SELECT * FROM sys_funcionalidades_operacoes 
                        WHERE id_funcionalidade = '{$idFuncionalidade}' AND id_operacao = '{$idOperacao}'";

                $result = $this->db->query($sql)->getRow();

                if ($result) {
                    $id_funcionalidade_oper[] = $result->id_funcionalidade_oper;
                }
            }

            if (!empty($id_funcionalidade_oper)) {
                foreach ($id_funcionalidade_oper as $idOperacao) {
                    $insertSql = "INSERT INTO sys_usuario_permissoes (id_funcionalidade_oper, dt_criacao, criado_por, id_usuario) 
                                  VALUES ('{$idOperacao}', '{$dtCriacao}', '{$criadoPor}', '{$idUser}')";

                    $this->db->query($insertSql);
                }
            }

            return ['status' => 'success', 'message' => 'Permissões atualizadas com sucesso'];

        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }

    }

    public function deletePermissoesUsuario($idUser)
    {
        $builder = $this->db->table('sys_usuario_permissoes');

        return $builder->where('id_usuario', $idUser)->delete();
    }

    public function getFuncionalidades() 
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
                f.is_ativo,
                f.url
            FROM sys_funcionalidades f 
            WHERE f.id_modulo = ?";

            $modulo['funcionalidades'] = $this->db->query($sqlFuncionalidades, [$modulo['id_modulo']])->getResultArray();

        }

        return $modulos;
    }

    public function getFuncionalidadesPermitidas($idUser) 
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
            $sqlFuncionalidades = "SELECT DISTINCT
                                        f.id_funcionalidade, 
                                        f.nm_funcionalidade, 
                                        f.dsc_resumo, 
                                        f.icone, 
                                        f.is_ativo,
                                        f.url
                                    FROM sys_funcionalidades f
                                    JOIN sys_funcionalidades_operacoes fo ON fo.id_funcionalidade = f.id_funcionalidade
                                    JOIN sys_usuario_permissoes up ON up.id_funcionalidade_oper = fo.id_funcionalidade_oper
                                    WHERE f.id_modulo = ? AND up.id_usuario = ?";
            $modulo['funcionalidades'] = $this->db->query($sqlFuncionalidades, [$modulo['id_modulo'], $idUser])->getResultArray();
        }

        return $modulos;
    }
}
