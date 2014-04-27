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

Now. It is no fun to create a group/table, without adding fields to it. This may be done at creation time like like this
```php
Pario::create('TestGroup', function($group) {
	$group->addType( Pario::type("String"), "columnone" );
	$group->addType( Pario::type("String"), "columntwo" );
});
```
This will create a database table called **pario_testgroup** containing two varchar columns named
- columnone
- columntwo

**Now, whats the smart stuff?**
The smart stuff is the ParioTypes. With these controls you may specify what gets into your database, what does not and how it does so.
A ParioType must at least consist of two methods and a member variable. A ParioType may look like this.

```php
	class String extends ParioType {

		//The databaseType is the field type of the column. We are using the Laravel Schema types here.
		protected $databasType = "string";

		//The recommended html input type for this field
		protected $recommendedField = "text";

		//The renderOutput method renders the data retrieved from the database, before returning it.
		public function renderOutput($string) {
			return ( is_string($string) ? $string : "");
		}

		//The renderInsert method renders the data before it enters the database.
		public function renderInsert($string) {

			return ( is_string($string) ? $string : "");
		}

	}
```

A ParioType may contain as many helper functions you would like, giving you the possibility to make things like DateTime now, add time, remove etc. all through bindings.
You may also create your own extended versions of the ParioType, for example if you would like to make specific form field outputs for each ParioType.

```php
	namespace yourname\yourpackage;

	class String extends \Jonm\Pario\String {
		public function formField() {
			return '<input type="text" name="' . $this->getSlug() . '" />';
	}
}

```
Now, simply add your namespace to the ParioType namespace array
```php
	Pario::addTypeSpace("yourname\\yourpackage");
```
Now, if you use the
```php
Pario::type("string");
```
It will fetch yours first.
And how is that usable, well, how about this, using the extended ParioType from before?

```php
	$group = Pario::make("TestGroup");

	foreach( $group->getTypes() as $type ) {
		print $type->formField();
	}
```
Will not output a formfield for the type.

This means that you can make 100% dynamic database transactions, without having to know the actual table definition ( I am sensing a pretty nifty GUI coming someday).



**Fetching a ParioGroup**
```php
	$group = Pario::make('TestGroup');
```
**Adding a row to the group**
```php
	$group = Pario::make('TestGroup');
	$group->add( [ "columnone" => "Foo", "columntwo" => "Bar" ] );
```

**Getting a single row from the group**
```php
	$group = Pario::make('TestGroup');

	$result = $group->find( 1 );

	print $result->columnone;

```

**Getting all rows from a group**
```php
	$group = Pario::make('TestGroup');

	$result = $group->all();

	foreach( $result as $r ) {
		print $r->columnone;
	}

```
