<?php

namespace Cv\LaravelDataExtra\Pipes;

use Cv\LaravelDataExtra\Attributes\FromCurrentUser;
use Cv\LaravelDataExtra\Attributes\FromCurrentUserProperty;
use Illuminate\Http\Request;
use Spatie\LaravelData\DataPipes\DataPipe;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataClass;
use Spatie\LaravelData\Support\DataProperty;

class FillCurrentUserPropertiesDataPipe implements DataPipe
{
    public function handle(mixed $payload, DataClass $class, array $properties, CreationContext $creationContext): array
    {
        if (! $payload instanceof Request) {
            return $properties;
        }

        foreach ($class->properties as $dataProperty) {
            /** @var null|FromCurrentUser|FromCurrentUserProperty $attribute */
            $attribute = $dataProperty->attributes->first(
                fn (object $attribute) => $attribute instanceof FromCurrentUser || $attribute instanceof FromCurrentUserProperty
            );

            if ($attribute === null) {
                continue;
            }

            // if inputMappedName exists, use it first
            $name = $dataProperty->inputMappedName ?: $dataProperty->name;
            if (! $attribute->replaceWhenPresentInBody && array_key_exists($name, $properties)) {
                continue;
            }

            $user = $payload->user($attribute->guard);

            if (
                $user === null
                || ($attribute->userClass && ! ($user instanceof $attribute->userClass))) {
                continue;
            }

            $value = $this->resolveValue($dataProperty, $attribute, $user);
            if ($value === null) {
                continue;
            }
            $properties[$name] = $value;

            // keep the original property name
            if ($name !== $dataProperty->name) {
                $properties[$dataProperty->name] = $properties[$name];
            }
        }

        return $properties;
    }

    private function resolveValue(
        DataProperty $dataProperty,
        FromCurrentUser|FromCurrentUserProperty $attribute,
        mixed $user,
    ) {
        if ($attribute instanceof FromCurrentUser) {
            return $user;
        }

        if (empty($user)) {
            return null;
        }

        return data_get($user, $attribute->property ?? $dataProperty->name);
    }
}
