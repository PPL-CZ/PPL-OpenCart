<?php
namespace PPLCZ\ModelNormalizer;

use PPLCZ\Data\BatchData;
use PPLCZ\Model\Model\BatchModel;
use PPLCZ\TLoader;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class BatchModelDenormalizer implements DenormalizerInterface
{
    use TLoader;

    public function __construct($registry)
    {
        $this->setRegistry($registry);
    }

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        if ($data instanceof BatchData) {
            $batch = new BatchModel();
            $batch->setId($data->id);
            $batch->setCreated($data->created_at);
            $batch->setLock($data->lock);
            $batch->setRemoteBatchId($data->remote_batch_id);
            return $batch;
        }

        return null;
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return $data instanceof BatchData && $type === BatchModel::class;
    }
}