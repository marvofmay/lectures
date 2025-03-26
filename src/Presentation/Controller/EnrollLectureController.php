<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Presentation\Controller;

use Gwo\AppsRecruitmentTask\Application\Command\EnrollLectureCommand;
use Gwo\AppsRecruitmentTask\Domain\Document\Lecture\Lecture;
use Gwo\AppsRecruitmentTask\Domain\DTO\EnrollLectureDTOFactory;
use Gwo\AppsRecruitmentTask\Domain\Enum\PermissionLectureEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class EnrollLectureController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $commandBus,
        private readonly TranslatorInterface $translator
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

            $this->commandBus->dispatch(new EnrollLectureCommand($dtoOrResponse));

            return new JsonResponse(['message' => $this->translator->trans('lecture.enroll.add.success', [], 'lectures')], Response::HTTP_CREATED);
        } catch (\Exception $error) {
            $message = sprintf('%s: %s', $this->translator->trans('lecture.enroll.add.error', [], 'lectures'), $error->getMessage());

            return new JsonResponse(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
