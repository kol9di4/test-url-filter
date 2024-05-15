<?php

namespace App\Controller;

use App\Entity\Product;
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
    #[Route('/catalog', name: 'app_catalog')]
    public function catalog(): Response
    {
        return $this->redirectToRoute('app');
    }

    #[Route('/catalog/{brand}', name: 'app_brand_full', methods: ['GET','POST'])]
    public function brand_full(string $brand, Request $request): Response
    {
        $isBrandExists = count($this->productRepository->isBrandsExists($brand))>0;
        if (!$isBrandExists) {
            return $this->redirectToRoute('app');
        }
        $products = (new ProductFilter($this->productRepository, $brand, $request->query->all()))->getProducts();
        if ($request->isMethod('POST')) {
            $products = (new ProductFilter($this->productRepository, $brand, $request->request->all()))->getProducts();
//            dd($products);
            return $this->render('brands/products.html.twig', [
                'products' => $products,
            ]);
        }
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
        $products = (new ProductFilter($this->productRepository, $brand, $parameters))->getProducts();
        $productsForCVS = $this->normalizeProducts($products);
        $response = $this->setResponseForDownloadFile($productsForCVS, $brand);
        return $response;
    }

    protected function normalizeProducts(array $products) : array{
        $newArr = [];
        $newArr[] = ['Brand','Product','Price','Material','Color'];
        foreach ($products as $product) {
            $newArr[] = [$product->getBrand(), $product->getProduct(), $product->getPrice(), $product->getMaterial(), $product->getColor()];
        }
        return $newArr;
    }
    protected function setResponseForDownloadFile(array $array, string $filename) : StreamedResponse{
        $response = new StreamedResponse(function () use ($array) {
            $buffer = fopen('php://output', 'w');
            fputs($buffer, chr(0xEF) . chr(0xBB) . chr(0xBF));
            foreach($array as $val) {
                fputcsv($buffer, $val, ';');
            }
            fclose($buffer);
        });
        $response = $this->setHeadersForDonwloadFile($response, $filename);

        return $response;
    }
    private function setHeadersForDonwloadFile(StreamedResponse $response, string $filename) : StreamedResponse {
        $response->headers->set('Content-Type', 'File Transfer');
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'.csv"');
        return $response;
    }
}