<?php

namespace SymfonyExtraBundle\SwiftmailerExtension\Plugin;

use Swift_Events_SendEvent;
use Swift_Events_SendListener;

/**
 * @author Alex Oleshkevich <alex.oleshkevich@muehlemann-popp.ch>
 */
class AutoFromHeaderPlugin implements Swift_Events_SendListener
{
    protected $senderEmail;
    protected $senderName;
    
    public function __construct($email = null, $name = null)
    {
        $this->senderEmail = $email;
        $this->senderName = $name;
    }

    /**
     * @param Swift_Events_SendEvent $evt
     */
    public function beforeSendPerformed(Swift_Events_SendEvent $evt)
    {
        $message = $evt->getMessage();
        if (empty($message->getFrom()) && $this->senderEmail) {
            $message->setFrom($this->senderEmail, $this->senderName);
        }
    }

    /**
     * Not used.
     */
    public function sendPerformed(Swift_Events_SendEvent $evt)
    {
        
    }
}
