<?php

namespace App\Services;

class WhatsAppMessageBuilder
{
    protected array $sections = [];
    protected string $footer = '';
    protected const SEPARATOR = "\n--------------------------------\n";

    public function __construct(?string $customFooter = null)
    {
        // Fallback to app name or configured branding
        $this->footer = $customFooter
            ?? config('app.brand_whatsapp_footer', "— Sent via " . config('app.name', 'Private Atelier') . " Concierge");
    }

    public static function make(?string $customFooter = null): self
    {
        return new self($customFooter);
    }

    /**
     * Add a block of content.
     */
    public function addSection(string $content, array $variables = []): self
    {
        foreach ($variables as $key => $val) {
            $content = str_replace("{{{$key}}}", (string)$val, $content);
        }

        $this->sections[] = trim($content);
        return $this;
    }

    /**
     * Compile plain text with separators and footer.
     */
    public function render(): string
    {
        $body = implode(self::SEPARATOR, array_filter($this->sections));

        if (!empty($this->footer)) {
            $body .= "\n\n" . $this->footer;
        }

        return trim($body);
    }

    /**
     * Generate the direct WhatsApp API link with encoded text.
     */
    public function buildUrl(string $phone): string
    {
        // Sanitize phone number to international E.164 without leading '+'
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        $encodedText = rawurlencode($this->render());

        return "https://api.whatsapp.com/send?phone={$cleanPhone}&text={$encodedText}";
    }
}
