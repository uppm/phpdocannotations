# PHP-Doc-Annotations parser

## Example
```php
<?php
use modules\phpdocannotations\Annotation;
use modules\phpdocannotations\AnnotationParser;

/**
 * @Annotation()
 */
final class TestAnnotation {
    public $key;
}

/**
 * @TestAnnotation(key = "Hello world")
 */
class Test {
}

$testClassAnnotations = new AnnotationParser(Test::class);

$testAnnotation = $testClassAnnotations->getAnnotation(TestAnnotation::class);

echo $testAnnotation->key;
```