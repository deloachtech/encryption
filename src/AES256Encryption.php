<?php
/*
 * This file is part of the aarondeloach/encryption package.
 *
 * Copyright (c) Aaron DeLoach
 * https://adeloach.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AaronDeLoach\Encryption;

/**
 * Usage:
 *
 * $key = AES256Encryption::generateKey();
 * There should be no issue using the same key for all records in terms of
 * security. As long as the key itself is strong, using a different key
 * does not give you any advantage (in fact, it will be a real headache to
 * manage), so one key should usually be good enough. There are probably many
 * solutions to storing keys securely, but those depend on the level of
 * security you really need.
 *
 * $iv = AES256Encryption::generateIv();
 * You can simply store the initialization vector (IV) in the database next
 * to the encrypted data. The IV itself is not supposed to be secret. It
 * usually acts as a salt, to avoid a situation where two identical plaintext
 * records get encrypted into identical ciphertext. Storing the IV in the
 * database for each row will eliminate the concern over losing this data.
 * Ideally, each row IV would be unique and random but not secret.
 *
 * $encryptedText = AES256Encryption::encrypt($text, $key, $iv);
 * $decryptedText = AES256Encryption::decrypt($encryptedText, $key, $iv);
 */
class AES256Encryption
{
    public const BLOCK_SIZE = 8;
    public const IV_LENGTH = 16;
    public const CIPHER = 'AES256';


    public static function encrypt(string $plainText, string $key, string $iv): string
    {
        $plainText = static::getPaddedText($plainText);
        return base64_encode(openssl_encrypt($plainText, static::CIPHER, $key, OPENSSL_RAW_DATA, $iv));
    }


    public static function decrypt(string $encryptedText, string $key, string $iv): string
    {
        return openssl_decrypt(base64_decode($encryptedText), static::CIPHER, $key, OPENSSL_RAW_DATA, $iv);
    }


    public static function generateKey(): string
    {
        return random_bytes(32);
    }


    public static function generateIv(bool $allowLessSecure = false): string
    {
        if (!$random = openssl_random_pseudo_bytes(openssl_cipher_iv_length(static::CIPHER))) {
            if (function_exists('sodium_randombytes_random16')) {
                $random = sodium_randombytes_random16();
            } else {
                try {
                    $random = random_bytes(static::IV_LENGTH);
                } catch (\Exception $e) {
                    if ($allowLessSecure) {
                        $permitted_chars = implode(
                            '',
                            array_merge(
                                range('A', 'z'),
                                range(0, 9),
                                str_split('~!@#$%&*()-=+{};:"<>,.?/\'')
                            )
                        );
                        $random = '';
                        for ($i = 0; $i < static::IV_LENGTH; $i++) {
                            $random .= $permitted_chars[mt_rand(0, (static::IV_LENGTH) - 1)];
                        }
                    } else {
                        throw new \Exception('Unable to generate initialization vector (IV)');
                    }
                }
            }
        }
        return $random;
    }


    protected static function getPaddedText(string $plainText): string
    {
        $stringLength = strlen($plainText);
        if ($stringLength % static::BLOCK_SIZE) {
            $plainText = str_pad($plainText, $stringLength + static::BLOCK_SIZE - $stringLength % static::BLOCK_SIZE, "\0");
        }
        return $plainText;
    }

}
