<?php

namespace NielsNumbers\LocaleRouting\Detectors;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use NielsNumbers\LocaleRouting\Contracts\DetectorInterface;

class UserDetector implements DetectorInterface
{
    public function detect(Request $request): ?string
    {
        return Auth::user()?->locale ?? null;
    }
}
