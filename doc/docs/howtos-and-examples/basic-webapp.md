# A basic WebApp

Learning by example how to create a web application with eQual.

This tutorial details the required steps for creating a webapp from scratch using eQual.
As sample webapp, we are going to build a basic blog.

## 1. Create a new package
**(Estimated time : 2 minutes)**

This part is quite easy: in the `packages` folder, we create a new folder named `blog`.
In addition, inside this new folder, let's create a file named `manifest.json` and the following subdirectories (as they are mandatory): `classes` and `views`.

Tree structure is now:
```
  /
  /packages
    /blog
      /classes
      /views
      manifest.json
```

Content of `manifest.json` :

```json
{
    "name": "blog",
    "description": "Application Blog",
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
              "users"
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

## 2. Write some classes
**(Estimated time : 5 minutes)**

Now, we need to create a new kind of object. Let's call it "post".

Each post consists of a title and some text (content). 

So,in the folder `packages/blog/classes`, we add a new file named `Post.class.php`.

```php
<?php

namespace blog;
use equal\orm\Model;

class Post extends Model {

    public static function getColumns(){

        return [
            'title' => [
                'type'=>'string',
                'description' => "Title of the post.",
            ],
            'content' => [
                'type'=>'text',
                'description' => "Content of the post.",
            ],
            'published'=> [
                'type'=>'date',
                'description' => "The date the post is published.",
            ],
            'author_full_name' => [
                'type'          => 'computed',
                'result_type'   => 'string',
                'store'         => true,
                'function'      => 'calcAuthorFullName'
            ]
        ];
    }

}

```

The `author_full_name` field is of type computed and its function for this is `calcAuthorFullName`. When a field is computed, we think a good practice is to prefix with `calc` the name of the function.   

Post extends from Model which has the property :        
```php
lib/equal/orm/Model.class.php

  'creator' => [
                'type'              => 'many2one',
                'foreign_object'    => 'core\User',
                'default'           => QN_ROOT_USER_ID
            ], 
            
```

The special field `creator` gives us the id of the user (`core\User`) who created the post.

We can use `$self` (which is an instance of Post) and its method [read()](https://doc.equal.run/architecture-concepts/orm/#read) to get the creator of a post. We can call subproperties so we will get the creator['fullname'] entry.

```php

     public static function calcAuthorFullName($self){
        $result = [];
        $posts = $self->read(['id', 'creator' => ['fullname']]);
        foreach($posts as $id => $post) {
            $result[$id] = $post['creator']['fullname'];
        };
        return $result;
    }

```


Finally, our file looks like this:

```php
<?php

namespace blog;

use equal\orm\Model;

class Post extends Model {

    public static function getColumns(){

        return [
            'title' => [
                'type'=>'string',
                'description' => "Title of the post.",
            ],
            'content' => [
                'type'=>'text',
                'description' => "Content of the post.",
                ],
            'published'=> [
                'type'=>'date',
                'description' => "The date the post is published.",
                'default'   => null
                ],
            'author_full_name' => [
                'type'          => 'computed',
                'result_type'   => 'string',
                'store'         => true,
                'function'      => 'calcAuthorFullName'
            ]
        ];
    }

    public static function calcAuthorFullName($self){
        $result = [];
        $posts = $self->read(['id', 'creator' => ['fullname']]);
        foreach($posts as $id => $post) {
            $result[$id] = $post['creator']['fullname'];
        };
        return $result;
    }
}

```

Tree structure is now:
```
  /
  /packages
    /blog
      /classes
          Post.class.php      
      /views
      manifest.json
```

## 3. Create some init data

**(Estimated time : 1 minutes)**

in the `blog` folder, we create a new folder named `init` and its subdirectory `data`.
Inside the `data` folder, we create a file named `blog_Post.json`.

Tree structure is now:
```
  /
  /packages
    /blog
      /classes
        Post.class.php      
      /init
        /data
            blog_Post.json
      /views
      manifest.json

```
Content of `blog_Post.json` : 

```json
[
    {
        "name": "blog\\Post",
        "lang": "en",
        "data": [
            {
                "id" : 1,
                "title" : "Post test 1",
                "content": "Lorem ipsum dolor sit amet consectetur, adipisicing elit. Natus, hic voluptates libero explicabo fuga commodi magni nulla ea iure corporis corrupti cum dolores ducimus voluptatem rem provident! Animi, numquam et?",
                "published" : "2023-11-27",
                "creator" : 1
            },
            {
                "id" : 2,
                "title" : "Post test 2",
                "content": "Lorem ipsum dolor sit amet consectetur, adipisicing elit. Natus, hic voluptates libero explicabo fuga commodi magni nulla ea iure corporis corrupti cum dolores ducimus voluptatem rem provident! Animi, numquam et?",
                "published" : "2023-11-27",
                "creator": 1
            }
        ]
    }
]

