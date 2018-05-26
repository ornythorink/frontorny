<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;
use AppBundle\Utils\Configuration;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
	$locale = $request->attributes->get('_locale');
        $request->setLocale('fr');
	    $jsonhit = [];
	    $hits = [];
        $client = new Client();

       $json = $client->request('GET',
            Configuration::getApiUrl( $this->container->get('kernel')->getEnvironment())
            .$locale.'/category/root');
        $decoded = [];
        $decoded = json_decode($json->getBody());

	try{
        $jsonhit = $client->request('GET',
            Configuration::getApiUrl( $this->container->get('kernel')->getEnvironment())
            .$locale.'/hits');
        $hits = json_decode($jsonhit->getBody());
	} catch(\Exception $e)
	{
	}

        echo '<pre>';
        //var_dump($hits);
        echo '</pre>';

        return $this->render('AppBundle:Default:index.html.twig',
            array(
                'hits'        => $hits,
                'items'       => array(),
                'brandFilter' => array(),
                'priceFilter' => array(),
                'locale'      => $locale,
                'rootcat'     => $decoded
            )
        );


    }


}
