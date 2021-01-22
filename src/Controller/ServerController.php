<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Zend\EventManager\Exception\RuntimeException;
use App\Repository\BanRepository;
use App\Entity\Server;

/**
* Class ServerController
* @package App\Controller
* @Route("/server", name="server")
*/
class ServerController extends AbstractController
{
	/**
	* Load the site definition and redirect to the default page.
	*
	* @Route("/new", name="_new")
	*/
	public function addNewServerAction( Request $request, UrlGeneratorInterface $urlGenerator )
	{		
		$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
		
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, POST');
		header("Access-Control-Allow-Headers: X-Requested-With");
	
		// Doctrine connection manager
		$entityManager = $this -> getDoctrine() -> getManager();
		$connection = $entityManager -> getConnection();
		
		$name = $request -> get('name');
		$description = $request -> get('description');
		$icon = $request -> get('icon');
		
		$server = new Server();
		$server -> setName($name);
		$server -> setDescription($description);
		$server -> setImage($icon);
		$server -> setCreated(new \DateTime('now'));
		
		$entityManager -> persist($server);
		$entityManager -> flush();
		
		return new RedirectResponse($urlGenerator->generate('homepage', array('successMessage' => "Server $name aggiunto con successo")));
	}
	
}