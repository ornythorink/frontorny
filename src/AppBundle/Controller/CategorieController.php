<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Utils\Configuration;


class CategorieController extends Controller
{
    /**
     * @Route("/c/{slug}/{page}", name="categorie", defaults={"page" = 1})
     */
    public function indexAction(Request $request, $slug, $page)
    {
        $locale = $request->attributes->get('_locale');
        $request->setLocale('fr');

        //$agent = $_SERVER['HTTP_USER_AGENT'] ;
        //$ip = $_SERVER['REMOTE_ADDR'];

        $client = new Client();

        $json = $client->request('GET',
            Configuration::getApiUrl( $this->container->get('kernel')->getEnvironment() )
            . $locale .'/category/sdc/'
            . $slug );

        $decoded = json_decode($json->getBody());

        $adapter = new ArrayAdapter($decoded);
        $pagerfanta = new Pagerfanta($adapter);

        $pagerfanta->setMaxPerPage(20); // 10 by default
        $maxPerPage = $pagerfanta->getMaxPerPage();

        try  {
            $pagerfanta->setCurrentPage($page);
        }catch(NotValidCurrentPageException $e) {
            throw new NotFoundHttpException('Illegal page');
        }

        $currentPage = $pagerfanta->getCurrentPage();

        $nbResults = $pagerfanta->getNbResults();
        $currentPageResults = $pagerfanta->getCurrentPageResults();

        $pagerfanta->getNbPages();

        $pagerfanta->haveToPaginate(); // whether the number of results if higher than the max per page

        $jsonsubcat = $client->request('GET',
            Configuration::getApiUrl( $this->container->get('kernel')->getEnvironment() )
            . $locale .'/category/sub/'. $slug );
        $decodedCat = json_decode($jsonsubcat->getBody());

        // replace this example code with whatever you need
        return $this->render('AppBundle:Default:category.html.twig',
            array(
                'locale'      => $locale,
                'slug'        =>$slug,       /* @todo pourquoi passer item ? */
                'items'       => array(),    /* @todo idem pour item */
                'brandFilter' => array(),
                'priceFilter' => array(),
                'pagination' => $pagerfanta,
                'subcat'      => $decodedCat
            )
        );
    }
    /**
     * @Route("/l/{id}", name="linkoffer" ,   options = { "expose" = true }),
     *
     */
    public function countLinkedAction(Request $request, $id)
    {
        $locale = $request->attributes->get('_locale');
        $request->setLocale('fr');
        $client = new Client();

        $json = $client->request('GET',
            Configuration::getApiUrl( $this->container->get('kernel')->getEnvironment() )
            . $locale .'/linked/'. $id);
        $decoded = json_decode($json->getBody()->getContents() );

        //var_dump($decoded);

        $count = count($decoded[0]->offers);
        return new JsonResponse($count);
    }

}
