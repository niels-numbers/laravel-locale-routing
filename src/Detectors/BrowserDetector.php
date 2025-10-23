<?php

namespace NielsNumbers\LocaleRouting\Detectors;

use Illuminate\Http\Request;
use NielsNumbers\LocaleRouting\Contracts\DetectorInterface;

class BrowserDetector implements DetectorInterface
{
    public function detect(Request $request): ?string
    {
        // @toDo integrate browser detetcion ..
        return null;
    }
}
