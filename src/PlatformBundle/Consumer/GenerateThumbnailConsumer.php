<?php

namespace PlatformBundle\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;
use Liip\ImagineBundle\Imagine\Data\DataManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use PlatformBundle\Services\AmazonS3;

/**
 * Write a thumbnail image using the LiipImagineBundle
 */
class GenerateThumbnailConsumer implements ConsumerInterface
{
    private $logger;
    private $em;
    private $dataManager;
    private $filterManager;
    private $amazonS3;
    private $kernelRootDir;

    public function __construct(LoggerInterface $logger, EntityManager $entityManager, DataManager $dataManager,
                                FilterManager $filterManager, AmazonS3 $amazonS3, $kernelRootDir)
    {
        $this->logger = $logger;
        $this->em = $entityManager;
        $this->dataManager = $dataManager;
        $this->filterManager = $filterManager;
        $this->amazonS3 = $amazonS3;
        $this->kernelRootDir = $kernelRootDir;
    }

    /**
     * Executes consumer work
     *
     * @param AMQPMessage $msg
     *
     * @return null
     */
    public function execute(AMQPMessage $msg)
    {
        try {
            list($repositoryName, $entityId, $filter, $saveDir) = unserialize($msg->body);

            $entity = $this->em->getRepository($repositoryName)->findOneById($entityId);

            if ($entity) {
                $absoluteDir = $this->kernelRootDir.'/../web/'.$saveDir;

                $bucket = implode('-', array_filter(explode('/', $saveDir)));
                $contentType = 'image/'.$entity->getFileExt();

                $this->amazonS3->delete(basename($entity->getUrl()), $bucket);

                $tmpFileName = $this->writeTmpImg($entity->getFile(), $entity->getFileExt(), $absoluteDir);
                $thumbFileName = $this->generateThumb($filter, $absoluteDir, $saveDir, $tmpFileName);

                $url = $this->amazonS3->upload($absoluteDir, $thumbFileName, $contentType, $bucket);
                unlink($absoluteDir.$thumbFileName);

                $entity->setUrl($url)->setReady(1)->setFile(null)->setFileExt(null);

                $this->em->persist($entity);
                $this->em->flush();
                $this->em->detach($entity);
            } else {
                $this->logger->warning(sprintf('Repository %s entity %s not found', $repositoryName, $entityId));
            }
        }
        catch (\Exception $e) {
            $this->logger->warning('Failed to generate a thumbnail', array(
                'exception' => $e->getMessage()
            ));
        }
    }

    /**
     * Write binary data to temporary file
     *
     * @param string $file
     * @param string $fileExt
     * @param string $absoluteDir
     *
     * @return string
     */
    private function writeTmpImg($file, $fileExt, $absoluteDir)
    {
        $tmpFileName = uniqid(rand(), true).'.'.$fileExt;
        $path = $absoluteDir.$tmpFileName;
        file_put_contents($path, $file);
        return $tmpFileName;
    }

    /**
     * Generates thumbnail and writes it to file
     *
     * @param string $filter
     * @param string $absoluteDir
     * @param string $saveDir
     * @param string $tmpFileName
     *
     * @return string $thumbFileName
     */
    private function generateThumb($filter, $absoluteDir, $saveDir, $tmpFileName)
    {
        $image = $this->dataManager->find($filter, $saveDir.$tmpFileName);
        $response = $this->filterManager->applyFilter($image, $filter);
        $thumb = $response->getContent();

        $thumbFileName = $filter.'_'.$tmpFileName;

        file_put_contents($absoluteDir.$thumbFileName, $thumb);
        unlink($absoluteDir.$tmpFileName);

        return $thumbFileName;
    }
}
