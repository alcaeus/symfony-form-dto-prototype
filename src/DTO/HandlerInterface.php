<?php

namespace App\DTO;

use App\Metadata\Dto;

interface HandlerInterface
{
    /**
     * Handles a DTO
     *
     * @param mixed $data Submitted data
     *
     * @return bool True if the DTO was applied, false otherwise
     */
    public function handle(Dto $metadata, WrappingDto $dto, $data): bool;
}
