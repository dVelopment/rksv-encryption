<?php

namespace RKSV;

use TrafficCophp\ByteBuffer\Buffer;

class Signer
{
    const TYPE_STANDARD = 'standard';
    const TYPE_CANCEL = 'cancel';
    const TYPE_TRAINING = 'training';
    const TYPE_START = 'start';
    const TYPE_ZERO = 'zero';

    /**
     * @param float $totalInEur
     * @param string $base64AesKey
     * @param string $receiptNumber
     * @param string $registerId
     * @param string $receiptType
     *
     * @return string
     */
    public static function encryptTotalInEur($totalInEur, $base64AesKey, $receiptNumber, $registerId, $receiptType = self::TYPE_STANDARD)
    {
        // make sure there are no conversion errors
        $centString = str_replace('.', '', number_format($totalInEur, 2, '.', ''));

        return static::encryptTotalInCents(intval($centString), $base64AesKey, $receiptNumber, $registerId, $receiptType);
    }

    /**
     * @param int $totalInCents
     * @param string $base64AesKey - base64 encoded
     * @param string $receiptNumber
     * @param string $registerId
     * @param string $receiptType
     *
     * @return string
     */
    public static function encryptTotalInCents($totalInCents, $base64AesKey, $receiptNumber, $registerId, $receiptType = self::TYPE_STANDARD)
    {
        switch ($receiptType) {
            case self::TYPE_CANCEL:
            case self::TYPE_TRAINING:
                $toEncode = $receiptType === self::TYPE_CANCEL ? 'STO' : 'TRA';

                return base64_encode($toEncode);
                break;
            default:
                if ($receiptType === self::TYPE_START) {
                    $totalInCents = 0;
                }

                $iv = self::createInitializationVector($receiptNumber, $registerId);
                $key = base64_decode($base64AesKey);

                // initialize data to be encrypted
                $toEncrypt = new Buffer(16);
                $totalAsBytes = self::getByteRepresentationOfTotal($totalInCents);
                $toEncrypt->write($totalAsBytes->read(0, $totalAsBytes->length()), 0);

                $encrypted = openssl_encrypt(
                    $toEncrypt->__toString(),
                    'AES-256-CTR',
                    $key,
                    OPENSSL_RAW_DATA,
                    $iv
                );

                // return the first N bytes
                return base64_encode(substr($encrypted, 0, $totalAsBytes->length()));
                break;
        }
    }

    /**
     * creates a base64 encoded, random key with 256 bits length
     *
     * @return string
     */
    public static function createAesKey()
    {
        return base64_encode(openssl_random_pseudo_bytes(32));
    }

    /**
     * @param string $receiptNumber
     * @param string $registerId
     *
     * @return string
     */
    private static function createInitializationVector($receiptNumber, $registerId)
    {
        return substr(hash('sha256', $registerId . $receiptNumber, true), 0, 16);
    }

    /**
     * @param int $totalInCents
     *
     * @return Buffer
     */
    private static function getByteRepresentationOfTotal($totalInCents = 0)
    {
        // create a byte array with length of 8
        $byteBuffer = new Buffer(8);

        // write the total
        $byteBuffer->writeInt32BE($totalInCents, 4);

        return $byteBuffer;
    }
}
