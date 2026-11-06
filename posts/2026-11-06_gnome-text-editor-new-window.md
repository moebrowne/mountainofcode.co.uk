# Make GNOME Text Editor Always Open A New Window

#GNOME

I use workspaces a lot and it is super annoying when I open text-editor and instead of opening a new window on the same
workspace I get jumped to the workspace where text-editor is already running, and it opens a new tab. This is a [known
issue](https://gitlab.gnome.org/GNOME/gnome-text-editor/-/work_items/835)

Initially I thought I had fixed this by changing my keyboard shortcut to include the `--new-window` agument, however
that was only half the solution. Opening a file which opened in text-editor had the same behaviour.

The fix for the 'open with' problem was to edit the `.desktop` entry. Specifically `/usr/share/applications/org.gnome.TextEditor.desktop`,
or actually it's better to make a local copy in `~/.local/share/applications/` and edit that.

The line which needs changing is `Exec=gnome-text-editor %U` needs to change to `Exec=gnome-text-editor --new-window %U`.
After that run `update-desktop-database ~/.local/share/applications`.
