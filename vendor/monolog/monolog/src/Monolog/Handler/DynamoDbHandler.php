<?php declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chwnam\ThreadsToPosts\Vendor\Monolog\Handler;

use Chwnam\ThreadsToPosts\Vendor\Aws\Sdk;
use Chwnam\ThreadsToPosts\Vendor\Aws\DynamoDb\DynamoDbClient;
use Chwnam\ThreadsToPosts\Vendor\Monolog\Formatter\FormatterInterface;
use Chwnam\ThreadsToPosts\Vendor\Aws\DynamoDb\Marshaler;
use Chwnam\ThreadsToPosts\Vendor\Monolog\Formatter\ScalarFormatter;
use Chwnam\ThreadsToPosts\Vendor\Monolog\Level;
use Chwnam\ThreadsToPosts\Vendor\Monolog\LogRecord;

/**
 * Amazon DynamoDB handler (http://aws.amazon.com/dynamodb/)
 *
 * @link https://github.com/aws/aws-sdk-php/
 * @author Andrew Lawson <adlawson@gmail.com>
 */
class DynamoDbHandler extends AbstractProcessingHandler
{
    public const DATE_FORMAT = 'Y-m-d\TH:i:s.uO';

    protected DynamoDbClient $client;

    protected string $table;

    protected Marshaler $marshaler;

    public function __construct(DynamoDbClient $client, string $table, int|string|Level $level = Level::Debug, bool $bubble = true)
    {
        $this->marshaler = new Marshaler;

        $this->client = $client;
        $this->table = $table;

        parent::__construct($level, $bubble);
    }

    /**
     * @inheritDoc
     */
    protected function write(LogRecord $record): void
    {
        $filtered = $this->filterEmptyFields($record->formatted);
        $formatted = $this->marshaler->marshalItem($filtered);

        $this->client->putItem([
            'TableName' => $this->table,
            'Item' => $formatted,
        ]);
    }

    /**
     * @param  mixed[] $record
     * @return mixed[]
     */
    protected function filterEmptyFields(array $record): array
    {
        return array_filter($record, function ($value) {
            return [] !== $value;
        });
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultFormatter(): FormatterInterface
    {
        return new ScalarFormatter(self::DATE_FORMAT);
    }
}
