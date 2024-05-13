<?php

namespace App\Controller;

use App\ProductFilter;
use App\Repository\ProductRepository;
use phpDocumentor\Reflection\DocBlock\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\CsvEncoder;

class BrandsController extends AbstractController
{
    protected string $brand = '';
    protected array $products = [];
    public function __construct(
        protected ProductRepository $productRepository,
    )
    {
    }

    #[Route('/', name: 'app')]
    public function index(): Response
    {
        $brands = $this->productRepository->findAllBrands() ?? null;
        if(empty($brands))
        {
            return $this->redirectToRoute('app_upload_csv');
        }
        return $this->render('brands/index.html.twig', [
            'brands' => $brands,
        ]);
    }
    #[Route('/catalog/{brand}', name: 'app_brand_full', methods: ['GET'])]
    public function brand_full(string $brand = 'Vans', Request $request): Response
    {
//        dd($this->productRepository->findAllColors());
        $this->brand = $brand;
        $this->products = (new ProductFilter($this->productRepository, $this->brand, $request->query->all()))
            ->getProducts();
        return $this->render('brands/brand.html.twig', [
            'brand' => $this->brand,
            'products' => $this->products,
            'colors' => $this->productRepository->findAllColors(),
            'materials' => $this->productRepository->findAllMaterials(),
            'maxPrice' => $this->productRepository->findMaxPrice(),
        ]);
    }

    #[Route('/catalog/{brand}/download', name: 'app_brand_download', methods: ['GET'])]
    public function download(string $brand): Response
    {

        return $this->render('brands/brand.html.twig', [
            'brand' => $this->brand,
            'products' => $products,
        ]);
    }
}