<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Represents the stock quantity of one product in one store.
 */
#[ORM\Entity]
#[ORM\Table(name: 'stocks')]
class Stock
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Store::class, inversedBy: 'stocks')]
    #[ORM\JoinColumn(name: 'store_id', referencedColumnName: 'store_id', nullable: false)]
    private ?Store $store = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'stocks')]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'product_id', nullable: false)]
    private ?Product $product = null;

    #[ORM\Column(type: 'integer')]
    private int $quantity = 0;

    /**
     * Returns the related store.
     */
    public function getStore(): ?Store
    {
        return $this->store;
    }

    /**
     * Sets the related store.
     */
    public function setStore(?Store $store): self
    {
        $this->store = $store;

        return $this;
    }

    /**
     * Returns the related product.
     */
    public function getProduct(): ?Product
    {
        return $this->product;
    }

    /**
     * Sets the related product.
     */
    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Returns the available quantity.
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * Sets the available quantity.
     */
    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}