<?php

namespace NielsNumbers\LocaleRouting;

class Config
{
    private array $settings;

    public function __construct(array $settings = [])
    {
        $this->settings = $settings;
    }

    private function get(string $key, $default = null): mixed
    {
        return $this->settings[$key] ?? $default;
    }

    public function supportedLocales(): array
    {
        return $this->get('supported_locales', []);
    }

    public function hideDefaultLocale(): bool
    {
        return $this->get('hide_default_locale', true);
    }

    public function macroRegisterName(): string
    {
        return $this->get('macro_register_name', 'localized');
    }
}