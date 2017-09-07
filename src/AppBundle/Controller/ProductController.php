<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use GuzzleHttp\Client;
use AppBundle\Utils\Configuration;

class ProductController extends Controller
{
    /**
     * @Route("/p/{slug}/{id}", name="product")
     */
    public function indexAction(Request $request, $slug, $id)
    {
        $locale = $request->attributes->get('_locale');
        $client = new Client();

        $json =
        $client->request('GET',
            Configuration::getApiUrl( $this->container->get('kernel')->getEnvironment() )
            . $locale .'/product/slug/'. $slug .'/id/' . $id );

        $decoded = json_decode($json->getBody());
        //echo  '<pre>';var_dump($decoded->products[0]);exit;
        return $this->render('AppBundle:Default:product.html.twig',
            array(
                'locale'      => $locale,
                'item'       => $decoded->products[0],
                'brandFilter' => array(),
                'priceFilter' => array(),
            )
        );
    }
}
