<?php

namespace test;

use Exception;

class Parser
{
    private bool $commaFlag = false;
    private bool $semicolonFlag = false;
    private bool $countAverageLineCountFlag = false;
    private bool $replaceDates = false;

    private string $textsPathDir;
    private string $pathToCsvFile;

    private string $outputDirPath;

    private string $separatorSemicolon = ";";
    private string $separatorComa = ",";

    private string $optionComa = "comma";
    private string $optionSemicolon= "semicolon";

    private string  $optionCountAverageLineCount = 'countAverageLineCount';
    private string  $optionReplaceDates = 'replaceDates';


    public function __construct(string $textsPathDir, string $pathToCsvFile, string $outputDirPath)
    {
        $this->textsPathDir = $textsPathDir;
        $this->pathToCsvFile = $pathToCsvFile;
        $this->outputDirPath = $outputDirPath;
    }

    public function setOptions(array $options): void
    {
        foreach ($options as $option) {
            if ($option == $this->optionComa) {
                $this->commaFlag = true;
            } elseif ($option == $this->optionSemicolon) {
                $this->semicolonFlag = true;
            } elseif ($option == $this->optionCountAverageLineCount) {
                $this->countAverageLineCountFlag = true;
            } elseif ($option == $this->optionReplaceDates) {
                $this->replaceDates = true;
            }
        }
    }

    /**
     * @throws Exception
     */
    public function getResultOfParsing(): array
    {
        $results = null;

        if ($this->commaFlag && $this->countAverageLineCountFlag) {
            $results = $this->getCountAverageLineCountByUser($this->separatorComa);
        } elseif ($this->semicolonFlag && $this->countAverageLineCountFlag) {
            $results = $this->getCountAverageLineCountByUser($this->separatorSemicolon);
        } elseif ($this->commaFlag && $this->replaceDates) {
            $results = $this->replaceDates($this->separatorComa);
        } elseif ($this->semicolonFlag && $this->replaceDates) {
            $results = $this->replaceDates($this->separatorSemicolon);
        }

        return $results;
    }

    /**
     * @throws Exception
     */
    private function getCountAverageLineCountByUser(string $separator): array
    {
        $parserCSV = new CsvFileReader($this->pathToCsvFile, $separator);
        $userData = $parserCSV->getUserDataFromFile();
        return FilesService::getFileInfoByUsersInfo($this->textsPathDir, $userData);
    }

    /**
     * @throws Exception
     */
    private function replaceDates(string $separator): array
    {
        $parserCSV = new CsvFileReader($this->pathToCsvFile, $separator);
        $userData = $parserCSV->getUserDataFromFile();

        /**  array <string, float> @var $resultsArray*/
        $resultsArray = [];

        foreach ($userData as $userId => $fio) {
            $userFileHelper = new FilesService($this->textsPathDir, $userId);
            $result = $userFileHelper->getFilesByUser();
            $results = FilesService::moveUsersFiles($this->textsPathDir, $this->outputDirPath, $result);
            $resultsArray[$fio] = $results;
        }

        return $resultsArray;
    }

    public function setTextsPathDir(string $textsPathDir): void
    {
        $this->textsPathDir = $textsPathDir;
    }

    public function setPathToCsvFile(string $pathToCsvFile): void
    {
        $this->pathToCsvFile = $pathToCsvFile;
    }

    public function setOutputDirPath(string $outputDirPath): void
    {
        $this->outputDirPath = $outputDirPath;
    }

    public function setSeparatorSemicolon(string $separatorSemicolon): void
    {
        $this->separatorSemicolon = $separatorSemicolon;
    }

    public function setSeparatorComa(string $separatorComa): void
    {
        $this->separatorComa = $separatorComa;
    }

    public function setOptionComa(string $optionComa): void
    {
        $this->optionComa = $optionComa;
    }

    public function setOptionSemicolon(string $optionSemicolon): void
    {
        $this->optionSemicolon = $optionSemicolon;
    }

    public function setOptionReplaceDates(string $optionReplaceDates): void
    {
        $this->optionReplaceDates = $optionReplaceDates;
    }

}
