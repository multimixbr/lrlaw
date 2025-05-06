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

    public function index(): string
    {
        $filtros = [
            'id_pessoa' => $this->request->getGet('id_pessoa') ?? '',
            'nm_pessoa' => $this->request->getGet('nm_pessoa') ?? '',
            'tp_pessoa' => $this->request->getGet('tp_pessoa') ?? '',
            'documento' => $this->request->getGet('documento') ?? '',
            'tel_1'     => $this->request->getGet('tel_1') ?? '',
            'email'     => $this->request->getGet('email') ?? '',
        ];

        $pessoasModel = new PessoasModels();

        $pessoas = array_filter($filtros) ? $pessoasModel->getPessoasFiltro($filtros) : $pessoasModel->getAllPessoas();

        foreach ($pessoas as &$pessoa) {
            if (!empty($pessoa->documento)) {
                $pessoa->documento = $this->formatarDocumento($pessoa->documento);
            }
            if (!empty($pessoa->tel_1)) {
                $pessoa->tel_1 = $this->formatarTelefone($pessoa->tel_1);
            }
        }
    
        $dados = [
            'session' => session()->get(),
            'pessoas' => $pessoas,
            'filtros' => $filtros, 
        ];

        return $this->render('pessoas/visualizarPessoas', $dados);
    }

    public function novo(): string
    {
        $tipo = $this->request->getGet('tipo'); 
        
        $dados['tipo'] = $tipo;

        $session = session()->get();

        $dados['session'] = $session;

        $dados['estados'] = $this->enderecosModels->getEstadosBrasil();

        return $this->render('pessoas/cadastrarPessoas', $dados);
    }

    public function savePessoas()
    {
        $email = $this->request->getPost('email');

        if (!empty($email)) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return redirect()->back()->with('errors', 'O endereço de e-mail fornecido é inválido.');
            }
        }

        $pessoaData = [
            'nm_pessoa'      => $this->request->getPost('nm_pessoa'),
            'tp_pessoa'      => $this->request->getPost('tp_pessoa'),
            'tp_cad_cli'     => $this->request->getPost('tp_cad_cli'),
            'tp_cad_for'     => $this->request->getPost('tp_cad_for'),
            'tp_cad_parte'   => $this->request->getPost('tp_cad_parte'),
            'tp_cad_adv'     => $this->request->getPost('tp_cad_adv'),
            'documento'      => $this->removerCaracteresEspeciais($this->request->getPost('documento')),
            'tel_1'          => $this->removerCaracteresEspeciais($this->request->getPost('tel_1')),
            'tel_2'          => $this->removerCaracteresEspeciais($this->request->getPost('tel_2')), 
            'email'          => $email,
            'classificacao'  => $this->request->getPost('classificacao'),
            'observacao'     => $this->request->getPost('observacao'),
            'criado_por'     => session()->get('username'),
            'dt_criacao'     => date('Y-m-d H:i:s'), 
            'is_ativo'       => 1, 
            'dt_alteracao'   => date('Y-m-d H:i:s'), 
            'alterado_por'   => session()->get('username') 
        ];

        $this->pessoasModels->db->transBegin();

        if ($this->pessoasModels->save($pessoaData)) {
            $id_pessoa = $this->pessoasModels->getInsertID();

            $enderecoData = [
                'id_pessoa'     => $id_pessoa,
                'rua'           => $this->request->getPost('rua'),
                'numero'        => $this->request->getPost('numero'),
                'complemento'   => $this->request->getPost('complemento'),
                'bairro'        => $this->request->getPost('bairro'),
                'cep'           => $this->removerCaracteresEspeciais($this->request->getPost('cep')), 
                'cidade'        => $this->request->getPost('cidade'),
                'estado'        => $this->request->getPost('estado')
            ];

            if ($this->enderecosModels->save($enderecoData)) {
                $this->pessoasModels->db->transCommit();

                session()->setFlashdata('success', 'Pessoa e endereço salvos com sucesso!');
                return redirect()->to('pessoas/pessoasControllers/visualizar/' . $id_pessoa); 
            } else {
                $this->pessoasModels->db->transRollback();
                return redirect()->back()->with('errors', $this->enderecosModels->errors()); 
            }
        } else {
            $this->pessoasModels->db->transRollback();
            return redirect()->back()->with('errors', $this->pessoasModels->errors());
        }
    }

    public function visualizar($id)
    {
        $pessoas = $this->pessoasModels->getPessoaByID($id);

        if ($pessoas) {
            $dados['pessoas'] = $pessoas;
            $dados['enderecos'] = $this->enderecosModels->getAllEnderecosByPessoaID($id);
            $dados['estados'] = $this->enderecosModels->getEstadosBrasil();
            return $this->render('pessoas/visualizar', $dados);
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
            return $this->render('pessoas/editar', $dados);
        } else {
            return redirect()->to(base_url('pessoas/pessoasControllers'))->with('error', 'Pessoa não encontrada.');
        }
    }

    public function atualizar($id)
    {
        $email = $this->request->getPost('email');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->with('errors', 'O endereço de e-mail fornecido é inválido.');
        }

        $pessoaData = [
            'nm_pessoa'      => $this->request->getPost('nm_pessoa'),
            'tp_cad_cli'     => $this->request->getPost('tp_cad_cli'),
            'tp_cad_for'     => $this->request->getPost('tp_cad_for'),
            'tp_cad_parte'   => $this->request->getPost('tp_cad_parte'),
            'tp_cad_adv'   => $this->request->getPost('tp_cad_adv'),
            'tel_1'          => $this->removerCaracteresEspeciais($this->request->getPost('tel_1')), 
            'tel_2'          => $this->removerCaracteresEspeciais($this->request->getPost('tel_2')),
            'email'          => $email,
            'classificacao'  => $this->request->getPost('classificacao'),
            'observacao'     => $this->request->getPost('observacao'),
            'dt_alteracao'   => date('Y-m-d H:i:s'), 
            'alterado_por'   => session()->get('username')
        ];

        $this->pessoasModels->db->transBegin();

        if ($this->pessoasModels->update($id, $pessoaData)) {
            $enderecoData = [
                'rua'           => $this->request->getPost('rua'),
                'numero'        => $this->request->getPost('numero'),
                'complemento'   => $this->request->getPost('complemento'),
                'bairro'        => $this->request->getPost('bairro'),
                'cep'           => $this->removerCaracteresEspeciais($this->request->getPost('cep')), 
                'cidade'        => $this->request->getPost('cidade'),
                'estado'        => $this->request->getPost('estado')
            ];

            $id_endereco = $this->enderecosModels->getAllEnderecosByPessoaID($id)->id_endereco; 

            if ($this->enderecosModels->update($id_endereco, $enderecoData)) {
                $this->pessoasModels->db->transCommit();

                return redirect()->to(base_url('pessoas/pessoasControllers/visualizar/' . $id))->with('success', 'Pessoa e endereço atualizados com sucesso!');
            } else {
                $this->pessoasModels->db->transRollback();
                return redirect()->back()->with('errors', $this->enderecosModels->errors()); 
            }
        } else {
            $this->pessoasModels->db->transRollback();
            return redirect()->back()->with('errors', $this->pessoasModels->errors()); 
        }
    }

    public function excluir($id)
    {
        $pessoas = $this->pessoasModels->find($id);

        if ($pessoas) {
            $data = [
                'is_ativo' => 0,
                'dt_alteracao' => date('Y-m-d H:i:s'), 
                'alterado_por' => session()->get('username') 
            ];

            $this->pessoasModels->update($id, $data);
            return redirect()->to(base_url('pessoas/pessoasControllers'))->with('success', 'Pessoa inativada com sucesso.');
        } else {
            return redirect()->to(base_url('pessoas/pessoasControllers'))->with('error', 'Pessoa não encontrada.');
        }
    }

    public function ajax($type)
    {
        switch ($type) {
            case 'buscaClienteNDI':
                // Verifica se o ID do NDI foi enviado
                if (isset($_POST['id_ndi']) && !empty($_POST['id_ndi'])) {
                    $id_ndi = $_POST['id_ndi'];

                    // Busca o cliente no banco de dados
                    $cliente = $this->pessoasModels->getPessoaByNDI($id_ndi);

                    // Retorna os dados como JSON
                    if ($cliente) {
                        return $this->response->setJSON([
                            'success' => true,
                            'id_pessoa' => $cliente->id_cliente,
                            'nm_pessoa' => $cliente->nm_pessoa
                        ]);
                    } else {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Nenhum cliente encontrado para este NDI.'
                        ]);
                    }
                } else {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'ID do NDI não enviado.'
                    ]);
                }
                break;
        }
    }
}
