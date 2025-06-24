<?php

namespace Tests\Unit\Commands\Shopify;

use App\Console\Commands\Shopify\ImportProductsCommand;
use App\Jobs\Shopify\ImportProductsJob;
use Mockery;
use ReflectionClass;
use Tests\TestCase;
use Illuminate\Support\Facades\Queue;

class ImportProductsCommandTest extends TestCase
{
    public function testItDispatchesTheImportProductsJob(): void
    {
        Queue::fake();

        $commandMock = Mockery::mock(ImportProductsCommand::class);

        $commandMock
            ->shouldReceive('option')
            ->with('force')
            ->andReturnFalse();

        $commandMock->expects('handle')->passthru();
        $commandMock->handle();

        Queue::assertPushed(ImportProductsJob::class, function(ImportProductsJob $job): bool {
            $jobReflection = new ReflectionClass($job);
            $this->assertFalse($jobReflection->getProperty('force')->getValue($job));

            return true;
        });
    }

    public function testItDispatchesTheImportProductsJobWithForce(): void
    {
        Queue::fake();

        $commandMock = Mockery::mock(ImportProductsCommand::class);

        $commandMock
            ->shouldReceive('option')
            ->with('force')
            ->andReturnTrue();

        $commandMock->expects('handle')->passthru();
        $commandMock->handle();

        Queue::assertPushed(ImportProductsJob::class, function(ImportProductsJob $job): bool {
            $jobReflection = new ReflectionClass($job);
            $this->assertTrue($jobReflection->getProperty('force')->getValue($job));

            return true;
        });
    }
}
