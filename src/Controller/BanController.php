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
use App\Repository\PlayerRepository;
use App\Repository\ServerRepository;
use App\Repository\ImageRepository;
use App\Entity\Ban;
use App\Mojang\MojangAPI;
use App\Entity\Player;
use App\Entity\Image;

/**
* Class BanController
* @package App\Controller
* @Route("/ban", name="ban")
*/
class BanController extends AbstractController
{
	/**
	* Load the site definition and redirect to the default page.
	*
	* @Route("/checkUUID", name="_checkUuid")
	*/
	public function checkUuidAction( Request $request, PlayerRepository $playerRepository )
	{		
		$this->denyAccessUnlessGranted('ROLE_ADMIN');
		
		$entityManager = $this -> getDoctrine() -> getManager();	
		$nickname = $request -> get('nickname');
		
		$player = $playerRepository -> findOneBy(array('name' => $nickname));
		if($player) {
			$uuid = $player -> getUuid();
		} else {
			$uuid = MojangAPI::formatUuid(MojangAPI::getUuid($nickname));
		}
		
		$response = new Response();
		$response -> setContent(json_encode($uuid));
		$response -> headers -> set('Content-Type', 'application/json');
		return $response;
	}
	
	
	/**
	* Load the site definition and redirect to the default page.
	*
	* @Route("/new", name="_new")
	*/
	public function addNewBanAction( Request $request, PlayerRepository $playerRepository, ServerRepository $serverRepository, UrlGeneratorInterface $urlGenerator )
	{		
		$this->denyAccessUnlessGranted('ROLE_ADMIN');
		
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, POST');
		header("Access-Control-Allow-Headers: X-Requested-With");
	
		// Doctrine connection manager
		$entityManager = $this -> getDoctrine() -> getManager();
		$connection = $entityManager -> getConnection();
		
		$nickname 			= $request -> get('nickname');
		$startDate 			= $request -> get('start_date');
		$startTime 			= $request -> get('start_time');
		$isPerma 			= $request -> get('is_perma');
		$endDateMonths 		= $request -> get('end_date_months');
		$endDateDays 		= $request -> get('end_date_days');
		$endDateHours 		= $request -> get('end_date_hours');
		$endDateMinutes 	= $request -> get('end_date_minutes');
		$reason 			= $request -> get('reason');
		$description 		= $request -> get('description');
		$idServer 			= $request -> get('server');
		
		// Controllo se esiste un player
		// per il nickname inserito
		$player = $playerRepository -> findOneBy(array('name' => $nickname));
		if(!$player) {
			$player = new Player();
			$player -> setUuid(MojangAPI::formatUuid(MojangAPI::getUuid($nickname)));
			$player -> setName($nickname);
			$player -> setReputation(100);
			$player -> setCreated(new \DateTime('now'));
			
			$entityManager -> persist($player);
			$entityManager -> flush();
		}
		
		$sessionUsername = $this -> getUser() -> getUsername();
		$operator = $playerRepository -> findOneBy(array('name' => $sessionUsername));
		if(!$operator) {
			$operator = new Player();
			$operator -> setUuid(MojangAPI::formatUuid(MojangAPI::getUuid($sessionUsername)));
			$operator -> setName($sessionUsername);
			$operator -> setReputation(100);
			$operator -> setCreated(new \DateTime('now'));
			
			$entityManager -> persist($operator);
			$entityManager -> flush();
		}
		
		$server = $serverRepository -> find($idServer);
		
		$ban = new Ban();
		$ban -> setIdPlayer($player);
		$ban -> setIdServer($server);
		$ban -> setIdOperator($operator);
		$ban -> setStartDate(new \DateTime($startDate . " " . $startTime));
		$ban -> setIsPerma($isPerma);
		if(!$isPerma) {
			$startDateCopy = new \DateTime($startDate . " " . $startTime);
			if(!empty($endDateMonths)) {
				$startDateCopy = $startDateCopy -> modify("+" . $endDateMonths . " months");
			}
			if(!empty($endDateDays)) {
				$startDateCopy = $startDateCopy -> modify("+" . $endDateDays . " days");
			}
			if(!empty($endDateHours)) {
				$startDateCopy = $startDateCopy -> modify("+" . $endDateHours . " hours");
			}
			if(!empty($endDateMinutes)) {
				$startDateCopy = $startDateCopy -> modify("+" . $endDateMinutes . " minutes");
			}
			$ban -> setEndDate($startDateCopy);
		}
		$ban -> setReason($reason);
		$ban -> setDescription($description);
		$ban -> setImagePassword('ciao');
		$ban -> setCreated(new \DateTime('now'));
		$ban -> setIsApplied(false);
		
		$entityManager -> persist($ban);
		$entityManager -> flush();
		
		return new RedirectResponse($urlGenerator->generate('homepage', array('successMessage' => "Ban inserito con successo")));
	}
	
