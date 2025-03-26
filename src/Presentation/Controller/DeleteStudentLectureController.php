<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Presentation\Controller;

use Gwo\AppsRecruitmentTask\Application\Command\DeleteStudentFromLectureCommand;
use Gwo\AppsRecruitmentTask\Domain\Document\Lecture\Lecture;
use Gwo\AppsRecruitmentTask\Domain\DTO\DeleteStudentFromLectureDTOFactory;
use Gwo\AppsRecruitmentTask\Domain\Enum\PermissionLectureEnum;
use Gwo\AppsRecruitmentTask\Domain\Interface\Lecture\LectureReaderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class DeleteStudentLectureController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $commandBus,
        private readonly LoggerInterface $logger,
        private readonly TranslatorInterface $translator,
        private readonly LectureReaderInterface $lectureReaderRepository,
    ) {
    }

    #[Route('/api/lectures/{lectureUUID}/students/{studentUUID}', name: 'api.lectures.student.delete', methods: ['POST'])]
    public function deleteStudentFromLecture(string $lectureUUID, string $studentUUID, DeleteStudentFromLectureDTOFactory $dtoFactory): JsonResponse
    {
        try {
            $lecture = $this->lectureReaderRepository->findByUUID($lectureUUID);
            if (null === $lecture) {
                return new JsonResponse(['message' => $this->translator->trans('lecture.notFound', [], 'lectures')], Response::HTTP_NOT_FOUND);
            }

            $this->denyAccessUnlessGranted(PermissionLectureEnum::DELETE->value, $lecture);

            $dtoOrResponse = $dtoFactory->createFromRequest($lectureUUID, $studentUUID);
            if ($dtoOrResponse instanceof JsonResponse) {
                return $dtoOrResponse;
            }

            $this->commandBus->dispatch(new DeleteStudentFromLectureCommand($dtoOrResponse));

            return new JsonResponse(['message' => $this->translator->trans('lecture.student.delete.success', [], 'lectures')], Response::HTTP_CREATED);
        } catch (\Exception $error) {
            $message = sprintf('%s: %s', $this->translator->trans('lecture.student.delete.error', [], 'lectures'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
