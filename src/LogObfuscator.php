<?php

declare(strict_types=1);

namespace Roslov\LogObfuscator;

/**
 * Obfuscates text (usually logs) to hide sensitive information like passwords, tokens, etc.
 */
final class LogObfuscator
{
    /**
     * Patterns and replacements (pattern → replacement)
     */
    private const REPLACEMENTS = [
        // Tokens up to 8 chars
        '/("token":\s*")([^"]{1,8})(")/ui' => '$1××××××××$3',
        // Long tokens
        '/("token":\s*")([^"]{2})([^"]{3,})([^"]{4})(")/ui' => '$1$2××××××××$4$5',
        // Password
        '/("password":\s*")([^"]+)(")/ui' => '$1××××××××$3',
        // The regular expression is taken from https://regexland.com/base64/ and modified to accept
        // JSON-encoded base64 strings which are truncated
        '#((\\\\?[a-z\d+\/]){4}){10,}((\\\\?[a-z\d+\/]){1,3})?={0,2}$#ui' => '< TRUNCATED BASE64 ENCODED VALUE >',
        // The regular expression is taken from https://regexland.com/base64/ and modified to accept
        // JSON-encoded base64 strings
        '#((\\\\?[a-z\d+\/]){4}){10,}((\\\\?[a-z\d+\/]){3}=|(\\\\?[a-z\d+\/]){2}==)?#ui' => '< BASE64 ENCODED VALUE >',
    ];

    /**
     * Patterns and replacements for a truncated text (pattern → replacement)
     */
    private const REPLACEMENTS_FOR_TRUNCATED_TEXT = [
        // Glues < TRUNCATED BASE64 ENCODED VALUE > placeholder all together to get final < BASE64 ENCODED VALUE >
        '#(< (TRUNCATED )?BASE64 ENCODED VALUE >[a-z\d+\/\\\\=]{0,99})+< BASE64 ENCODED VALUE >#ui' =>
            '< BASE64 ENCODED VALUE >',
        // Cleans up the “tails” before and after < BASE64 ENCODED VALUE > placeholder
        '#([a-z\d+\/\\\\=]{0,99}< (TRUNCATED )?BASE64 ENCODED VALUE >[a-z\d+\/\\\\=]{0,99})+#ui' =>
            '< BASE64 ENCODED VALUE >',
    ];

    /**
     * Maximum length for log string
     */
    private const MAX_LENGTH = 6000;

    /**
     * @var int Maximum allowed length for an obfuscated string
     */
    private int $maxLength;

    /**
     * @var array<string, string> Patterns and replacements (pattern → replacement)
     */
    private array $replacements;

    /**
     * Constructor.
     *
     * @param int $maxLength Maximum allowed length for an obfuscated string
     * @param array<string, string> $additionalReplacements Additional patterns and replacements (pattern → replacement)
     */
    public function __construct(int $maxLength = self::MAX_LENGTH, array $additionalReplacements = [])
    {
        $this->maxLength = $maxLength;
        $this->replacements = array_merge(self::REPLACEMENTS, $additionalReplacements);
    }

    /**
     * Obfuscates the text.
     *
     * @param string $text Original text
     *
     * @return string Obfuscated text
     */
    public function obfuscate(string $text): string
    {
        $obfuscatedText = '';
        foreach (mb_str_split($text, $this->maxLength) as $textPart) {
            $pattern = array_keys($this->replacements);
            $replacement = array_values($this->replacements);
            $obfuscatedText .= preg_replace($pattern, $replacement, $textPart);
        }

        return $this->finalizeObfuscation($obfuscatedText);
    }

    /**
     * Finalizes obfuscation of concatenation of the parts of the original text that was previously obfuscated
     *
     * @param string $text Concatenation of the parts of the original text that was previously obfuscated
     *
     * @return string Groomed obfuscated text
     */
    private function finalizeObfuscation(string $text): string
    {
        foreach (self::REPLACEMENTS_FOR_TRUNCATED_TEXT as $pattern => $replacement) {
            $text = preg_replace($pattern, $replacement, $text);
        }

        return $text;
    }
}
