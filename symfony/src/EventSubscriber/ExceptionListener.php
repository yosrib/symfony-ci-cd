<?php

namespace App\EventSubscriber;

use App\Exception\ExceptionNormalizer\ExceptionNormalizer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;

class ExceptionListener implements EventSubscriberInterface
{
    public function __construct(
        protected ExceptionNormalizer $exceptionNormalizer,
        protected SerializerInterface $serializer
    ) {
    }

    public function processException(ExceptionEvent $event): void
    {
        if ($event->getThrowable() instanceof HttpExceptionInterface) {
            $event->setResponse(new Response($this->serializer->serialize(
                $this->exceptionNormalizer->normalize($event->getThrowable()), 'json'
            )));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => [['processException', 255]],
        ];
    }
}