<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Infrastructure\Persistence\MongoDB;

use MongoDB\Client;
use MongoDB\Driver\Command;
use MongoDB\Model\BSONDocument;

final readonly class DatabaseClient
{
    private Client $mongoClient;

    public function __construct(
        private string $databaseUri,
        private string $databaseName,
    ) {
        $this->mongoClient = new Client($this->databaseUri);
    }

    public function upsert(string $collectionName, array $query, array $document): void
    {
        $upsert = new Command([
            'update' => $collectionName,
            'updates' => [
                [
                    'q' => $query,
                    'u' => $document,
                    'upsert' => true,
                    'multi' => false,
                ],
            ],
        ]);

        $this->mongoClient->getManager()->executeCommand($this->databaseName, $upsert);
    }

    public function getByQuery(string $collectionName, array $query, array $options = []): array
    {
        $documents = $this->mongoClient
            ->selectCollection($this->databaseName, $collectionName)
            ->find($query, $options);

        return array_map(
            static fn(BSONDocument $document): array => $document->getArrayCopy(),
            $documents->toArray(),
        );
    }

    public function countDocuments(string $collectionName, array $query): int
    {
        return $this->mongoClient
            ->selectCollection($this->databaseName, $collectionName)
            ->countDocuments($query);
    }

    public function deleteDocuments(string $collectionName, array $query): void
    {
        $delete = new Command([
            'delete' => $collectionName,
            'deletes' => [
                [
                    'q' => $query,
                    'limit' => 1,
                ],
            ],
        ]);

        $this->mongoClient->getManager()->executeCommand($this->databaseName, $delete);
    }

    public function dropDatabase(): void
    {
        $this->mongoClient->dropDatabase($this->databaseName);
    }
}
