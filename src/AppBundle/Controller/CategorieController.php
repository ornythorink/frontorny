<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use GuzzleHttp\Client;

class CategorieController extends Controller
{
    /**
     * @Route("/c/{slug}/{page}", name="categorie", defaults={"page" = 1})
     */
    public function indexAction(Request $request, $slug, $page)
    {
        $locale = $request->attributes->get('_locale');
        //$agent = $_SERVER['HTTP_USER_AGENT'] ;
        //$ip = $_SERVER['REMOTE_ADDR'];

        $client = new Client();

        $json = $client->request('GET','http://127.0.0.1:8001/'. $locale .'/category/sdc/'
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


        // replace this example code with whatever you need
        return $this->render('AppBundle:Default:category.html.twig',
            array(
                'locale'      => $locale,
                'slug'        =>$slug,
                'items'       => array(),
                'brandFilter' => array(),
                'priceFilter' => array(),
                'pagination' => $pagerfanta,
            )
        );
    }
}
