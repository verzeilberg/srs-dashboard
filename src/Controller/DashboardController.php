<?php

namespace App\Controller;

use App\Service\Zabbix\ZabbixClient;
use Exception;
use RuntimeException;
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
        $hostsDedicated = $zabbixClient->getHosts(['production-db-dedicated']);
        $hostsShared = $zabbixClient->getHosts(['production-db-shared']);
        $hostsGluster = $zabbixClient->getHosts(['gluster']);
        $hostsTransfer = $zabbixClient->getHosts(['transfer']);


        return $this->render('dashboard/index.html.twig', [
            'hostsDedicated' => $hostsDedicated,
            'hostsShared' => $hostsShared,
            'hostsGluster' => $hostsGluster,
            'hostsTransfer' => $hostsTransfer
        ]);
    }

    /**
     * @Route("/get_host_ids", name="app_ajax_get_hosts_id")
     * @throws Exception
     */
    public function getHostStatus(Request $request, ZabbixClient $zabbixClient): Response
    {

        if (!$request->isXmlHttpRequest())
        {
            throw new RuntimeException();
        }

        $hostIds = $request->get('hostids');
        $problems = [];
        foreach ($hostIds as $hostId)
        {
            $problem = $zabbixClient->getProblems(false, 1, [$hostId]);
            if (array_key_exists(0, $problem)) {
                if ($problem[0]->r_clock > 0) {
                    continue;
                }
                $problems[$hostId]['problem'] = $problem[0]->name;
                $problems[$hostId]['severity'] = $problem[0]->severity;
            }
        }
        return new JsonResponse($problems);
    }
}
