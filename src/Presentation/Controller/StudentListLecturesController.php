<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Presentation\Controller;

use Gwo\AppsRecruitmentTask\Application\Query\StudentListLecturesQuery;
use Gwo\AppsRecruitmentTask\Domain\Document\Lecture\Lecture;
use Gwo\AppsRecruitmentTask\Domain\Enum\PermissionLectureEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Contracts\Translation\TranslatorInterface;

class StudentListLecturesController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $queryBus,
        private readonly TranslatorInterface $translator,
        private readonly Security $security,
    ) {
    }

    #[Route('/api/students/lectures', name: 'api.students.lectures.list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        try {
            $this->denyAccessUnlessGranted(PermissionLectureEnum::LIST_LECTURES->value, Lecture::class);

            try {
                $handledStamp = $this->queryBus->dispatch(new StudentListLecturesQuery((string) $this->security->getUser()->getId()));
            } catch (HandlerFailedException $e) {
                throw $e->getPrevious();
            }

            return new JsonResponse(['data' => $handledStamp->last(HandledStamp::class)->getResult()], Response::HTTP_OK);
        } catch (AuthenticationCredentialsNotFoundException) {
            return new JsonResponse(['message' => $this->translator->trans('message.noLogin', [], 'messages')], Response::HTTP_BAD_REQUEST);
        } catch (AccessDeniedException) {
            return new JsonResponse(['message' => $this->translator->trans('message.noPermissions', [], 'messages')], Response::HTTP_FORBIDDEN);
        } catch (\Exception) {
            $message = sprintf('%s', $this->translator->trans('student.lectures.list.error', [], 'students'));

            return new JsonResponse(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
