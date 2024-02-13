<?php
// src/Document/Review.php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as Type;


#[MongoDB\EmbeddedDocument]
class Review
{
    #[MongoDB\Field(type: Type::INT)]
    protected ?int $value;

    #[MongoDB\Field(type: Type::STRING)]
    protected ?string $content;

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }
}