<?php 

namespace App\Form;

use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\String\Slugger\AsciiSlugger;

class FormListenerFactory {
    
    function autoSlug(string $field) : callable {
        return function (PreSubmitEvent $preSubmitEvent) use ($field){
            $data = $preSubmitEvent->getData();
            if (empty($data['slug'])) {
                $slugger = new AsciiSlugger();
                $data['slug'] = strtolower($slugger->slug($data[$field]));
                $preSubmitEvent->setData($data);
            }
        };


    }


    public function timestamps() : callable{
        return function (PostSubmitEvent $postSubmitEvent){
            $data = $postSubmitEvent->getData();
            
            $data->setUpdatedAt(new \DateTimeImmutable());
            if (!$data->getId()) {
                $data->setCreatedAt(new \DateTimeImmutable());
            }
        };
    }


}
