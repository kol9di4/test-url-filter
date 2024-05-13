<?php

namespace App\Controller;

use App\ProductFilter;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BrandsController extends AbstractController
{
    public function __construct(
        protected ProductRepository $productRepository,
    )
    {
    }

    #[Route('/', name: 'app')]
    public function index(): Response
    {
        $brands = $this->productRepository->findAllBrands() ?? null;
        if (empty($brands)) {
            return $this->redirectToRoute('app_upload_csv');
        }
        return $this->render('brands/index.html.twig', [
            'brands' => $brands,
        ]);
    }

    #[Route('/catalog/{brand}', name: 'app_brand_full', methods: ['GET'])]
    public function brand_full(string $brand = 'Vans', Request $request): Response
    {
        $products = (new ProductFilter($this->productRepository, $brand, $request->query->all()))
            ->getProducts();
        return $this->render('brands/brand.html.twig', [
            'brand' => $brand,
            'products' => $products,
            'colors' => $this->productRepository->findAllColors(),
            'materials' => $this->productRepository->findAllMaterials(),
            'maxPrice' => $this->productRepository->findMaxPrice(),
        ]);
    }

    #[Route('/catalog/{brand}/download', name: 'app_brand_download', methods: ['GET','POST'])]
    public function download(string $brand, Request $request): StreamedResponse
    {
        $parameters = $request->query->all();
        $products = (new ProductFilter($this->productRepository, $brand, $parameters))
            ->getProducts();
        $newArr = [];
        $newArr[] = ['Brand','Product','Price','Material','Color'];
        foreach ($products as $product) {
            $newArr[] = [$product->getBrand(), $product->getProduct(), $product->getPrice(), $product->getMaterial(), $product->getColor()];
        }
        $buffer = fopen(__DIR__ . '/file.csv', 'w');
        fputs($buffer, chr(0xEF) . chr(0xBB) . chr(0xBF));
        foreach ($newArr as $val) {
            fputcsv($buffer, $val, ';');
        }
        fclose($buffer);
        $response = new StreamedResponse(function () {
            // opening a stream to file with reading permissions
            $stream = fopen(__DIR__ . '/file.csv', 'r');
            while (!feof($stream)) {
                echo fread($stream, 1024); // Reading by 1KB from file
                flush(); // Forcing output to buffer
            }
            fclose($stream); // Closing the stream
        });

        // Setting the response headers
        $response->headers->set('Content-Type', 'File Transfer');
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$brand.'.csv"');

        return $response;
    }
}