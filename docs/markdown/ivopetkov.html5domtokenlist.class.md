# IvoPetkov\HTML5DOMTokenList

## Properties

##### public int $length

## Methods

##### public void [add](ivopetkov.html5domtokenlist.add.method.md) ( [ string[] $tokens ] )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Adds the given tokens to the list.

##### public void [remove](ivopetkov.html5domtokenlist.remove.method.md) ( [ string[] $tokens ] )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Removes the specified tokens from the list. If the string does not exist in the list, no error is thrown.

##### public null|string [item](ivopetkov.html5domtokenlist.item.method.md) ( int $index )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns an item in the list by its index (returns null if the number is greater than or equal to the length of the list).

##### public bool [toggle](ivopetkov.html5domtokenlist.toggle.method.md) ( string $token [, bool $force ] )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Removes a given token from the list and returns false. If token doesn't exist it's added and the function returns true.

##### public bool [contains](ivopetkov.html5domtokenlist.contains.method.md) ( string $token )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns true if the list contains the given token, otherwise false.

##### public void [replace](ivopetkov.html5domtokenlist.replace.method.md) ( string $old , string $new )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Replaces an existing token with a new token.

##### public [ArrayIterator](http://php.net/manual/en/arrayiterator.php) [entries](ivopetkov.html5domtokenlist.entries.method.md) ( void )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns an iterator allowing you to go through all tokens contained in the list.

## Details

File: /src/HTML5DOMTokenList.php

