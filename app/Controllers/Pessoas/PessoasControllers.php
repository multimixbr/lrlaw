<?php

namespace App\Controllers\Pessoas;

use App\Controllers\BaseController;
use App\Models\Pessoas\PessoasModels;
use App\Models\Enderecos\EnderecosModels;

class PessoasControllers extends BaseController
{
    protected $pessoasModels;
    protected $enderecosModels;

    public function __construct()
    {
        $this->pessoasModels = new PessoasModels();
        $this->enderecosModels = new EnderecosModels();
    }

    private function loadDashboardView($viewName, $data = [])
    {
        echo view('dashboard/dashboard');
        return view($viewName, $data);
    }

    public function index(): string
    {
        return $this->loadDashboardView('pessoas/pessoas');
    }

    public function filtrar()
    {
        // Captura os filtros da requisição GET, incluindo o filtro por ID da pessoa (id_pessoa)
        $dados['filtros'] = [
            'id_pessoa' => $this->request->getGet('id_pessoa'), // Filtro por ID da pessoa
            'nm_pessoa' => $this->request->getGet('nm_pessoa'),
            'tp_pessoa' => $this->request->getGet('tp_pessoa'),
            'documento' => $this->request->getGet('documento'),
            'tel_1' => $this->request->getGet('tel_1'),
            'email' => $this->request->getGet('email')
        ];
    
        // Instância da model
        $pessoasModel = new PessoasModels();
    
        // Passa os filtros para o método da model
        $dados['pessoas'] = $pessoasModel->getPessoasFiltro($dados['filtros']);
    
        // Formata o documento e telefone dos dados retornados do banco de dados
        foreach ($dados['pessoas'] as &$pessoa) {
            // Formatar documento
            if (!empty($pessoa->documento)) {
                $pessoa->documento = $this->formatarDocumento($pessoa->documento);
            }
        
            // Formatar telefone
            if (!empty($pessoa->tel_1)) {
                $pessoa->tel_1 = $this->formatarTelefone($pessoa->tel_1);
            }
        }
    
        // Passa os dados para a view
        return $this->loadDashboardView('pessoas/visualizarPessoas', $dados);
    }

    public function cadastrarPessoas(): string
    {
        $session = session()->get();

        $dados['session'] = $session;

        $dados['estados'] = $this->enderecosModels->getEstadosBrasil();

        return $this->loadDashboardView('pessoas/cadastrarPessoas', $dados);
    }

    public function visualizarPessoas(): string
    {
        $session = session()->get();
    
        $dados['session'] = $session;
        $dados['pessoas'] = $this->pessoasModels->getAllPessoas();
    
        // Iterar sobre os resultados e aplicar a formatação de documento e telefone
        foreach ($dados['pessoas'] as &$pessoa) {
            // Formatar documento
            if (!empty($pessoa->documento)) {
                $pessoa->documento = $this->formatarDocumento($pessoa->documento);
            }
        
            // Formatar telefone
            if (!empty($pessoa->tel_1)) {
                $pessoa->tel_1 = $this->formatarTelefone($pessoa->tel_1);
            }
        }
    
        // Filtros com valores padrão, incluindo o filtro por ID
        $dados['filtros'] = [
            'id_pessoa' => '', // Filtro por ID adicionado
            'nm_pessoa' => '',
            'tp_pessoa' => '',
            'documento' => '',
            'tel_1' => '',
            'email' => ''
        ];
    
        return $this->loadDashboardView('pessoas/visualizarPessoas', $dados);
    }

    public function savePessoas()
    {
        $email = $this->request->getPost('email');

        // Validação do e-mail
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->with('errors', 'O endereço de e-mail fornecido é inválido.');
        }

