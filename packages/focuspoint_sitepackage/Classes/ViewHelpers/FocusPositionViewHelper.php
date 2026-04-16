<?php

declare(strict_types=1);

namespace Vendor\FocuspointSitepackage\ViewHelpers;

use TYPO3\CMS\Core\Imaging\ImageManipulation\Area;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Returns a CSS object-position value computed from a FileReference's focusArea.
 *
 * Usage:
 *   <fp:focusPosition image="{image}" cropVariant="default" />
 *   → "72.50% 33.33%"
 *
 * As CSS custom property:
 *   style="--focus-position:{fp:focusPosition(image:image, cropVariant:'default')}"
 */
final class FocusPositionViewHelper extends AbstractViewHelper
{
    private const DEFAULT_POSITION = '50% 50%';

    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('image', 'object', 'FileReference with crop data', true);
        $this->registerArgument('cropVariant', 'string', 'Name of the crop variant', false, 'default');
    }

    public function render(): string
    {
        /** @var FileReference $image */
        $image = $this->arguments['image'];
        $variantName = $this->arguments['cropVariant'];

        $cropJson = $image->getProperty('crop');
        if (empty($cropJson)) {
            return self::DEFAULT_POSITION;
        }

        $collection = CropVariantCollection::create((string)$cropJson);
        $focusArea = $collection->getFocusArea($variantName);

        if ($focusArea->isEmpty()) {
            return self::DEFAULT_POSITION;
        }

        $cropArea = $collection->getCropArea($variantName);

        return self::computePosition($focusArea, $cropArea);
    }

    private static function computePosition(Area $focusArea, Area $cropArea): string
    {
        $focus = $focusArea->asArray();
        $crop = $cropArea->isEmpty()
            ? ['x' => 0.0, 'y' => 0.0, 'width' => 1.0, 'height' => 1.0]
            : $cropArea->asArray();

        $focusCenterX = $focus['x'] + $focus['width'] / 2;
        $focusCenterY = $focus['y'] + $focus['height'] / 2;

        $positionX = self::remapToPercentage($focusCenterX, $crop['x'], $crop['width']);
        $positionY = self::remapToPercentage($focusCenterY, $crop['y'], $crop['height']);

        return sprintf('%.2f%% %.2f%%', $positionX, $positionY);
    }

    private static function remapToPercentage(float $point, float $cropOffset, float $cropSize): float
    {
        $percentage = ($point - $cropOffset) / $cropSize * 100;

        return max(0.0, min(100.0, $percentage));
    }
}
