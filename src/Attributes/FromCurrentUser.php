<?php

namespace Cv\LaravelDataExtra\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class FromCurrentUser
{
    /**
     * @param  class-string|null  $userClass
     */
    public function __construct(
        public ?string $guard = null,
        public bool $replaceWhenPresentInBody = true,
        public ?string $userClass = null
    ) {}
}
