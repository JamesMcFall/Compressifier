## Compressifier for ExpressionEngine
Compressifier is still in development, but will be an ExpressionEngine plugin allowing EE developers to combine selected JS and CSS files into singular, minified JS/CSS files.

#### Example
The below template code:

    {exp:compressifier}
    /public/css/bootstrap.css
    /public/css/docs.css
    /public/js/bootstrap.js
    {/exp:compressifier}
    
Outputs:

    <link rel="stylesheet" type="text/css" href="/public/compressifier/compressify.css" />
    <script src="/public/compressifier/compressify.js"></script>
    
    
#### Still in development
Currently Compressifier combines all of the selected files and generates the compressified files but **does not minify them**. It also currently re-generates these compressified files on each request. I will be introducing either manual versioning or some automatic ability to detect changes and re-generate the files after I have sorted the minification.
