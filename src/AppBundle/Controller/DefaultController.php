<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {

        $locale = $request->attributes->get('_locale');
        $request->setLocale('fr');


        //$agent = $_SERVER['HTTP_USER_AGENT'] ;
        //$ip = $_SERVER['REMOTE_ADDR'];

        $client = new Client();

        $json = $client->request('GET','http://127.0.0.1:8001/{locale}/category/root');

        $decoded = json_decode($json->getBody());
// var_dump($decoded);exit;
        return $this->render('AppBundle:Default:index.html.twig',
            array(
                'items'       => array(),
                'brandFilter' => array(),
                'priceFilter' => array(),
                'locale' => $locale,
                'rootcat' => $decoded
            )
        );
    }
}
