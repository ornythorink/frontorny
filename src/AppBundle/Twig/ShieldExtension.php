<?php

namespace AppBundle\Twig;
use AppBundle\Utils\Config;

class ShieldExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('shield', array($this, 'shieldFilter')),
        );
    }

    public function shieldFilter($name)
    {
        $target = substr(md5( 'KOK2' . $name ),1) . base64_encode($name);
        $page = Config::get('BASEURL') . '/f/' . $target;
        return $page;
    }

    public function getName()
    {
        return 'shield';
    }
}