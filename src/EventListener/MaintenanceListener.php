<?php
/**
 * MaintenanceListener class.
 */

namespace App\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class MaintenanceListener
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $maintenance = $this->container->hasParameter('maintenance') ? $this->container->getParameter('maintenance') : null;
        $supportEmail = $this->container->hasParameter('support_email') ? $this->container->getParameter('support_email') : null;
        $maintenanceText = 'We are running some maintenance. Please check back after a while.';
        if (!empty($supportEmail)) {
            $maintenanceText .= '<br> <a href="mailto:'.$supportEmail.'">Contact Support</a>';
        }

        $debug = in_array($this->container->get('kernel')->getEnvironment(), ['test']);
        $exceptRoutes = in_array($event->getRequest()->attributes->get('_route'), ['app_login']);

        if ('true' === $maintenance && !$debug && !$exceptRoutes && !$this->container->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            $content = $this->container->get('twig')->render('error_layout.html.twig', [
                'code' => 'Ooops!',
                'status' => 'Under Maintenance',
                'description' => $maintenanceText,
            ]);

            $event->setResponse(new Response($content, 503));
            $event->stopPropagation();
        }
    }
}
