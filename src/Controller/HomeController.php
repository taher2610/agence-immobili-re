<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annontation\Route;
use Twig\Environement;
use App\Repository\PropertyRepository;

class HomeController extends AbstractController
{
    /** 
    *  @var Environement
    *  @return Response
    */
    private $twig;
    public function __construct($twig)
    {
        $this->twig=$twig;
    }


    public function index(PropertyRepository $repository):Response
    {
        $properties = $repository->findLatest();
        return $this->render('pages/home.html.twig', ['properties' => $properties]);
    }

}