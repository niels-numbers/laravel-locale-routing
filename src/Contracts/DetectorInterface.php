<?php

namespace NielsNumbers\LocaleRouting\Contracts;

use Illuminate\Http\Request;

interface DetectorInterface
{
    public function detect(Request $request): ?string;
}
