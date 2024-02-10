<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Img;
use App\Repository\ImgRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

//use Symfony\Component\Uid\Uuid;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelectorConverter;

use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Mime\MimeTypeGuesser;

class ImgController extends AbstractController
{
    #[Route('/img', name: 'app_img')]
    public function index(EntityManagerInterface $entityManager): Response
    {
          $form = $this->createFormBuilder()
            ->add('picurl', TextType::class)
			->setAction($this->generateUrl('addurl'))
			->setMethod('POST')

            ->add('save', SubmitType::class, ['label' => 'Go'])
            ->getForm();
		
		
		
		
		return $this->render('img/index.html.twig', [
            'controller_name' => 'ImgController',
			'form' => $form->createView(),
			'rs1'=>'Upload images'
        ]);
    }
	
	
	public function removeLastOccurrence($haystack, $needle) {
		// Find the position of the last occurrence of the needle in the haystack
		$lastPos = strrpos($haystack, $needle);

		// If the needle is found
		if ($lastPos !== false) {
			// Remove the last occurrence by replacing it with an empty string
			return substr_replace($haystack, '', $lastPos, strlen($needle));
		}

		// If the needle is not found, return the original string
		return $haystack;
	}
	
	public function getLastPart($string, $delimiter) {
    // Find the last occurrence of the delimiter within the string
		$lastDelimiterPos = strrpos($string, $delimiter);
		
		// If the delimiter is found
		if ($lastDelimiterPos !== false) {
			// Return the portion of the string after the last occurrence of the delimiter
			return substr($string, $lastDelimiterPos + strlen($delimiter));
		}
		
		// If the delimiter is not found, return the original string
		return $string;
	}
	
	
	#[Route('/imgx/addurl',  name: 'addurl',  methods: ['POST', 'GET'])]
    public function addurl(EntityManagerInterface $entityManager, Request $request): Response
    {

		$url = $request->get('form')['picurl'];	
		//$$url = "https://mayak.travel/";		
		//echo "url $url <br> ";
		$client = HttpClient::create();

        // Fetch HTML content from the external URL
        $response = $client->request('GET', $url);
        $htmlContent = $response->getContent();
		
		
		//$mimeTypes = MimeTypes::getDefault();

		//echo "$htmlContent";

        // Create a new instance of the DomCrawler
        



		if ($response->getStatusCode() === 200) 
		{
           //echo " Yes : $url <br>";
		   $crawler = new Crawler($htmlContent);

            // Generate a unique filename
			
			/*
            $filename = Uuid::v4() . '.jpg'; // Example: abc123.jpg

            // Define the directory to save the image
            $directory = $this->getParameter('kernel.project_dir') . '/public/uploads/images';// Get this parameter from your services.yaml

            // Save the image to the directory
            $filesystem = new Filesystem();
            $filesystem->dumpFile($directory.'/'.$filename, $content);
			
            // Optionally, save the filename to the database
			*/
			$crawler1 = new Crawler($htmlContent);
			$crawler = $crawler1->getNode(0);

			$images = $crawler1->filter('img');
			//$images = $domElement->filter('img')->extract(['src']);
			//var_dump($images);

			// Process the extracted image URLs
			foreach ($images as $image1) 
			{	
				
				$src = $image1->getAttribute('src');
				$filename = $this->getLastPart($src, "/");
				$url1 =  $this->removeLastOccurrence($url, "/"); 
				$src = $url1.$src;
				
				try {
					$mimeTypes = MimeTypes::getDefault();
					$mimeType = $mimeTypes->guessMimeType($src);
					$isImage = $mimeTypes->isGuesserSupported() && MimeTypeGuesser::getInstance()->isMimeTypeImage($mimeType);
				} catch (\Exception $e) {
					error_log($e->getMessage());
					$isImage = false;
				}
				
				
				

				// Check if the MIME type represents an image
				
				try
				{
					$size = getimagesize($src);
					if( !is_null($size)&& is_array($size) && $size > 0 )
					{	
						$size1 = $size[1];	
						$imagedatabase = new Img();
						$imagedatabase->setPicurl($src);
						$imagedatabase->setPicsize($size1);
						$entityManager->persist($imagedatabase);
						$entityManager->flush();	
					}
				}
				catch(\Exception $e)
				{
					error_log($e->getMessage());
				}
			}

			$rs1 = " All OK ";   
        } 
		else 
		{
            $rs1 = "failed";
			//echo "failed";
        }
	
		return $this->redirect($this->generateUrl('img_result', 
		));
		
    }
	
	
	#[Route('/imgresult', name: 'img_result')]
    public function indexresult(Request $request, EntityManagerInterface $entityManager , ImgRepository $imgRepository , PaginatorInterface $paginator): Response
    {

		$query = $entityManager->getRepository(Img::class)->findAll();
		//$images = $imgRepository->findAllPaginated($request->query->getInt('page', 1));
		//$images = $entityManager->getRepository(Img::class)->findAllPaginated($request->query->getInt('page', 1));
		$kilobytes = 0;

		$images = $paginator->paginate(
			$query, // Query to paginate
			$request->query->getInt('page', 1), // Page number
			12 // Limit per page
		);
		
		foreach ($query as $img) {
            $kilobytes += $img->getPicsize();
        }
		
		
		
		return $this->render('img/images.html.twig', [
            'controller_name' => 'ImgController',
			'images' => $images,
			'kilobytes' => $kilobytes,
        ]);
    }
	
	
	
	
}
