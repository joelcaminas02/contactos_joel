<?php

namespace App\Entity;

use App\Repository\ContactoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactoRepository::class)]
class Contacto
{

    /**

     * @ORM\Id

     * @ORM\GeneratedValue

     * @ORM\Column(type="integer")

     */

    private $id;

    /**

     * @ORM\Column(type="string", length=255)

     * @Assert\NotBlank

     * (message="El nombre es obligatorio")

     */

    private $nombre;

    /**

     * @ORM\Column(type="string", length=15)

     * @Assert\NotBlank

     * (message="El teléfono es obligatorio")

     */

    private $telefono;

    /**

     * @ORM\Column(type="string", length=255)

     * @Assert\NotBlank()

     * @Assert\Email

     * (message="El email {{ value }} no es válido")

     */

    private $email;

    /**

     * @ORM\ManyToOne(targetEntity=Provincia::class)

     */

    private $provincia;

    public function getId(): ?int

    {

        return $this->id;

    }

    public function getNombre(): ?string

    {

        return $this->nombre;

    }

    public function setNombre(?string $nombre): self

    {

        $this->nombre = $nombre;

        return $this;

    }

    public function getTelefono(): ?string

    {

        return $this->telefono;

    }

    public function setTelefono(?string $telefono): self

    {

        $this->telefono = $telefono;

        return $this;

    }

    public function getEmail(): ?string

    {

        return $this->email;

    }

    public function setEmail(?string $email): self

    {

        $this->email = $email;

        return $this;

    }
}