```

This will create two posts when we initialize our application.

## 4. Create views
**(Estimated time : 10 minutes)**

In the `packages/blog/views`, we create three new files:

Tree structure is now:
```
  /
  /packages
    /blog
      /classes
        Post.class.php      
      /init
        /data
            blog_Post.json      
      /views
        menu.app.left.json
        Post.form.default.json
        Post.list.default.json
      manifest.json
```
### 4.1. menu.app.left.json
A menu with posts and users entries.

```json

{
  "name": "blog menu",
  "access": {
    "groups": [
      "project.default.blog"
    ]
  },
  "layout": {
    "items": [
      {
        "id": "project.project.test",
        "label": "Blog Menu",
        "description": "",
        "icon": "menu_book",
        "type": "parent",
        "children": [
          {
            "id": "project.project.blog",
            "type": "entry",
            "label": "Posts",
            "description": "List of the Posts",
            "context": {
              "entity": "blog\\Post",
              "view": "list.default"
            }
          },
          {
            "id": "project.project.user",
            "type": "entry",
            "label": "Users",
            "description": "Users of the app",
            "context": {
              "entity": "core\\User",
              "view": "list.default"
            }
          }
        ]
      }
    ]
  }
}

```

### 4.2. Post.form.default.json
A form view to create and update posts.

```json
{
  "name": "post.form.default",
  "description": "Create of update a Post",
  "layout": {
    "groups": [
      {
        "label": "New Group",
        "id": "group.0",
        "sections": [
          {
            "label": "Post",
            "id": "section.0",
            "rows": [
              {
                "id": "row.0",
                "label": "New Row",
                "columns": [
                  {
                    "id": "column.0",
                    "label": "New Column",
                    "width": "100%",
                    "items": [
                      {
                        "type": "field",
                        "value": "title",
                        "width": "25%"
                      },
                      {
                        "type": "field",
                        "value": "published",
                        "width": "25%"
                      },
                      {
                        "type": "field",
                        "value": "content",
                        "width": "50%",
                        "widget": {}
                      }
                    ]
                  }
                ]
              }
            ]
          }
        ]
      }
    ]
  }
}
```

### 4.3. Post.list.default.json
A list view to display a list of posts.

```json
{
    "name": "post.list.default",
    "description": "Displays a list of posts",
    "layout": {
        "items": [
            {
                "type": "field",
                "value": "",
                "width": "10%"
            },
            {
                "type": "field",
                "value": "author",
                "width": "25%"
            },
            {
                "type": "field",
                "value": "title",
                "width": "10%"
            },
            {
                "type": "field",
                "value": "content",
                "width": "50%"
            },
            {
                "type": "field",
                "value": "published",
                "width": "25%"
            },
            {
                "type": "field",
                "value": "creator",
                "width": "25%"
            }
        ]
    }
}
```


## 5. Test package consistency and initialize the blog backend app 
**(Estimated time : 1 minute)**

```bash
./equal.run --do=test_package-consistency --package=blog 

./equal.run --do=init_package --package=blog --import=true

```

## 6. Overview of the application

Go to http://equal.local/apps/

Login using the `core/init/data/core_User.json` or your own credentials.
Then click on the `Blog` button to visit the app.

At this stage you have a back end where users can connect and create blog posts.

Now we want to create a front-end so that everybody can read our blog posts. 

## 7. Create an application
**(Estimated time : 15 minutes)**

We will create a simple view in html and fetch the posts from back end in using vanilla javascript.

in the `blog` folder, we create a new folder named `apps` and its subdirectory `blog`.
Inside the `apps/blog` folder, we create three files.

Tree structure is now:
```
  /
  /packages
    /blog
      /apps
        /blog
          export.sh
          index.html
          manifest.json
      /classes
          Post.class.php      
      /init
        /data
      /views
        menu.app.left.json
        Post.form.default.json
        Post.list.default.json
      manifest.json
```

### 7.1. index.html

```html

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eQual Blog</title>
    <link rel="icon" type="image/x-icon" href="https://doc.equal.run/assets/img/favicon.png">
    <style>
      .list {
          list-style: none;
          display: grid;
          gap : 1rem;
      }
      .post {
          background-color: lightgray;
          border-radius: 10px;
          padding: 1rem;
      }
      
      h1,h2 {
          text-align: center;
      }
  </style>
</head>

<body>
    <main>
        <h1>eQual Blog</h1>

        <ul class="list"></ul>

        <script type="module">
            (async () => {
                const apiUrl="http://equal.local/?get=model_collect&entity=blog%5CPost&fields[]=id&fields[]=author_full_name&fields[]=title&fields[]=content&fields[]=published";
                let response = await fetch(apiUrl, {
                    method: "GET",
                    headers: { "Accept": "*/*" }
                });
                let posts = await response.json();
                let ul = document.querySelector(".list");
                posts.forEach(post => {
                    let blogPost = document.createElement("article");
                    let newTitle = document.createElement("h2");
                    let newContent = document.createElement("div");
                    let newAuthor = document.createElement("h3");
                    let newPublished = document.createElement('div');
                    blogPost.classList.add("post")
                    newTitle.textContent = post.title;
                    newContent.innerHTML = post.content;
                    newAuthor.textContent = `Written by ${post.author_full_name}`;
                    newPublished.textContent = `On ${new Date(post.published).toLocaleDateString()}`;
                    blogPost.appendChild(newTitle);
                    blogPost.appendChild(newAuthor);
                    blogPost.appendChild(newPublished);
                    blogPost.appendChild(newContent);
                    ul.appendChild(blogPost);
                });
            })();
        </script>
    </main>
