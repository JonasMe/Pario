Pario
=====

Pario makes it easy to work with your database, without having much knowledge here of.

You may programatically add tables to your database, containing columns which are bound to a ParioType. A definition of this table will be saved on your disk in the Storage/Pario folder.

**Creating a table is easy.**
```php
Pario::create('TestGroup', function($group) {
	
});
```
The Create function will automatically
- Make a new instance of the ParioGroup class
- Do the tasks specified in the function parameter
- Save the group definition and the database table

