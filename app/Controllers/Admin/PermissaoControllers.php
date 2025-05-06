<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\Admin\PermissoesModels;

class permissaoControllers extends BaseController
{
    protected $userModel;
    protected $permissoesModels;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->permissoesModels = new PermissoesModels();
    }

    public function index()
    {
        return $this->render('config/permissoes');
    }

    public function alterar($idUser)
    {
        $dados['usuario'] = $this->userModel->getUserById($idUser);
        $dados['permissoes'] = $this->userModel->getPermissoes();
        $dados['permissoesUser'] = $this->userModel->getPermissoesUser($idUser);

        return $this->render('admin/permissoes', $dados);
    }

    public function savePermissoes($idUser)
    {
        $operacoes = [];
        
        // Verifica se há dados no POST e se 'permissions' está definido
        if (isset($_POST['permissions']) && is_array($_POST['permissions'])) {
            foreach ($_POST['permissions'] as $permissao) {
                // Se começar com "f|" trata como funcionalidade
                if (strpos($permissao, 'f|') === 0) {
                } else {
                    // Senão, espera o formato "id_funcionalidade|id_operacao"
                    $parts = explode('|', $permissao);
                    if (count($parts) === 2) {
                        $operacoes[] = [
                            'id_funcionalidade' => $parts[0],
                            'id_operacao'      => $parts[1]
                        ];
                    }
                }
            }
        }
        
        $this->permissoesModels->deletePermissoesUsuario($idUser);

        $result = $this->permissoesModels->atualizarPermissoes($idUser, $operacoes);
    
        if ($result['status'] === 'success') {
            return redirect()->to(base_url('admin/permissaoControllers/alterar/' . $idUser))->with('success', $result['message']);
        } else {
            return redirect()->to(base_url('admin/permissaoControllers/alterar/' . $idUser))->with('error', $result['message']);
        }
    }

}
