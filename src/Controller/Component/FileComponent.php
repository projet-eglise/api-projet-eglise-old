<?php

declare(strict_types=1);

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Filestack\FilestackClient;

/**
 * File component
 */
class FileComponent extends Component
{
    protected $_defaultConfig = [];
    private FilestackClient $filestackClient;

    /**
     * initialize class
     *
     * @param array $config
     */
    public function initialize(array $config): void
    {
        $this->filestackClient = new FilestackClient(Configure::read('FilestackApiKey'));
    }

    /**
     * Upload a file and return the link to retrieve the image.
     *
     * @param string $fileLink
     * @return string
     */
    public function upload(string $fileLink): string
    {
        $file = $this->filestackClient->upload($fileLink);
        print_r($file->url());
        print_r($file);
        return $file->url();
    }
}
