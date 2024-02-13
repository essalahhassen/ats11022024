<?php
// src/Document/Product.php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as Type;
use App\Repository\ProductRepository;
#[MongoDB\Document(db:"ATS",collection:"ATS",repositoryClass: ProductRepository::class)]


class Product
{
    #[MongoDB\Id]
    protected ?string $id;

    #[MongoDB\Field(type: Type::STRING)]
    protected ?string $productName;

    #[MongoDB\Field(type: Type::STRING)]
    protected ?string $description;

    #[MongoDB\Field(type: Type::FLOAT)]
    protected ?float $price;

    #[MongoDB\Field(type: Type::STRING)]
    protected ?string $category;

    #[MongoDB\Field(type: Type::STRING)]
    protected ?string $imageUrl;

    #[MongoDB\EmbedMany(targetDocument: Review::class)]
    protected $reviews;

    public function __construct()
    {
        $this->reviews = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getProductName(): ?string
    {
        return $this->productName;
    }

    public function setProductName(string $productName): self
    {
        $this->productName = $productName;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    public function getReviews()
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        $this->reviews[] = $review;
        return $this;
    }

    public function removeReview(Review $review): self
    {
        $this->reviews->removeElement($review);
        return $this;
    }
}