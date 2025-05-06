<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class UsuariosControllers extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $dados['usuarios'] = $this->userModel->getAllUsersAtivos();

        return $this->render('admin/usuarios', $dados);
    }

    public function alterar($idUser)
    {
        $dados['permissoesUsuario'] = $this->userModel->getPermissoes();
    }

}
