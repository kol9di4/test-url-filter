<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class UploadCSVController extends AbstractController
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
    )
    {
    }

    #[Route('/upload-csv', name: 'app_upload_csv', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        if(($request->files->count()>0))
        {
            if($request->files->all()['csv']->getMimeType() === 'text/csv'){
                $pathFile = $request->files->all()['csv']->getRealPath();
                if (($handle = fopen($pathFile, "r")) !== FALSE) {
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $newProduct = new Product();
                        $newProduct->setBrand($data[0]);
                        $newProduct->setProduct($data[1]);
                        $newProduct->setPrice($data[2]);
                        $newProduct->setMaterial($data[3]);
                        $newProduct->setColor($data[4]);
                        $newProduct->setAvailability($data[5] == 'true' ? 1 : 0);
                        $entityManager->persist($newProduct);
                        $entityManager->flush();
                    }
                    fclose($handle);
                    return $this->redirectToRoute('app_upload_csv');

                }
            }
            else{
                $this->addFlash('error', 'Only CSV-files');
                return $this->redirectToRoute('app_upload_csv');
            }
//          dd(file_get_contents($file->getRealPath()));
        }
        return $this->render('upload_csv/index.html.twig', [
            'controller_name' => 'UploadCSVController',
        ]);
    }
}
