# Configuration
You can define a double's configuration using the 2nd argument of the `dummy`, `mock` and `alias` methods :

    {.language-php} // Get double instance with config
    $double_instance = Double::dummy(MyClass::class, [
        'allow_final_doubles' => true,
        'allow_non_existent_classes' => true'
    ])->getInstance();

Or you can also use config methods :   

    {.language-php} // Get double instance with config
    $double_instance = Double::dummy(MyClass::class)
        ->allowFinalDoubles(true)
        ->allowNonExistentClasses(true)
        ->getInstance();
    
## Configuration list
Here is a list of all available config parameters :

- `allow_final_doubles` : Set this parameter to `false` to disable trying to make doubles of final classes/methods. You can also use the `allowFinalDoubles method. Read more [here](#dealing-with-final-classes).
- `allow_protected_methods` : Set this parameter to `false` to disallow testing protected methods.You can also use the `allowProtectedMethods` method. Read more [here](#dealing-with-protected-methods).
- `allow_non_existent_classes` : Set this parameter to `false` to disallow alias doubles of non existent classes. You can also use the `allowNonExistentClasses` method. Read more [here](#dealing-with-non-existent-methods).
- `test_unexpected_methods` : Set this parameter to `true` to automatically receive an assertion error whenever an unexpected method is called. You can also use the `testUnexpectedMethods` method. Read more [here](#testing-unexpected-methods-automatically).

## Notes

### Dealing with final classes
The sitphp/commands library can create doubles of classes marked final or having final methods. However, the double will not extend the original class. Therefore, that double will not be an instance of the original class and that may sometimes be a problem for testing.
If you really need your double class to be an instance of the original class implementing final methods, you can set the `allow_final_doubles` config parameter to `false`. Your double will then extend your original class but final calls will always behave like in the original class and will not be testable. Also note that this will work only work if your class has final methods but is not marked final.

    {.language-php}// This will fail if MyFinalClass class is marked final
    $double_instance = Double::dummy(MyFinalClass::class, ['allow_final_doubles' => false])->getInstance();
    
    // This will work if MyClassWithFinalMethods class has final methods but is not marked final (final methods will not be testable)
    $double_instance = Double::dummy(MyClassWithFinalMethods::class, ['allow_final_doubles' => false])->getInstance();
  
### Dealing with protected methods
By default, protected methods can be tested. But you can disable this feature by setting the `allow_protected_methods` config parameter to `false`. You will then receive an error message whenever you try to test a protected method.
    
    {.language-php}// Disallow  protected methods from being tested
    $double_instance = Double::dummy(MyClass::class, ['allow_protected_methods' => false])->getInstance();
    
    // This will fail if myProtectedMethod method is protected
    $double_instance::_method('myProtectedMethod');
    
### Dealing with non existent classes
By default, the alias methods allow you to create doubles of non existent classes. You can disable this feature by setting the `allow_non_existent_classes` config parameter to `false`.
    
    {.language-php} // This will fail if "MyNonExistentClass" class is a non existent class
    $double_instance = Double::alias('MyNonExistentClass', ['allow_non_existent_classes' => false])->getInstance();
    
### Testing unexpected methods automatically
You can configure library to automatically send an assertion error whenever a method, that you didn't previously assert, is called. For that, you have to set the `test_unexpected_methods` config parameter to `true`.
            
    {.language-php} // Automatically test unexpected methods are not being called
    $double_instance = Double::dummy(MyClass::class, ['test_unexpected_methods' => true])->getInstance();
    
    // This will not show assert count error
    $double_instance::_method('expectedMethod');
    $double_instance->expectedMethod();
    
    // This will show an assert count error
    $double_instance->unexpectedMethod();

       
### Identifying a double
Every double will implement the `\Doubles\DoubleInterface` interface so you can identify it when you need to.
    
    {.language-php} // Identify a double instance
    if($double_instance instanceof \Doubles\DoubleInterface){
        echo ('You have a double');
    }