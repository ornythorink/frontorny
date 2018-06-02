<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;
use AppBundle\Utils\Configuration;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;


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
    	} 
        catch(\Exception $e)
    	{

    	}

        $fileSystem = new Filesystem();
        foreach ($hits->products as $value) {
            try {

                    $exist = $fileSystem->exists( 'bundles/thumbs/' . md5($value->image));

            } catch (IOExceptionInterface $exception) {
                echo "An error occurred while searchinf file ".$exception->getPath();
            }

            // if image does not exist in cache
            if($exist === false )
            {
                // get the remote image
                $image = file_get_contents($value->image);

                // create destination file
                $fileSystem->touch( 'bundles/thumbs/' . md5($value->image));

                // write image
                $fileSystem->appendToFile(
                    'bundles/thumbs/' . md5($value->image) ,
                    $image
                );
                $fileSystem->chmod(
                    'bundles/thumbs/' .md5($value->image),
                    0755);

                $im = new \Imagick('bundles/thumbs/' .md5($value->image));
                $im->scaleImage(150, 150);
                $im->writeImage('bundles/thumbs/' .md5($value->image));

                $value->bigimage = 'bundles/thumbs/' . md5($value->image) ;
            } else {
                $value->bigimage = 'bundles/thumbs/' . md5($value->image) ;
            }
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
