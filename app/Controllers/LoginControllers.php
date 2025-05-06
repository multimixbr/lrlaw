<?php

namespace App\Controllers;

use App\Models\UserModel;

class LoginControllers extends BaseController
{
    public function index(): string
    {
        return view('login');
    }

    public function autenticar()
    {
        $session = session();
        
        $model = new UserModel();
        
        $username = $_POST['username'];
        $password = $_POST['password'];
    
        // Hash the input password with MD5 to compare with the stored hash
        $hashedPassword = md5($password);
    
        $user = $model->where('username', $username)->first();
    
        if ($user) {
            if ($hashedPassword === $user['senha']) {
                $session->set([
                    'id_usuario' => $user['id_usuario'],
                    'username' => $user['username'],
                    'isLoggedIn' => true
                ]);
                $session->setFlashdata('success', 'Usuário autenticado.');
                return redirect()->to('home');
            } else {
                $session->setFlashdata('error', 'Senha incorreta.');
                return redirect()->to('/');
            }
        } else {
            $session->setFlashdata('error', 'Usuário não encontrado.');
            return redirect()->to('/');
        }
    }

    public function logout()
    {
        $session = session();

        $session->destroy();

        // Verificar se a sessão foi destruída
        if (!$session->has('isLoggedIn')) {
            // Sessão foi destruída com sucesso
            return redirect()->to('/')->with('success', 'Você saiu com sucesso.');
        } else {
            // Algum problema ocorreu, sessão não foi destruída
            return redirect()->to('/home')->with('error', 'Erro ao realizar logout. Tente novamente.');
        }
    }
}