</body>

</html>

```

### 7.2. manifest.json

```json

{
    "name": "blog",
    "description": "blog screen displayed as default App.",
    "version": "1.0",
    "authors": ["YesBabylon"],
    "license": "LGPL-3",
    "url": "/blog",
    "icon": "home",
    "color": "#d2252c",
    "access": {
        "groups": [
            "users"
        ]
    },
    "show_in_apps": false
}

```
* Now in ``packages/blog/manifest.json` add `blog` in "apps"
```json 
{
    "name": "blog",
    "description": "Application Blog",
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
              "users"
            ]
          },
          "params": {
            "menus": {
              "left": "app.left"
            }
          }
        },
        "blog"
      ],
    "tags": [ ]
}

```

### 7.3. export.sh

With the index.html, we will create a zip file called web.app. You can run it directly in your console or you can make a script in export.sh.

```bash

zip web.app index.html

```

Running the export.sh script will create zip the file into web.app in your console.  

```bash

cd /var/www/html/packages/blog/apps/blog/
sh export.sh
```

Initialize your package in /var/www/html/:

```bash
cd /var/www/html/
./equal.run --do=init_package --package=blog

```

This will create the app in /var/www/html/public/blog. 

Now if you got to http://equal.local/blog/, you should see the blog page. 

## 8. Create a controller

We have not created any controller in eQual. If you look at the apiUrl you see that we use the basic built in get=model_collect which will only return data if your user is logged in in eQual. What we want is for everyone to be able to access the blog and read the posts. So we are going to make a public controller and a custom route.

Let's create a controller `packages/blog/data/post/collect.php` and a route `packages/blog/init/routes/98-blog.json`.

Tree structure is now:
```
  /
  /packages
    /blog
      /actions
      /apps
        /blog
          export.sh
          index.html
          manifest.json
      /classes
          Post.class.php      
      /data
        /post
          collect.php
      /init
        /data
        /routes
          98-blog.json
      /views
        menu.app.left.json
        Post.form.default.json
        Post.list.default.json
      manifest.json
```

### 8.1. collect.php
```php
<?php

use \blog\Post;

list($params, $providers) = eQual::announce([
    'description'   => 'This is the blog_post_collect controller.',
    'response'      => [
        'charset'       => 'utf-8',
        'accept-origin' => '*',
        'content-type' => 'application/json'
    ],
    'params'        => [
    ],
    'access'        => [
        'visibility'    => 'public',
        'groups'        => ['users']
    ],
    'providers'         => ['context', "auth"]
]);
/**
 * @var \equal\php\context  $context
 * @var \equal\auth\AuthenticationManager $auth
 */
list($context,$auth) = [$providers['context'],$providers['auth']];

$auth->su();

$params = [
    'fields' => [
        'creator.name',
        'author_full_name',
        'published',
        'title',
        'content'
    ],
];

$res = Post::search()->read($params['fields'])->adapt('json')->get(true);

$context->httpResponse()
        ->body($res)
        ->status(200)
        ->send();

```

### 8.2. 98-blog.json

Let's create the route `/posts` which will use our collect controller to get all posts.

```json
{"\/posts": {
    "GET" : {
        "description" : "Get all blog posts.",
        "operation" : "?get=blog_post_collect"
    }
}}

```

We can change the apiUrl in `packages/blog/apps/blog/index.html` to `http://equal.local/posts` :

```javascript
<script type="module">
    (async () => {
        const apiUrl = "http://equal.local/posts"
        let response = await fetch(apiUrl, {
            method: "GET",
            headers: { "Accept": "*/*" }
        });
        let posts = await response.json();
        let ul = document.querySelector(".list");
        posts.forEach(post => {
            let blogPost = document.createElement('li');
            let newTitle = document.createElement("h2");
            let newContent = document.createElement("div");
            let newAuthor = document.createElement("h3");
            let newPublished = document.createElement('div');
            blogPost.classList.add("post")
            newTitle.textContent = post.title;
            newContent.innerHTML = post.content;
            newAuthor.textContent = `Written by ${post.author_full_name}`;
            newPublished.textContent = `On ${new Date(post.published).toLocaleDateString()}`;
            blogPost.appendChild(newTitle);
            blogPost.appendChild(newAuthor);
            blogPost.appendChild(newPublished);
            blogPost.appendChild(newContent);
            ul.appendChild(blogPost);
        });
    })();
</script>

```

Rerun the export.sh script and initialize your package to apply the changes and your blog should be available for everyone to read now at http://equal.local/blog/.