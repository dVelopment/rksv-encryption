<?php

namespace RKSV\Test;


use RKSV\Signer;


class SignerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider encryptedTotalProvider
     *
     * @param string $base64AesKey
     * @param string $registerId
     */
    public function testEncryptTotalInStandardDocuments($expected, $receiptNumber, $total, $base64AesKey, $registerId)
    {
        $actual = Signer::encryptTotalInEur($total, $base64AesKey, $receiptNumber, $registerId);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider credentialsProvider
     *
     * @param string $base64AesKey
     * @param string $registerId
     */
    public function testEncryptTrainingDocuments($base64AesKey, $registerId)
    {
        $expected = 'VFJB';
        $actual = Signer::encryptTotalInEur(0, $base64AesKey, '83468', $registerId, Signer::TYPE_TRAINING);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider credentialsProvider
     *
     * @param string $base64AesKey
     * @param string $registerId
     */
    public function testEncryptCancelDocuments($base64AesKey, $registerId)
    {
        $expected = 'U1RP';
        $actual = Signer::encryptTotalInEur(0, $base64AesKey, '83468', $registerId, Signer::TYPE_CANCEL);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider credentialsProvider
     */
    public function testEncryptStartDocuments($base64AesKey, $registerId)
    {
        $expected = 'sqv3XHcI8mU=';
        $actual = Signer::encryptTotalInEur(0, $base64AesKey, '83468', $registerId, Signer::TYPE_START);

        $this->assertEquals($expected, $actual);
    }

    public function encryptedTotalProvider()
    {
        $credentials = $this->credentialsProvider()[0];

        return array_map(function ($data) use ($credentials) {
            return array_merge($data, $credentials);
        }, [
            [
                'vXvqlWy0zcM=', // expected
                '83470',        // receiptNumber
                87.33           // total in EUR
            ],
            [
                '/VWheHux01k=',
                '83472',
                129.58,
            ],
            [
                'gisjqXaAGUg=',
                '83473',
                129.58,
            ],
            [
                'ZRlKkpihSxQ=',
                '83476',
                129.58,
            ],
        ]);
    }

    public function credentialsProvider()
    {
        return [
            [
                'RCsRmHn5tkLQrRpiZq2ucwPpwvHJLiMgLvwrwEImddI=', // base64AesKey
                'DEMO-CASH-BOX817', // registerId
            ],
        ];
    }
}
