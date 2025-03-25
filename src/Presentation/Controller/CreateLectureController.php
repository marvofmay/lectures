<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Presentation\Controller;

use Gwo\AppsRecruitmentTask\Domain\DTO\CreateLectureDTO;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class CreateLectureController extends AbstractController
{
    public function __construct(private Security $security, private readonly LoggerInterface $logger, private readonly TranslatorInterface $translator)
    {
    }

    #[Route('/api/lectures', name: 'api.lectures.create', methods: ['POST'])]
    public function create(): JsonResponse
    {
        try {
            //$createLectureAction->execute($createLectureDTO);
            $user = $this->security->getUser()->getRoles();
            var_dump($user);
            die();
            return new JsonResponse(
                ['message' => $this->translator->trans('lecture.add.success', [], 'lectures')],
                Response::HTTP_CREATED
            );
        } catch (\Exception $error) {
            $message = sprintf('%s: %s', $this->translator->trans('lecture.add.error', [], 'lectures'), $error->getMessage());
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