	/**
	* Load the site definition and redirect to the default page.
	*
	* @Route("/view", name="_view")
	*/
	public function viewBan( Request $request, BanRepository $banRepository, UrlGeneratorInterface $urlGenerator )
	{	
		$idBan = $request -> get('id');
		$ban = $banRepository -> find($idBan);
		
		$isDarkmode = $request -> cookies -> get('dark_mode');
		
		return $this->render('ban/ban.html.twig', [
			'ban' => $ban,
			'darkMode' => $isDarkmode
        ]);
	}
	
	/**
	* Load the site definition and redirect to the default page.
	*
	* @Route("/delete", name="_delete")
	*/
	public function deleteAction( Request $request, BanRepository $banRepository, UrlGeneratorInterface $urlGenerator )
	{		
		$this->denyAccessUnlessGranted('ROLE_ADMIN');
		
		$entityManager = $this -> getDoctrine() -> getManager();	
		$banId = $request -> get('ban_id');
		
		$ban = $banRepository -> find($banId);
		if($ban) {
			$ban -> setFgDeleted(true);
			$entityManager -> persist($ban);
			$entityManager -> flush();
		}
		
		return new RedirectResponse($urlGenerator->generate('homepage'));
	}
	
	/**
	* Load the site definition and redirect to the default page.
	*
	* @Route("/addImageToBan/{id}", name="_addImageToBan")
	*/
	public function addImageToBanAction( Request $request, $id, BanRepository $banRepository ){
		
		$this -> denyAccessUnlessGranted('ROLE_ADMIN');
		
		$entityManager = $this -> getDoctrine() -> getManager();
		
		$response = new Response();
		$status = 1;
		$message = "Picture uploaded successfully";
		
		foreach( $_FILES as $name => $file ){
			$check = getimagesize($file["tmp_name"]);
		
			// Check if the file is an image
			if( $check === false ){
				$status = 0;
				$message = "The uploaded file is not a picture";
				
				$responseArray = array( 'status' => $status, 'message' => $message );
				$response -> setContent( json_encode($responseArray) );
				return $response;
			}
				
			// Get the temp file path
			$uploadedImage = file_get_contents( $file["tmp_name"] );
			$ext = substr(strrchr($file["name"], '.'), 1);
			
			$newFileName = "ban_".$id."_picture_" . (new \DateTime('now')) -> format("d_m_Y_H_i_s") . "." . $ext;
			$newPath = $this->getParameter('ban_images_directory') . "/" . $newFileName;
			
			file_put_contents($newPath, $uploadedImage);
			
			$ban = $banRepository -> find( $id );
			
			$image = new Image();
			$image -> setPath($newPath);
			$image -> setCreated(new \DateTime('now'));
			$image -> setBan($ban);
			$image -> setName($newFileName);
			
			$entityManager -> persist( $image );
			$entityManager -> flush();
		}
		
		$responseArray = array( 'status' => $status, 'message' => $message );
		$response -> setContent( json_encode($responseArray) );
		return $response;
		
	}
	
	/**
	* Load the site definition and redirect to the default page.
	*
	* @Route("/deleteImageBan", name="_deleteImageBan")
	*/
	public function deleteImageBanAction( Request $request, ImageRepository $imageRepository ){
		
		$this->denyAccessUnlessGranted('ROLE_ADMIN');
		
		$entityManager = $this -> getDoctrine() -> getManager();
		
		$image = $imageRepository -> find( $request -> get('id') );
		$idBan = $image -> getBan() -> getId();
		
		if (!empty($image)){
			$entityManager -> remove( $image );
			$entityManager -> flush();
		}
		
		return $this -> redirect( $this -> generateUrl( 'ban_view', array('id' => $idBan) ) );
		
	}
	
}