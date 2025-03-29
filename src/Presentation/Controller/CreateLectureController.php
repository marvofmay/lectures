<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Presentation\Controller;

use Gwo\AppsRecruitmentTask\Application\Command\CreateLectureCommand;
use Gwo\AppsRecruitmentTask\Domain\Document\Lecture\Lecture;
use Gwo\AppsRecruitmentTask\Domain\DTO\CreateLectureDTOFactory;
use Gwo\AppsRecruitmentTask\Domain\Enum\PermissionLectureEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Contracts\Translation\TranslatorInterface;

class CreateLectureController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $commandBus,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Route('/api/lectures', name: 'api.lectures.create', methods: ['POST'])]
    public function create(Request $request, CreateLectureDTOFactory $dtoFactory): JsonResponse
    {
        try {
            $this->denyAccessUnlessGranted(PermissionLectureEnum::CREATE->value, Lecture::class);

            $dtoOrResponse = $dtoFactory->createFromRequest($request);
            if ($dtoOrResponse instanceof JsonResponse) {
                return $dtoOrResponse;
            }

            $this->commandBus->dispatch(new CreateLectureCommand($dtoOrResponse));

            return new JsonResponse(['message' => $this->translator->trans('lecture.add.success', [], 'lectures')], Response::HTTP_CREATED);
        } catch (AuthenticationCredentialsNotFoundException) {
            return new JsonResponse(['message' => $this->translator->trans('message.noLogin', [], 'messages')], Response::HTTP_BAD_REQUEST);
        } catch (AccessDeniedException) {
            return new JsonResponse(['message' => $this->translator->trans('message.noPermissions', [], 'messages')], Response::HTTP_FORBIDDEN);
        } catch (\Exception) {
            $message = sprintf('%s', $this->translator->trans('lecture.add.error', [], 'lectures'));
            return new JsonResponse(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
