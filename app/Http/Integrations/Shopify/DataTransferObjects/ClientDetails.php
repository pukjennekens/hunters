<?php

declare(strict_types=1);

namespace App\Http\Integrations\Shopify\DataTransferObjects;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;

class ClientDetails extends Data
{
    public function __construct(
        #[MapName('accept_language')]
        public readonly ?string $acceptLanguage,
        #[MapName('browser_height')]
        public readonly ?string $browserHeight,
        #[MapName('browser_ip')]
        public readonly ?string $browserIp,
        #[MapName('browser_width')]
        public readonly ?string $browserWidth,
        #[MapName('session_hash')]
        public readonly ?string $sessionHash,
        #[MapName('user_agent')]
        public readonly ?string $userAgent,
    ) {}
}