        // Dados recebidos do formulário para a tabela de pessoas
        $pessoaData = [
            'nm_pessoa'      => $this->request->getPost('nm_pessoa'),
            'tp_pessoa'      => $this->request->getPost('tp_pessoa'),
            'tp_cad_cli'     => $this->request->getPost('tp_cad_cli'),
            'tp_cad_for'     => $this->request->getPost('tp_cad_for'),
            'tp_cad_parte'   => $this->request->getPost('tp_cad_parte'),
            'tp_cad_adv'     => $this->request->getPost('tp_cad_adv'),
            'documento'      => $this->removerCaracteresEspeciais($this->request->getPost('documento')), // Remove tudo que não é número
            'tel_1'          => $this->removerCaracteresEspeciais($this->request->getPost('tel_1')), // Remove tudo que não é número
            'tel_2'          => $this->removerCaracteresEspeciais($this->request->getPost('tel_2')), // Remove tudo que não é número
            'email'          => $email,
            'classificacao'  => $this->request->getPost('classificacao'),
            'observacao'     => $this->request->getPost('observacao'),
            'criado_por'     => session()->get('username'),
            'dt_criacao'     => date('Y-m-d H:i:s'), // Data e hora atuais
            'is_ativo'       => 1, // Marcado como ativo
            'dt_alteracao'   => date('Y-m-d H:i:s'), // Mesmo valor da criação
            'alterado_por'   => session()->get('username') // O mesmo que criou
        ];

        // Inicia a transação
        $this->pessoasModels->db->transBegin();

