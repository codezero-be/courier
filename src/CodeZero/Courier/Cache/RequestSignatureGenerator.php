<?php namespace CodeZero\Courier\Cache; 

class RequestSignatureGenerator {

    public function generate($method, $url, array $data = [], array $headers = [])
    {
        $signatureParts = [$method, $url];
        $signatureParts[] = http_build_query($data);
        $signatureParts[] = http_build_query($headers);

        return implode('|', $signatureParts);
    }

}