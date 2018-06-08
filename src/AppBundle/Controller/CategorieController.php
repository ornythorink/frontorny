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
    public function indexAction(Request $request, $slug, $page, $filter= null)
    {
        // @todo ne pas oublier le forçage de la locale et gérer les traductions en
        $locale = $request->attributes->get('_locale');
        $request->setLocale('fr');

        $client = new Client();

        $json = $client->request('GET',
            Configuration::getApiUrl( $this->container->get('kernel')->getEnvironment() )
            . $locale .'/category/products/'
            . $slug );

        $decoded = json_decode($json->getBody());

        // @todo stocker l'ensemble des produits pour les filtrer
//        echo '<pre>';
//      var_dump($decoded->products);exit;
//        echo '</pre>';

        $stopwords_json = $client->request('GET',
            Configuration::getApiUrl( $this->container->get('kernel')->getEnvironment() )
            . $locale .'/stopwordsbyslug/'
            . $slug );
        $stopwords = json_decode($stopwords_json->getBody());

//        echo '<pre>';
//      var_dump($stopwords);exit;
//        echo '</pre>';
        $stoplist = [];
        foreach($stopwords as $key => $word){
            $stoplist[] = $word;
        }

        foreach($decoded->products as $key => &$value){
            foreach($stoplist as $stop){
                if(isset($value))
                {
                    if(stripos($value->name, $stop->stopword) !== false){
                        unset($decoded->products[$key]);
                    }
                }

            }
        }

        //try if null
        $adapter = new ArrayAdapter($decoded->products);
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

         $jsoncrump = $client->request('GET',
            Configuration::getApiUrl( $this->container->get('kernel')->getEnvironment() )
            . $locale .'/category/breadcrump/'. $slug );

        $breadcrump = json_decode($jsoncrump->getBody());
//        echo "<pre>";
//        var_dump($breadcrump);exit;
//        echo "</pre>";

        // replace this example code with whatever you need
        return $this->render('AppBundle:Default:category.html.twig',
            array(
                'locale'      => $locale,
                'slug'        => $slug,       /* @todo pourquoi passer item ? */
                'breadcrump'  => $breadcrump,
                'brandFilter' => (array) $decoded->metadata->brands, /* @todo pourquoi stdclass */
                'priceFilter' => array(),
                'pagination'  => $pagerfanta,
                'subcat'      => $decodedCat,

            )
        );
    }

    /**
     * @Route("/c/filter/{slug}/{page}/", name="filtercategory" , defaults={"page" = 1})
     *
     */
    public function filterAction(Request $request, $slug, $page)
    {
        // @todo ne pas oublier le forçage de la locale et gérer les traductions en
        $locale = $request->attributes->get('_locale');
        $request->setLocale('fr');

        $client = new Client();

        $json = $client->request('GET',
            Configuration::getApiUrl(
                $this->container->get('kernel')->getEnvironment() )
            . $locale .'/category/products/'
            . $slug );

        $decoded = json_decode($json->getBody());
        $adapter = new ArrayAdapter($decoded->products);
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
                'brandFilter' => (array) $decoded->metadata->brands, /* @todo pourquoi stdclass */
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
