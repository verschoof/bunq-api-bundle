bunq api bundle
===============

A Symfony2 bundle to implement the bunq client


Installation
============

Require the package

    composer require verschoof/bunq-api-bundle

Register bundle in the AppKernel

    $bundles = array(
        ...
        new Verschoof\BunqApiBundle\BunqApiBundle(),
        ...
    )

Add configuration to your config.yml

    bunq_api:
        storage_location: "var/data/bunq"
        key:              "Api key"
        uri:              "End point"
        permitted_ips:    "List of permitted ips"

Make sure the private and public certificate exists

    cd var/data/bunq/certificates/
    openssl genpkey -algorithm RSA -out private.pem -pkeyopt rsa_keygen_bits:2048 && openssl rsa -pubout -in private.pem -out public.pem

Don't forget to make `var/data/bunq` as symlink so your installation tokens/certificates are not gone after a deploy. 

Usage
=====

See `Resources/config/service.yml` for the availble resources

    $paymentResource = $this->get('bunq_payment_resource');
    $listOfPayments  = $paymentResource->listPayments(1, 1);
    

See https://github.com/verschoof/bunq-api for more information
