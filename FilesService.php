<?php

namespace test;

use DateTime;
use Exception;

class FilesService
{
    private string $userId;

    private string $dirPath;

    public function __construct(string $dirPath, string $userId)
    {
        $this->dirPath = $dirPath;
        $this->userId = $userId;
    }


    public function getFilesByUser(): array
    {
        $result = [];
        $dir = opendir($this->dirPath);

        while($file = readdir($dir)) {
            if (stripos($file, $this->userId) !== 0) {
                continue;
            }
            $result[] = $file;
        }

        return $result;
    }

    public function getAverageSizeInFilesArray(array $arrayPath): float
    {
        $sumAverages = 0;
        $count = 0;
        foreach ($arrayPath as $path) {
            $sumAverages += count(file($this->dirPath . "/" . $path));
            $count++;
        }
        return $sumAverages ? floatval($sumAverages / $count) : 0.0;
    }

    /**
     * @throws Exception
     */
    public static function formatDateInFileChange(string $filePath): int
    {
        $fd = fopen($filePath, 'r');
        if (!$fd) {
            throw new Exception('Не удалось открыть файл');
        }
        $count = 0;
        $pointer = 0;
        $arrayChangeStrings = [];
        while(!feof($fd)) {
            $str = fgets($fd);
            if (preg_match('/^(\d{1,2})\/(\d{1,2})(?:\/(\d{2}))?$/', $str)) {
                $date = new DateTime($str);
                $arrayChangeStrings[$pointer] = $date->format('m-d-Y');
                $count++;
            }
            $pointer++;
        }
        fclose($fd);

        self::replaceDate($arrayChangeStrings, $filePath);

        return $count;
    }

    public static function replaceDate(array $arrayChangeStrings, string $filePath): void
    {
        # заменяем в файле строки
        $array = file($filePath);
        if ($array) {
            foreach ($arrayChangeStrings as $key => $value) {
                $array[$key] = $value . "\n";
            }
        }
        # записываем обратно в файл
        file_put_contents($filePath, $array);
    }

    /**
     * @throws Exception
     */
    public static function moveUsersFiles(string $dirPath, string $dirPathOutput, array $filePaths): int
    {
        $dirPath = "/" . $dirPath . "/";
        $dirPathOutput = $dirPathOutput . "/";


        $systemName = php_uname();
        if (stripos($systemName, 'Windows') === 0) {
            $dirPath = "\\" . $dirPath . "\\";
            $dirPathOutput = $dirPathOutput . "\\";
        }

        if (!is_dir($dirPathOutput)) {
            mkdir($dirPathOutput);
        }
        $result = 0;
        foreach ($filePaths as $filePath) {
            $result += FilesService::formatDateInFileChange(__DIR__ . $dirPath . $filePath);
            if (!file_exists(__DIR__ . $dirPath .$filePath)) {
                throw new Exception('Файл не найден ' . __DIR__ . $dirPath . $filePath);
            }
            rename(__DIR__ . $dirPath . $filePath, $dirPathOutput . $filePath);
        }

        return $result;
    }

    public static function getFileInfoByUsersInfo(string $filePath, array $userData): array
    {
        /**  array <string, float> @var $infoByUser*/
        $infoByUser = [];
        foreach ($userData as $id => $fio) {
            $userFileHelper = new FilesService($filePath, $id);
            $usersFile = $userFileHelper->getFilesByUser();
            $infoByUser[$fio] = $userFileHelper->getAverageSizeInFilesArray($usersFile);
        }

        return $infoByUser;
    }

    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }

    public function setDirPath(string $dirPath): void
    {
        $this->dirPath = $dirPath;
    }
}
