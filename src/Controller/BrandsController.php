<?php

namespace App\Controller;

use App\ProductFilter;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BrandsController extends AbstractController
{
    protected string $brand = '';
    public function __construct(
        protected ProductRepository $productRepository,
    )
    {
    }

    #[Route('/', name: 'app')]
    public function index(): Response
    {

        $brands = $this->productRepository->findAllBrands();
        return $this->render('brands/index.html.twig', [
            'brands' => $brands,
        ]);
    }
    #[Route('/catalog/{brand}', name: 'app_brand_full', methods: ['GET'])]
    public function brand_full(string $brand = 'Vans', Request $request): Response
    {
//        dd($this->productRepository->findAllColors());
        $this->brand = $brand;
        $products = (new ProductFilter($this->productRepository, $this->brand, $request->query->all()))
            ->getProducts();
        return $this->render('brands/brand.html.twig', [
            'brand' => $this->brand,
            'products' => $products,
            'colors' => $this->productRepository->findAllColors(),
            'materials' => $this->productRepository->findAllMaterials(),
            'maxPrice' => $this->productRepository->findMaxPrice(),
        ]);
    }

    #[Route('/catalog/{brand}/download', name: 'app_brand_download', methods: ['GET'])]
    public function download(string $brand): Response
    {
        $products = $this->productRepository->findAllProductsInBrand($this->brand);
        return $this->render('brands/brand.html.twig', [
            'brand' => $this->brand,
            'products' => $products,
        ]);
    }
}