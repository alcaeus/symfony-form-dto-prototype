# Symfony Form DTO prototype

This prototype introduces the concept of data transfer objects as its own mapped data entity. It is purely a proof of
concept, not a feature-complete implementation. 

The advantage is that you code behaviour into a DTO that then manipulates an entity from the data it received.

## The DTO

A DTO is a simple class with public properties that hold values. These values are pre-populated from an object using the
getter set in the annotation and applied to an object using the specified setter.

A DTO is mapped with multiple fields, which can either support scalar values, collections (not yet supported), and
nested DTOs. Again, setters and getters define how the field is accessed on the property. Using this, you can also map
read-only properties. Take the `Address` DTO for example. This is an immutable value object, so the field setters in the
DTO must be empty. Instead, the DTO setter is used to access all properties at once and build a fresh value object:
```php
/**
 * @Dto(set=fn (?object &$object, self $dto): void => $object = new AddressEntity($dto->street, ...))
 */
class Address
{
    /** @Field(get=fn (?object &$object): string => $object ? $object->getStreet : '') */
    public $street;
    // ...
}
```

Note that due to a limitation of the Doctrine annotations library used above, and in the native Attributes functionality
that will replace it, the accessor closures cannot be written as above. Instead, a special annotation or attribute must
be used to create a closure via a callable:
```php
/**
 * @Dto(set=@ClosureFromCallable({Address::class, "createSetter"}))
 */
class Address
{
    public static function createSetter(): Closure
    {
        return static function (?AddressEntity &$data, self $address): void {
            $data = new AddressEntity($address->street, $address->city, $address->postalCode, $address->country);
        };
    }
}
```

The supplied static method is invoked and expected to return a closure. This closure will later be used to access data.

# HttpFormRequestHandler

To complete the proof of concept, a HttpFormRequestHandler shows how data can be read. In this case, we're using a DTO
handler that reads data for a form from a HTTP request. The form is created automatically based on the mapping, and can
be augmented using the configuration. Setting up validation is possible, but not yet implemented.

The request handler binds the request to the form, and checks if the submitted form was valid. If it was, it validates
the DTO, and applies it to the original object. This ensures that no entities will be left in an invalid state at any
time. If a DTO aborts its handling, this leads to inconsistent state. Fixing this is beyond the scope of this proof of
concept.

# Future work

* Change the handler return type to allow further handler. For example, the `HttpFormRequestHandler` could want to
  return a view instance of the form to allow for further processing. Another option is exposing constraint violations
  encountered while handling the data.
* A `SerializerHandler` class could populate DTO values from a serialized request using the Serializer component. The
  serializer config for the DTO would be created automatically, similar to the form in `HttpFormRequestHandler`.
* A `ConsoleInputHandler` class could populate DTO values from interactive questions in a Console command. Components
  that allow binding an `Input` to forms help with this, but ideally this would be implemented natively. 
