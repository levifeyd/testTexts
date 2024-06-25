<?php

namespace test;

use Exception;

class CsvFileReader
{
    private int $IndexId = 0;
    private int $IndexFio = 1;
    private string $csvFilename;
    private string $separator;

    public function __construct(string $csvFilename, string $separator)
    {
        $this->csvFilename = $csvFilename;
        $this->separator = $separator;
    }

    /**
     * @throws Exception
     */
    public function getUserDataFromFile(): array
    {
        $handle = fopen($this->csvFilename, "r");
        if ($handle === false) {
            throw new Exception('Ошибка открытия файла, ' . $this->csvFilename);
        }

        /**  array <int, string> @var $userData*/
        $userData = [];
        $iterator = 0;
        while ($data = fgetcsv($handle, 10000, $this->separator)) {
            # пропускаем первую итерацию, т.к там данные о полях
            if ($iterator && isset($data[$this->IndexFio])) {
                $userData[$data[$this->IndexId]] = $data[$this->IndexFio];
            } else if (!isset($data[$this->IndexFio])) {
                throw new Exception("Некорректный разделить для обработки CSV файла" . $this->separator);
            }
            $iterator++;
        }
        fclose($handle);

        return $userData;
    }

    public function setIndexId(int $IndexId): void
    {
        $this->IndexId = $IndexId;
    }

    public function setIndexFio(int $IndexFio): void
    {
        $this->IndexFio = $IndexFio;
    }

    public function setCsvFilename(string $csvFilename): void
    {
        $this->csvFilename = $csvFilename;
    }

    public function setSeparator(string $separator): void
    {
        $this->separator = $separator;
    }
}
