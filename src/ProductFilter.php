<?php

namespace App;

use App\Repository\ProductRepository;

class ProductFilter
{
    protected array $colors = [];
    protected array $materials = [];
    protected int $availability = -1;
    protected int $minPrice = 0;
    protected int $maxPrice = 0;
    public function __construct(
        protected ProductRepository $productRepository,
        protected string $brand,
        protected array $parameters,
    )
    {
    }

    public function getProducts() : array {
        $this->parse();
        return $this->productRepository->
            findAllProductsInBrand(
                $this->brand,
                $this->colors,
                $this->minPrice,
                $this->maxPrice,
                $this->materials,
                $this->availability
            );
    }

    protected function parse() : void{
        extract($this->parameters);
        $this->maxPrice = $this->productRepository->findMaxPrice();
        if(isset($colors))
            $this->colors = $colors;
        if(isset($materials))
            $this->materials = $materials;
        if(isset($availability))
            $this->availability = $availability;
        if(isset($minPrice))
            $this->minPrice = $minPrice;
        if(isset($maxPrice))
            $this->maxPrice = $maxPrice;
    }
}