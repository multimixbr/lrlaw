<?php

namespace App\Controllers\Config;

use App\Controllers\BaseController;
use App\Models\Pessoas\PessoasModels;
use App\Models\Financeiro\LancamentosModels;
use App\Models\Financeiro\AnexosModel;
use App\Models\UserModel;

class ConfigControllers extends BaseController
{
    protected $lancamentosModels;
    protected $pessoasModel;
    protected $anexosModel;

    public function __construct()
    {
        $this->lancamentosModels = new LancamentosModels();
        $this->pessoasModel = new PessoasModels();
        $this->anexosModel = new AnexosModel();
    }

    private function loadDashboardView($viewName, $data = [])
    {
        echo view('dashboard/dashboard');
        return view($viewName, $data);
    }

    public function index(): string
    {
        return $this->loadDashboardView('config/config');
    }

    public function alterarSenha()
    {
        $session = session();
        $userModel = new UserModel();

        $userId = $session->get('id_usuario'); // Certifique-se de que o ID do usuário está na sessão
        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        // Verifica se a nova senha e a confirmação coincidem
        if ($newPassword !== $confirmPassword) {
            // Registro de auditoria para tentativa com senhas incompatíveis
            $this->audit([
                'descricao'       => "Tentativa de alterar senha com confirmação incompatível para usuário ID: $userId.",
                'usuario'         => $session->get('username'),
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'ALTERAR_SENHA',
                'modulo'          => 'Configurações',
                'funcionalidade'  => 'Alterar Senha',
                'operacao'        => 'Erro',
            ]);

            $session->setFlashdata('error', 'Nova senha e confirmação não coincidem.');
            return redirect()->back();
        }

        // Busca o usuário no banco de dados
        $user = $userModel->find($userId);
        if (!$user) {
            // Registro de auditoria para usuário não encontrado
            $this->audit([
                'descricao'       => "Tentativa de alterar senha para usuário não encontrado. ID: $userId.",
                'usuario'         => $session->get('username'),
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'ALTERAR_SENHA',
                'modulo'          => 'Configurações',
                'funcionalidade'  => 'Alterar Senha',
                'operacao'        => 'Erro',
            ]);

            $session->setFlashdata('error', 'Usuário não encontrado.');
            return redirect()->back();
        }

        // Verifica se a senha atual está correta
        if (md5($currentPassword) !== $user['senha']) {
            // Registro de auditoria para senha atual incorreta
            $this->audit([
                'descricao'       => "Tentativa de alterar senha com senha atual incorreta para usuário ID: $userId.",
                'usuario'         => $session->get('username'),
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'ALTERAR_SENHA',
                'modulo'          => 'Configurações',
                'funcionalidade'  => 'Alterar Senha',
                'operacao'        => 'Erro',
            ]);

            $session->setFlashdata('error', 'Senha atual incorreta.');
            return redirect()->back();
        }

        // Atualiza a senha no banco de dados
        $newPasswordHash = md5($newPassword);
        $userModel->update($userId, ['senha' => $newPasswordHash]);

        // Registro de auditoria para alteração bem-sucedida
        $this->audit([
            'descricao'       => "Senha alterada com sucesso para usuário ID: $userId.",
            'usuario'         => $session->get('username'),
            'ip_user'         => $this->request->getIPAddress(),
            'acao'            => 'ALTERAR_SENHA',
            'modulo'          => 'Configurações',
            'funcionalidade'  => 'Alterar Senha',
            'operacao'        => 'Atualizar',
        ]);

        // Define uma mensagem de sucesso e redireciona
        $session->setFlashdata('success', 'Senha alterada com sucesso.');
        return redirect()->to(base_url('config/configControllers')); // Redireciona para a base_url
    }

    // public function permissao()
    // {
    //     $session = session();
    //     $userModel = new UserModel();

    //     if ($this->request->getMethod() == 'post') {
    //         $userId = $session->get('user_id'); // Certifique-se de que o ID do usuário está na sessão

    //         $userRole = $this->request->getPost('user_role');

