<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;
use AppBundle\Utils\Configuration;

class NavbarController extends Controller
{
    public function indexAction(Request $request)
    {
        $locale = $request->attributes->get('_locale');
        $client = new Client();
        $response = $client->get(Configuration::getApiUrl( $this->container->get('kernel')->getEnvironment())
         . $locale .'/category/root');

        $rootCategories = json_decode($response->getBody()->getContents() ,true );
        return $this->render('AppBundle:Default:navbar.html.twig',
            array(
                'categories' => $rootCategories,
            )
        );
    }
}
