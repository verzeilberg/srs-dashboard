<?php

namespace App\Controller;

use App\Service\Zabbix\ZabbixClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="app_dashboard")
     */
    public function index(ZabbixClient $zabbixClient): Response
    {
        $hosts = $zabbixClient->getHosts(['production-db']);

        $ids = [
            10340
        ];
        $alerts = $zabbixClient->getAlerts($ids);
        $problems = $zabbixClient->getProblems($ids);

        return $this->render('dashboard/index.html.twig', [
            'hosts' => $hosts
        ]);
    }

    /**
     * @Route("/get_host_ids", name="app_ajax_get_hosts_id")
     */
    public function getHostStatus(Request $request, ZabbixClient $zabbixClient): Response
    {

        if (!$request->isXmlHttpRequest())
        {


        }

        $hostIds = $request->get('hostids');
        $problems = [];
        foreach ($hostIds as $hostId)
        {
            $problem = $zabbixClient->getProblems([$hostId]);
            if (array_key_exists(0, $problem)) {
                $problems[$hostId]['problem'] = $problem[0]->name;
            }
        }
        return new JsonResponse($problems);
    }
}
