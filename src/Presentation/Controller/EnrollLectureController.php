<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Presentation\Controller;

use Gwo\AppsRecruitmentTask\Application\Command\EnrollLectureCommand;
use Gwo\AppsRecruitmentTask\Domain\Document\Lecture\Lecture;
use Gwo\AppsRecruitmentTask\Domain\DTO\EnrollLectureDTOFactory;
use Gwo\AppsRecruitmentTask\Domain\Enum\PermissionLectureEnum;
use Gwo\AppsRecruitmentTask\Domain\Service\LectureEnroller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Contracts\Translation\TranslatorInterface;

class EnrollLectureController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $commandBus,
        private readonly TranslatorInterface $translator,
        private readonly LectureEnroller $lectureEnroller,
        private Security $security,
    ) {
    }

    #[Route('/api/lectures/{uuid}/enroll', name: 'api.lectures.enroll', methods: ['POST'])]
    public function enroll(string $uuid, EnrollLectureDTOFactory $dtoFactory): JsonResponse
    {
        try {
            $this->denyAccessUnlessGranted(PermissionLectureEnum::ENROLL->value, Lecture::class);

            $dtoOrResponse = $dtoFactory->createFromRequest($uuid);
            if ($dtoOrResponse instanceof JsonResponse) {
                return $dtoOrResponse;
            }

            $errors = $this->lectureEnroller->validateBeforeQueuedEnrollment($uuid);
            if (0 === count($errors)) {
                $this->commandBus->dispatch(new EnrollLectureCommand($dtoOrResponse, (string) $this->security->getUser()->getId()));

                // uncomment if rabbitmq
                //$message = sprintf('%s: %s', $this->translator->trans('lecture.enroll.queued.success', [], 'lectures'));

                $message = sprintf('%s', $this->translator->trans('lecture.enroll.add.success', [], 'lectures'));

                return new JsonResponse(['message' => $message], Response::HTTP_CREATED);
            } else {
                $message = sprintf('%s: %s', $this->translator->trans('lecture.enroll.add.error', [], 'lectures'), $errors['message']);

                return new JsonResponse(['message' => $message], $errors['code']);
            }
        } catch (AuthenticationCredentialsNotFoundException) {
            return new JsonResponse(['message' => $this->translator->trans('message.noLogin', [], 'messages')], Response::HTTP_BAD_REQUEST);
        } catch (AccessDeniedException) {
            return new JsonResponse(['message' => $this->translator->trans('message.noPermissions', [], 'messages')], Response::HTTP_FORBIDDEN);
        } catch (\Exception) {
            $message = sprintf('%s', $this->translator->trans('lecture.enroll.queued.error', [], 'lectures'));

            return new JsonResponse(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
