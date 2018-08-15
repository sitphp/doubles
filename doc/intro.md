# Introduction

## How doubles can help you with testing ?
Let's say you have two classes : `ClassToTest` and `Foo`. And say that you are using class `Foo` inside class `ClassToTest`. We could then say that class `ClassToTest` is dependent on class `Foo`.
If you wanted to test the methods of class `ClassToTest`, you might not want to be disturbed by class `Foo` which you could test separatly. So what you would do is create a class `Double` which would have all the methods class `Foo` behaving the way you want for your test. You would then use this `Double` class instead of the `Foo` class inside your `ClassToTest` class. Then you could test class `ClassToTest` without worrying about if class `Foo` is doing what it is suppose to do or not. So with doublit, you would do something like this :
        
    {.language-php} /* Double of class "Foo" */
    $double = Doublit::dummy_instance(Foo::class);
    
    /* Instantiate class "ClassToTest" with a double of class "Foo" */
    $class_to_test = new ClassToTest($double);

Doublit can generate doubles that look like the original classes but can be manipulated and tested (sort of a copy of a class). These doubles can then be used instead of the original classes for your test. Doublit can create doubles of any kind of class, interface or trait. 
Here are the 3 things that doubles can do for testing :

- The method calls of a double can be counted : you can test how many times a double method has been called.
- The method arguments of a double  can be tested : you can test which arguments were passed to a double method.
- The method of a double can be manipulated : you can overwrite a double method to change its behaviour.

>{.note.info} Note : Doubles are often called "mocks". But in Doublit, the "mock" word is used to name a special kind of double.

## What you should know ?
A double is just a class that implements the same methods as the original class (like a copy) except that these methods can be tested and manipulated. The are two types of doubles that you should know about : doubles of type "dummy" and doubles of type "mock" :

### What are doubles of type "dummy" ?
A double is called a "dummy" when all the methods of the original class are overwritten to return `null`.
For example, let's say your original class looks like this :

    {.language-php} class Name {
        protected $name;

        function getName(){
            return $this->name;
        }

        function setName($name){
            $this->name = $name;
        }
    }

Here is what the double of type "dummy" would look like :

    {.language-php} class NameDouble extends Name {
        function getName(){
            return null;
        }
        function setName($name){
            return null;
        }
    }

As you can see, every method of the original class is overwritten in the double to return `null`.

### What are doubles of type "mock" ?
A double is called a "mock" when all the methods of the original class are overwritten to behave the same as in the original class.
Here is what a "mock" of our previously created "Name" class  would look like :

    {.language-php} class NameDouble extends Name {
        protected $name;
        
        function getName(){
            return parent::getName();
        }
        function setName($name){
            return parent::setName($name);
        }
    }
    
As you can see, the double will behave exactly the same as the original class. But unlike the original class, it can be tested and manipulated so can use it instead of our original class for our test.