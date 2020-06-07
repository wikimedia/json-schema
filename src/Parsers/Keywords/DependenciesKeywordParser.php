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
use Opis\JsonSchema\Keywords\DependenciesKeyword;
use Opis\JsonSchema\Parsers\{AbstractKeywordParser, ISchemaParser};

class DependenciesKeywordParser extends AbstractKeywordParser
{
    /**
     * @inheritDoc
     */
    public function type(): string
    {
        return self::TYPE_OBJECT;
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
        if (!is_object($value)) {
            throw $this->keywordException("{keyword} must be an object", $info);
        }

        $list = [];
        foreach ($value as $name => $s) {
            if ($s === true) {
                continue;
            }
            if ($s === false || is_object($s)) {
                $list[$name] = $s;
                continue;
            } elseif (!is_array($s)) {
                throw $this->keywordException("{keyword} must be an object containing json schemas or arrays of property names", $info);
            }
            if (!$s) {
                continue;
            }
            foreach ($s as $p) {
                if (!is_string($p)) {
                    throw $this->keywordException("{keyword} must be an object containing json schemas or arrays of property names", $info);
                }
            }
            $list[$name] = array_unique($s);
        }

        return $list ? new DependenciesKeyword($list) : null;
    }
}