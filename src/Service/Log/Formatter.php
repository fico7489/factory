<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service\Log;

use Monolog\Formatter\FormatterInterface;
use Monolog\LogRecord;

class Formatter implements FormatterInterface
{
    public function format(LogRecord $record)
    {
        if (($record->context['sql'] ?? null) and isset($record->context['params'])) {
            $sql = $record->context['sql'];

            $parameters = $record->context['params'] ?? [];

            $sql = $this->populateSql($sql, $parameters);
            $sql = "\n".\SqlFormatter::format($sql, false)."\n";

            // echo \SqlFormatter::format($sql);exit;

            return $sql;
        }

        return null;
    }

    public function formatBatch(array $records)
    {
    }

    private function populateSql(string $sql, array $params): string
    {
        foreach ($params as $value) {
            $sql = preg_replace('[\?]', "'".$value."'", $sql, 1);
        }

        foreach ($params as $name => $value) {
            $sql = str_replace(':'.$name, "'".$value."'", $sql);
        }

        return $sql;
    }
}
