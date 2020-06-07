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

namespace Opis\JsonSchema\Schemas;

use Opis\JsonSchema\{IContext, Info\ISchemaInfo, ISchema};
use Opis\JsonSchema\Parsers\ISchemaParser;
use Opis\JsonSchema\Errors\IValidationError;

final class LazySchema extends AbstractSchema
{

    private ISchemaParser $parser;

    private ?ISchema $schema = null;

    /**
     * @param ISchemaInfo $info
     * @param ISchemaParser $parser
     */
    public function __construct(ISchemaInfo $info, ISchemaParser $parser)
    {
        parent::__construct($info);
        $this->parser = $parser;
    }

    /**
     * @inheritDoc
     */
    public function validate(IContext $context): ?IValidationError
    {
        return $this->schema()->validate($context);
    }

    /**
     * @return ISchema
     */
    public function schema(): ISchema
    {
        if ($this->schema === null) {
            $this->schema = $this->parser->parseSchema($this->info);
        }

        return $this->schema;
    }
}