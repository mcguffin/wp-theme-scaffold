Theme Scaffold
==============

Create a blank WordPress theme with bootstrap lib.

Theme _bs Cloned from the fabulous _s theme by Automattic.

Usage:
------
```
$ cd /wordpress/wp-content/themes
$ python /path/to/theme.py "Theme Name" [options]
```
### options:
- `--force`         Override existing theme

### Make theme.py available from everywhere

```
$ mkdir -p ~/.scripts
$ cd ~/.scripts
$ git clone git@github.com:mcguffin/wp-theme-scaffold.git
$ ln -s ./wp-theme-scaffold/theme.py ./wp-theme
```

Finally add `~/.scripts/` to the PATH variable in your `~/.bash_profile`

#### Usage is now:
```
$ cd /wordpress/wp-content/themes
$ wp-theme "Theme Name" [options]
```



