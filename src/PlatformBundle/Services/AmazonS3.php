<?php

namespace PlatformBundle\Services;

use Aws\S3\S3Client;

/**
 * Class allows to upload and remove files from Amazon Simple Storage Service
 */
class AmazonS3
{
    private $s3;

    function __construct(S3Client $s3)
    {
        $this->s3 = $s3;
    }

    /**
     * Uploads file to S3
     *
     * @param string $path
     * @param string $filename
     * @param string $contentType
     * @param string $bucket
     * @param array $headers
     *
     * @return string ObjectURL
     */
    public function upload($path, $filename, $contentType, $bucket, $headers = [])
    {
        $config = array(
            'Body' => fopen($path.$filename, 'r'),
            'Key' => $filename,
            'ContentType' => $contentType,
            'Bucket' => $bucket,
            'ACL' => 'public-read'
        );

        foreach ($headers as $key => $header) {
            $config[$key] = $header;
        }

        $result = $this->s3->putObject($config);

        return $result['ObjectURL'];
    }

    /**
     * Removes file from S3
     *
     * @param string $filename
     * @param string $bucket
     *
     */
    public function delete($filename, $bucket)
    {
        if ($filename) {
            $config = array(
                'Key' => $filename,
                'Bucket' => $bucket
            );
            $this->s3->deleteObject($config);
        }
    }
}
