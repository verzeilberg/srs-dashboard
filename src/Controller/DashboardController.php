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
        $hosts = $zabbixClient->getHosts(['production-gluster']);
        $ids = [
            10320,
            10322,
            10321,
            10323,
            10373,
            10374
        ];
        $alerts = $zabbixClient->getAlerts($ids);

        echo '<pre>';
        var_dump($alerts); die;

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

        $problems = $zabbixClient->getAlerts($hostIds);


        return new JsonResponse($problems);
    }
}
