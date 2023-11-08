<?php

namespace App\Shortener\Service;

use App\ORM\Model\UrlConverterRepository;
use App\Shortener\Helpers\Validation\UrlValidator;
use App\Shortener\Interfaces\InterfaceUrlDecoder;
use App\Shortener\Interfaces\InterfaceUrlEncoder;
use App\Shortener\Repository\FileRepository;
use Exception;
use InvalidArgumentException;

class UrlConverter implements InterfaceUrlDecoder, InterfaceUrlEncoder
{
    /**
     * @var UrlValidator
     */
    protected $validator;
    /**
     * @var FileRepository
     */
    protected $fileRepository;

    /**
     * @var UrlConverterRepository
     */
    protected $databaseRepository;
    /**
     * @var
     */
    protected $numberCharCode;
    /**
     * @var
     */
    protected $codeSalt;

    /**
     * @var bool
     */
    protected bool $saveToDatabase;


    /**
     * @param UrlValidator $validator
     * @param FileRepository $fileRepository
     * @param UrlConverterRepository $databaseRepository
     * @param $numberCharCode
     * @param $codeSalt
     * @param $saveToDatabase
     */
    public function __construct(UrlValidator $validator, FileRepository $fileRepository, UrlConverterRepository $databaseRepository, $numberCharCode, $codeSalt, $saveToDatabase)
    {
        $this->validator = $validator;
        $this->fileRepository = $fileRepository;
        $this->databaseRepository = $databaseRepository;
        $this->numberCharCode = $numberCharCode;
        $this->codeSalt = $codeSalt;
        $this->saveToDatabase = $saveToDatabase;
    }

    /**
     * @param string $url
     * @return string
     */
    public function encode(string $url): string
    {
        $result = $this->prepareUrl($url);
        return $result;
    }

    /**
     * @param string $url
     * @return string
     */
    public function prepareUrl(string $url): string
    {
        $this->validator->validation($url);
        if (http_response_code() === 200) {
            if ($this->saveToDatabase) {
                if (!$this->databaseRepository->checkUrlDatabase($url)) {
                    $code = $this->codingUrl($url);
                    if ($this->databaseRepository->saveAll($code, $url)) {
                        return $code;
                    } else {
                        throw new Exception("Код и URL не были сохранены - ");
                    }
                } else {
                    return $this->databaseRepository->getCode($url);
                }
            } else {
                if ($this->fileRepository->checkUrlFile($url)) {
                    $code = $this->codingUrl($url);
                    if ($this->fileRepository->saveAll($code, $url)) {
                        return $code;
                    } else {
                        throw new Exception("Код и URL не были сохранены - ");
                    }
                } else {
                    return $this->fileRepository->getCode($url);
                }
            }
        } elseif (http_response_code() === 400) {
            throw new InvalidArgumentException("URL не существует или недоступен - ");
        }
    }

    /**
     * @param string $url
     * @return string
     */
    protected function codingUrl(string $url): string
    {
        $codeSalt = $this->getCodeSalt();
        $numberCharCode = $this->getNumberCharCode();

        $url = $url . $codeSalt;
        $urlArray = str_split($url);
        shuffle($urlArray);
        $urlShuffled = implode('', $urlArray);
        return mb_substr($urlShuffled, 0, $numberCharCode);
    }

    /**
     * @param string $code
     * @return string
     */
    public function decode(string $code): string
    {
        if ($this->saveToDatabase) {
            return $this->databaseRepository->getUrl($code);
        } else {
            return $this->fileRepository->getUrl($code);
        }
    }

    /**
     * @return mixed
     */
    public function getNumberCharCode()
    {
        return $this->numberCharCode;
    }

    /**
     * @param mixed $numberCharCode
     */
    public function setNumberCharCode($numberCharCode): void
    {
        $this->numberCharCode = $numberCharCode;
    }

    /**
     * @return mixed
     */
    public function getCodeSalt()
    {
        return $this->codeSalt;
    }

    /**
     * @param mixed $codeSalt
     */
    public function setCodeSalt($codeSalt): void
    {
        $this->codeSalt = $codeSalt;
    }
}