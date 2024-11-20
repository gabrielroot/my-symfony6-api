<?php

namespace App\EventListener;

use DateTime;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use App\Interface\IAudit;
use Symfony\Component\Uid\Uuid;

class AuditListener
{
    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->setAuditTrait($args);
        $this->setUuidTrait($args);
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->setAuditTrait($args);
    }
    private function setAuditTrait(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (is_subclass_of($entity, IAudit::class)) {
            $this->setDateTime($entity);
        }
    }

    private function setUuidTrait(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity->getId() && property_exists($entity, 'uuid')) {
            $entity->setUuid(Uuid::v4());
        }
    }

    private function setDateTime(IAudit $entity): void
    {
        $entity->setUpdatedAt(new DateTime('now'));

        if (!$entity->getId())
            $entity->setCreatedAt(new DateTime('now'));
    }
}
