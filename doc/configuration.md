# Configuration

## Configuration

### Global configuration
The double configuration can be defined for all doubles using the `Doublit::config` method :</p>
    
    {.language-php} // Set config for all doubles
    Doublit::config([
        'config_param_1' => 'value_1',
        'config_param_2' => 'value_1'
    ]);
   
### Double configuration
You can also define configuration for a specific double using the 4th argument of an instance method or the 3rd argument of a name method</p>

    {.language-php} // Get double instance with config
    $my_double = Doublit::dummy_instance(MyClass::class, null, null, [
        'config_param_1' => 'value_1',
        'config_param_2' => 'value_1'
    ]);
    
    // Get double class name with config
    $my_double = Doublit::dummy_name(MyClass::class, null, [
        'config_param_1' => 'value_1',
        'config_param_2' => 'value_1'
    ]);
    
### Configuration list
Here is a list of all available config parameters :

- `allow_final_doubles` : Set this parameter to `false` to stop Doublit from trying to make doubles of final classes/methods. Read more [here](#dealing-with-final-classes).
- `allow_protected_methods` : Set this parameter to `false` to disallow testing protected methods. Read more [here](#dealing-with-protected-methods).
- `allow_non_existent_classes` : Set this parameter to `false` to disallow alias doubles of non existent classes. Read more [here](#dealing-with-non-existent-methods).
- `test_unexpected_methods` : Set this parameter to `true` to automatically receive an assertion error whenever an unexpected method is called. Read more [here](#testing-unexpected-methods-automatically).

## Notes

### Dealing with final classes
Doublit can create doubles of classes marked final or having final methods. However, the double will not extend the original class. Therefore, that a double will not be an instance of the original class. This can be a problem for arguments type hinting for example.
If you really need your double class to be an instance of a class implementing final methods, you can set the `allow_final_doubles` config parameter to `false`. Your double will then extend your original class but final calls will always behave like in the original class and will not be testable. This will work if the class has final methods but you will receive an error if your class is marked final.

    {.language-php}// This will fail if MyFinalClass class is marked final
    $my_double = Doublit::dummy_name(MyFinalClass::class, null, null, ['allow_final_doubles' => false]);
    
    // This will work if MyClassWithFinalMethods class has final methods but is not marked final (final methods will not be testable)
    $my_double = Doublit::dummy_name(MyClassWithFinalMethods::class, null, null, ['allow_final_doubles' => false]);
  
### Dealing with protected methods
By default, protected methods can be tested. But you can disable this feature by setting the `allow_protected_methods` config parameter to `false`. You will then receive an error message whenever you try to test a protected method.
    
    {.language-php}// Disallow  protected methods from being tested
    $my_double = Doublit::dummy_instance(MyClass::class, null, null, ['allow_protected_methods' => false]);
    
    // This will fail if myProtectedMethod method is protected
    $my_double::_myMethod('myProtectedMethod');
    
### Dealing with non existent classes
By default, the Doublit alias methods allow you to create doubles of non existent classes. You can disable this feature by setting the `allow_non_existent_classes` config parameter to `false`.
    
    {.language-php} // This will fail if "MyNonExistentClass" class is a non existent class
    $my_double = Doublit::alias_instance('MyNonExistentClass', null, null, ['allow_non_existent_classes' => false]);
    
### Testing unexpected methods automatically
Doublit can automatically send an assertion error whenever a method, that you didn't previously assert, is called. For that, you have to set the `test_unexpected_methods` config parameter to `true`.
            
    {.language-php} // Automatically test unexpected methods are not being called
    $my_double = Doublit::dummy_instance(MyClass::class, null, null, ['test_unexpected_methods' => true]);
    
    // This will not show assert count error
    $my_double::_myMethod('expectedMethod');
    $my_double->expectedMethod();
    
    // This will show an assert count error
    $my_double->unexpectedMethod();

       
### Identifying a double
Every double will implement the `\Doublit\DoubleInterface` interface so you can identify it when you need to.
    
    {.language-php} // Identify a double instance
    if($my_double instanceof \Doublit\DoubleInterface){
        echo ('You have a double');
    }