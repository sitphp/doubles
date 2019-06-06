# Introduction

## Requirements
The "sitphp/doubles" library requires at least PhpUnit 6 and at least PHP 7. It should be installed from composer which will make sure your configuration matches requirements.
 > {.note .info} Note : You can get composer here : [https://getcomposer.org](https://getcomposer.org).
        
## Install
Once you have composer installed, add the line `"sitphp/doubles": "~2.1"` in the `"require-dev"` section of your composer.json file :

```json 
{
    "require-dev": {
        "sitphp/doubles": "~2.1.0"
    }
}
```

Then run the following composer command :

```bash
composer update
```
        
This will install the latest version of the "sitphp/doubles' library with the required PhpUnit package.

## What is a double
A double is just a class that implements the same methods as the original class (like a copy) except that these methods can be tested and manipulated. The are two types of doubles that you should know about : doubles of type "dummy" and doubles of type "mock" :

### What is a "dummy" double ?
A double is called a "dummy" when all the methods of the original class are overwritten to return `null`.
For example, let's say your original class looks like this :

```php    
class Name {
    protected $name;

    function getName(){
        return $this->name;
    }

    function setName($name){
        $this->name = $name;
    }
}
```

Here is what the double of type "dummy" would look like :

```php
class NameDouble extends Name {
    function getName(){
        return null;
    }
    function setName($name){
        return null;
    }
}
```

As you can see, every method of the original class is overwritten in the double to return `null`.

### What is a "mock" double  ?
A double is called a "mock" when all the methods of the original class are overwritten to behave the same as in the original class.
Here is what a "mock" of our previously created "Name" class  would look like :
```php
class NameDouble extends Name {
    protected $name;
    
    function getName(){
        return parent::getName();
    }
    function setName($name){
        return parent::setName($name);
    }
}
```
    
As you can see, the double will behave exactly the same as the original class. But unlike the original class, it can be tested and manipulated so can use it instead of our original class for our test.

## How doubles can help you with testing ?
Let's say you have two classes : `ClassToTest` and `Foo`. And say that you are using class `Foo` inside class `ClassToTest`. We could then say that class `ClassToTest` is dependent on class `Foo`.

If you wanted to test the methods of class `ClassToTest`, you might not want to be disturbed by class `Foo` which you could test separatly. So you would create a class `Double` which would have all the methods class `Foo` behaving the way you want for your test. You would then use this `Double` class instead of the `Foo` class inside your `ClassToTest` class. You could then test class `ClassToTest` without worrying about if class `Foo` is doing what it is suppose to do or not. You would do something like this :
 
 
```php     
/* Double of class "Foo" */
$double = Double::dummy(Foo::class)->getInstance();

/* Instantiate class "ClassToTest" with a double instance of class "Foo" instead of an instance of the original "Foo" class  */
$class_to_test = new ClassToTest($double);
```

The "sitphp/doubles" library can generate doubles that look like the original classes but can be manipulated and tested (sort of a copy of a class). These doubles can then be used instead of the original classes for your test. This library can create doubles of any kind of class, interface or trait. 
Here are the 3 things that doubles can do for testing :

- **The method calls of a double can be counted** : you can test how many times a double method has been called.
- **The method arguments of a double  can be tested** : you can test which arguments were passed to a double method.
- **The method of a double can be manipulated** : you can overwrite a double method to change its behaviour.

>{.note .info} Note : Doubles are often called "mocks". But in this library, the "mock" word is used to name a special kind of double.
