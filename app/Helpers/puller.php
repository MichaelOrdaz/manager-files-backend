<?php

/**
     * @param $path
     * @return string
     * @throws FileNotFoundException
     */
function getS3Url($path, $expiredTime = '+30 minutes')
{
    $disk = Storage::disk('s3');
    if ( $path == null ) {
        return null;
    }
    if ($disk->exists($path)) {

        $s3_client = $disk->getDriver()->getAdapter()->getClient();
        $command = $s3_client->getCommand(
            'GetObject',
            [
                'Bucket' => env('AWS_BUCKET','bachiller'),
                'Key'    => $path,
            ]
        );

        $request = $s3_client->createPresignedRequest($command, $expiredTime);

        return (string) $request->getUri();
    } else {
        return null;
    }
}