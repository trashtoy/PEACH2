<?php

namespace Peach\Http;

/**
 * この HeaderField が複数回セットできることをあらわすマーカーインタフェースです.
 * "Set-Cookie" などが該当します.
 */
interface RepeatableHeaderField extends HeaderField
{
}
