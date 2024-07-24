# Actions

Actions are a convenient way to describe the available actions that have been designed for a given entity, and that can be manually applied on it.

Furthermore, each action can be attached to a series of policies in order to automatically restrict the availability of the action according to the context (user, object, collection, ...).

Actions can be invoked using the method `::do($action)` on any entity Collection.

If the action is unknown or if it cannot be performed due to one or more broken policies, an exception is raised.

Example:

`Report.class`

```php
<?php
public static function getActions() {
    return [
        'init' => [
            'description'   => "Generates the lines of the Report.",
            'policies'      => [],
            'function'      => 'doInit'
        ]
    ];
}

public static function doInit($self) {
    $self->read(['status', 'account_lines_ids' => ['account_id', 'total']]);
    foreach($self as $id => $report) {
        foreach($report['account_lines_ids'] as $line_id => $line) {
            ReportLine::create([
                'report_id'             => $id,
                'service_account_id'    => $line['account_id'],
                'total'                 => $line['total']
               ]);
           }
        }
    }
```

`./packages/[package]/actions/init.php`
```php
<?php
// create a new report and initialize it (will generate all lines)
Report::create()->do('init');
```

