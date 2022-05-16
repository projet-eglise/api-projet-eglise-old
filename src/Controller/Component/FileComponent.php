<?php

declare(strict_types=1);

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Http\Exception\BadRequestException;
use Filestack\FilestackClient;
use Laminas\Diactoros\UploadedFile;

/**
 * File component
 */
class FileComponent extends Component
{
    protected $_defaultConfig = [];
    private FilestackClient $FilestackClient;

    /**
     * initialize class
     *
     * @param array $config
     */
    public function initialize(array $config): void
    {
        $this->FilestackClient = new FilestackClient(Configure::read('FilestackApiKey'));
    }

    /**
     * Checks if an image is good or not.
     *
     * @param UploadedFile $image
     * @return void
     */
    public function checkImageFile(UploadedFile $image)
    {
        if ($image->getSize() > 10000000) {
            throw new BadRequestException("Votre image est trop grosse ...");
        }
        if ($image->getSize() == 0) {
            throw new BadRequestException("L'image n'a pas de taille");
        }

        $imageSize = @getimagesize($image->getStream()->getMetadata()["uri"]);
        if (!in_array($image->getClientMediaType(),  ['image/jpg', 'image/png', 'image/jpeg']) || $imageSize === false) {
            throw new BadRequestException("Nous avons un problème avec votre image");
        }
    }

    /**
     * Upload a file and return the link to retrieve the image.
     *
     * @param string $fileLink
     * @return string
     */
    public function upload(string $fileLink): string
    {
        $file = $this->FilestackClient->upload($fileLink);
        return $file->url();
    }
}
