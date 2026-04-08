<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a product sold by the stores.
 */
#[ORM\Entity]
#[ORM\Table(name: 'products')]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'product_id', type: 'integer')]
    private ?int $productId = null;

    #[ORM\Column(name: 'product_name', type: 'string', length: 255)]
    private string $productName = '';

    #[ORM\ManyToOne(targetEntity: Brand::class, inversedBy: 'products')]
    #[ORM\JoinColumn(name: 'brand_id', referencedColumnName: 'brand_id', nullable: false)]
    private ?Brand $brand = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'products')]
    #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'category_id', nullable: false)]
    private ?Category $category = null;

    #[ORM\Column(name: 'model_year', type: 'smallint')]
    private int $modelYear = 0;

    #[ORM\Column(name: 'list_price', type: 'decimal', precision: 10, scale: 2)]
    private string $listPrice = '0.00';

    /**
     * @var Collection<int, Stock>
     */
    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Stock::class)]
    private Collection $stocks;

    /**
     * Initializes collection properties.
     */
    public function __construct()
    {
        $this->stocks = new ArrayCollection();
    }

    /**
     * Returns the product identifier.
     */
    public function getProductId(): ?int
    {
        return $this->productId;
    }

    /**
     * Returns the product name.
     */
    public function getProductName(): string
    {
        return $this->productName;
    }

    /**
     * Sets the product name.
     */
    public function setProductName(string $productName): self
    {
        $this->productName = $productName;

        return $this;
    }

    /**
     * Returns the related brand.
     */
    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    /**
     * Sets the related brand.
     */
    public function setBrand(?Brand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * Returns the related category.
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * Sets the related category.
     */
    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Returns the model year.
     */
    public function getModelYear(): int
    {
        return $this->modelYear;
    }

    /**
     * Sets the model year.
     */
    public function setModelYear(int $modelYear): self
    {
        $this->modelYear = $modelYear;

        return $this;
    }

    /**
     * Returns the list price.
     */
    public function getListPrice(): string
    {
        return $this->listPrice;
    }

    /**
     * Sets the list price.
     */
    public function setListPrice(string $listPrice): self
    {
        $this->listPrice = $listPrice;

        return $this;
    }

    /**
     * Returns related stock entries.
     *
     * @return Collection<int, Stock>
     */
    public function getStocks(): Collection
    {
        return $this->stocks;
    }

    /**
     * Attaches a stock entry to the product.
     */
    public function addStock(Stock $stock): self
    {
        if (!$this->stocks->contains($stock)) {
            $this->stocks->add($stock);
            $stock->setProduct($this);
        }

        return $this;
    }

    /**
     * Detaches a stock entry from the product.
     */
    public function removeStock(Stock $stock): self
    {
        if ($this->stocks->removeElement($stock) && $stock->getProduct() === $this) {
            $stock->setProduct(null);
        }

        return $this;
    }
}