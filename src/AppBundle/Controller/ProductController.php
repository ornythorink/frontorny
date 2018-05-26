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
    public function indexAction( Request $request, $slug, $id)
    {
        // @todo ne pas oublier le forçage de la locale et gérer les traductions en
        $locale = $request->attributes->get('_locale');
        $client = new Client();
        $request->setLocale('fr');

        $client = new Client();

        $json = $client->request('GET',
            Configuration::getApiUrl( $this->container->get('kernel')->getEnvironment() )
            . $locale .'/product/full/' . $id);

        $decoded = json_decode($json->getBody());
 ;
        return $this->render('AppBundle:Default:product.html.twig',
            array(
                'locale'      => $locale,
                'item'       => $decoded[0],
                'brandFilter' => array(),
                'priceFilter' => array(),
            )
        );
    }
}
