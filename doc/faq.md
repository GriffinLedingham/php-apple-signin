# FAQ.

## SSL certificate problem: unable to get local issuer certificate
To ensure data security, curl requests to enable domain name certificate verification. If you encounter this prompt, identify that your PHP environment is not configured with a newer list of root certificates. Please refer to: https://curl.haxx.se/docs/sslcerts.html. Download: https://curl.haxx.se/ca/cacert.pem, and configure `php.ini` ***curl.cainfo = "XX your path XX/cacert.pem"***
