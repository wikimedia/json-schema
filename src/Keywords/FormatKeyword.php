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

namespace Opis\JsonSchema\Keywords;

use Opis\JsonSchema\{
    IContext,
    IFormat,
    IKeyword,
    ISchema
};
use Opis\JsonSchema\Errors\IValidationError;

class FormatKeyword implements IKeyword
{
    use ErrorTrait;

    protected ?string $name;

    /** @var callable[]|IFormat[] */
    protected ?array $types;

    /**
     * @param string $name
     * @param callable[]|IFormat[] $types
     */
    public function __construct(string $name, array $types)
    {
        $this->name = $name;
        $this->types = $types;
    }

    /**
     * @inheritDoc
     */
    public function validate(IContext $context, ISchema $schema): ?IValidationError
    {
        $type = $context->currentDataType();

        if (!isset($this->types[$type])) {
            return null;
        }

        $format = $this->types[$type];
        if ($type instanceof IFormat) {
            $ok = $format->validate($context->currentData());
        } else {
            $ok = $format($context->currentData());
        }

        if ($ok) {
            return null;
        }

        return $this->error($schema, $context, 'format', "The data must match the '{$this->name}' format", [
            'format' => $this->name,
            'type' => $type,
        ]);
    }
}