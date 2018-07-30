# Creating a double

## Creating a double instance
Doublit can easily make doubles of any kind of class, interface or trait. It doesn't matter if the class/interface or trait has final, abstract or static calls. You should first import the `Doublit\Doublit` class which your are going to use to create your doubles :
    
    {.language-php}
    // Import the doublit class
    use \Doublit\Doublit;
    ...

### Double instance of type "dummy"
A doubles of type "dummy" will overwrite the original class methods to return `null` and the original class `__construct` method  will not be executed (unless you [pass constructor arguments](#passing-constructor-arguments)). If you don't understand what doubles of type "dummy" are, you can get more details on the [introduction page](/doc/intro). To get a "dummy" double instance of a given class, simply pass the name your original class to the `Doublit::dummy_instance` method :
    
    {.language-php} // Get a double instance of type "dummy" for class "MyClass"
    $my_double = Doublit::dummy_instance(MyClass::class);
    
    // This will return null
    $my_double->myMethod();

> {.note.info} Note : In this example, we are creating a double instance of a class but it works the same way to create doubles of interfaces and traits.
        
### Double instance of type "mock"
A doubles of type "mock" will overwrite the original class methods to behave exactly the same as in the original class. If you don't understand what doubles of type "mock" are, you can get more details on the [introduction page](/doc/intro). You can use "mock" doubles when want to test a class but leave its behaviour unchanged. To get a "mock" double instance of a given class, simply pass the name of your original class to the `Doublit::mock_instance` method :
            
    {.language-php} // Get a double instance of type "mock" for class "MyClass"
    $my_double = Doublit::mock_instance(MyClass::class);
    
    // This will run the "myMethod" method like on the original class
    $my_double->myMethod();

        
### Double instance of type "alias"          
Doubles of type "alias" will take the name of given original class. So if you have a class named "MyClass" and you create an "alias" double of that class, the double class will take the name "MyClass" and replace your original classes. You should avoid using "alias" doubles to replace existent classes because they have drawbacks. Doubles of type "alias" can also be used to create testable non existent classes. Here is what you should be aware of when using "alias" doubles :

- An "alias" double will not implement any method from the original class. You will have to define yourself which methods you want to build your "alias" double with.
- All "alias" double implemented methods will return `null` by default an cannot behave like the original class methods.
- If you are using "alias" doubles to replace an existent class, this class should not be loaded before you create your "alias" double. Otherwise you will receive an error .
                
To get an "alias" double instance of a given class, simply pass the name of your original class to the `Doublit::alias_instance` method with the names of the methods you want to implement. These methods should be written between brackets and separated with commas. This is how you would create an "alias" double of a non existent class :

    {.language-php} // Get an double instance of type "alias" for non existent class "MyNonExistentClass" with methods "myMethod" and "myOtherMethod"
    $my_double = Doublit::alias_instance('MyNonExistentClass[myMethod, myOtherMethod]');

And this is how you would create an "alias" double of an existent class :
    
    {.language-php} // Get a double instance of type "alias" for existent class "MyClass" with methods "myMethod" and "myOtherMethod"
    $my_double = Doublit::alias_instance(MyClass::class.'[myMethod, myOtherMethod]');

    // Test class using the class "MyClass"
    $my_class_to_test = new MyClassToTest();
    $my_class_to_test->methodUsingMyClass();
        
## Implementing undefined methods
When you create a double, it will automatically implements the methods of the original class (unless you're creating an [alias](#double-instance-of-type-alias) double). If you want your double to implement some methods that are not present in the original class, you should declare these methods after the declaration of your class, in a list of methods between brackets and separated with commas. If you want to implement a static method, you should prepend it with a `static:` keyword :
    
    {.language-php}// Get a double instance with method "myMethod" and static method "myOtherMethod"
    $my_double = Doublit::dummy_instance(MyClass::class.'[myMethod, static:myOtherMethod]');

## Passing constructor arguments
You can pass constructor arguments in the second argument of the Doublit instance methods :
    
    {.language-php} // Get double instance and run original constructor with arguments "first_argument" and "second_argument"
    $my_dummy_double = Doublit::dummy_instance(MyClass::class, ['first_argument', 'second_argument']);
    
> {.note.info} Note : When you pass constructor arguments, the original `__construct` method will be called with the given arguments regardless if your double is of type "dummy" or "mock". In the same way, if you don't pass constructor arguments, the original `__construct` method will not be called regardless if your double is of type "dummy" or "mock".

## Implementing interfaces and trait
Use the 3rd parameter of the Doublit instance methods to declare the interfaces and/or traits that you might want to implement in your double class :
    
    {.language-php} // Implement one interface
    $my_dummy_double = Doublit::mock_instance(MyClass::class, null, MyInterface::class);

    // Implement many interface
    $my_dummy_double = Doublit::mock_instance(MyClass::class, null, [MyInterface::class, MyOtherInterface::class]);
    
> {.note.info} Note : Every double class will automatically implement the `Doublit\DoubleInterface` interface in case you need to identify a double instance.

## Naming the double class 
For some reason, you may want to define yourself the name of your double class. For that, you have to prepend your original class name with the double class name you want to give followed by a ":" character :

    {.language-php} // Get a double instance named "MyDoubleClassName" for class "MyClass"
    $my_dummy_double_class = Doublit::dummy_instance('MyDoubleClassName:'.MyClass::class);

## Non instantiated class double
Sometimes, you may need to instantiate the double yourself or you may not need a double instance at all (if you are working with a static class for example). For that you can use the double method to get only the class name of the generated double.
    
    {.language-php} // Get class name of double of type "dummy" for class "MyClass"
    $my_dummy_double_class_name = Doublit::dummy_name(MyClass::class, MyInterfaceToImplement::class);
    
    // Get class name of double of type "mock" for class "MyClass"
    $my_mock_double_class_name = Doublit::mock_name(MyClass::class, MyInterfaceToImplement::class);
    
    // Get class name of double of type "alias" for class "MyClass"
    $my_alias_double_class_name = Doublit::alias_name(MyClass::class, MyInterfaceToImplement::class);

    // Instantiate the "dummy" double
    $my_dummy_double_class_name = new $my_double_class_name();
    
> {.note.info} Note : The `dummy_name` and `mock_name` and `alias_name` methods work the same as the `dummy_instance`, `mock_instance` and `alias_name` classes except, of course, that they cannot take constructor arguments.

## Extra
The methods `dummy_instance`, `mock_instance`, `alias_instance` and the methods `dummy_name`, `mock_name`, `alias_name` are actually just shortcuts to respectively the `instance` method and the `name` method. You can use these directly if you prefer :

    {.language-php} // Instance methods
    $my_dummy_double_instance = Doublit::instance('dummy', MyClass::class, ...);
    $my_mock_double_instance = Doublit::instance('mock', MyClass::class, ...);
    $my_alias_double_instance = Doublit::instance('alias', MyClass::class, ...);
    
    // Name methods
    $my_dummy_double_class = Doublit::name('dummy', MyClass::class, ...);
    $my_mock_double_class = Doublit::name('mock', MyClass::class, ...);
    $my_alias_double_class = Doublit::name('alias', MyClass::class, ...);
    
