<?php

namespace App\Listener;

use App\Entity\Property;
use Doctrine\Common\EventSubscriber;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ImageCacheSubscriber implements EventSubscriber {

    /**
     * @var CacheManager
     */
    private $cacheManager;


    /**
     * @var $uploaderHelper
     */
    private $uploaderHelper;
    public function __construct(CacheManager $cacheManager, UploaderHelper $uploaderHelper)
    {
        $this->cacheManager = $cacheManager;
        $this->uploaderHelper = $uploaderHelper;
    }

    public function getSubscribedEvents()
    {
        return[
            'preRemove',
            'PreUpdate'
        ];
    }

    public function preRemove(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        if(!$entity instanceof Property) {
            return;
        }
        $this->cacheManager->remove($this->uploaderHelper->asset($entity,'imageFile'));

    }

    public function preUpdate(PreFlushEventArgs $args) {
        $entity = $args->getEntity();
        if(!$entity instanceof Property){
            return;
        }

        if($entity->getImageFile() instanceof UploadedFile) {
            $this->cacheManager->remove($this->uploaderHelper->asset($entity,'imageFile'));
        }
    }
}