        // Tenta salvar os dados da pessoa
        if ($this->pessoasModels->save($pessoaData)) {
            // Recupera o ID da pessoa recém-inserida
            $id_pessoa = $this->pessoasModels->getInsertID();

            // Dados recebidos do formulário para a tabela de endereços
            $enderecoData = [
                'id_pessoa'     => $id_pessoa, // Associa o endereço à pessoa
                'rua'           => $this->request->getPost('rua'),
                'numero'        => $this->request->getPost('numero'),
                'complemento'   => $this->request->getPost('complemento'),
                'bairro'        => $this->request->getPost('bairro'),
                'cep'           => $this->removerCaracteresEspeciais($this->request->getPost('cep')), // Remove tudo que não é número
                'cidade'        => $this->request->getPost('cidade'),
                'estado'        => $this->request->getPost('estado')
            ];

            // Tenta salvar os dados do endereço
            if ($this->enderecosModels->save($enderecoData)) {
                // Confirma a transação se ambos os inserts foram bem-sucedidos
                $this->pessoasModels->db->transCommit();

                session()->setFlashdata('success', 'Pessoa e endereço salvos com sucesso!');
                return redirect()->to('pessoas/pessoasControllers'); // Redireciona em caso de sucesso
            } else {
                // Reverte a transação em caso de erro ao salvar o endereço
                $this->pessoasModels->db->transRollback();
                return redirect()->back()->with('errors', $this->enderecosModels->errors()); // Retorna erros
            }
        } else {
            // Reverte a transação em caso de erro ao salvar a pessoa
            $this->pessoasModels->db->transRollback();
            return redirect()->back()->with('errors', $this->pessoasModels->errors()); // Retorna erros
        }
    }

    public function visualizar($id)
    {
        $pessoas = $this->pessoasModels->getPessoaByID($id);

        if ($pessoas) {
            $dados['pessoas'] = $pessoas;
            $dados['enderecos'] = $this->enderecosModels->getAllEnderecosByPessoaID($id);
            $dados['estados'] = $this->enderecosModels->getEstadosBrasil();
            return $this->loadDashboardView('pessoas/visualizar', $dados);
        } else {
            return redirect()->to(base_url('pessoas/pessoasControllers'))->with('error', 'Pessoa não encontrada.');
        }
    }
    
    public function editar($id)
    {
        $pessoas = $this->pessoasModels->getPessoaByID($id);
        
        if ($pessoas) {
            $dados['pessoas'] = $pessoas;
            $dados['enderecos'] = $this->enderecosModels->getAllEnderecosByPessoaID($id);
            $dados['estados'] = $this->enderecosModels->getEstadosBrasil();
            return $this->loadDashboardView('pessoas/editar', $dados);
        } else {
            return redirect()->to(base_url('pessoas/pessoasControllers'))->with('error', 'Pessoa não encontrada.');
        }
    }

    public function atualizar($id)
    {
        // Validação básica de e-mail
        $email = $this->request->getPost('email');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->with('errors', 'O endereço de e-mail fornecido é inválido.');
        }

        // Dados recebidos do formulário para a tabela de pessoas
        $pessoaData = [
            'nm_pessoa'      => $this->request->getPost('nm_pessoa'),
            'tp_cad_cli'     => $this->request->getPost('tp_cad_cli'),
            'tp_cad_for'     => $this->request->getPost('tp_cad_for'),
            'tp_cad_parte'   => $this->request->getPost('tp_cad_parte'),
            'tp_cad_adv'   => $this->request->getPost('tp_cad_adv'),
            'tel_1'          => $this->removerCaracteresEspeciais($this->request->getPost('tel_1')), // Remove tudo que não é número
            'tel_2'          => $this->removerCaracteresEspeciais($this->request->getPost('tel_2')), // Remove tudo que não é número
            'email'          => $email,
            'classificacao'  => $this->request->getPost('classificacao'),
            'observacao'     => $this->request->getPost('observacao'),
            'dt_alteracao'   => date('Y-m-d H:i:s'), // Data e hora atuais
            'alterado_por'   => session()->get('username') // Quem fez a alteração
        ];

        // Inicia a transação
        $this->pessoasModels->db->transBegin();

        // Tenta atualizar os dados da pessoa
        if ($this->pessoasModels->update($id, $pessoaData)) {
            // Dados recebidos do formulário para a tabela de endereços
            $enderecoData = [
                'rua'           => $this->request->getPost('rua'),
                'numero'        => $this->request->getPost('numero'),
                'complemento'   => $this->request->getPost('complemento'),
                'bairro'        => $this->request->getPost('bairro'),
                'cep'           => $this->removerCaracteresEspeciais($this->request->getPost('cep')), // Remove tudo que não é número
                'cidade'        => $this->request->getPost('cidade'),
                'estado'        => $this->request->getPost('estado')
            ];

            // Tenta atualizar os dados do endereço
            $id_endereco = $this->enderecosModels->getAllEnderecosByPessoaID($id)->id_endereco; // Assumindo que há uma função para pegar o ID do endereço

            if ($this->enderecosModels->update($id_endereco, $enderecoData)) {
                // Confirma a transação se ambos os updates foram bem-sucedidos
                $this->pessoasModels->db->transCommit();

                return redirect()->to(base_url('pessoas/pessoasControllers'))->with('success', 'Pessoa e endereço atualizados com sucesso!');
            } else {
                // Reverte a transação em caso de erro ao atualizar o endereço
                $this->pessoasModels->db->transRollback();
                return redirect()->back()->with('errors', $this->enderecosModels->errors()); // Retorna erros
            }
        } else {
            // Reverte a transação em caso de erro ao atualizar a pessoa
            $this->pessoasModels->db->transRollback();
            return redirect()->back()->with('errors', $this->pessoasModels->errors()); // Retorna erros
        }
    }

    public function excluir($id)
    {
        // Verifica se a pessoa existe
        $pessoas = $this->pessoasModels->find($id);

        if ($pessoas) {
            // Atualiza o campo 'is_ativo' para 0
            $data = [
                'is_ativo' => 0,
                'dt_alteracao' => date('Y-m-d H:i:s'), // Atualiza a data de alteração
                'alterado_por' => session()->get('username') // Define quem alterou (supondo que o ID do usuário está na sessão)
            ];

            $this->pessoasModels->update($id, $data);
            return redirect()->to(base_url('pessoas/pessoasControllers'))->with('success', 'Pessoa inativada com sucesso.');
        } else {
            return redirect()->to(base_url('pessoas/pessoasControllers'))->with('error', 'Pessoa não encontrada.');
        }
    }
}
