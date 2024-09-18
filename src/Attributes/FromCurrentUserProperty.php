<?php

namespace Cv\LaravelDataExtra\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class FromCurrentUserProperty
{
    public function __construct(
        public ?string $guard = null,
        public ?string $property = null,
        public bool $replaceWhenPresentInBody = true,
        public ?string $userClass = null
    ) {}
}
