<?php

namespace SmartEnum;

class EnumException extends \Exception
{
    public static function becauseUnknownMember(string $enum, ?string $member) : EnumException
    {
        return new self(sprintf('Unknown member "%s" for enum %s', $member, $enum));
    }
}
