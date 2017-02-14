# RKSV encryption tool

This tool is meant be used in systems that implement the Austrian 
"Registrierkassensicherheitsverordnung" (yes, that's one word ...).

This package has been developed on request by [Pocketbill](https://www.pocketbill.at/).

## API

### `createAesKey`

Creates a base64 encoded, random key with 256bits length. Ready to be used as input for the encryption methods
and displayed to your users for registering it in FinanzOnline.

### `encryptTotalInEur`

Just a convenience method which converts your EUR amount to cents before passing it to `encryptTotalInCents`.
See below for the input parameters

### `encryptTotalInCents`

The actual encryption method. It will encrypt your total value, according to the regulations.

It takes in the following parameters:

- `totalInCents` *(int)* the total value you want to encrypt
- `base64AesKey` *(string)* base64 encoded AES key to use
- `receiptNumber` *(string)* the receipt number of the document you're about to sign
- `registerId` *(string)* the ID of the cash register your representing
- `receiptType` *(string)* the type of document you're about to sign. Possible values are:
    - `Signer::TYPE_STANDARD` just a regular receipt
    - `Signer::TYPE_CANCEL` the receipt for a cancellation of another receipt
    - `Signer::TYPE_TRAINING` a training document your users might want to create for training/testing purposes
    - `Signer::TYPE_START` a start document which is created when you start using a (new) cash register
    - `Signer::TYPE_ZERO` a "zero document" which is created every month/year or when the signature service became 
    available again, after having been unavailable for at least 1 other document.

## Usage examples

See `test/RKSV/Test/SignerTest.php`.