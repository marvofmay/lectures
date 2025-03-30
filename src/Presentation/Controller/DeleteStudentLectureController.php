<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Presentation\Controller;

use Gwo\AppsRecruitmentTask\Application\Command\DeleteStudentFromLectureCommand;
use Gwo\AppsRecruitmentTask\Domain\DTO\DeleteStudentFromLectureDTOFactory;
use Gwo\AppsRecruitmentTask\Domain\Enum\PermissionLectureEnum;
use Gwo\AppsRecruitmentTask\Domain\Exception\NotFoundLectureEnrollmentException;
use Gwo\AppsRecruitmentTask\Domain\Interface\Lecture\LectureReaderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Contracts\Translation\TranslatorInterface;

class DeleteStudentLectureController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $commandBus,
        private readonly TranslatorInterface $translator,
        private readonly LectureReaderInterface $lectureReaderRepository,
    ) {
    }

    #[Route('/api/lectures/{lectureUUID}/students/{studentUUID}', name: 'api.lectures.student.delete', methods: ['DELETE'])]
    public function deleteStudentFromLecture(string $lectureUUID, string $studentUUID, DeleteStudentFromLectureDTOFactory $dtoFactory): JsonResponse
    {
        try {
            $lecture = $this->lectureReaderRepository->findByUUID($lectureUUID);
            if (null === $lecture) {
                return new JsonResponse(['message' => $this->translator->trans('lecture.notFound', [], 'lectures')], Response::HTTP_NOT_FOUND);
            }

            $this->denyAccessUnlessGranted(PermissionLectureEnum::DELETE_STUDENT->value, $lecture);

            $dtoOrResponse = $dtoFactory->createFromRequest($lectureUUID, $studentUUID);
            if ($dtoOrResponse instanceof JsonResponse) {
                return $dtoOrResponse;
            }

            try {
                $this->commandBus->dispatch(new DeleteStudentFromLectureCommand($dtoOrResponse));
            } catch (HandlerFailedException $e) {
                throw $e->getPrevious();
            }

            return new JsonResponse(['message' => $this->translator->trans('lecture.student.delete.success', [], 'lectures')], Response::HTTP_OK);
        } catch (NotFoundLectureEnrollmentException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (AuthenticationCredentialsNotFoundException) {
            return new JsonResponse(['message' => $this->translator->trans('message.noLogin', [], 'messages')], Response::HTTP_BAD_REQUEST);
        } catch (AccessDeniedException) {
            return new JsonResponse(['message' => $this->translator->trans('message.noPermissions', [], 'messages')], Response::HTTP_FORBIDDEN);
        } catch (\Exception) {
            $message = sprintf('%s', $this->translator->trans('lecture.student.delete.error', [], 'lectures'));

            return new JsonResponse(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
