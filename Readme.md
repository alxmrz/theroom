# The room

Demo of semi-3d abilities of PsyXEngine.

[<img src="./resources/screen.png" width="256" height="128" />](./resources/screen.png)

Run with JIT to see 15 framerate, otherwise it will be slower (with xdebug enabled extremely slow): 

`php -dopcache.enable_cli=1 -dopcache.jit=on -dopcache.jit_buffer_size=100M   src/main.php --withMap`

Remove `--withMap` if you do not need map displayed.