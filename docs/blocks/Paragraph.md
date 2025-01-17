# Paragraph block

## Working with strings

Creating a paragraph from a simple string:
```php
<?php

$paragraph = Paragraph::fromString("Simple paragraph.");
$paragraph->toString(); // "Simple paragraph."
```

Or creating an empty paragraph:
```php
<?php

$paragraph = Paragraph::create();
$paragraph->toString(); // empty string
```

## Working with `RichText` objects

```php
<?php

// "Simple text" will be bold and italic
$text = RichText::createText("Simple text")->bold()->italic();

$paragraph = Paragraph::create()->appendText($text);
```

While working with multiple texts:

```php
$text = [
    RichText::createText("Paragraphs can be "),
    RichText::createText("bold")->bold(),
    RichText::createText(", "),
    RichText::createText("underlined")->underline(),
    RichText::createText(" and much more!"),
];

$paragraph = Paragraph::create()->withText($text);
```

Note that `withText()` will replace the text on a new instance of `Paragraph`.