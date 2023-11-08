<?php

namespace App\ORM\Model;

use Illuminate\Database\Eloquent\Model;
use App\Shortener\Interfaces\InterfaceRepository;
use InvalidArgumentException;

class UrlConverterRepository extends Model implements InterfaceRepository
{
    /**
     * @var string
     */
    protected $table = 'url_converter';
    /**
     * @var string[]
     */
    protected $fillable = ['url', 'code'];
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @param string $code
     * @param string $url
     * @return bool
     */
    public function saveAll(string $code, string $url): bool
    {
        $this->url = $url;
        $this->code = $code;
        return $this->save();
    }

    /**
     * @param string $code
     * @return string
     */
    public function getUrl(string $code): string
    {
        $result = self::where('code', $code)->first();
        if($result !== null){
            return $result->url;
        }else {
            throw new InvalidArgumentException("Не удалось разкодировать, такого Url в базе данных не существует - ");
        }
    }

    /**
     * @param string $url
     * @return string
     */
    public function getCode(string $url): string
    {
        $result = self::where('url', $url)->first();
        return $result->code;
    }

    /**
     * @param string $url
     * @return mixed
     */
    public function checkUrlDatabase(string $url){
        return self::where('url', $url)->first();
    }
}