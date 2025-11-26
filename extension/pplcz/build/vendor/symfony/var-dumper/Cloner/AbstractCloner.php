<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PPLCZVendor\Symfony\Component\VarDumper\Cloner;

use PPLCZVendor\Symfony\Component\VarDumper\Caster\Caster;
use PPLCZVendor\Symfony\Component\VarDumper\Exception\ThrowingCasterException;
/**
 * AbstractCloner implements a generic caster mechanism for objects and resources.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
abstract class AbstractCloner implements ClonerInterface
{
    public static $defaultCasters = ['__PHP_Incomplete_Class' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\Caster', 'castPhpIncompleteClass'], 'PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\CutStub' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'castStub'], 'PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\CutArrayStub' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'castCutArray'], 'PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\ConstStub' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'castStub'], 'PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\EnumStub' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'castEnum'], 'Fiber' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\FiberCaster', 'castFiber'], 'Closure' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castClosure'], 'Generator' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castGenerator'], 'ReflectionType' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castType'], 'ReflectionAttribute' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castAttribute'], 'ReflectionGenerator' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castReflectionGenerator'], 'ReflectionClass' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castClass'], 'ReflectionClassConstant' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castClassConstant'], 'ReflectionFunctionAbstract' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castFunctionAbstract'], 'ReflectionMethod' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castMethod'], 'ReflectionParameter' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castParameter'], 'ReflectionProperty' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castProperty'], 'ReflectionReference' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castReference'], 'ReflectionExtension' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castExtension'], 'ReflectionZendExtension' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castZendExtension'], 'PPLCZVendor\\Doctrine\\Common\\Persistence\\ObjectManager' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'PPLCZVendor\\Doctrine\\Common\\Proxy\\Proxy' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\DoctrineCaster', 'castCommonProxy'], 'PPLCZVendor\\Doctrine\\ORM\\Proxy\\Proxy' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\DoctrineCaster', 'castOrmProxy'], 'PPLCZVendor\\Doctrine\\ORM\\PersistentCollection' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\DoctrineCaster', 'castPersistentCollection'], 'PPLCZVendor\\Doctrine\\Persistence\\ObjectManager' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'DOMException' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castException'], 'DOMStringList' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castLength'], 'DOMNameList' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castLength'], 'DOMImplementation' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castImplementation'], 'DOMImplementationList' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castLength'], 'DOMNode' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castNode'], 'DOMNameSpaceNode' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castNameSpaceNode'], 'DOMDocument' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castDocument'], 'DOMNodeList' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castLength'], 'DOMNamedNodeMap' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castLength'], 'DOMCharacterData' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castCharacterData'], 'DOMAttr' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castAttr'], 'DOMElement' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castElement'], 'DOMText' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castText'], 'DOMTypeinfo' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castTypeinfo'], 'DOMDomError' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castDomError'], 'DOMLocator' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castLocator'], 'DOMDocumentType' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castDocumentType'], 'DOMNotation' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castNotation'], 'DOMEntity' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castEntity'], 'DOMProcessingInstruction' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castProcessingInstruction'], 'DOMXPath' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castXPath'], 'XMLReader' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\XmlReaderCaster', 'castXmlReader'], 'ErrorException' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castErrorException'], 'Exception' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castException'], 'Error' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castError'], 'PPLCZVendor\\Symfony\\Bridge\\Monolog\\Logger' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'PPLCZVendor\\Symfony\\Component\\DependencyInjection\\ContainerInterface' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'PPLCZVendor\\Symfony\\Component\\EventDispatcher\\EventDispatcherInterface' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'PPLCZVendor\\Symfony\\Component\\HttpClient\\AmpHttpClient' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castHttpClient'], 'PPLCZVendor\\Symfony\\Component\\HttpClient\\CurlHttpClient' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castHttpClient'], 'PPLCZVendor\\Symfony\\Component\\HttpClient\\NativeHttpClient' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castHttpClient'], 'PPLCZVendor\\Symfony\\Component\\HttpClient\\Response\\AmpResponse' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castHttpClientResponse'], 'PPLCZVendor\\Symfony\\Component\\HttpClient\\Response\\CurlResponse' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castHttpClientResponse'], 'PPLCZVendor\\Symfony\\Component\\HttpClient\\Response\\NativeResponse' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castHttpClientResponse'], 'PPLCZVendor\\Symfony\\Component\\HttpFoundation\\Request' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castRequest'], 'PPLCZVendor\\Symfony\\Component\\Uid\\Ulid' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castUlid'], 'PPLCZVendor\\Symfony\\Component\\Uid\\Uuid' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castUuid'], 'PPLCZVendor\\Symfony\\Component\\VarDumper\\Exception\\ThrowingCasterException' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castThrowingCasterException'], 'PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\TraceStub' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castTraceStub'], 'PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\FrameStub' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castFrameStub'], 'PPLCZVendor\\Symfony\\Component\\VarDumper\\Cloner\\AbstractCloner' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'PPLCZVendor\\Symfony\\Component\\ErrorHandler\\Exception\\SilencedErrorContext' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castSilencedErrorContext'], 'PPLCZVendor\\Imagine\\Image\\ImageInterface' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\ImagineCaster', 'castImage'], 'PPLCZVendor\\Ramsey\\Uuid\\UuidInterface' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\UuidCaster', 'castRamseyUuid'], 'PPLCZVendor\\ProxyManager\\Proxy\\ProxyInterface' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\ProxyManagerCaster', 'castProxy'], 'PHPUnit_Framework_MockObject_MockObject' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'PPLCZVendor\\PHPUnit\\Framework\\MockObject\\MockObject' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'PPLCZVendor\\PHPUnit\\Framework\\MockObject\\Stub' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'PPLCZVendor\\Prophecy\\Prophecy\\ProphecySubjectInterface' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'PPLCZVendor\\Mockery\\MockInterface' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'PDO' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\PdoCaster', 'castPdo'], 'PDOStatement' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\PdoCaster', 'castPdoStatement'], 'AMQPConnection' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\AmqpCaster', 'castConnection'], 'AMQPChannel' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\AmqpCaster', 'castChannel'], 'AMQPQueue' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\AmqpCaster', 'castQueue'], 'AMQPExchange' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\AmqpCaster', 'castExchange'], 'AMQPEnvelope' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\AmqpCaster', 'castEnvelope'], 'ArrayObject' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castArrayObject'], 'ArrayIterator' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castArrayIterator'], 'SplDoublyLinkedList' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castDoublyLinkedList'], 'SplFileInfo' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castFileInfo'], 'SplFileObject' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castFileObject'], 'SplHeap' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castHeap'], 'SplObjectStorage' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castObjectStorage'], 'SplPriorityQueue' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castHeap'], 'OuterIterator' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castOuterIterator'], 'WeakReference' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castWeakReference'], 'Redis' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\RedisCaster', 'castRedis'], 'RedisArray' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\RedisCaster', 'castRedisArray'], 'RedisCluster' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\RedisCaster', 'castRedisCluster'], 'DateTimeInterface' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\DateCaster', 'castDateTime'], 'DateInterval' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\DateCaster', 'castInterval'], 'DateTimeZone' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\DateCaster', 'castTimeZone'], 'DatePeriod' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\DateCaster', 'castPeriod'], 'GMP' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\GmpCaster', 'castGmp'], 'MessageFormatter' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\IntlCaster', 'castMessageFormatter'], 'NumberFormatter' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\IntlCaster', 'castNumberFormatter'], 'IntlTimeZone' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\IntlCaster', 'castIntlTimeZone'], 'IntlCalendar' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\IntlCaster', 'castIntlCalendar'], 'IntlDateFormatter' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\IntlCaster', 'castIntlDateFormatter'], 'Memcached' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\MemcachedCaster', 'castMemcached'], 'PPLCZVendor\\Ds\\Collection' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\DsCaster', 'castCollection'], 'PPLCZVendor\\Ds\\Map' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\DsCaster', 'castMap'], 'PPLCZVendor\\Ds\\Pair' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\DsCaster', 'castPair'], 'PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\DsPairStub' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\DsCaster', 'castPairStub'], 'mysqli_driver' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\MysqliCaster', 'castMysqliDriver'], 'CurlHandle' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castCurl'], ':curl' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castCurl'], ':dba' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castDba'], ':dba persistent' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castDba'], 'GdImage' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castGd'], ':gd' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castGd'], ':mysql link' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castMysqlLink'], ':pgsql large object' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\PgSqlCaster', 'castLargeObject'], ':pgsql link' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\PgSqlCaster', 'castLink'], ':pgsql link persistent' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\PgSqlCaster', 'castLink'], ':pgsql result' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\PgSqlCaster', 'castResult'], ':process' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castProcess'], ':stream' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castStream'], 'OpenSSLCertificate' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castOpensslX509'], ':OpenSSL X.509' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castOpensslX509'], ':persistent stream' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castStream'], ':stream-context' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castStreamContext'], 'XmlParser' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\XmlResourceCaster', 'castXml'], ':xml' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\XmlResourceCaster', 'castXml'], 'RdKafka' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castRdKafka'], 'PPLCZVendor\\RdKafka\\Conf' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castConf'], 'PPLCZVendor\\RdKafka\\KafkaConsumer' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castKafkaConsumer'], 'PPLCZVendor\\RdKafka\\Metadata\\Broker' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castBrokerMetadata'], 'PPLCZVendor\\RdKafka\\Metadata\\Collection' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castCollectionMetadata'], 'PPLCZVendor\\RdKafka\\Metadata\\Partition' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castPartitionMetadata'], 'PPLCZVendor\\RdKafka\\Metadata\\Topic' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castTopicMetadata'], 'PPLCZVendor\\RdKafka\\Message' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castMessage'], 'PPLCZVendor\\RdKafka\\Topic' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castTopic'], 'PPLCZVendor\\RdKafka\\TopicPartition' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castTopicPartition'], 'PPLCZVendor\\RdKafka\\TopicConf' => ['PPLCZVendor\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castTopicConf']];
    protected $maxItems = 2500;
    protected $maxString = -1;
    protected $minDepth = 1;
    /**
     * @var array<string, list<callable>>
     */
    private $casters = [];
    /**
     * @var callable|null
     */
    private $prevErrorHandler;
    private $classInfo = [];
    private $filter = 0;
    /**
     * @param callable[]|null $casters A map of casters
     *
     * @see addCasters
     */
    public function __construct(?array $casters = null)
    {
        if (null === $casters) {
            $casters = static::$defaultCasters;
        }
        $this->addCasters($casters);
    }
    /**
     * Adds casters for resources and objects.
     *
     * Maps resources or objects types to a callback.
     * Types are in the key, with a callable caster for value.
     * Resource types are to be prefixed with a `:`,
     * see e.g. static::$defaultCasters.
     *
     * @param callable[] $casters A map of casters
     */
    public function addCasters(array $casters)
    {
        foreach ($casters as $type => $callback) {
            $this->casters[$type][] = $callback;
        }
    }
    /**
     * Sets the maximum number of items to clone past the minimum depth in nested structures.
     */
    public function setMaxItems(int $maxItems)
    {
        $this->maxItems = $maxItems;
    }
    /**
     * Sets the maximum cloned length for strings.
     */
    public function setMaxString(int $maxString)
    {
        $this->maxString = $maxString;
    }
    /**
     * Sets the minimum tree depth where we are guaranteed to clone all the items.  After this
     * depth is reached, only setMaxItems items will be cloned.
     */
    public function setMinDepth(int $minDepth)
    {
        $this->minDepth = $minDepth;
    }
    /**
     * Clones a PHP variable.
     *
     * @param mixed $var    Any PHP variable
     * @param int   $filter A bit field of Caster::EXCLUDE_* constants
     *
     * @return Data
     */
    public function cloneVar($var, int $filter = 0)
    {
        $this->prevErrorHandler = \set_error_handler(function ($type, $msg, $file, $line, $context = []) {
            if (\E_RECOVERABLE_ERROR === $type || \E_USER_ERROR === $type) {
                // Cloner never dies
                throw new \ErrorException($msg, 0, $type, $file, $line);
            }
            if ($this->prevErrorHandler) {
                return ($this->prevErrorHandler)($type, $msg, $file, $line, $context);
            }
            return \false;
        });
        $this->filter = $filter;
        if ($gc = \gc_enabled()) {
            \gc_disable();
        }
        try {
            return new Data($this->doClone($var));
        } finally {
            if ($gc) {
                \gc_enable();
            }
            \restore_error_handler();
            $this->prevErrorHandler = null;
        }
    }
    /**
     * Effectively clones the PHP variable.
     *
     * @param mixed $var Any PHP variable
     *
     * @return array
     */
    protected abstract function doClone($var);
    /**
     * Casts an object to an array representation.
     *
     * @param bool $isNested True if the object is nested in the dumped structure
     *
     * @return array
     */
    protected function castObject(Stub $stub, bool $isNested)
    {
        $obj = $stub->value;
        $class = $stub->class;
        if (\PHP_VERSION_ID < 80000 ? "\x00" === ($class[15] ?? null) : \str_contains($class, "@anonymous\x00")) {
            $stub->class = \get_debug_type($obj);
        }
        if (isset($this->classInfo[$class])) {
            [$i, $parents, $hasDebugInfo, $fileInfo] = $this->classInfo[$class];
        } else {
            $i = 2;
            $parents = [$class];
            $hasDebugInfo = \method_exists($class, '__debugInfo');
            foreach (\class_parents($class) as $p) {
                $parents[] = $p;
                ++$i;
            }
            foreach (\class_implements($class) as $p) {
                $parents[] = $p;
                ++$i;
            }
            $parents[] = '*';
            $r = new \ReflectionClass($class);
            $fileInfo = $r->isInternal() || $r->isSubclassOf(Stub::class) ? [] : ['file' => $r->getFileName(), 'line' => $r->getStartLine()];
            $this->classInfo[$class] = [$i, $parents, $hasDebugInfo, $fileInfo];
        }
        $stub->attr += $fileInfo;
        $a = Caster::castObject($obj, $class, $hasDebugInfo, $stub->class);
        try {
            while ($i--) {
                if (!empty($this->casters[$p = $parents[$i]])) {
                    foreach ($this->casters[$p] as $callback) {
                        $a = $callback($obj, $a, $stub, $isNested, $this->filter);
                    }
                }
            }
        } catch (\Exception $e) {
            $a = [(Stub::TYPE_OBJECT === $stub->type ? Caster::PREFIX_VIRTUAL : '') . '⚠' => new ThrowingCasterException($e)] + $a;
        }
        return $a;
    }
    /**
     * Casts a resource to an array representation.
     *
     * @param bool $isNested True if the object is nested in the dumped structure
     *
     * @return array
     */
    protected function castResource(Stub $stub, bool $isNested)
    {
        $a = [];
        $res = $stub->value;
        $type = $stub->class;
        try {
            if (!empty($this->casters[':' . $type])) {
                foreach ($this->casters[':' . $type] as $callback) {
                    $a = $callback($res, $a, $stub, $isNested, $this->filter);
                }
            }
        } catch (\Exception $e) {
            $a = [(Stub::TYPE_OBJECT === $stub->type ? Caster::PREFIX_VIRTUAL : '') . '⚠' => new ThrowingCasterException($e)] + $a;
        }
        return $a;
    }
}
