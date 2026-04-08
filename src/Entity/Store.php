<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a physical store.
 */
#[ORM\Entity]
#[ORM\Table(name: 'stores')]
class Store
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'store_id', type: 'integer')]
    private ?int $storeId = null;

    #[ORM\Column(name: 'store_name', type: 'string', length: 255)]
    private string $storeName = '';

    #[ORM\Column(type: 'string', length: 25, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $street = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private ?string $state = null;

    #[ORM\Column(name: 'zip_code', type: 'string', length: 10, nullable: true)]
    private ?string $zipCode = null;

    /**
     * @var Collection<int, Stock>
     */
    #[ORM\OneToMany(mappedBy: 'store', targetEntity: Stock::class)]
    private Collection $stocks;

    /**
     * @var Collection<int, Employee>
     */
    #[ORM\OneToMany(mappedBy: 'store', targetEntity: Employee::class)]
    private Collection $employees;

    /**
     * Initializes collection properties.
     */
    public function __construct()
    {
        $this->stocks = new ArrayCollection();
        $this->employees = new ArrayCollection();
    }

    /**
     * Returns the store identifier.
     */
    public function getStoreId(): ?int
    {
        return $this->storeId;
    }

    /**
     * Returns the store name.
     */
    public function getStoreName(): string
    {
        return $this->storeName;
    }

    /**
     * Sets the store name.
     */
    public function setStoreName(string $storeName): self
    {
        $this->storeName = $storeName;

        return $this;
    }

    /**
     * Returns the phone number.
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * Sets the phone number.
     */
    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Returns the email address.
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Sets the email address.
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Returns the street address.
     */
    public function getStreet(): ?string
    {
        return $this->street;
    }

    /**
     * Sets the street address.
     */
    public function setStreet(?string $street): self
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Returns the city.
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * Sets the city.
     */
    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Returns the state code.
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * Sets the state code.
     */
    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Returns the ZIP code.
     */
    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    /**
     * Sets the ZIP code.
     */
    public function setZipCode(?string $zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * Returns store stock entries.
     *
     * @return Collection<int, Stock>
     */
    public function getStocks(): Collection
    {
        return $this->stocks;
    }

    /**
     * Attaches a stock entry to the store.
     */
    public function addStock(Stock $stock): self
    {
        if (!$this->stocks->contains($stock)) {
            $this->stocks->add($stock);
            $stock->setStore($this);
        }

        return $this;
    }

    /**
     * Detaches a stock entry from the store.
     */
    public function removeStock(Stock $stock): self
    {
        if ($this->stocks->removeElement($stock) && $stock->getStore() === $this) {
            $stock->setStore(null);
        }

        return $this;
    }

    /**
     * Returns store employees.
     *
     * @return Collection<int, Employee>
     */
    public function getEmployees(): Collection
    {
        return $this->employees;
    }

    /**
     * Attaches an employee to the store.
     */
    public function addEmployee(Employee $employee): self
    {
        if (!$this->employees->contains($employee)) {
            $this->employees->add($employee);
            $employee->setStore($this);
        }

        return $this;
    }

    /**
     * Detaches an employee from the store.
     */
    public function removeEmployee(Employee $employee): self
    {
        if ($this->employees->removeElement($employee) && $employee->getStore() === $this) {
            $employee->setStore(null);
        }

        return $this;
    }
}