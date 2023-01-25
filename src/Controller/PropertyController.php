<?php
namespace App\Controller;
use App\Entity\contact;
use App\Form\ContactType;
use App\Notification\ContactNotification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Property;
use App\Repository\PropertyRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\PropertySearch;
use App\Form\PropertySearchType;
use ContainerUuk2nn8\getContactNotificationService;


class PropertyController extends AbstractController
{
    /**
     * @var PropertyRepository
     */
    /**
     * @var ObjectManager
     */

    private $repository;


    public function __construct(PropertyRepository $repository){
        $this->repository = $repository;
    }



    /**
     * @Route("/biens",name="property.index")
     * @return Response
     */

    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $search = new PropertySearch();
        $form = $this->createForm(PropertySearchType::class, $search);
        $form->handleRequest($request);

        $properties = $paginator->paginate(
            $this->repository->findAllVisibleQuery($search),
            $request->query->getInt('page',1),
            12
        );
        return $this->render('property/index.html.twig',[
            'current_menu' => 'properties',
            'properties' => $properties,
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/biens/{slug}-{id}", name="property.show" , requirements={"slug": "[a-z0-9\-]*"})
     * @return Response
     */

    public function show (Property $property, string $slug,$id, Request $request, ContactNotification $notification):Response
    {
        $contact = new contact();
        $contact ->setProperty($property);
        $form = $this->createForm(ContactType::class , $contact);


        if($property->getSlug() !== $slug){
            return $this->redirectToRoute('property.show' , [
                'id'=> $property->getId(),
                'slug'=> $property->getSlug()
            ],301);
        }
        $property = $this->repository->find($id);


        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $notification->notify($contact);
            $this->addFlash('sucess','votre mail a bien été envoyé');
            return $this->redirectToRoute('property.show' , [
                'id'=> $property->getId(),
                'slug'=> $property->getSlug()
            ]);
        }

        return $this->render('property/show.html.twig',['property' => $property,'current_menu' => 'properties'
            ,'form'=>$form->createView()
        ]);

    }

}