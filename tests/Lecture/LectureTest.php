<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Tests\Lecture;

use Gwo\AppsRecruitmentTask\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class LectureTest extends ApiTestCase
{
    /** @test */
    public function lecturerCanCreateNewLecture(): void
    {
        $token = $this->loginUser();
        $lectureData = [
            'name' => 'KPI',
            'studentLimit' => 20,
            'startDate' => '2025-03-29 08:00',
            'endDate' => '2025-03-29 15:00',
        ];

        $lectureResponse = $this->makeRequest('POST', '/api/lectures', json_encode($lectureData), [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);

        $this->assertEquals(Response::HTTP_CREATED, $lectureResponse->getStatusCode());
    }

    /** @test */
    public function studentCannotCreateNewLecture(): void
    {
        $token = $this->loginUser(self::STUDENT_ETHAN);
        $lectureData = [
            'name' => 'Lecture about IT',
            'studentLimit' => 15,
            'startDate' => (new \DateTime('+1 day'))->format('Y-m-d H:i'),
            'endDate' => (new \DateTime('+1 day +4 hours'))->format('Y-m-d H:i'),
        ];

        $lectureResponse = $this->makeRequest('POST', '/api/lectures', json_encode($lectureData), [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);

        $this->assertEquals(Response::HTTP_FORBIDDEN, $lectureResponse->getStatusCode());
    }

    /** @test */
    public function studentCanEnrollToLecture(): void
    {
        $token = $this->loginUser(self::STUDENT_ETHAN);
        $lectureId = $this->getLectureIdByName(self::LECTURE_NAME);

        $enrollResponse = $this->makeRequest('POST', '/api/lectures/'.$lectureId.'/enroll', '', [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);

        $this->assertEquals(Response::HTTP_CREATED, $enrollResponse->getStatusCode());
    }

    /** @test */
    public function lecturerCanRemoveStudentFromOwnLecture(): void
    {
        $lectureName = 'PCK and CPK ...';
        $token = $this->loginUser(self::LECTURER_NATALIE);
        $lectureData = [
            'name' => $lectureName,
            'studentLimit' => 20,
            'startDate' => (new \DateTime('+1 day'))->format('Y-m-d H:i'),
            'endDate' => (new \DateTime('+1 day +4 hours'))->format('Y-m-d H:i'),
        ];

        $lectureResponse = $this->makeRequest('POST', '/api/lectures', json_encode($lectureData), [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);

        $lectureId = $this->getLectureIdByName($lectureName);
        $this->assertEquals(Response::HTTP_CREATED, $lectureResponse->getStatusCode());

        $token = $this->loginUser(self::STUDENT_ETHAN);
        $enrollResponse = $this->makeRequest('POST', '/api/lectures/'.$lectureId.'/enroll', '', [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);
        $this->assertEquals(Response::HTTP_CREATED, $enrollResponse->getStatusCode());

        $studentId = $this->getUserIdByName(self::STUDENT_ETHAN, self::ROLE_STUDENT);

        $token = $this->loginUser(self::LECTURER_NATALIE);
        $deleteStudentResponse = $this->makeRequest('DELETE', '/api/lectures/'.$lectureId.'/students/'.$studentId, '', [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);

        $this->assertEquals(Response::HTTP_OK, $deleteStudentResponse->getStatusCode());
    }

    /** @test */
    public function lecturerTriesToRemoveStudentFromNonExistentLecture(): void
    {
        $lectureName = 'Some ...';
        $token = $this->loginUser(self::LECTURER_DANIEL);
        $lectureData = [
            'name' => $lectureName,
            'studentLimit' => 20,
            'startDate' => (new \DateTime('+1 day'))->format('Y-m-d H:i'),
            'endDate' => (new \DateTime('+1 day +4 hours'))->format('Y-m-d H:i'),
        ];

        $lectureResponse = $this->makeRequest('POST', '/api/lectures', json_encode($lectureData), [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);

        $lectureId = $this->getLectureIdByName($lectureName);
        $this->assertEquals(Response::HTTP_CREATED, $lectureResponse->getStatusCode());

        $token = $this->loginUser(self::STUDENT_OLIVIA);
        $enrollResponse = $this->makeRequest('POST', '/api/lectures/'.$lectureId.'/enroll', '', [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);
        $this->assertEquals(Response::HTTP_CREATED, $enrollResponse->getStatusCode());

        $studentId = $this->getUserIdByName(self::STUDENT_ETHAN, self::ROLE_STUDENT);

        $token = $this->loginUser(self::LECTURER_NATALIE);
        $deleteStudentResponse = $this->makeRequest('DELETE', '/api/lectures/3f1a3b8e-9c2d-4e7b-b8a9-123456789abc/students/'.$studentId, '', [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $deleteStudentResponse->getStatusCode());
    }

    /** @test */
    public function cannotEnrollToLectureIfStudentLimitExceeded(): void
    {
        $this->addLimitedLecture();
        $token = $this->loginUser(self::STUDENT_ETHAN);
        $lectureId = $this->getLectureIdByName(self::LECTURE_LIMITED_NAME);
        $enrollResponse = $this->makeRequest('POST', '/api/lectures/'.$lectureId.'/enroll', '', [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);
        $this->assertEquals(Response::HTTP_CREATED, $enrollResponse->getStatusCode());

        $token = $this->loginUser(self::STUDENT_MICHAEL);
        $enrollResponse = $this->makeRequest('POST', '/api/lectures/'.$lectureId.'/enroll', '', [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);

        $this->assertEquals(Response::HTTP_CONFLICT, $enrollResponse->getStatusCode());
    }

    /** @test */
    public function cannotEnrollToLectureIfAlreadyStarted(): void
    {
        $this->addExpiredLecture();
        $token = $this->loginUser(self::STUDENT_ETHAN);
        $lectureId = $this->getLectureIdByName(self::LECTURE_EXPIRED_NAME);

        $enrollResponse = $this->makeRequest('POST', '/api/lectures/'.$lectureId.'/enroll', '', [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $enrollResponse->getStatusCode());
    }

    /** @test */
    public function cannotEnrollToSameLectureMoreThanOnce(): void
    {
        $token = $this->loginUser(self::STUDENT_ETHAN);

        $lectureId = $this->getLectureIdByName(self::LECTURE_NAME);
        $enrollResponse = $this->makeRequest('POST', '/api/lectures/'.$lectureId.'/enroll', '', [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);
        $this->assertEquals(Response::HTTP_CREATED, $enrollResponse->getStatusCode());

        $enrollResponse = $this->makeRequest('POST', '/api/lectures/'.$lectureId.'/enroll', '', [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);
        $this->assertEquals(Response::HTTP_CONFLICT, $enrollResponse->getStatusCode());
    }

    /** @test */
    public function studentCanFetchListOfEnrolledLectures(): void
    {
        $token = $this->loginUser(self::STUDENT_ETHAN);
        $lectureId = $this->getLectureIdByName(self::LECTURE_NAME);

        $enrollResponse = $this->makeRequest('POST', '/api/lectures/'.$lectureId.'/enroll', '', [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);
        $this->assertEquals(Response::HTTP_CREATED, $enrollResponse->getStatusCode());

        $listLecturesResponse = $this->makeRequest('GET', '/api/students/lectures', '', [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);

        $responseData = json_decode($listLecturesResponse->getContent(), true);

        $this->assertArrayHasKey('data', $responseData, 'Brak klucza "data" w odpowiedzi.');
        $this->assertIsArray($responseData['data'], 'Pole "data" nie jest tablicą.');
        $this->assertNotEmpty($responseData['data'], 'Tablica "data" jest pusta.');

        $lecture = $responseData['data'][0];

        $this->assertArrayHasKey('name', $lecture, 'Brak klucza "name" w odpowiedzi.');
        $this->assertArrayHasKey('lecturerName', $lecture, 'Brak klucza "lecturerName" w odpowiedzi.');
        $this->assertArrayHasKey('startDate', $lecture, 'Brak klucza "startDate" w odpowiedzi.');
        $this->assertArrayHasKey('endDate', $lecture, 'Brak klucza "endDate" w odpowiedzi.');
        $this->assertArrayHasKey('studentLimit', $lecture, 'Brak klucza "studentLimit" w odpowiedzi.');

        $this->assertSame('Lecture about education', $lecture['name'], 'Niepoprawna nazwa wykładu.');
        $this->assertSame('Emma', $lecture['lecturerName'], 'Niepoprawne imię wykładowcy.');
        $this->assertSame((new \DateTime('+1 day'))->format('Y-m-d H:i'), $lecture['startDate'], 'Niepoprawna data rozpoczęcia.');
        $this->assertSame((new \DateTime('+1 day +4 hours'))->format('Y-m-d H:i'), $lecture['endDate'], 'Niepoprawna data zakończenia.');
        $this->assertSame(10, $lecture['studentLimit'], 'Niepoprawny limit studentów.');
    }
}
