<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a store employee.
 */
#[ORM\Entity]
#[ORM\Table(name: 'employees')]
class Employee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'employee_id', type: 'integer')]
    private ?int $employeeId = null;

    #[ORM\ManyToOne(targetEntity: Store::class, inversedBy: 'employees')]
    #[ORM\JoinColumn(name: 'store_id', referencedColumnName: 'store_id', nullable: false)]
    private ?Store $store = null;

    #[ORM\Column(name: 'employee_name', type: 'string', length: 255)]
    private string $employeeName = '';

    #[ORM\Column(name: 'employee_email', type: 'string', length: 255, unique: true)]
    private string $employeeEmail = '';

    #[ORM\Column(name: 'employee_password', type: 'string', length: 255)]
    private string $employeePassword = '';

    #[ORM\Column(name: 'employee_role', type: 'string', length: 20)]
    private string $employeeRole = 'employee';

    /**
     * Returns the employee identifier.
     */
    public function getEmployeeId(): ?int
    {
        return $this->employeeId;
    }

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
     * Returns the employee name.
     */
    public function getEmployeeName(): string
    {
        return $this->employeeName;
    }

    /**
     * Sets the employee name.
     */
    public function setEmployeeName(string $employeeName): self
    {
        $this->employeeName = $employeeName;

        return $this;
    }

    /**
     * Returns the employee email.
     */
    public function getEmployeeEmail(): string
    {
        return $this->employeeEmail;
    }

    /**
     * Sets the employee email.
     */
    public function setEmployeeEmail(string $employeeEmail): self
    {
        $this->employeeEmail = $employeeEmail;

        return $this;
    }

    /**
     * Returns the employee password hash.
     */
    public function getEmployeePassword(): string
    {
        return $this->employeePassword;
    }

    /**
     * Sets the employee password hash.
     */
    public function setEmployeePassword(string $employeePassword): self
    {
        $this->employeePassword = $employeePassword;

        return $this;
    }

    /**
     * Returns the employee role.
     */
    public function getEmployeeRole(): string
    {
        return $this->employeeRole;
    }

    /**
     * Sets the employee role.
     */
    public function setEmployeeRole(string $employeeRole): self
    {
        $this->employeeRole = $employeeRole;

        return $this;
    }
}