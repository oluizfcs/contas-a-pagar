<?php

namespace App\Controllers\Services;

use App\Models\Database;

class Pagination
{
    private String $table;
    private int $maxRowsPerPage;
    private int $lastPage;

    public function __construct(string $table, int $maxRowsPerPage)
    {
        $this->table = $table;
        $this->maxRowsPerPage = $maxRowsPerPage;

        $this->calculateLastPage();
    }

    private function calculateLastPage(): void
    {
        $rowCount = Database::countAll($this->table);

        $this->lastPage = (int) ($rowCount / $this->maxRowsPerPage);

        if ($rowCount % $this->maxRowsPerPage != 0) {
            $this->lastPage++;
        }
    }

    public function getPage(int $page, string $columns): array
    {
        return Database::getByPage($this->table, $columns, $this->maxRowsPerPage, $page);
    }

    public function getLastPage(): int
    {
        return $this->lastPage;
    }

    public function getMaxRowsPerPage(): int
    {
        return $this->maxRowsPerPage;
    }
}
