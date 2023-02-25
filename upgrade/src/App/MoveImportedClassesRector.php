<?php

namespace Filament\Upgrade\App;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\UseUse;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class MoveImportedClassesRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [UseUse::class];
    }

    public function refactor(Node | UseUse $node): ?Node
    {
        if ($this->isName($node->name, 'Filament\\Resources\\Form')) {
            $node->name = new Name('Filament\\Forms\\Form');
        } elseif ($this->isName($node->name, 'Filament\\Resources\\Table')) {
            $node->name = new Name('Filament\\Tables\\Table');
        } else {
            return null;
        }

        return $node;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Fix the imports within resource classes',
            [
                new CodeSample(
                    '\\Filament\\Resources\\Form',
                    '\\Filament\\Forms\\Form',
                ),
            ]
        );
    }
}
