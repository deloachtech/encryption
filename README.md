Encryption Package
==================

An Advanced Encryption Standard (AES) 256-bit class for PHP.

Encryption is fundamental to contemporary internet security. An encryption system scrambles sensitive data using mathematical calculations to turn data into code. The original data can only be revealed with the correct key, allowing it to remain secure from everyone but the authorized parties.

The AES algorithm was approved by the NSA for handling top secret information. AES has since become the industry standard for encryption. Its open nature means AES software can be used for both public and private, commercial and noncommercial implementations.

Installation
------------

```bash
composer require aarondeloach/encryption
```

Or download the package and use the class itself.

Usage
-----

# Create a key

```php
echo AES256Encryprion::generateKey();
```

There should be no issue using the same key for all records in terms of security. As long as the key itself is strong, using a different key does not give you any advantage (in fact, it will be a real headache to manage), so one key should usually be good enough. There are probably many solutions to storing keys securely, but those depend on the level of security you really need.

# Encrypting data

```php
$iv = AES256Encryption::generateIv();
$encryptedText = AES256Encryption::encrypt($text, $key, $iv);
```

Note: You can simply store the initialization vector (IV) in the database next to the encrypted data. The IV itself is not supposed to be secret. It usually acts as a salt, to avoid a situation where two identical plaintext records get encrypted into identical ciphertext. Storing the IV in the database for each row will eliminate the concern over losing this data. Ideally, each row IV would be unique and random but not secret.

# Decrypting data

```php
$decryptedText = AES256Encryption::decrypt($encryptedText, $key, $iv);
```
