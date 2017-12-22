# IvoPetkov\HTML5DOMNodeList
## Constants

`const integer STD_PROP_LIST`

`const integer ARRAY_AS_PROPS`

## Properties

`public int $length`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The list items count

## Methods

```php
public \IvoPetkov\HTML5DOMElement|null item ( int $index )
```

Returns the item at the specified index

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$index`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The item index

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The item at the specified index or null if not existent

### Inherited methods

From ArrayObject:

```php
public void offsetExists ( $index )
public void offsetGet ( $index )
public void offsetSet ( $index ,  $newval )
public void offsetUnset ( $index )
public void append ( $value )
public void getArrayCopy ( void )
public void count ( void )
public void getFlags ( void )
public void setFlags ( $flags )
public void asort ( void )
public void ksort ( void )
public void uasort ( $cmp_function )
public void uksort ( $cmp_function )
public void natsort ( void )
public void natcasesort ( void )
public void unserialize ( $serialized )
public void serialize ( void )
public void getIterator ( void )
public void exchangeArray ( $array )
public void setIteratorClass ( $iteratorClass )
public void getIteratorClass ( void )
```

