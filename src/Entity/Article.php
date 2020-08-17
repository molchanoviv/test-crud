<?php
/**
 * Copyright (c) Diffco US, Inc
 */

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * App\Entity\Article
 *
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 * @ORM\Table(name="article")
 */
class Article
{
    /**
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     *
     * @JMS\Expose()
     * @JMS\Groups({"rest"})
     */
    protected ?int $id;

    /**
     * @var string|null
     * @ORM\Column(type="string", name="title", nullable=true, length=255)
     *
     * @JMS\Expose()
     * @JMS\Groups({"rest"})
     *
     * @Assert\NotBlank(message="title can not be blank")
     */
    protected ?string $title;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime", name="created_at", nullable=true)
     * @Gedmo\Timestampable(on="create")
     *
     * @JMS\Expose()
     * @JMS\Groups({"rest"})
     */
    protected ?\DateTime $createdAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime", name="updated_at", nullable=true)
     * @Gedmo\Timestampable(on="update")
     *
     * @JMS\Expose()
     * @JMS\Groups({"rest"})
     */
    protected ?\DateTime $updatedAt;

    /**
     * @var string|null
     * @ORM\Column(type="text", name="body", nullable=true)
     *
     * @JMS\Expose()
     * @JMS\Groups({"rest"})
     *
     * @Assert\NotBlank(message="body can not be blank")
     */
    protected ?string $body;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     *
     * @return void
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     *
     * @return void
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string|null
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * @param string|null $body
     *
     * @return void
     */
    public function setBody(?string $body): void
    {
        $this->body = $body;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
