# IvoPetkov\HTML5DOMTokenList

Represents a set of space-separated tokens of an element attribute.

```php
IvoPetkov\HTML5DOMTokenList {

	/* Properties */
	public readonly int $length
	public readonly string $value

	/* Methods */
	public __construct ( DOMElement $element , string $attributeName )
	public void add ( [ string[] $tokens ] )
	public bool contains ( string $token )
	public ArrayIterator entries ( void )
	public null|string item ( int $index )
	public void remove ( [ string[] $tokens ] )
	public void replace ( string $old , string $new )
	public bool toggle ( string $token [, bool $force ] )

}
```

## Properties

##### public readonly int $length

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The number of tokens.

##### public readonly string $value

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;A space-separated list of the tokens.

## Methods

##### public [__construct](ivopetkov.html5domtokenlist.__construct.method.md) ( [DOMElement](http://php.net/manual/en/class.domelement.php) $element , string $attributeName )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Creates a list of space-separated tokens based on the attribute value of an element.

##### public void [add](ivopetkov.html5domtokenlist.add.method.md) ( [ string[] $tokens ] )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Adds the given tokens to the list.

##### public bool [contains](ivopetkov.html5domtokenlist.contains.method.md) ( string $token )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns true if the list contains the given token, otherwise false.

##### public [ArrayIterator](http://php.net/manual/en/class.arrayiterator.php) [entries](ivopetkov.html5domtokenlist.entries.method.md) ( void )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns an iterator allowing you to go through all tokens contained in the list.

##### public null|string [item](ivopetkov.html5domtokenlist.item.method.md) ( int $index )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns an item in the list by its index (returns null if the number is greater than or equal to the length of the list).

##### public void [remove](ivopetkov.html5domtokenlist.remove.method.md) ( [ string[] $tokens ] )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Removes the specified tokens from the list. If the string does not exist in the list, no error is thrown.

##### public void [replace](ivopetkov.html5domtokenlist.replace.method.md) ( string $old , string $new )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Replaces an existing token with a new token.

##### public bool [toggle](ivopetkov.html5domtokenlist.toggle.method.md) ( string $token [, bool $force ] )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Removes a given token from the list and returns false. If token doesn't exist it's added and the function returns true.

## Details

Location: ~/src/HTML5DOMTokenList.php

---

[back to index](index.md)

