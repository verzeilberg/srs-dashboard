<?php

namespace App\Controller;

use App\Service\Zabbix\ZabbixClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="app_dashboard")
     */
    public function index(ZabbixClient $zabbixClient): Response
    {
        $hosts = $zabbixClient->getHosts(['production-db-dedicated']);

        return $this->render('dashboard/index.html.twig', [
            'hosts' => $hosts
        ]);
    }
}
