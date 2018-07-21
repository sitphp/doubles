# Introduction

## How Doublit can help you with testing ?
Doublit can help you to test your PHP classes by generating doubles that look like the original classes but can be manipulated and tested (sort of a copy of a class). These doubles can then be used instead of the original classes for your test. Doublit can create doubles of any kind of class, interface or trait. 
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

## How to integrate Doublit with PhpUnit ?
In order to run your PhpUnit test cases with Doublit, you must simply extend the `Doublit\TestCase` class. This class extends the  `PHPUnit\Framework\TestCase` class so you can write your tests like normally in PhpUnit :

    {.language-php} class MyTestClass extends \Doublit\TestCase {
    
        function testMyFirtsTest(){
            // your test here ...
        }
        
        function testMySecondTest(){
            // your test here ..
        }
    
    }
    
This will make sure the `Doublit\Doublit::close()` method is executed at the end of all your tests.