    //         // Valida o nível de acesso
    //         $validRoles = ['admin', 'editor', 'viewer'];
    //         if (!in_array($userRole, $validRoles)) {
    //             $session->setFlashdata('error', 'Nível de acesso inválido.');
    //             return redirect()->back();
    //         }

    //         // Atualiza o nível de acesso no banco de dados
    //         $userModel->update($userId, ['role' => $userRole]);

    //         $session->setFlashdata('success', 'Nível de acesso atualizado com sucesso.');
    //         return redirect()->to(base_url()); // Redireciona para a base_url
    //     }

    //     // Carrega a visão caso não seja uma requisição POST
    //     return $this->loadDashboardView('config/permissao');
    // }

    public function cadastrarUser()
    {
        $session = session();
        $userModel = new UserModel();

        $username = $this->request->getPost('username');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $criadoPor = $session->get('username'); // Usuário que está criando o registro

        // Verificação básica de campos obrigatórios
        if (empty($username) || empty($email) || empty($password)) {
            // Registro de auditoria para tentativa com campos obrigatórios ausentes
            $this->audit([
                'descricao'       => "Tentativa de cadastro de usuário com campos obrigatórios ausentes.",
                'usuario'         => $criadoPor,
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'CADASTRAR',
                'modulo'          => 'Configurações',
                'funcionalidade'  => 'Cadastrar Usuário',
                'operacao'        => 'Erro',
            ]);

            $session->setFlashdata('error', 'Todos os campos são obrigatórios.');
            return redirect()->back();
        }

        // Verifica se o e-mail ou nome de usuário já existe no banco de dados
        if ($userModel->where('email', $email)->orWhere('username', $username)->first()) {
            // Registro de auditoria para tentativa de cadastro com dados duplicados
            $this->audit([
                'descricao'       => "Tentativa de cadastro de usuário com e-mail ou username já existentes. Username: $username, Email: $email.",
                'usuario'         => $criadoPor,
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'CADASTRAR',
                'modulo'          => 'Configurações',
                'funcionalidade'  => 'Cadastrar Usuário',
                'operacao'        => 'Erro',
            ]);

            $session->setFlashdata('error', 'Nome de usuário ou e-mail já existe.');
            return redirect()->back();
        }

        // Criação do hash da senha usando MD5
        $passwordHash = md5($password);

        // Dados do novo usuário
        $newUserData = [
            'username'     => $username,
            'email'        => $email,
            'senha'        => $passwordHash, // Campo "senha" usando MD5
            'is_ativo'     => 1, // Define como ativo por padrão
            'dt_criacao'   => date('Y-m-d H:i:s'), // Data e hora da criação
            'criado_por'   => $criadoPor, // Usuário que criou o registro
            'dt_alteracao' => date('Y-m-d H:i:s'), // Data da última alteração
            'alterado_por' => $criadoPor, // Inicialmente, será o mesmo que criou
        ];

        // Insere o novo usuário no banco de dados
        if ($userModel->insert($newUserData)) {
            // Registro de auditoria para cadastro bem-sucedido
            $this->audit([
                'descricao'       => "Usuário cadastrado com sucesso. Username: $username, Email: $email.",
                'usuario'         => $criadoPor,
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'CADASTRAR',
                'modulo'          => 'Configurações',
                'funcionalidade'  => 'Cadastrar Usuário',
                'operacao'        => 'Criar',
            ]);

            $session->setFlashdata('success', 'Usuário cadastrado com sucesso.');
            return redirect()->to(base_url('config/configControllers')); // Redireciona para a base_url
        } else {
            // Registro de auditoria para erro ao cadastrar o usuário
            $this->audit([
                'descricao'       => "Erro ao cadastrar usuário. Username: $username, Email: $email.",
                'usuario'         => $criadoPor,
                'ip_user'         => $this->request->getIPAddress(),
                'acao'            => 'CADASTRAR',
                'modulo'          => 'Configurações',
                'funcionalidade'  => 'Cadastrar Usuário',
                'operacao'        => 'Erro',
            ]);

            $session->setFlashdata('error', 'Falha ao cadastrar o usuário. Tente novamente.');
            return redirect()->back();
        }
    }

}
