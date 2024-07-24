<link href="https://fonts.googleapis.com/icon?family=Material+Icons"rel="stylesheet">

<style>
    img {
        max-width : 80% !important;
        margin-left : 50%;
        transform : translateX(-50%);
        max-height : 65vh !important;
    }
</style>

# eQual Workbench user guide

## Getting started

### Requirements

To use eQual Workbench you will need a working instance of eQual (branch dev-2.0). Please refer to [this part of the documentation](/getting-started/installation/) if not.

### Installation / Updating

You simply need to init the `core` package to install workbench on your instance.

```bash
./equal.run --do=core_init_package --package=core
```

Once this done, you may find the Workbench application at the url `/workbench` or in the `/apps` page of your eQual instance as follows :

<img src="/assets/img/workbench_apps_icon.png" style="height : 200px;">

If you don't see the button, make sure your user is member of the group admin.

## Create an eQual application

This part will drive you through all the different tools that eQual gives you to develop your application with a little tutorial.

### Creating eQual components

In workbench, you can find the component creator at the bottom left of every component selector of the app.

<img src="/assets/img/workbench_creator_location.png">

The component creator allows you to create various type of components in eQual using templates to create a working basic component.

The component creator is context aware and will autofill the fields with you current context.
You will have to respect the naming convention of each components to be able to create them.

For our tutorial, let's start by creating a package called `tutorial` and the Model `tutorial\Post` using the component creator :

<img src="/assets/img/workbench_creator_package.png" style="width : 49%;">
<img src="/assets/img/workbench_creator_model.png" style="width : 49%;">

### Using the base menu to find precise components

You can find precise components in eQual Workbench by using the base menu to search in all the component of the instance :

<img src="/assets/img/workbench_find_explanation.png" style="height : 500px;">

To search for a component, you can use the search bar **(1)** to filter the component using this syntax :

`[component_type]:[package]>[keywords...]`

You can also use the drop down menu **(2)** to select the type (it will change it in the search bar and vice versa).

Let's look for our new created Model :

<img src="/assets/img/workbench_find_example.png" style="width : 30%">

### Model's side menu

It is now time to take a look to our model. By clicking on it in the component list, the right side of the screen will render the model's side menu :

<img src="/assets/img/workbench_modelsp.png">

This side menu give a lot of information about the Model : 

1. Name and description of the Model.
2. The parent Model of the current Model (`Model` refers to `equal\orm\Model` the base Model of eQual).
3. The table name where object of this Model are or will be stored in the database.
4. The list of the fields of the Model (the tabs expand to give more information about the specific field).

Then, there are buttons to interact with the Model :

<ol start="5">
    <li>
        Leads to the field editor of the Model <a href="#model-field-edition"> see</a>
    </li>
    <li>
        Leads to the submenu of the views of the Model <a href="#submenus">see</a>
    </li>
    <li>
        Leads to the translations editor of the Model <a href="#translate-a-component">see</a>
    </li>
    <li>
        Leads to the workflow editor og the Model <a href="#creating-a-workflow">see</a>
    </li>
</ol>

The other buttons are Work in progress currently.

### Model field edition

After clicking on **(5)** on the model side menu, here we are in the model field editor :

<img src="/assets/img/workbench_model_first_look.png">

#### Header

You can see that the header has slightly changed. It is the same in all the different page of Worbench but have variation based on the possible interaction with the current page. Here we have :

1. Back button : allows to go back to the previous context (last page in the state you quit it)
2. cancel/redo : allows to cancel and redo actions as `CTRL+Z` and `CTRL+Y` do.
3. save : allows you sync your changes with the files of the instance of eQual
4. more : here are custom actions depending on the tool you are using. Here, you have the ability to show the JSON that will be turned into the PHP code during the save.

#### Field list

On the left of the screen you can see the list of the field **(5)** of the edited Model.

The icon on the left of the name **(6)** gives two information about the field :

- The icon represent the type of the field :

