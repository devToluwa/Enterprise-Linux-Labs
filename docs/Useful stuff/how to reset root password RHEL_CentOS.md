  # How to reset root password for RHEL/CentOS machines
  I wrote this cause i ran into an issue where I made a VM for a minecraft server and set a specific password but forgot it.
  I think this is useful in general, and also useful in a **RHCSA** Exam

  ## Step 1: Interrupt the Boot Process
  1. Reoobt the VM
  2. As soon as the **GRUB boot menu** appears (the one with the list of kernels to choose from), use your arrow keys and make sure youre on the kernal that you use (usually the top entry)
  3. Press `e` to edit the boot parameters

  ## Step 2: Modify the Kernel Line
  1. Look for the line that begins with `linux`.
  2. Move your cursor to the end of that line.
  3. Add a space and type: `rd.break`
  4. Press `Ctrl+X` to boot with these **temporary settings**

  ## Step 3: Remount and Change the Password
  After booting the system will drop you into an emergency shell (`switch_root:/#`). At this point, the system is mounted as "read-only" under `/sysroot`. You need to make it writable and switch into it.

  1. **Remount the filesystem with these permissions:**\
  `mount -0 remount,rw /sysroot`

  2. **Enter the system environment:**\
  `chroot /sysroot`

  3. **Reset the password:**
  Type the following and follow the prompts to enter your new password:\
  `passwd`

## Step 4: SELinux TRAP
This step most people forget. Because you changed teh password file outside the normal boot process **SELinux** will block you from logging in unless you tell it to relabel the files on the next boot. To solve this we do the below:

**1. Create the relabel trigger file**\
`touch /.autorelabel`

**2. Exit and Reboot**\
`exit`\
`exit`