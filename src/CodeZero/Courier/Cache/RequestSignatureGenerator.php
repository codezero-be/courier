<?php namespace CodeZero\Courier\Cache; 

class RequestSignatureGenerator {

    /**
     * Generate a signature that identifies a request
     *
     * @param string $method
     * @param string $url
     * @param array $data
     * @param array $headers
     * @param string $basicAuthCredentials
     *
     * @return string
     */
    public function generate($method, $url, array $data = [], array $headers = [], $basicAuthCredentials = '')
    {
        $signatureParts = [$method, $url];
        $signatureParts[] = http_build_query($data);
        $signatureParts[] = http_build_query($headers);
        $signatureParts[] = $basicAuthCredentials;

        return implode('|', $signatureParts);
    }

}