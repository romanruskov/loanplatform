<?php

namespace PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * @ORM\Table(name="avatar")
 * @ORM\Entity(repositoryClass="PlatformBundle\Entity\Repository\AvatarRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields="investor")
 * @ExclusionPolicy("all")
 */
class Avatar
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Expose
     */
    private $url;

    /**
     * @ORM\Column(type="smallint", length=1)
     * @Expose
     */
    private $ready = 1;

    /**
     * @ORM\Column(type="blob", nullable=true)
     */
    private $file;

    /**
     * @ORM\Column(type="string", length=8, nullable=true)
     */
    private $fileExt;

    /**
     * @ORM\OneToOne(targetEntity="Investor", inversedBy="avatar")
     * @ORM\JoinColumn(name="investor_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $investor;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set avatar url
     *
     * @param string $url
     *
     * @return Avatar
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get avatar url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set avatar ready status
     *
     * @param int $ready
     *
     * @return Avatar
     */
    public function setReady($ready)
    {
        $this->ready = $ready;

        return $this;
    }

    /**
     * Get avatar ready status
     *
     * @return int
     */
    public function getReady()
    {
        return $this->ready;
    }

    /**
     * Set avatar file
     *
     * @param string $file
     *
     * @return Avatar
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get avatar file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set avatar file extension
     *
     * @param string $fileExt
     *
     * @return Avatar
     */
    public function setFileExt($fileExt)
    {
        $this->fileExt = $fileExt;

        return $this;
    }

    /**
     * Get avatar file extension
     *
     * @return string
     */
    public function getFileExt()
    {
        return $this->fileExt;
    }

    /**
     * Set investor
     *
     * @param Investor $investor
     *
     * @return Avatar
     */
    public function setInvestor($investor)
    {
        $this->investor = $investor;

        return $this;
    }

    /**
     * Get investor
     *
     * @return Investor
     */
    public function getInvestor()
    {
        return $this->investor;
    }

    /**
     * Converts uploaded file into blob and saves to database
     *
     * @param UploadedFile $file
     */
    public function upload(UploadedFile $file)
    {
        $this->setFile(stream_get_contents(fopen($file->getRealPath(), 'rb')));
        $this->setFileExt($file->guessExtension());
        $this->setReady(0);
    }

    /**
     * Clears entry and removes uploaded file
     */
    public function remove()
    {
        if ($this->url && $this->ready) {
            $this->url = '';
        }
    }
}
