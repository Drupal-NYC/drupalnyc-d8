<?php

/**
 * @file
 * Pantheon hooks.
 */

// Maintenance on.
echo "Switch to maintenance mode...\n";
passthru('drush sset system.maintenance_mode 1 -y');
echo "In maintenance mode.\n";
// Run drush deploy.
echo "Process all updates\n";
passthru('drush deploy');
echo "Updates complete.\n";
// Maintenance off.
echo "Come out of maintenance mode\n";
passthru('drush sset system.maintenance_mode 0 -y');
echo "All deployment tasks are complete.\n";
