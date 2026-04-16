<?php

declare(strict_types=1);

namespace Vendor\FocuspointSitepackage\Tests\Unit\ViewHelpers;

use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Vendor\FocuspointSitepackage\ViewHelpers\FocusPositionViewHelper;

final class FocusPositionViewHelperTest extends UnitTestCase
{
    #[Test]
    public function returnsDefaultPositionWhenCropJsonIsEmpty(): void
    {
        $image = $this->createFileReferenceWithCrop('');
        self::assertSame('50% 50%', $this->renderWithArguments($image));
    }

    #[Test]
    public function returnsDefaultPositionWhenNoFocusAreaInJson(): void
    {
        $cropJson = json_encode([
            'default' => [
                'cropArea' => ['x' => 0, 'y' => 0, 'width' => 1, 'height' => 1],
                'selectedRatio' => 'free',
            ],
        ], JSON_THROW_ON_ERROR);

        $image = $this->createFileReferenceWithCrop($cropJson);
        self::assertSame('50% 50%', $this->renderWithArguments($image));
    }

    #[Test]
    public function computesCenteredFocusOnFullImage(): void
    {
        $cropJson = $this->buildCropJson(
            focusArea: ['x' => 0.25, 'y' => 0.25, 'width' => 0.5, 'height' => 0.5],
        );
        $image = $this->createFileReferenceWithCrop($cropJson);

        self::assertSame('50.00% 50.00%', $this->renderWithArguments($image));
    }

    #[Test]
    public function computesTopLeftFocus(): void
    {
        $cropJson = $this->buildCropJson(
            focusArea: ['x' => 0.0, 'y' => 0.0, 'width' => 0.2, 'height' => 0.2],
        );
        $image = $this->createFileReferenceWithCrop($cropJson);

        self::assertSame('10.00% 10.00%', $this->renderWithArguments($image));
    }

    #[Test]
    public function computesBottomRightFocus(): void
    {
        $cropJson = $this->buildCropJson(
            focusArea: ['x' => 0.7, 'y' => 0.8, 'width' => 0.2, 'height' => 0.2],
        );
        $image = $this->createFileReferenceWithCrop($cropJson);

        self::assertSame('80.00% 90.00%', $this->renderWithArguments($image));
    }

    #[Test]
    public function remapsFocusIntoCroppedRegion(): void
    {
        $cropJson = $this->buildCropJson(
            cropArea: ['x' => 0.25, 'y' => 0.25, 'width' => 0.5, 'height' => 0.5],
            focusArea: ['x' => 0.4, 'y' => 0.4, 'width' => 0.2, 'height' => 0.2],
        );
        $image = $this->createFileReferenceWithCrop($cropJson);

        self::assertSame('50.00% 50.00%', $this->renderWithArguments($image));
    }

    #[Test]
    public function clampsToZeroWhenFocusIsOutsideCropArea(): void
    {
        $cropJson = $this->buildCropJson(
            cropArea: ['x' => 0.5, 'y' => 0.5, 'width' => 0.5, 'height' => 0.5],
            focusArea: ['x' => 0.0, 'y' => 0.0, 'width' => 0.1, 'height' => 0.1],
        );
        $image = $this->createFileReferenceWithCrop($cropJson);

        self::assertSame('0.00% 0.00%', $this->renderWithArguments($image));
    }

    #[Test]
    public function clampsToHundredWhenFocusIsBeyondCropArea(): void
    {
        $cropJson = $this->buildCropJson(
            cropArea: ['x' => 0.0, 'y' => 0.0, 'width' => 0.5, 'height' => 0.5],
            focusArea: ['x' => 0.9, 'y' => 0.9, 'width' => 0.1, 'height' => 0.1],
        );
        $image = $this->createFileReferenceWithCrop($cropJson);

        self::assertSame('100.00% 100.00%', $this->renderWithArguments($image));
    }

    #[Test]
    public function respectsCropVariantArgument(): void
    {
        $cropJson = json_encode([
            'default' => [
                'cropArea' => ['x' => 0, 'y' => 0, 'width' => 1, 'height' => 1],
                'selectedRatio' => 'free',
                'focusArea' => ['x' => 0.0, 'y' => 0.0, 'width' => 0.2, 'height' => 0.2],
            ],
            'mobile' => [
                'cropArea' => ['x' => 0, 'y' => 0, 'width' => 1, 'height' => 1],
                'selectedRatio' => 'free',
                'focusArea' => ['x' => 0.7, 'y' => 0.8, 'width' => 0.2, 'height' => 0.2],
            ],
        ], JSON_THROW_ON_ERROR);

        $image = $this->createFileReferenceWithCrop($cropJson);

        self::assertSame('10.00% 10.00%', $this->renderWithArguments($image, 'default'));
        self::assertSame('80.00% 90.00%', $this->renderWithArguments($image, 'mobile'));
    }

    #[Test]
    public function typicalPortraitFaceAtTopCenter(): void
    {
        $cropJson = $this->buildCropJson(
            focusArea: ['x' => 0.35, 'y' => 0.05, 'width' => 0.3, 'height' => 0.2],
        );
        $image = $this->createFileReferenceWithCrop($cropJson);

        self::assertSame('50.00% 15.00%', $this->renderWithArguments($image));
    }

    private function renderWithArguments(FileReference $image, string $cropVariant = 'default'): string
    {
        $viewHelper = new FocusPositionViewHelper();
        $viewHelper->initializeArguments();
        $viewHelper->setArguments([
            'image' => $image,
            'cropVariant' => $cropVariant,
        ]);

        return $viewHelper->render();
    }

    private function createFileReferenceWithCrop(string $cropJson): FileReference
    {
        $fileReference = $this->createMock(FileReference::class);
        $fileReference->method('getProperty')
            ->with('crop')
            ->willReturn($cropJson);

        return $fileReference;
    }

    /**
     * @param array{x: float, y: float, width: float, height: float} $cropArea
     * @param array{x: float, y: float, width: float, height: float} $focusArea
     */
    private function buildCropJson(
        array $cropArea = ['x' => 0, 'y' => 0, 'width' => 1, 'height' => 1],
        array $focusArea = ['x' => 1 / 3, 'y' => 1 / 3, 'width' => 1 / 3, 'height' => 1 / 3],
        string $variant = 'default',
    ): string {
        return json_encode([
            $variant => [
                'cropArea' => $cropArea,
                'selectedRatio' => 'free',
                'focusArea' => $focusArea,
            ],
        ], JSON_THROW_ON_ERROR);
    }
}
