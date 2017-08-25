<?php

namespace AppBundle\Twig;

class UnicodeExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('unicode', array($this, 'unicodeFilter')),
        );
    }

    public function unicodeFilter($name, $source)
    {
        if($source == 'TDD')
        {
            $name = utf8_encode(utf8_decode(iconv("UTF-8", "ISO-8859-1//TRANSLIT", $name)));
        }
        elseif ($source == 'SDC') {
             $name = utf8_encode(iconv("UTF-8", "ISO-8859-1//TRANSLIT", $name));
        }


        return $name;
    }

    public function getName()
    {
        return 'unicode';
    }
}