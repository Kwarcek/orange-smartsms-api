<?php

namespace Kwarcek\OrangeSmartsmsApi\DTO;

class SMSMessage
{
    public function __construct(
        public string $sender,
        public string $recipient,
        public string $content
    )
    {
    }
}