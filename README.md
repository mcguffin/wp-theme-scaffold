Theme Scaffold
==============

Create blank WordPress theme with bootstrap lib.

Usage:
```
$ cd /wordpress/wp-content/themes
$ python /path/to/theme.py "Theme Name" [options]
```
# options: #
- `--force`         Override existing plugin

# Make theme.py available from everywhere #
```
$ mkdir ~/.scripts
$ cd ~/.scripts
$ git clone git@github.com:mcguffin/wp-theme-scaffold.git
$ ln -s ./wp-theme-scaffold/theme.py ./wp-theme
```

Finally add `~/.scripts/` to the PATH variable in your `~/.bash_profile`