| Icon                                                 | type       | Icon                                                 | type       | Icon                                                 | type       |
|------------------------------------------------------|------------|------------------------------------------------------|------------|------------------------------------------------------|------------|
| <span class="material-icons">format_quote</span>     | string     | <span class="material-icons">today</span>            | date       | <span class="material-icons">looks_one</span>        | binary     |
| <span class="material-icons">123</span>              | integer    | <span class="material-icons">event</span>            | datetime   | <span class="material-icons">height</span>           | many2many  |
| <span class="material-icons">data_array</span>       | array      | <span class="material-icons">access_time</span>      | time       | <span class="material-icons">type_specimen</span>    | alias      |
| <span class="material-icons">money</span>            | float      | <span class="material-icons">article</span>          | text       | <span class="material-icons">call_split</span>       | one2many   |
| <span class="material-icons">question_mark</span>    | boolean    | <span class="material-icons">call_merge</span>       | many2one   | <span class="material-icons">functions</span>        | computed   |

- The background color represents the state of the field :
    - Gray is inherited from `equal\orm\Model`. It can't be edited or deleted because it is required by eQual to manage it in DB
    - Yellow is inherited. You can't delete an inherited field.
    - Green is Overridden. It's an inherited field that has been edited in this Model. It cannot be deleted but can be reset to it's parent value
    - Blue is basic field, neither inherited, neither overridden.

As in the component menu, the field creator **(7)** is located at the bottom left of the screen. If you do not fill the name input, it will give an arbitrary name to the field.

#### Edition pane

On the right of the screen, when a field is selected, you have the ability to edit it. 

you can edit the name of the field directly at the **position 8** only if the field is not inherited.

The properties of the class are split in two parts **(9)**. You will find the more used option in the basic tab. These options **(10)** are bound to the type of the field, workbench will allow you to edit only the properties that applies to the current selected type.

#### tutorial

Let's add properties to our `Post` Model :

<img src="/assets/img/workbench_field_title.png" style="width : 49%">
<img src="/assets/img/workbench_field_content.png" style="width : 49%">
<img src="/assets/img/workbench_field_published.png" style="width : 49%">
<img src="/assets/img/workbench_field_author_name.png" style="width : 49%">

If you have theses fields at the end you can save the model.

<img src="/assets/img/workbench_field_list.png" style="height : 400px;">

You will need to add the function for the `computed` in the php file to make it work, Workbench does not allow logic edition for now.

```php
<?php

public static function calcAuthorName($self){
    $result = [];
    $posts = $self->read(['id', 'creator' => ['fullname']]);
    foreach($posts as $id => $post) {
        $result[$id] = $post['creator']['fullname'];
    };
    return $result;
}
```

### Translate a component

