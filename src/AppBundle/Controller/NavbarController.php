<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;

class NavbarController extends Controller
{
    public function indexAction(Request $request)
    {
        $locale = $request->attributes->get('_locale');
        $client = new Client();
        $response = $client->get('http://127.0.0.1:8001/'. $locale .'/category/root');

        $rootCategories = json_decode($response->getBody()->getContents() ,true );
        return $this->render('AppBundle:Default:navbar.html.twig',
            array(
                'categories' => $rootCategories,
            )
        );
    }
}
