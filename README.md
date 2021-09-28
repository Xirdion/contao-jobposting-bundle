# Jobposting-Bundle

The contao jobposting bundle adds the ability to display and maintain job offers.

## Features

- Compatible with Contao 4.9 and higher versions (PHP 8 Support)
- Job-Archives and Job-Categories
- List- and Reader-Module to show the jobs in the frontend
- Own content element to show a specific job in the frontend
- Every job has its own unique link
- JSON+LD is generated for its job 

## Installation

**Via Composer**
```shell
$ composer require dreibein/contao-jobposting-bundle
```

## Dependencies

- PHP: `>=7.4`
- Contao: `^4.9`

## Besonderheiten
Jobs werden in den Modulen und Content-Elementen nur über die \Contao\TemplateInheritance::insert() Funktion eingebunden.

**Job-Display:** (Content-Element)
```php
$this->insert('template_name', $this->job);
```

**Job-Reader:**
```php
$this->insert('template_name', $this->job);
```

**Job-List:**
```php
foreach ($this->jobs as $job) {
    $this->insert('template_name', $job);
}
```

