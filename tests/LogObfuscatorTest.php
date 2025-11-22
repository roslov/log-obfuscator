<?php

declare(strict_types=1);

namespace Roslov\LogObfuscator\Tests;

use PHPUnit\Framework\TestCase;
use Roslov\LogObfuscator\LogObfuscator;

/**
 * Tests LogObfuscator.
 */
final class LogObfuscatorTest extends TestCase
{
    /**
     * Tests obfuscation.
     *
     * @param string $text Original text
     * @param string $expected Expected result
     *
     * @dataProvider dataProvider
     */
    public function testObfuscate(string $text, string $expected): void
    {
        $obfuscator = new LogObfuscator(100);
        $this->assertEquals($expected, $obfuscator->obfuscate($text));
    }

    /**
     * Returns the test data.
     *
     * @return string[][] Data
     */
    public function dataProvider(): array
    {
        // phpcs:disable SlevomatCodingStandard.Arrays.DisallowPartiallyKeyed.DisallowedPartiallyKeyed
        return [
            [
                'simple text',
                'simple text',
            ],
            [
                <<<'JSON'
                    {
                        "token": "123456"
                    }
                    JSON,
                <<<'JSON'
                    {
                        "token": "××××××××"
                    }
                    JSON,
            ],
            [
                <<<'JSON'
                    {
                        "token":
                        "123456"
                    }
                    JSON,
                <<<'JSON'
                    {
                        "token":
                        "××××××××"
                    }
                    JSON,
            ],
            [
                <<<'JSON'
                    {
                        "token": "12345678"
                    }
                    JSON,
                <<<'JSON'
                    {
                        "token": "××××××××"
                    }
                    JSON,
            ],
            [
                <<<'JSON'
                    {
                        "token": "123456789"
                    }
                    JSON,
                <<<'JSON'
                    {
                        "token": "12××××××××6789"
                    }
                    JSON,
            ],
            'token multi line global replacement' => [
                <<<'JSON'
                    [
                        {
                            "token": "123456789"
                        },
                        {
                            "token": "123456789"
                        },
                        {
                            "token": "1234567890"
                        }
                    ]
                    JSON,
                <<<'JSON'
                    [
                        {
                            "token": "12××××××××6789"
                        },
                        {
                            "token": "12××××××××6789"
                        },
                        {
                            "token": "12××××××××7890"
                        }
                    ]
                    JSON,
            ],
            'token single line global replacement' => [
                '{"token": "123456789"},{"token": "123456789"},{"token": "1234567890"}',
                '{"token": "12××××××××6789"},{"token": "12××××××××6789"},{"token": "12××××××××7890"}',
            ],
            [
                <<<'JSON'
                    {
                        "token": "12345678901234567890123456789012"
                    }
                    JSON,
                <<<'JSON'
                    {
                        "token": "12××××××××9012"
                    }
                    JSON,
            ],
            [
                <<<'JSON'
                    {
                        "password": "secure-info"
                    }
                    JSON,
                <<<'JSON'
                    {
                        "password": "××××××××"
                    }
                    JSON,
            ],
            [
                <<<'JSON'
                    {
                        "name": "User",
                        "token": "12345678901234567890123456789012",
                        "password": "secure-info"
                    }
                    JSON,
                <<<'JSON'
                    {
                        "name": "User",
                        "token": "12××××××××9012",
                        "password": "××××××××"
                    }
                    JSON,
            ],
            // phpcs:disable Generic.Files.LineLength.TooLong
            [
                <<<'JSON'
                    {
                        "image": [
                            {
                                "filename": "300x300-62aa0f237d68c953397488.png",
                                "data": "MTIzNzg5MDExMTIzNDU2Nzg5MDExMTIzNDU2Nzg5MDExMTIzNDU2Nzg5MDExMTIzNDg5MDExg5MDExMTIzNDg5MDExg5MDExMTIddzN5MDEx"
                            }
                        ]
                    }
                    JSON,
                <<<'JSON'
                    {
                        "image": [
                            {
                                "filename": "300x300-62aa0f237d68c953397488.png",
                                "data": "< BASE64 ENCODED VALUE >"
                            }
                        ]
                    }
                    JSON,
            ],
            [
                <<<'JSON'
                    {
                        "image": [
                            {
                                "filename": "300x300-62aa0f237d68c953397488.png",
                                "data": "MTIzNzg5MDExMTIzNDU2Nzg5MDExMTIzNDU2Nzg5MDExMTIzNDU2Nzg5MDExMTIzNDg5MDExg5MDExMTIzNDg5MDExg5MDExMTIddzN="
                            }
                        ]
                    }
                    JSON,
                <<<'JSON'
                    {
                        "image": [
                            {
                                "filename": "300x300-62aa0f237d68c953397488.png",
                                "data": "< BASE64 ENCODED VALUE >"
                            }
                        ]
                    }
                    JSON,
            ],
            [
                <<<'JSON'
                    {
                        "image": [
                            {
                                "filename": "600x314-62aa0f24278b1009456568.png",
                                "data": "MTIzNzg5MDExMTIzNDU2Nzgff5MDExMTIzNDU2Nzg5MDExMTIzNDU2Nzg5MDExMTIzNDg5MDExg5MDExMTIzNDg5MDExg5MDhhExMT=="
                            }
                        ]
                    }
                    JSON,
                <<<'JSON'
                    {
                        "image": [
                            {
                                "filename": "600x314-62aa0f24278b1009456568.png",
                                "data": "< BASE64 ENCODED VALUE >"
                            }
                        ]
                    }
                    JSON,
            ],
            // phpcs:enable Generic.Files.LineLength.TooLong
            [
                'MTIzN\/g5MDEx+TIzNDU2Nzg5MDExMTIzNDU2Nzg5MDExMTIzNDU2Nzg5MDExMTIzNDg5MDExMTIzNzg5MDExMTIzNDU2Nzg5MDExM'
                . 'TIzNDU2Nzg5MDExMTIzNDU2Nzg5MDtxMTIzNDg5MDExMTIzNzg5MDExMTIzNDU2Nzg5MDExMTIzNDU2Nzg5MDExMTIzNDU2Nzg5M'
                . 'DExMTIzNDg5MDExMTIzNzg5MDExMTIzNDU2Nzg5MDExMTIzNDU2Nzg5MDExMTIzNDU2Nzg5MDExMTIzNDg5MDExMTIzNzg5MDExM'
                . 'TIzNDU2Nzg5MDExMTIzNDU2Nzg5MDExMTIzNDU2Nzg5MDExMTIzNDg5MDExNDU2Nzg5MDExMTIzNDg5MDExNDU2Nzg5MDEx',
                '< BASE64 ENCODED VALUE >',
            ],
            [
                '"some_data","data":"MTIzN\/g5MDEx+TIzNDU2Nzg5MDExMTIzNDU2Nzg5MDExMTIzNDU2Nzg5MDExMTIzNDg5MDExMTIzNzg5M'
                . 'DExMTIzNDU2Nzg5MDExMTIzNDU2Nzg5MDExMTIzNDU2Nzg5MDtxMTIzNDg5MDExMTIzNzg5MDExMTIzNDU2Nzg5MDExMTIzNDU2N'
                . 'zg5MDExMTIzNDU2Nzg5MDExMTIzNDg5MDExMTIzNzg5MDExMTIzNDU2Nzg5MDExMTIzNDU2Nzg5MDExMTIzNDU2Nzg5MDExMTIzN'
                . 'Dg5MDExMTIzNzg5MDExMTIzNDU2Nzg5MDExMTIzNDU2Nzg5MDExMTIzNDU2Nzg5MDExMTIzNDg5MDExNDU2Nzg5MDExMTIzNDg5M'
                . 'DExNDU2Nzg5MDEx","other_data"',
                '"some_data","data":"< BASE64 ENCODED VALUE >","other_data"',
            ],
            [
                'MTIzNg5MDEx+TIzNDU2Nzg5MDExMTIzNDU2Nzg5MDExMTIzNDU2Nzg5MDExMTIzNDg5MDExMTIzNzg5MDExMTIzNDU2Nzg5MDIzNDU'
                . '2Nzg5MDE\/MTIzNDU2Nzg5MDtxMTIzNDg5MDExMTIzNzg5MDExMTIzNDU2Nzg5MDExMTIzNDU2Nzg5MDExMTIzNDU2Nzg5MDExMT'
                . 'IzNDg5MDExMTIzNzg5MDExMTIzNDU2Nzg5MDExMTIzNDU2Nzg5MDExMTIzNDU2Nzg5MDExMTIzNDg5MDExMTIzNzg5MDExMTIzND'
                . 'U2Nzg5MDExMTIzNDU2Nzg5MDExMTIzNDU2Nzg5MDExMTIzNDg5MDExNDU2Nzg5MDExMTIzNDg5MDExNDU2Nzg5MDEx=",'
                . '"other_data"',
                '< BASE64 ENCODED VALUE >","other_data"',
            ],
            [
                'MTIzNg5MDEx+TIzNDU2Nzg5MDExMTIzNDU2Nzg5MDExMTIzNDU2Nzg5MDExMTIzNDg5MDExMTIzNzg5MDExMTIzNDU2Nzg5MDIzNDU'
                . '2Nzg5MD/MTIzNDU2Nzg5MDtxMTIzNDg5MDExMTIzNzg5MDExMTIzNDU2Nzg5MDExMTIzNDU2Nzg5MDExMTIzNDU2Nzg5MDExMTIz'
                . 'NDg5MDExMTIzNzg5MDExMTIzNDU2Nzg5MDExMTIzNDU2Nzg5MDExMTIzNDU2Nzg5MDExMTIzNDg5MDExMTIzNzg5MDExMTIzNDU2'
                . 'Nzg5MDExMTIzNDU2Nzg5MDExMTIzNDU2Nzg5MDExMTIzNDg5MDExNDU2Nzg5MDExMTIzNDg5MDExNzg5MDEx==",'
                . '"other_data"',
                '< BASE64 ENCODED VALUE >","other_data"',
            ],
        ];
        // phpcs:enable SlevomatCodingStandard.Arrays.DisallowPartiallyKeyed.DisallowedPartiallyKeyed
    }
}
