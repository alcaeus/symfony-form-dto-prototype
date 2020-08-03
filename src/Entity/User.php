<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/** @ODM\Document */
class User
{
    /** @ODM\Id */
    private int $id;

    /** @ODM\Field */
    private string $name;

    /** @ODM\ReferenceMany(targetDocument=User::class) */
    private Collection $contacts;

    /** @ODM\EmbedOne(targetDocument=Address::class) */
    private ?Address $address = null;

    public function __construct(string $name)
    {
        $this->id = rand();
        $this->name = $name;
        $this->contacts = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getContacts(): array
    {
        return $this->contacts->toArray();
    }

    public function addContact(User $contact): void
    {
        if ($this->contacts->contains($contact)) {
            throw new \RuntimeException('User is already a contact.');
        }

        $this->contacts->add($contact);
    }

    public function removeContact(User $contact): void
    {
        if (!$this->contacts->contains($contact)) {
            throw new \RuntimeException('User is not a contact.');
        }

        $this->contacts->add($contact);
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function changeAddress(string $street, string $city, string $zipCode, string $country): void
    {
        $this->address = new Address($street, $city, $zipCode, $country);
    }

    public function updateAddress(Address $address): void
    {
        $this->address = $address;
    }
}
