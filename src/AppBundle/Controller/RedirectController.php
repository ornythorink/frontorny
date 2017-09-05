<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;
use AppBundle\Utils\Configuration;

class RedirectController extends Controller
{
    /**
     * @Route("/f/{id}", name="redirect")
     */
    public function indexAction(Request $request, $id)
    {
        $locale = $request->attributes->get('_locale');

        $client = new Client();
        $json = $client->request('GET',
            Configuration::getApiUrl( $this->container->get('kernel')->getEnvironment() )
            . $locale .'/products/' . $id);
        $decoded = json_decode($json->getBody());

        if($decoded === null)
        {
            return $this->redirectToRoute('homepage', array(), 301);
        }

        return $this->render('AppBundle:Default:redirect.html.twig',
            array(
                'locale'      => $locale,
                'url'         => $decoded->url,
            )
        );
    }
}
