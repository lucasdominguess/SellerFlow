<?php

namespace App\DTOs\Business;

use App\Models\Business\ProductImage;
use Illuminate\Support\Facades\Storage;

class ProductImageResponseDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $url,
        public readonly int $position,
        public readonly bool $is_cover,
    ) {}

    public static function fromModel(ProductImage $model): self
    {
        return new self(
            id: $model->id,
            url: Storage::url($model->path),
            position: $model->position,
            is_cover: $model->position === 0,
        );
    }

    public function toArray(): array
    {
        return [
            'id'       => $this->id,
            'url'      => $this->url,
            'position' => $this->position,
            'is_cover' => $this->is_cover,
        ];
    }
}
