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
use App\Repository\UserRepository;
use App\Repository\ServerRepository;
use App\Entity\User;
use App\Entity\Ban;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Cookie;

class DefaultController extends AbstractController
{
	/**
	* Load the site definition and redirect to the default page.
	*
	* @Route("/", name="homepage")
	*/
	public function homepageAction( Request $request, BanRepository $banRepository, ServerRepository $serverRepository, UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder )
	{		
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, POST');
		header("Access-Control-Allow-Headers: X-Requested-With");
	
		// Doctrine connection manager
		$entityManager = $this -> getDoctrine() -> getManager();
		$connection = $entityManager -> getConnection();
		
		$currentPage = 1;
		if(!empty($request -> get('page'))) {
			$currentPage = $request -> get('page');
		}
		
		$pageLimit = 5;
		if(!empty($request -> get('page_limit'))) {
			$currentPage = $request -> get('page_limit');
		}
		
		$q 			= $request -> get('q');
		$sql 		= 	"SELECT ban
						FROM App\Entity\Ban ban
						WHERE ban.fg_deleted is NULL OR ban.fg_deleted = 0
						ORDER BY ban.startDate ASC";
		if(!empty($q)) {
			$sql = "SELECT ban
					 FROM App\Entity\Ban ban
					 JOIN App\Entity\Player player WITH player.id = ban.idPlayer
					 WHERE lower(player.name) LIKE :playerName
					 AND ban.fg_deleted is NULL OR ban.fg_deleted = 0
					 GROUP BY ban
					 ORDER BY ban.startDate ASC";
		}
		
		$query = $entityManager->createQuery(
            $sql
        );
		
		if(!empty($q)) {
			$query->setParameter('playerName', $q);
		}
		
		$banCount 		= $query -> getResult();
		$totalPages 	= ceil(sizeof($banCount) / $pageLimit);
		
		$query -> setMaxResults($pageLimit);
		$query -> setFirstResult($pageLimit * ($currentPage - 1));
		
        $banList =  $query->getResult();
		
		$serverList = $serverRepository -> findAll();
		
		$isDarkmode = $request -> cookies -> get('dark_mode');
		
		$visiblePages = [];
		if($currentPage - 1 > 0) {
			$visiblePages[] = $currentPage - 1;
		}
		$visiblePages[] = $currentPage;
		if($currentPage + 1 <= $totalPages) {
			$visiblePages[] = $currentPage + 1;
		}
		
		
		return $this->render('default/home.html.twig', [
			'bans' => $banList,
			'serverList' => $serverList,
			'successMessage' => !empty($request -> get('successMessage')) ? $request -> get('successMessage') : null,
			'darkMode' => $isDarkmode,
			'totalPages' => $totalPages,
			'currentPage' => $currentPage,
			'visiblePages' => $visiblePages
        ]);
	}
	
	/**
	* Load the site definition and redirect to the default page.
	*
	* @Route("/toggleDarkMode", name="toggle_darkmode")
	*/
	public function toggleDarkModeAction( Request $request, UrlGeneratorInterface $urlGenerator )
	{			
		// Doctrine connection manager
		$entityManager = $this -> getDoctrine() -> getManager();
		$connection = $entityManager -> getConnection();
		
		$isDarkmode = $request -> get('is_dark_mode');
				
		$response = new RedirectResponse($urlGenerator->generate('homepage'));
		
		$cookie = Cookie::create('dark_mode')
				->withValue($isDarkmode)
				->withExpires((new \DateTime('now'))->modify('+1 week'))
				->withDomain('banlog.forgottenworld.it')
				->withSecure(true);
	
		$response -> headers -> clearCookie('dark_mode');
		$response -> headers -> setCookie($cookie);
		return $response;
	}
	
	/**
	* Load the site definition and redirect to the default page.
	*
	* @Route("/searchPlayer", name="search_player")
	*/
	public function searchPlayerAction( Request $request )
	{			
		// Doctrine connection manager
		$entityManager = $this -> getDoctrine() -> getManager();
		$connection = $entityManager -> getConnection();
		
		$term = strtolower($request -> get('q'));
				
		$sql = 	"SELECT name, concat('Ban totali: ', count(ban.id)), concat('https://crafatar.com/renders/head/',uuid,'.png') as image
				 FROM player player
				 JOIN ban ban ON ban.id_player_id = player.id
				 WHERE lower(name) LIKE '%$term%'
				 GROUP BY player.id";
		$query = $connection -> prepare($sql);
		$query -> execute();
		$result = $query -> fetchAll();
		
		$responseArray = array();
		$responseArray['items'] = $result;
		
		$response = new Response();
		$response -> setContent(json_encode($responseArray));
		$response -> headers -> set('Content-Type', 'application/json');
		return $response;
	}
	
}