<?php
/* ============================================================================
 * Copyright 2020 Zindex Software
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================================ */

namespace Opis\JsonSchema\Parsers\Keywords;

use Opis\JsonSchema\IKeyword;
use Opis\JsonSchema\Info\ISchemaInfo;
use Opis\JsonSchema\Keywords\ItemsKeyword;
use Opis\JsonSchema\Parsers\{AbstractKeywordParser, ISchemaParser};

class ItemsKeywordParser extends AbstractKeywordParser
{
    /**
     * @inheritDoc
     */
    public function type(): string
    {
        return self::TYPE_ARRAY;
    }

    /**
     * @inheritDoc
     */
    public function parse(ISchemaInfo $info, ISchemaParser $parser, object $shared): ?IKeyword
    {
        $schema = $info->data();

        if (!$this->keywordExists($schema)) {
            return null;
        }

        $value = $this->keywordValue($schema);

        if (is_bool($value)) {
            if ($value) {
                return null;
            }
        } elseif (is_array($value)) {
            foreach ($value as $v) {
                if (!is_bool($v) && !is_object($v)) {
                    throw $this->keywordException("{keyword} must contain an array of json schemas (objects or booleans)", $info);
                }
            }
        } elseif (!is_object($value)) {
            throw $this->keywordException("{keyword} must be a json schema or an array of json schemas", $info);
        }

        return new ItemsKeyword($value);
    }
}