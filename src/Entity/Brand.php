<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a bicycle brand.
 */
#[ORM\Entity]
#[ORM\Table(name: 'brands')]
class Brand
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'brand_id', type: 'integer')]
    private ?int $brandId = null;

    #[ORM\Column(name: 'brand_name', type: 'string', length: 255)]
    private string $brandName = '';

    /**
     * @var Collection<int, Product>
     */
    #[ORM\OneToMany(mappedBy: 'brand', targetEntity: Product::class)]
    private Collection $products;

    /**
     * Initializes collection properties.
     */
    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    /**
     * Returns the brand identifier.
     */
    public function getBrandId(): ?int
    {
        return $this->brandId;
    }

    /**
     * Returns the brand name.
     */
    public function getBrandName(): string
    {
        return $this->brandName;
    }

    /**
     * Sets the brand name.
     */
    public function setBrandName(string $brandName): self
    {
        $this->brandName = $brandName;

        return $this;
    }

    /**
     * Returns related products.
     *
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    /**
     * Attaches a product to the brand.
     */
    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setBrand($this);
        }

        return $this;
    }

    /**
     * Detaches a product from the brand.
     */
    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product) && $product->getBrand() === $this) {
            $product->setBrand(null);
        }

        return $this;
    }
}