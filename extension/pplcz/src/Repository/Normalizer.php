<?php
namespace PPLCZ\Repository;

use Opencart\System\Engine\Model;
use PPLCZ\Serializer;

class Normalizer extends Model
{
    private $serializer;

    public function normalize($data, ?string $format = null, array $context = [])
    {
        if (!$this->serializer)
            $this->serializer = new Serializer($this->registry);

        return $this->serializer->normalize($data, $format, $context);
    }

    public function denormalize($data, string $type, array $context = [])
    {
        if (!$this->serializer)
            $this->serializer = new Serializer($this->registry);
        return $this->serializer->denormalize($data, $type, null, $context );
    }
}