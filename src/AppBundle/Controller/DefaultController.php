<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $locale = $request->attributes->get('_locale');

        return $this->render('AppBundle:Default:index.html.twig',
            array(
                'items'       => array(),
                'brandFilter' => array(),
                'priceFilter' => array(),
                'locale' => $locale,
            )
        );
    }
}
