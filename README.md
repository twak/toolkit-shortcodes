Shortcodes Plugin
=================

The following shortcodes are provided by this plugin:

### panel
Adds markup to show enclosed text in a bootstrap "panel". The shortcode encloses content and has a two possible attributes ("title" and "footer").

The following code:
```
[panel title="Panel Title" footer="Panel Footer"]Panel Content[/panel]
```
results in the following markup:
```html
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Panel Title</h3>
    </div>
    <div class="panel-body">Panel Content</div>
    <div class="panel-footer">Panel Footer</div>
</div>
```

### button
Adds markup to create a button. Buttons can have links, titles and types (from _success_, _info_, _warning_, _danger_, _purple_).

The following code:
```
[button link="http://example.com/" text="Click Me!" type="danger"]
```
results in the following markup
```html
<a href="http://example.com/" class="btn btn-lg btn-danger">Click Me!</a>
```

### downloadfile
This adds bootstrap "islands" for download links incorporating icons for different file types. Supported file types are currently _word_, _powerpoint_, _zip_, _pdf_, and _excel_. The shortcode has two possible attributes (`type` and `url`) and encloses the text content which will form the link to the file.

The following code:
```
[downloadfile type="pdf" url="http://example.com/document.pdf"]Download a PDF file[/downloadfile]
```
results in the following markup:
```html
<h4><a class="island island-sm island-m-b skin-box-module downloadlink type-pdf" href="http://example.com/document.pdf">Download a PDF file</a></h4>
```

### gallery
This shortcode replaces the default wordpress shortcode for image galleries and wraps elements in markup which is supported by the theme (i.e. by adding bootstrap classes and enclosing items in `<div>` elements with the appropriate size for the number of columns chosen)