<?php

namespace Synapse\Email;

use Synapse\Entity\AbstractEntity;

/**
 * Email entity
 */
class EmailEntity extends AbstractEntity
{
    /**
     * Possible values for the status field
     */
    const STATUS_PENDING  = 'pending';
    const STATUS_QUEUED   = 'queued';
    const STATUS_SENT     = 'sent';
    const STATUS_REJECTED = 'rejected';
    const STATUS_ERROR    = 'error';
    const STATUS_UNKNOWN  = 'unknown';

    /**
     * {@inheritDoc}
     */
    protected $object = [
        'id'              => null,
        'hash'            => null,
        'status'          => null,
        'subject'         => null,
        'recipient_email' => null,
        'recipient_name'  => null,
        'sender_email'    => null,
        'sender_name'     => null,
        'template_name'   => null,
        'template_data'   => null,
        'message'         => null,
        'bcc'             => null,
        'attachments'     => null,
        'headers'         => null,
        'sent'            => null,
        'created'         => null,
        'updated'         => null,
    ];

    /**
     * {@inheritDoc}
     */
    public function exchangeArray(array $values)
    {
        $entity = parent::exchangeArray($values);

        $entity->setCreated(time());
        $entity->setStatus(self::STATUS_PENDING);

        return $entity;
    }
}
