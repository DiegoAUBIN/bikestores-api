<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a bicycle category.
 */
#[ORM\Entity]
#[ORM\Table(name: 'categories')]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'category_id', type: 'integer')]
    private ?int $categoryId = null;

    #[ORM\Column(name: 'category_name', type: 'string', length: 255)]
    private string $categoryName = '';

    /**
     * @var Collection<int, Product>
     */
    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Product::class)]
    private Collection $products;

    /**
     * Initializes collection properties.
     */
    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    /**
     * Returns the category identifier.
     */
    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }

    /**
     * Returns the category name.
     */
    public function getCategoryName(): string
    {
        return $this->categoryName;
    }

    /**
     * Sets the category name.
     */
    public function setCategoryName(string $categoryName): self
    {
        $this->categoryName = $categoryName;

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
     * Attaches a product to the category.
     */
    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setCategory($this);
        }

        return $this;
    }

    /**
     * Detaches a product from the category.
     */
    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product) && $product->getCategory() === $this) {
            $product->setCategory(null);
        }

        return $this;
    }
}