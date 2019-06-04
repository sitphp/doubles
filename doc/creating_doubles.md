# Creating a double

## Creating a double instance
The "sitphp/doubles" library can help you to make doubles of any kind of class, interface or trait easily. It doesn't matter if it has final, abstract or static calls.

### Double instance of type "dummy"
A doubles of type "dummy" will overwrite the original class methods to return `null`. If you are not sure what doubles of type "dummy" are, you can get more details on the [introduction page](/doc/intro). To create a "dummy" double of a given class use the `dummy` method.  :
    
    {.language-php} // Get a double instance of type "dummy" for "MyClass" class, interface or trait 
    $my_double = Double::dummy(MyClass::class)->getInstance();

    // This will return null
    $my_double->myMethod();
        
> {.note.info} Note : The constructor will not be executed so you don't need to pass any of the constructor arguments to the `getInstance` method.
        
### Double instance of type "mock"
A doubles of type "mock" will overwrite the original class methods to behave exactly the same as in the original class. If you are not sure what doubles of type "mock" are, you can get more details on the [introduction page](/doc/intro). You should use a "mock" double when you want to leave the behaviour of the original class unchanged. To get a "mock" double of a given class use the `mock` method and pass the constructor arguments to the `getInstance` method :
            
    {.language-php} // Get a double instance of type "mock" for "MyClass" class, interface or trait
    $my_double = Double::mock(MyClass::class)->getInstance($arg1, $arg2);
    
    // This will run the "myMethod" method like on the original class
    $my_double->myMethod();

        
### Double instance of type "alias"          
Doubles of type "alias" can be used to create doubles of non existent classes or to override existent classes. Overriding an existent class might be useful when you cannot inject the double in the class you want to test.You should avoid using "alias" doubles as much as possible because they have drawbacks. :

- An "alias" double will not implement any method from the original class. You will have to define yourself which methods you want to build your "alias" double with.
- All "alias" double implemented methods will return `null` by default an cannot behave like the original class methods.
- If you are using "alias" doubles to replace an existent class, this class should not be loaded before you create your "alias" double. Otherwise you will receive an error .
                
To get an "alias" double use the `alias` method. Then add the methods you want to implement with the `addMethod` method :

    {.language-php} // Get an double instance of type "alias" for non existent class "MyNonExistentClass" with methods "myMethod" and "myOtherMethod"
    $my_double = Double::alias('MyNonExistentClass')
        ->addMethod([myMethod, myOtherMethod])
        ->getInstance();
    
    // Get a double instance of type "alias" for existent class "MyClass" with methods "myMethod" and "myOtherMethod"
    $my_double = Double::alias(MyClass::class)
        ->setMethod(['myMethod', 'myOtherMethod'])
        ->getInstance();

    // Test class using the class "MyClass"
    $my_class_to_test = new MyClassToTest();
    $my_class_to_test->methodUsingMyClass();

## Get a double class name
Sometimes, you may need to instantiate the double yourself or you may not need a double instance at all (if you are working with a static class for example). You can get the class name of the generated double only with the `getClass` method.
    
    {.language-php} // Get class name of double of type "dummy" for class "MyClass"
    $my_dummy_double_class = Double::dummy(MyClass::class)->getClass();
    
    $my_dummy_double_class::foo();
    
        
## Implementing undefined methods
When you create a double, it will automatically implements all the methods of the original class (unless you're creating an [alias](#double-instance-of-type-alias) double). If you want your double to implement some methods that are not present in the original class, use the `addMethod` method. If you want to implement a static method, prefix it with the "static:" keyword :
    
    {.language-php}// Get a double instance with method "myMethod" and static method "myOtherMethod"
    $my_double = Double::dummy(MyClass::class)
        ->addMethod(['myMethod', 'static:myOtherMethod'])
        ->getInstance();

## Passing constructor arguments
You can pass constructor arguments in the getInstance method :
    
    {.language-php} // Get double instance and run original constructor with arguments "first_argument" and "second_argument"
    $my_dummy_double = Double::dummy(MyClass::class)->getInstance($arg1, $arg2);
    
> {.note.info} Note : When you pass constructor arguments, the original `__construct` method will be called with the given arguments regardless if your double is of type "dummy" or "mock". In the same way, if you don't pass constructor arguments, the original `__construct` method will not be called regardless if your double is of type "dummy" or "mock".

## Implementing interfaces and trait
Use `addInterface` and/or `addTrait` to declare the interfaces and/or traits you want to implement in your double class :
    
    {.language-php} // Implement one interface
    $my_dummy_double = Double::mock(MyClass::class)
		->addInterface(MyInterface::class)
		->getInstance();

    // Implement many interface
    $my_dummy_double = Double::mock_instance(MyClass::class)
		->addInterface([MyInterface::class, MyOtherInterface::class])
		->getInstance();
    
> {.note.info} Note : Every double class will automatically implement the `Doubles\DoubleInterface` interface in case you need to identify a double instance.

## Naming the double class 
Double class names are automaticaly generated but you can also set the name of the double class yourself with the `setName` method :

    {.language-php} // Get a double instance of class "MyClass" with class name "MyDoubleClassName". 
    $my_dummy_double_class = Double::dummy(MyClass::class)
		->setName('MyDoubleClassName')
		->getInstance();
  