# Workflows

Workflows are part of the Model definition.

When a workflow must be assigned to an entity, a dedicated method , `getWorkflow()`, must be defined for describing the workflow.  

The `getWorkflow()`method must return an associative array in which all possible status ate identified by a key of the map.

For a given entity, the Workflow reflects all possible values of the special field `status` (e.g. [`validated`, `suspended`, `confirmed`]), along with all possible transitions from one status to another (e.g. [`validate`, `suspend`, `confirm`]).


### Example

```php
<?php
public static function getWorkflow() {
        return [
            'created' => [
                'transitions' => [
                    'validate' => [
                        'watch'       => ['validated'],
                        'domain'      => ['validated', '=', true],
                        'description' => 'Update the user status as validated.',
                        'status'	  => 'validated',
                    ]
                ]
            ],
            'validated' => [
                'transitions' => [
                    'suspend' => [
                        'description' => 'Set the user status as suspended.',
                        'status'	  => 'suspended'
                    ],
                    'confirm' => [
                        'domain'      => ['validated', '=', true],
                        'description' => 'Update the user status as confirmed.',
                        'status'	  => 'confirmed'
                    ]
                ]
            ],
            ];
    }
```


* By default, no workflow is defined.

* The workflow assumes the existence of a 'status' field. Only values defined in the selection property of the status field can be assigned to it (and referenced in the workflow). The initial value must be specified in the default property.

* There can be only one workflow per class (a behavioral change implies a change in logic, justifying the creation of a new class, potentially in a new package).

* A workflow can be overwritten or overridden by inheritance.

* The workflow allows adding constraints to the fields of an entity (readonly, required, visible) based on the status.
    (canupdate ORM hook: automatic verification of the possibility to modify an object based on the status. In the workflow, at each state, the possibility to add a list of read-only fields (non-modifiables).

* The status field can have a dependencies property to force recalculation of computed fields when the value is modified.

* In the UI, the possible transitions from the current state are visible in the transition menu, actions on the right of the current status.

* If a getWorkflow method is defined, the schema returned by getSchema is possibly adapted based on the current status.

* The initial status is defined in the default property of the status field definition.

* The final status (if any) is the status for which there is no outgoing transition.




## Workflow Graph


* The workflow is composed of nodes that represent "statuses" (successive logical states through which an entity can pass).
* The nodes are connected to each other by vectors that represent "transitions."
* There can be multiple outgoing and incoming transitions on the same status.

If transitions are automatic based on external events, the object needs to be constantly monitored.
Transitions are automatic status changes based on modifications of object field values.

If transitions are actions, the possibility to move from one status to another (action) needs to be conditioned based on the graph.
Only actions available from a specific status can be activated.

=> Transitions can be activated either by complex actions (external) or by modifications of the object.

The idea is to allow both "manual" modification of the status (i.e., by the user or controller) by validating these modifications based on a logical schema, and to perform automatic transitions based on pre-established conditions.

The status influences the fields that can be modified the object, the display of the object, and the accessible statuses.




```json
{
  'status_name_A' : {      // each status has a descriptor that can have 3 properties : `readonly`, `columns` and `transitions`
    'readonly': {},        // bool, domain or function: if resulting to true, the whole object cannot be updated at this status (this is handled at the same level as the `canupdate()` method)
    'transitions' : {      // all possible transitions from the current status descriptor are listed in the transition property
      'transitionX' : {                          // each transition has an ID (name) and holds a transition descriptor
        'watch'      : ['field1', 'field3'],     // watcher: tester la transition en cas de modification de ces champs
        'domain'     : ['field1', '>', 'field3'],// conditions to be met in order to allow the transition
        'policies'   : ['policy1', 'policy2'],   // policies that apply on the transition (conditions defined at class level)
        'description': 'f1 > f3',
        'status'     : 'status_name_B'           // new value of the status (node in the flow) when the transition succeeds
      },
      'transitionY' : {
        'watch': ['has_quotation_sent'],   // watch
        'description': 'has_quotation_sent',
        'status': 'status_name_C'
      },
      'transitionZ' : {
        'description' : 'manual',           // signal de transition sans conditions
        'status'    : 'status_name_D',      // new status to be assigned to the object
        'onbefore'  : 'onbeforeTransitionC',// method to call before the transition (upon acceptation of the transition)
        'onafter'   : 'onafterTransitionC'  // method to call after the transition has been performed
      }
    }
  }
}
```



### Manual transitions

A) Controllers can directly modify the status of the object (permitted only if consistent with the workflow, status_from, status_to, and fulfilled conditions without any condition)

* Retrieve the workflow and look for the descriptor corresponding to the current status.
* Go through all existing transitions and filter among those whose status property matches the requested new status.
* If there is no match or if there is no transition with all the fulfilled conditions, an error is thrown.
* If there is a match without conditions or with all the fulfilled conditions, the status is updated. If there is a function property, a call is made with the current object.

B) A controller can invoke a transition on an object by emitting a "signal" (transition identifier): `ORM::transition(transition_id)`

* Retrieve the workflow and look for the descriptor corresponding to the current status.
* Search for the transition among those in the descriptor.
* If there is a match and any optional conditions are fulfilled, the status is updated. If there is a function property, a call is made with the current object.
* Otherwise, an error is thrown.



### Automated transitions

C) When modifying an object (all fields), a specific handler checks if conditions are met to automatically modify the status.  

* Retrieve the workflow and look for the descriptor corresponding to the current status.
* Go through all existing transitions and filter among those whose depends_on property contains any of the updated fields.
* If there is a match without conditions or with all the fulfilled conditions, the status is updated based on the status property.
* Otherwise, no action is taken.



