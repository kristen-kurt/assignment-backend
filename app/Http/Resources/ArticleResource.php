<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "title" => $this->article_title,
            "img" => $this->image_url,
            "source_url" => $this->url,
            "author" => $this->author,
            "date" => $this->published_at

        ];
        // return parent::toArray($request);
    }
}
