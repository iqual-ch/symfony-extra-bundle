<?php
namespace SymfonyExtraBundle\SwiftmailerExtension\Transport;

use Hip\MandrillBundle\Dispatcher;
use Hip\MandrillBundle\Message;
use Swift_Attachment;
use Swift_Events_EventDispatcher;
use Swift_Events_EventListener;
use Swift_Events_SendEvent;
use Swift_Mime_Message;
use Swift_Transport;
use Symfony\Component\HttpKernel\Kernel;

class MandrillTransport implements Swift_Transport
{
    /** The event dispatcher from the plugin API */
    private $_eventDispatcher;
    
    /**
     * @var Dispatcher
     */
    private $mandrillDispatcher;

    /**
     * Constructor.
     */
    public function __construct(Swift_Events_EventDispatcher $eventDispatcher, Kernel $kernel)
    {
        $this->_eventDispatcher = $eventDispatcher;
        $this->mandrillDispatcher = $kernel->getContainer()->get('hip_mandrill.dispatcher');
    }

    /**
     * Tests if this Transport mechanism has started.
     *
     * @return bool
     */
    public function isStarted()
    {
        return true;
    }

    /**
     * Starts this Transport mechanism.
     */
    public function start()
    {
    }

    /**
     * Stops this Transport mechanism.
     */
    public function stop()
    {
    }

    /**
     * Sends the given message.
     *
     * @param Swift_Mime_Message $message
     * @param string[]           $failedRecipients An array of failures by-reference
     *
     * @return int     The number of sent emails
     */
    public function send(Swift_Mime_Message $message, &$failedRecipients = null)
    {
        if ($evt = $this->_eventDispatcher->createSendEvent($this, $message)) {
            $this->_eventDispatcher->dispatchEvent($evt, 'beforeSendPerformed');
            if ($evt->bubbleCancelled()) {
                return 0;
            }
        }

        if ($evt) {
            $fromEmail = key($message->getFrom());
            $fromName = current($message->getFrom());
            
            $toEmail = key($message->getTo());
            $toName = current($message->getTo());
            
            $mandrillMessage = new Message;
            $mandrillMessage
                ->setFromEmail($fromEmail)
                ->setFromName($fromName)
                ->setBccAddress($message->getBcc())
                ->addTo($toEmail, $toName)
                ->setSubject($message->getSubject())
                ->setHtml($message->getBody());
            
            if ($message->getReplyTo()) {
                $mandrillMessage->setReplyTo(key($message->getReplyTo()));
            }

            foreach ($message->getChildren() as $child) {
                if ($child instanceof Swift_Attachment) {
                    $mandrillMessage->addAttachment($child->getContentType(), $child->getFilename(), base64_encode($child->getBody()));
                }
            }
            
            $this->mandrillDispatcher->send($mandrillMessage);
            
            $evt->setResult(Swift_Events_SendEvent::RESULT_SUCCESS);
            $this->_eventDispatcher->dispatchEvent($evt, 'sendPerformed');
        }

        $count = (
            count((array) $message->getTo())
            + count((array) $message->getCc())
            + count((array) $message->getBcc())
            );

        return $count;
    }

    /**
     * Register a plugin.
     *
     * @param Swift_Events_EventListener $plugin
     */
    public function registerPlugin(Swift_Events_EventListener $plugin)
    {
        $this->_eventDispatcher->bindEventListener($plugin);
    }
}
