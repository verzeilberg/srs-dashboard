<?php

namespace App\Service\Zabbix;

use App\System\DotEnv;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use stdClass;
class ZabbixClient
{
    protected $container;
    protected $httpClient;
    protected $zabbixCredentialsHost;
    protected $zabbixCredentialsUsername;
    protected $zabbixCredentialsPassword;

    const client_host = "https://spectacles.storeinfo.nl/api_jsonrpc.php";
    const client_username = "";
    const client_password = "";

    public function __construct(ContainerInterface $container)
    {
        $this->container  = $container;
        $this->httpClient = HttpClient::create([
            'headers' => [
                'Content-Type' => 'application/json-rpc',
            ]
        ]);

        $dotEnv = new Dotenv();
        $this->zabbixCredentialsUsername = $dotEnv->getEnv('zabbix_username');
        $this->zabbixCredentialsPassword = $dotEnv->getEnv('zabbix_password');
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function login(): ResponseInterface
    {
        $body          = new \stdClass();
        $body->jsonrpc = '2.0';
        $body->method  = 'user.login';

        $params           = new \stdClass();
        $params->user     = $this->zabbixCredentialsUsername ;
        $params->password = $this->zabbixCredentialsPassword;
        $body->params     = $params;

        $body->id   = 1;
        $body->auth = null;

        $jsonEncodedBody = json_encode($body);

        return $this->httpClient->request('POST', self::client_host, ['body' => $jsonEncodedBody]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getHosts(array $search = [])
    {
        $response = $this->login();

        $body          = new \stdClass();
        $body->jsonrpc = '2.0';
        $body->method  = 'host.get';

        $params           = new \stdClass();
        $params->output     = [
            "hostid",
            "host",
            "name"
        ];
        $params->selectInterfaces = [
            "interfaceid",
            "ip"
        ];

        if (count($search) > 0) {
            $host = new \stdClass();
            $host->host = $search;
            $params->search = $host;
        }

        $params->sortfield  =  ["name"];
        $params->sortorder  = "ASC";

        $body->params     = $params;

        $body->id   = 2;
        $body->auth = $response->toArray()["result"];

        $jsonEncodedBody = json_encode($body);

        return $this->returnResultObjects($this->httpClient->request('POST', self::client_host, ['body' => $jsonEncodedBody]));

    }

    public function getProblems()
    {
        $response = $this->login();

        $body          = new \stdClass();
        $body->jsonrpc = '2.0';
        $body->method  = 'problem.get';

        $params             =  new \stdClass();
        $params->recent  = "true";
        $params->sortfield  =  ["eventid"];
        $params->sortorder  = "DESC";
        $body->params       = $params;

        $body->id   = 1;
        $body->auth = $response->toArray()["result"];

        $jsonEncodedBody = json_encode($body);

        return $this->returnResultObjects($this->httpClient->request('POST', self::client_host, ['body' => $jsonEncodedBody]));
    }

    private function returnResultObjects($result)
    {

        $result = json_decode($result->getContent());
        return $result->result;
    }




}
