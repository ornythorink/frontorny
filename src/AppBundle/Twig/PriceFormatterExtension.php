<?php

namespace AppBundle\Twig;

class PriceFormatterExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('priceformatter', array($this, 'priceformatterFilter')),
        );
    }

    public function priceformatterFilter($number, $decimals = 2, $decPoint = '.', $thousandsSep = ',')
    {
            $price = number_format($number, $decimals, $decPoint, $thousandsSep);
            $price = $price . ' €' ;

        return $price;
    }

    public function getName()
    {
        return 'priceformatter';
    }
}