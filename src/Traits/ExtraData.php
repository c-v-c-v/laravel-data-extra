<?php

namespace Cv\LaravelDataExtra\Traits;

use Cv\LaravelDataExtra\Pipes\FillCurrentUserPropertiesDataPipe;
use ReflectionProperty;
use RuntimeException;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataPipeline;
use Spatie\LaravelData\DataPipes\FillRouteParameterPropertiesDataPipe;

/**
 * @mixin Data
 */
trait ExtraData
{
    public static function pipeline(): DataPipeline
    {
        $dataPipeline = parent::pipeline();

        $reflectionProperty = new ReflectionProperty($dataPipeline, 'pipes');
        $pipes = $reflectionProperty->getValue($dataPipeline);
        $position = array_search(FillRouteParameterPropertiesDataPipe::class, $pipes);
        if ($position === false) {
            throw new RuntimeException;
        }
        array_splice($pipes, $position + 1, 0, FillCurrentUserPropertiesDataPipe::class);
        $reflectionProperty->setValue($dataPipeline, $pipes);

        return $dataPipeline;
    }
}
