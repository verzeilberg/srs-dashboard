<?php

namespace App\Service\Zabbix;

use App\System\DotEnv;
use JsonException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use stdClass;

class ZabbixClient
{
    protected ContainerInterface $container;
    protected HttpClientInterface $httpClient;

    protected $zabbixCredentialsUsername;
    protected $zabbixCredentialsPassword;

    public const client_host = "https://spectacles.storeinfo.nl/api_jsonrpc.php";

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
        $params->output     = "extend";
        $params->selectInterfaces = "extend";
        $params->selectAcknowledges = "extend";
        $params->selectTags = "extend";
        $params->selectSuppressionData = "extend";

        if (count($search) > 0) {
            $host = new \stdClass();
            $host->host = $search;
            $params->search = $host;
        }


        $params->sortfield  =  ['name'];
        $params->sortorder  = 'ASC';

        $body->params     = $params;

        $body->id   = 2;
        $body->auth = $response->toArray()["result"];

        $jsonEncodedBody = json_encode($body);

        return $this->returnResultObjects($this->httpClient->request('POST', self::client_host, ['body' => $jsonEncodedBody]));

    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function getProblems(? bool $recent = false, int $limit = 0, array $hostids = null)
    {
        $response = $this->login();

        $body          = new \stdClass();
        $body->jsonrpc = '2.0';
        $body->method  = 'problem.get';

        $params             =  new \stdClass();
        $params->output = "extend";
        $params->selectAcknowledges = "extend";
        $params->selectTags = "extend";
        $params->selectSuppressionData = "extend";
        $params->recent = $recent;
        if (!empty($hostids)) {
            $params->hostids = $hostids;
        }
        if ($recent === false) {
            $timeTill = time();
            $timeFrom = $timeTill - (60 * 60 * 24 * 14);
            $params->time_from = $timeFrom;
            $params->time_till = $timeTill;
        }
        if ($limit > 0) {
            $params->limit = $limit;
        }
        $params->sortfield  =  ["eventid"];
        $params->sortorder  = "DESC";
        $body->params       = $params;

        $body->id   = 1;
        $body->auth = $response->toArray()["result"];

        $jsonEncodedBody = json_encode($body, JSON_THROW_ON_ERROR);

        return $this->returnResultObjects($this->httpClient->request('POST', self::client_host, ['body' => $jsonEncodedBody]));
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function getAlerts(array $hostids)
    {
        $response = $this->login();

        $body          = new \stdClass();
        $body->jsonrpc = '2.0';
        $body->method  = 'alert.get';

        $params          =  new \stdClass();

        $params->output  = 'extend';
        $timeTill = time();
        $timeFrom = $timeTill - (60*60*24);
        $params->time_from = $timeFrom;
        $params->time_till = $timeTill;
        $params->sortfield  =  ["clock"];
        $params->sortorder  = "DESC";
        $params->limit = 1;
        $params->hostids = $hostids;
        $body->params    = $params;

        $body->id   = 1;
        $body->auth = $response->toArray()["result"];

        $jsonEncodedBody = json_encode($body, JSON_THROW_ON_ERROR);

        return $this->returnResultObjects($this->httpClient->request('POST', self::client_host, ['body' => $jsonEncodedBody]));
    }


    /**
     * @throws JsonException
     */
    private function returnResultObjects($result)
    {

        $data = json_decode($result->getContent(), false, 512, JSON_THROW_ON_ERROR);

        return $data->result;
    }




}
