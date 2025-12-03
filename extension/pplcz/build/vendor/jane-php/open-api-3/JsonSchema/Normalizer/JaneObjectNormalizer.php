<?php

namespace PPLCZVendor\Jane\Component\OpenApi3\JsonSchema\Normalizer;

use PPLCZVendor\Jane\Component\OpenApi3\JsonSchema\Runtime\Normalizer\CheckArray;
use PPLCZVendor\Jane\Component\OpenApi3\JsonSchema\Runtime\Normalizer\ValidatorTrait;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\NormalizerInterface;
class JaneObjectNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    use CheckArray;
    use ValidatorTrait;
    protected $normalizers = array('PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\OpenApi' => 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\OpenApiNormalizer', 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Reference' => 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\ReferenceNormalizer', 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Info' => 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\InfoNormalizer', 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Contact' => 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\ContactNormalizer', 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\License' => 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\LicenseNormalizer', 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Server' => 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\ServerNormalizer', 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\ServerVariable' => 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\ServerVariableNormalizer', 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Components' => 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\ComponentsNormalizer', 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Schema' => 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\SchemaNormalizer', 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Discriminator' => 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\DiscriminatorNormalizer', 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\XML' => 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\XMLNormalizer', 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Response' => 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\ResponseNormalizer', 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\MediaType' => 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\MediaTypeNormalizer', 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Example' => 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\ExampleNormalizer', 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Header' => 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\HeaderNormalizer', 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\PathItem' => 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\PathItemNormalizer', 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Operation' => 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\OperationNormalizer', 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Responses' => 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\ResponsesNormalizer', 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Tag' => 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\TagNormalizer', 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\ExternalDocumentation' => 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\ExternalDocumentationNormalizer', 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Parameter' => 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\ParameterNormalizer', 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\RequestBody' => 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\RequestBodyNormalizer', 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\APIKeySecurityScheme' => 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\APIKeySecuritySchemeNormalizer', 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\HTTPSecurityScheme' => 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\HTTPSecuritySchemeNormalizer', 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\HTTPSecuritySchemeSub' => 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\HTTPSecuritySchemeSubNormalizer', 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\OAuth2SecurityScheme' => 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\OAuth2SecuritySchemeNormalizer', 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\OpenIdConnectSecurityScheme' => 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\OpenIdConnectSecuritySchemeNormalizer', 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\OAuthFlows' => 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\OAuthFlowsNormalizer', 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\ImplicitOAuthFlow' => 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\ImplicitOAuthFlowNormalizer', 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\PasswordOAuthFlow' => 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\PasswordOAuthFlowNormalizer', 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\ClientCredentialsFlow' => 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\ClientCredentialsFlowNormalizer', 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\AuthorizationCodeOAuthFlow' => 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\AuthorizationCodeOAuthFlowNormalizer', 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Link' => 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\LinkNormalizer', 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Encoding' => 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\EncodingNormalizer', 'PPLCZVendor\\Jane\\Component\\JsonSchemaRuntime\\Reference' => 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Runtime\\Normalizer\\ReferenceNormalizer'), $normalizersCache = array();
    public function supportsDenormalization($data, $type, $format = null, $context = []) : bool
    {
        return \array_key_exists($type, $this->normalizers);
    }
    public function supportsNormalization($data, $format = null, $context = []) : bool
    {
        return \is_object($data) && \array_key_exists(\get_class($data), $this->normalizers);
    }
    /**
     * @return array|string|int|float|bool|\ArrayObject|null
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $normalizerClass = $this->normalizers[\get_class($object)];
        $normalizer = $this->getNormalizer($normalizerClass);
        return $normalizer->normalize($object, $format, $context);
    }
    /**
     * @return mixed
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        $denormalizerClass = $this->normalizers[$class];
        $denormalizer = $this->getNormalizer($denormalizerClass);
        return $denormalizer->denormalize($data, $class, $format, $context);
    }
    private function getNormalizer(string $normalizerClass)
    {
        return $this->normalizersCache[$normalizerClass] ?? $this->initNormalizer($normalizerClass);
    }
    private function initNormalizer(string $normalizerClass)
    {
        $normalizer = new $normalizerClass();
        $normalizer->setNormalizer($this->normalizer);
        $normalizer->setDenormalizer($this->denormalizer);
        $this->normalizersCache[$normalizerClass] = $normalizer;
        return $normalizer;
    }
}
