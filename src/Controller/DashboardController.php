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
        echo '<pre>';
        $result = $zabbixClient->getHosts(['production-db-dedicated']);

        var_dump($result); die;


        foreach ($result as $host)
        {
            echo $host->host . '<br/>';
        }

        die;
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }
}
