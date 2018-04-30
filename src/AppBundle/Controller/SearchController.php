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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SearchController extends Controller
{
    private $router;
    public function __construct(UrlGeneratorInterface $router){
        $this->router = $router;

    }

    /**
     * @Route("/search/{page}/", name="search" , defaults={"page" = 1})
     */
    public function productbysearchAction(Request $request, $page = 1)
    {
        // @todo ne pas oublier le forçage de la locale et gérer les traductions en
        $locale = $request->attributes->get('_locale');
        $request->setLocale('fr');
        $client = new Client();

        $query = $request->query->get('query');
        $genre = $request->query->get('genre');

        $route = Configuration::getApiUrl($this->container->get('kernel')->getEnvironment())
            . $locale .'/search/?query='.$query .'&genre='.$genre;

        $json = $client->request('GET',
            $route);

        $decoded = json_decode($json->getBody());
        /*echo 'pre';
        var_dump($decoded);
        echo '/<pre>';*/
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


        $decoded = json_decode($json->getBody());

        return $this->render('AppBundle:Default:search.html.twig',
            array(
                'locale'      => $locale,
                'item'       => $decoded->products,
                'brandFilter' => array(),
                'priceFilter' => array(),
                'pagination'  => $pagerfanta
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
