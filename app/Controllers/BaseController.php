<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    protected $db;
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];

    protected function render($view, $data = []): string
    {
        $data['content'] = view($view, $data);

        return view('dashboard/dashboard', $data);
    }

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Inicializa a conexão com o banco de dados aqui
        $this->db = \Config\Database::connect();
    }

    protected function audit($data)
    {
        $data['dt_auditoria'] = date('Y-m-d H:i:s');
        $this->db->table('sys_auditoria')->insert($data);
    }

    protected function removerCaracteresEspeciais($valor)
    {
        return preg_replace('/[^0-9]/', '', $valor); // Mantém apenas os números
    }

    protected function formatarDocumento($documento)
    {
        $documentoLimpo = $this->removerCaracteresEspeciais($documento);

        if (strlen($documentoLimpo) == 11) {
            return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "$1.$2.$3-$4", $documentoLimpo); // CPF
        } elseif (strlen($documentoLimpo) == 14) {
            return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "$1.$2.$3/$4-$5", $documentoLimpo); // CNPJ
        }

        return $documento; // Retorna o documento original se não for CPF ou CNPJ válido
    }

    protected function formatarTelefone($telefone)
    {
        $telefoneLimpo = $this->removerCaracteresEspeciais($telefone);

        if (strlen($telefoneLimpo) == 11) {
            return preg_replace("/(\d{2})(\d{5})(\d{4})/", "($1) $2-$3", $telefoneLimpo); // Celular
        } elseif (strlen($telefoneLimpo) == 10) {
            return preg_replace("/(\d{2})(\d{4})(\d{4})/", "($1) $2-$3", $telefoneLimpo); // Fixo
        }

        return $telefone; // Retorna o telefone original se não for válido
    }

    protected function convertToDecimal($value)
    {
        // Remove qualquer ponto e substitui a vírgula por ponto
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);

        return $value;
    }

    protected function formatarDataParaAmericano($data) {
        if (!empty($data)) {
            $dataFormatada = \DateTime::createFromFormat('d/m/Y', $data);
            return $dataFormatada ? $dataFormatada->format('Y-m-d') : null;
        }
        return null;
    }
}
