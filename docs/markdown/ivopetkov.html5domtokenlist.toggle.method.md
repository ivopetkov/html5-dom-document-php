# IvoPetkov\HTML5DOMTokenList::toggle

Removes a given token from the list and returns false. If token doesn't exist it's added and the function returns true.

```php
public bool toggle ( string $token [, bool $force ] )
```

## Parameters

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$token`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The token you want to toggle.

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$force`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;A Boolean that, if included, turns the toggle into a one way-only operation. If set to false, the token will only be removed but not added again. If set to true, the token will only be added but not removed again.

## Returns

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;false if the token is not in the list after the call, or true if the token is in the list after the call.

## Details

Class: [IvoPetkov\HTML5DOMTokenList](ivopetkov.html5domtokenlist.class.md)

File: /src/HTML5DOMTokenList.php

---

[back to index](index.md)

