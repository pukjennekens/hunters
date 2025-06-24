<?php

declare(strict_types=1);

namespace App\Jobs\WooCommerce;

use App\Http\Integrations\WooCommerce\Requests\Products\DeleteProductRequest;
use App\Http\Integrations\WooCommerce\WooCommerceConnector;
use App\Http\Integrations\WordPress\Requests\Media\DeleteMediaRequest;
use App\Http\Integrations\WordPress\WordPressConnector;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;

class DeleteProductJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected readonly Product $product)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @throws FatalRequestException
     * @throws RequestException
     */
    public function handle(WooCommerceConnector $wooCommerceConnector, WordPressConnector $wordPressConnector): void
    {
        if (! empty($this->product->woocommerce_product_id)) {
            $wooCommerceConnector->send(new DeleteProductRequest($this->product->woocommerce_product_id));
        }

        $this->product->images()
            ->whereNotNull('woocommerce_id')
            ->get()
            // @phpstan-ignore-next-line argument.type
            ->each(fn (Image $image) => $wordPressConnector->send(new DeleteMediaRequest($image->woocommerce_id, true)));

        $this->product->delete();
    }
}