By using the button translation in the [Model side menu](#models-side-menu) or Controller side menu, You can access the translation editor.

<img src="/assets/img/workbench_traduction_top.png" style="width : 100%; height : auto;">

In the translation editor, you can select or create a lang to translate your entity **(1)**. 

The format for the lang is [ISO 639-1](https://fr.wikipedia.org/wiki/Liste_des_codes_ISO_639-1), either two lower case letter (representing a language) like `fr` or `en` or two lower case letter (representing a language) and two upper case letter (representing a country) separated by an underscore like `fr_BE` or `es_EC`.

Translating en entity means to translate its fields (or parameters name for controller), but also its views and error (due to constraints in models or exception in controllers)

#### Model translation

We will speak about model translation in this part but this also apply to controller, using params as their fields.

<img src="/assets/img/workbench_translation_model.png">

You can translate the name and description of the model and add plural variation to be used in the view render motor of eQualUI **(1)**.

Translating fields **(3)**, will allow your apps to display the name differently in the views depending on the lang of the user that visualizes it. It also override the `label` argument of a view field. You can chose whether or not a field is translated or not by checking the box **(2)**.

#### View translation

There is not much to translate in the view because the name of the fields are already translated in the [Model translation](#model-translation). You will be able to translate groups,sections,row,column and label fields names.

<img src="/assets/img/workbench_translation_view.png">

You can select which view to translate with the tab selector **(1)**, then you can translate the view the same way you translate the model **(2)**

#### Error translation

Error translation works differently. you need to precise for each field and error type the appropriate message.

<img src="/assets/img/workbench_translation_error.png">

First you can choose if a field has error to translate by checking the active box **(1)**

Then, you can add error type to the field **(2)** and translate them.

#### tutorial

Let's translate our Model `Post` in english.

<img src="/assets/img/workbench_translation_tuto.png">

### Creating a workflow

By using the button workflow in the [Model side menu](#models-side-menu), You can access the workflow editor :

<img src="/assets/img/workflow_overview.png">

To create or edit a workflow, you can use the different modes **(1)** of the editor. 

When in view or edit state, you can move the states of the workflow to adjust the visualization **(2)** by drag and dropping the state.

If you want to edit properties of states or transition select the appropriate mode and click on the edit button **(4)** of the item you want to edit, then use the contextual menu on the right **(3)** to alter the element.

When in create or edit transition mode, you can use the anchors **(5)** to move the start and the end of the transitions to adjust both the workflow and its visualization.

On save, the workflow will be converted to php associative array in the Model php file and the layout you created in core\Meta to be reused next time you want to visualize or edit the workflow.

#### tutorial

Let's create a workflow for Post. Set the name and icons as follows

<img src="/assets/img/workflow_tuto.png">

### submenus

By using the button view in the [Model side menu](#models-side-menu) or the button model,controller or view in the package side menu, You can access the component submenus. Here is an example with the view submenu.

<img src="/assets/img/workbench_view_submenu.png">

Submenus uses the same layout as the main menu but only display components of the package or view of the Model

#### tutorial

Let's create two views for Post using the view submenu : `form.default` and `list.default` (the basic view of a model). By clicking on the [component creator](#creating-equal-components) you can notice that the context is autofilled into the fields

<img src="/assets/img/workbench_view_creator.png">

You should end up with :

<img src="/assets/img/workbench_view_tuto.png">

### View side menu

The view side menu give information about the selected view and access to its edition.

<img src="/assets/img/workbench_view_sidemenu.png">

1. name and the description of the view
2. Information about the layout of the view
3. Leads to the view editor [see](#editing-views)
4. Leads to the translation of the Model/controller associated to the view [see](#translate-a-component)

### Editing Views

View are huge JSON file that are hard to read. Workbench give the user a simpler way to edit the view using an interface that mimic the result of the view in eQualUI

<img src="/assets/img/workbench_view_editor_top.png">

In the editor, view are segmented in 5 categories **(2)**. The content of these part may vary depending on the type of view you are editing.

1. Layout : This is the part that vary the most. The [layout](/usage/views#layout) is the part of the view that will display the object of a Model.
2. Header : Refers to the [header part](/usage/views#header) of a view. You can edit header actions and selection actions in this tab.
3. Actions : You can edit the custom [actions](/usage/views#action) of the view here
4. Routes : You can edit the routes of the contextual menu of the view here
5. Advanced : You can edit all the other properties of the view here such as the domain, the collect controller, the operations, ...

#### Form/Search view layout

When you are editing the layout of a form or a search view, the editor will create a render that looks like the render of eQual UI.

<img src="/assets/img/workbench_view_editor_form.png" style="width : 49%">
<img src="/assets/img/workbench_view_editor_comparison.png" style="width : 49%">

You can edit the layout visually using drag and drop. 

#### List view layout

The view layout is represented as a list of column in the editor. You can edit the column and change their order by dragging and dropping them

<img src="/assets/img/workbench_view_editor_list.png">

#### tutorial

Edit the views of post like that :

##### `form.default` :

<img src="/assets/img/workbench_view_editor_tuto_form.png">

Items have a width of 25%


##### `list.default` :

<img src="/assets/img/workbench_view_editor_tuto_list.png">

### controller side menu

The controller dide menu give information about the selected controller using the announcer of the controller, allows the user to call the controller for testing purpose and give access to parameter and return value edition and controller [traduction](#translate-a-component) edition.

<img src="/assets/img/workbench_controller_sp1.png">

1. Name and type (GET = data controller or DO = action controller) of the controller.
2. Description of the controller.
3. Response header : Meta data about the response you will get by calling this controller.

<img src="/assets/img/workbench_controller_sp2.png">

This list represents input parameters of the controller and can be filled to call the controller in workbench or generate cli command and http request to call the controller.

<img src="/assets/img/workbench_controller_sp3.png">

1. Allows you to call the controller in workbench and see its response if all the required input parameter have a value.
2. Allows you to copy the cli command to call the controller with the parameter you entered in workbench.
3. Allows you to copy the http request to call the controller with the parameter you entered in workbench.
4. Give you all the custom [routes](#route-side-menu) enabled in the instance that uses this controller.
5. Leads to the input parameters editor [see](#editing-controller-parameters)
6. Leads to the response editor [see](#editing-controller-response-type)
7. Leads to the translation editor of the controller [see](#translate-a-component)

### Editing controller parameters

The controller parameters editor works exactly like the [model field editor](#model-field-edition) without the inheritance handling and the advance panel.

<img src="/assets/img/workbench_controller_params.png">

### Editing controller response type

<img src="/assets/img/workbench_controller_return.png">

In the return editor, you can modify the http response header **(1)**, and the eQual type of the content of the body **(2)**.

Http response header indicates in which format the data is sent, the descriptor is an indication about the type of the content.

For example an array can be sent in XML format : `content-type : application/XML` and `descriptor : array` But can also be sent in json format : `content-type : application/json` and `descriptor : array`.

You can also precise the quantity (`one` or `many`) of variable you send **(3)**

In addition of the "classic" types of eQual you can indicate an "entity" type that allow you to reference an eQual Model, or a data of type "any" : type "any" can be precise as a composite structure of eQual types if possible

<img src="/assets/img/workbench_controller_return2.png">

### Menu side menu

<img src="/assets/img/workbench_menu_sp.png">

The menu side menu is in work in progress and only give access to the [menu editor](#edit-menus) **(1)** and menu [traduction editor](#translate-a-component) **(2)**

### Edit Menus

The menu editor allows you to create/edit/delete menu items whilst having a preview of the final result.

<img src="/assets/img/workbench_menu_editor.png">

1. Name of the view for display purpose only (unrelated to its id which is attached to its filename).
2. Manage access to the menu by group 
3. Preview and item selector (allow sorting by drag and drop on the same level)
4. Edition of the properties of the selected menu item

All the editable properties of the view in workbench are properties findable in the [original menu structure](/usage/views/#menu-views).

#### tutorial

Let's create a menu of type `left` and of name app in the package `tutorial`

<img src="/assets/img/workbench_menu_create.png">

Then, open the menu editor and edit the menu like so :

<img src="/assets/img/workbench_menu_tuto.png">

### Package side menu

<img src="/assets/img/workbench_package_sp.png">

1. information about the package : name, `package_consistency` reports and if the package has been initialized on the eQual instance.
2. The details about the `package_consistency` reports.
3. Button to open the [package initializer dialog](#initialize-a-package).
4. Leads to models [submenu](#submenus)
5. Leads to controllers [submenu](#submenus)
6. Leads to views [submenu](#submenus)
7. Leads to routes [submenu](#submenus)
8. Leads to init data editor [see](#create-intialdemo-data)
9. Leads to demo data editor [see](#create-intialdemo-data)

### Create intial/demo data

Demo and initial data are the same strucutre but they are not saved at the same place to allow the developper to chose which he wants to import if he wants to import them

<img src="/assets/img/workbench_init_overview.png">

1. This is the list of the init/demo files for the given package, you can navigate through them using the side pane.
2. You can select the entity you want to modify its entries in a given file using the tab selector.
3. This button allows you to interact with the Model properties, mostly delete the model's entry or select which languages you want to create the data for the model (uses also [ISO 639-1](https://fr.wikipedia.org/wiki/Liste_des_codes_ISO_639-1)).
4. You can visualize the data entry as a list here (uses the list.default view of the model, make sure you created it trying to display the properties before creating data). By clicking on an entry, you can edit it's values (fields that are not multilang are only editable in `en``) :
     <img src="/assets/img/workbench_init_entry.png">
5. To delete entry you need to select the desired items then click on this button
6. **id strict** and **toggle id** buttons allows you to tell to the editor if the entry is bound to a specific id or not. You may uses bound id to create relations between items or if a component uses the id in a domain.
7. The editor allows you to import initial data from the data of the eQual database. By clicking on this button, Workbench will fetch all the object of the Model and convert the one you select to initial/demo data
    <img src="/assets/img/workbench_init_import.png">
8. You can create initial data entries by hand using this button. this will open the edition menu automatically on the new item.

#### tutorial

Let's create some posts for our blog :

<img src="/assets/img/workbench_init_tuto.png">

### Initialize a package

First let's create the application for our package tutorial. Edit the `manifest.json` of the package as so :

```json
{
    "name": "tutorial",
    "description": "workbench tutorial package",
    "version": "1.0",
    "author": "YesBabylon",
    "license": "LGPL-3",
    "depends_on": [ "core" ],
    "apps": [
        {
          "id": "blog",
          "name": "Blog",
          "extends": "app",
          "description": "blog",
          "icon": "ad_units",
          "color": "#3498DB",
          "access": {
            "groups": [
              "users",
              "admins"
            ]
          },
          "params": {
            "menus": {
              "left": "app.left"
            }
          }
        }
      ],
    "tags": [ ]
}
```

Then, let's use the initialize button of the [package side menu](#package-side-menu).

<img src="/assets/img/workbench_package_init.png">

1. These checkboxes allow you to ask eQual to initialize the package's (and choose if you want to import their initial data) dependencies before initializing it.
2. This checkbox allow you to choose if you want to import the initial data of the package.

You can initialize the package `tutorial` as so.

if all gone well you see this message in your [package side menu](#package-side-menu)

<img src="/assets/img/workbench_package_initialized.png">

now, if you return to the /apps of your instance, you should see the blog app appear :

<img src="/assets/img/workbench_apps_icons.png">

Congratulations ! you have created your first app using eQual Workbench.

### Create and Edit Object-Relational UML 

Workbench has a UML Viewer/Editor that you can access by the side menu :

<img src="/assets/img/workbench_access_uml.png">

The UML editor uses information contained in the models' structure to create automatically a diagram

<img src="/assets/img/workbench_uml_overview.png">

The UML editor is similar to the [workflow editor](#creating-a-workflow) in is internal working. you can switch between 3 modes **(2)** to create your workflows.

You can load and save UML diagrams located in the folder `/uml` of the packages of your eQUal instance using header buttons **(1)**.

You can move by dragging the models in the visual representation **(3)** to adjust the graph

When editing a model **(4)** you can disable the inheritance links and relations link from this model. You can also hide fields to make the UML more readable.

This page handles printing. you can render your UML as PDF using the button **(5)** or by pressing `CTRL+P`. Make sure that the camera is in the top left corner of the diagram and edit the scale of the print to fit everting in the page.

<img src="/assets/img/workbench_uml_print.png">

Adding a model is as simple as adding his name in the model list. The relations will add themselves.

<img src="/assets/img/workbench_uml_add.png">

To delete a model from the representation, you can use the list of model, or select the model in the edition mode and press the delete button.

### Route side menu

Route edition is not currently supported by Workbench but you can view them in the main menu and they have a side menu :

<img src="/assets/img/workbench_routes.png">