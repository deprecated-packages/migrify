<?php

declare(strict_types=1);

namespace Migrify\ConfigPretifier\Pretifier;

use Migrify\MigrifyKernel\Exception\NotImplementedYetException;
use Nette\Neon\Entity;
use Nette\Neon\Neon;
use Nette\Utils\Strings;

final class NeonConfigPretifier
{
    /**
     * @var string
     */
    public const NEON_SUFFIX = 'neon';

    public function pretify(string $content): ?string
    {
        $oldContent = $content;

        $newContent = $this->clarifyNeon($content);
        if ($oldContent !== $newContent) {
            return $newContent;
        }

        return null;
    }

    private function createArrayFromEntity(Entity $entity): array
    {
        $data = [];

        if ($entity->value) {
            if (is_string($entity->value)) {
                $data['class'] = $entity->value;
            } else {
                throw new NotImplementedYetException();
            }

            if ($entity->attributes) {
                $data['arguments'] = $entity->attributes;
            }
        }

        return $data;
    }

    private function clarifyNeon(string $content): string
    {
        $neon = Neon::decode($content);

        foreach ($neon as $key => $value) {
            if ($key !== 'services') {
                continue;
            }

            foreach ($value as $nestedKey => $nestedValue) {
                if (! $nestedValue instanceof Entity) {
                    continue;
                }

                $neon[$key][$nestedKey] = $this->createArrayFromEntity($nestedValue);
            }
        }

        return $this->printNeon($neon);
    }

    private function printNeon(array $neon): string
    {
        $contentWithTabs = Neon::encode($neon, Neon::BLOCK);

        return Strings::replace($contentWithTabs, '#\t#', '    ');
    }
}
