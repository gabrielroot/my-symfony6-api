<?php

namespace App\Interface;

use DateTime;

interface IAudit
{
    public function getCreatedAt() : ?DateTime;

    public function setCreatedAt(DateTime $createdAt): self;

    public function getUpdatedAt(): ?DateTime;

    public function setUpdatedAt(?DateTime $updatedAt): self;

    public function getDeletedAt(): ?DateTime;

    public function setDeletedAt(?DateTime $deletedAt): self;

    public function isActive(): bool;
}