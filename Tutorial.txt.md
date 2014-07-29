We'll keep this brief,

Workflow
--------

Find the area you need to get work done and then open that area in your
favorite editor or IDE. Everything is isolated into it's own area and you
shouldn't need to worry about anything else.

If you need to work on multiple areas at once the recomendation is to open them
as seperate editor instances, but you may choose to open the containing
directory if you wish. Having multiple monitors helps with productivity.

Finding anything and everything
-------------------------------

The application and structure is designed to be "predictable" which is to say
everything flows in a linear fashion from the entry point. Entry points can be
recognized by the "main" in the file name and are always at the root of the
mini-program they control (`main.php`, `main.js`, `main.scss`, etc). Patterns
that allow for high "discoverability" though the control flow are encouraged
and patterns that cause confusion or require insider knowhow are discouraged.

Entry points may or not contain a main function; in some cases, for clarity,
the function is named more apropriatly or just missing entirely if there is
no need to have a function to begin with; either way the main logic from which
all other logic in the area of interest can be deduced from always has "main"
in the name.

You may think of any area with a "main" in it as a small program, if it helps
focus your efforts. Essentially logic will only flow inside it and the only
contact with the outside (meaning input) is though the "main" entry point,
also, output is only though the entry point as well by consequence.

Technologies
------------

The project uses, besides the simple enough to grasp ones:

 - freia, a php library: http://freialib.github.io/
 - sass, a css preprocessor: http://sass-lang.com/
 - react, client side interface library: http://facebook.github.io/react/
 - gulp, a build system: http://gulpjs.com/ (and by consequence nodejs)
 - browserify, javascript module system: http://browserify.org/
 - composer & npm for package management

And a few other things that are auto-magically handled for you.


	You're now set,
		don't forget to be awesome ~
