<?php

declare(strict_types=1);

namespace App\Http\Integrations\WordPress\Requests\Media;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class DeleteMediaRequest extends Request
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::DELETE;

    public function __construct(protected readonly string|int $mediaId, protected bool $force = false) {}

    protected function defaultQuery(): array
    {
        return [
            'force' => $this->force ? 'true' : 'false',
        ];
    }

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return "/media/{$this->mediaId}";
    }
}
