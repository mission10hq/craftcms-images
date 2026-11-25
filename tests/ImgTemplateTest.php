<?php

namespace mission10\images\tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\TwigTest;

/** Renders img.twig with plain Twig and stand-ins for Craft's asset and image services. */
final class ImgTemplateTest extends TestCase
{
    private const DESKTOP = ['width' => 1200, 'height' => 400];
    private const MOBILE = ['width' => 600, 'height' => 400];

    private function render(array $vars, bool $supportsWebP): string
    {
        $dir = dirname(__DIR__) . '/src/available-templates';
        $twig = new Environment(new ArrayLoader([
            'images/img' => file_get_contents("$dir/img.twig"),
            'images/macros' => file_get_contents("$dir/macros.twig"),
        ]), ['strict_variables' => false]);
        $twig->addTest(new TwigTest('instance of', fn($value, string $class) => $value instanceof $class));

        return $twig->render('images/img', $vars + [
            'craft' => ['app' => ['images' => ['supportsWebP' => $supportsWebP]]],
            'field' => new FakeField(new FakeImage()),
            'transform' => null,
            'mobileTransform' => null,
            'dimensions' => true,
        ]);
    }

    /** @return string[] srcset of each source with no type attribute, keyed by media query */
    private function untypedSources(string $html): array
    {
        preg_match_all('/<source media="([^"]+)" srcset="([^"]+)">/', $html, $m);
        return array_combine($m[1], $m[2]);
    }

    public static function webpSupport(): array
    {
        return ['server makes WebP' => [true], 'server cannot make WebP' => [false]];
    }

    #[DataProvider('webpSupport')]
    public function testMobileSourceUsesTheTransformWhenThereIsNoMobileTransform(bool $supportsWebP): void
    {
        $sources = $this->untypedSources($this->render(['transform' => self::DESKTOP], $supportsWebP));

        $this->assertSame('/_1200x400/hero.png', $sources['(max-width: 599px)']);
        $this->assertSame('/_1200x400/hero.png', $sources['(min-width: 600px)']);
    }

    #[DataProvider('webpSupport')]
    public function testNoSourceServesTheOriginalWhenATransformIsGiven(bool $supportsWebP): void
    {
        $html = $this->render(['transform' => self::DESKTOP], $supportsWebP);

        $this->assertStringNotContainsString('"/hero.png"', $html);
    }

    #[DataProvider('webpSupport')]
    public function testMobileSourceUsesTheMobileTransformWhenGiven(bool $supportsWebP): void
    {
        $sources = $this->untypedSources($this->render(
            ['transform' => self::DESKTOP, 'mobileTransform' => self::MOBILE],
            $supportsWebP,
        ));

        $this->assertSame('/_600x400/hero.png', $sources['(max-width: 599px)']);
        $this->assertSame('/_1200x400/hero.png', $sources['(min-width: 600px)']);
    }

    public function testWebpSourcesAreTransformed(): void
    {
        $html = $this->render(['transform' => self::DESKTOP], true);

        preg_match_all('/<source type="image\/webp" media="[^"]+" srcset="([^"]+)">/', $html, $m);
        $this->assertSame(['/_1200x400/hero.webp', '/_1200x400/hero.webp'], $m[1]);
    }
}

final class FakeField
{
    public function __construct(private FakeImage $image) {}

    public function one(): FakeImage
    {
        return $this->image;
    }
}

final class FakeImage
{
    public string $extension = 'png';
    public string $title = 'Hero';

    public function url(?array $transform = null): string
    {
        return $transform ? "/_{$transform['width']}x{$transform['height']}/hero.png" : '/hero.png';
    }

    public function getUrl(array $options): string
    {
        $t = $options['transform'];
        return "/_{$t['width']}x{$t['height']}/hero.{$options['format']}";
    }
}
