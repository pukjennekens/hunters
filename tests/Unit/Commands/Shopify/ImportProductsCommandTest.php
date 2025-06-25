<?php

declare(strict_types=1);

namespace Tests\Unit\Commands\Shopify;

use App\Console\Commands\Shopify\ImportProductsCommand;
use App\Jobs\Shopify\ImportProductsJob;
use Illuminate\Support\Facades\Queue;
use Mockery;
use ReflectionClass;
use Tests\TestCase;

class ImportProductsCommandTest extends TestCase
{
    public function test_it_dispatches_the_import_products_job(): void
    {
        Queue::fake();

        $commandMock = Mockery::mock(ImportProductsCommand::class);

        $commandMock
            ->shouldReceive('option')
            ->with('force')
            ->andReturnFalse();

        $commandMock->expects('handle')->passthru();
        $commandMock->handle();

        Queue::assertPushed(ImportProductsJob::class, function (ImportProductsJob $job): bool {
            $jobReflection = new ReflectionClass($job);
            $this->assertFalse($jobReflection->getProperty('force')->getValue($job));

            return true;
        });
    }

    public function test_it_dispatches_the_import_products_job_with_force(): void
    {
        Queue::fake();

        $commandMock = Mockery::mock(ImportProductsCommand::class);

        $commandMock
            ->shouldReceive('option')
            ->with('force')
            ->andReturnTrue();

        $commandMock->expects('handle')->passthru();
        $commandMock->handle();

        Queue::assertPushed(ImportProductsJob::class, function (ImportProductsJob $job): bool {
            $jobReflection = new ReflectionClass($job);
            $this->assertTrue($jobReflection->getProperty('force')->getValue($job));

            return true;
        });
    }
}
