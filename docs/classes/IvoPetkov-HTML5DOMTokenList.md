# IvoPetkov\HTML5DOMTokenList
Represents a set of space-separated tokens of an element attribute.

## Properties

`public int $length`

## Methods

```php
public void add ( [ string[] $tokens ] )
```

Adds the given tokens to the list.

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$tokens`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The tokens you want to add to the list.

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

```php
public void remove ( [ string[] $tokens ] )
```

Removes the specified tokens from the list. If the string does not exist in the list, no error is thrown.

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$tokens`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The token you want to remove from the list.

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

```php
public null|string item ( int $index )
```

Returns an item in the list by its index (returns null if the number is greater than or equal to the length of the list).

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$index`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The zero-based index of the item you want to return.

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

```php
public bool toggle ( string $token [, bool $force ] )
```

Removes a given token from the list and returns false. If token doesn't exist it's added and the function returns true.

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$token`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The token you want to toggle.

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$force`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;A Boolean that, if included, turns the toggle into a one way-only operation. If set to false, the token will only be removed but not added again. If set to true, the token will only be added but not removed again.

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;false if the token is not in the list after the call, or true if the token is in the list after the call.

```php
public bool contains ( string $token )
```

Returns true if the list contains the given token, otherwise false.

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$token`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The token you want to check for the existance of in the list.

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;true if the list contains the given token, otherwise false.

```php
public void replace ( string $old , string $new )
```

Replaces an existing token with a new token.

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$old`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The token you want to replace.

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$new`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The token you want to replace $old with.

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

```php
public ArrayIterator entries ( void )
```

Returns an iterator allowing you to go through all tokens contained in the list.

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

